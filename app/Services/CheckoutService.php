<?php

namespace App\Services;

use App\Enums\CheckoutStatus;
use App\Models\Plan;
use App\Models\User;
use App\Repositories\PlanRepository;
use App\Repositories\SubscriptionRepository;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Payment;
use Illuminate\Support\Facades\Session;
use Stripe\Customer;
use Stripe\PaymentIntent;
use Stripe\Stripe;

class CheckoutService
{
    /**
     * Inject dependencies used by checkout operations.
     */
    public function __construct(
        private readonly PlanRepository $plans,
        private readonly SubscriptionRepository $subscriptions,
    ) {
    }

    /**
     * Return the paid plan selected for checkout.
     */
    public function paidPlan(int|string $id)
    {
        return $this->plans->paidByIdOrFail($id);
    }

    /**
     * Return the latest incomplete payment identifier for a subscription.
     *
     * @param User $user
     * @param Plan $plan
     * @return string|null
     */
    public function incompletePaymentId(User $user, Plan $plan): ?string
    {
        $subscription = $this->subscriptions->findForUserByName($user->id, $plan->name);

        return $subscription && $subscription->hasIncompletePayment()
            ? $subscription->latestPayment()->id
            : null;
    }

    /**
     * Prepare the checkout index state for the selected plan.
     *
     * @param User $user
     * @param int|string $id
     * @param string $period
     * @return array
     */
    public function prepareCheckout(User $user, int|string $id, string $period): array
    {
        Session::forget('redirect');

        $plan = $this->paidPlan($id);

        if ($user->subscribed($plan->name)) {
            return ['status' => CheckoutStatus::Pricing];
        }

        if ($paymentId = $this->incompletePaymentId($user, $plan)) {
            return ['status' => CheckoutStatus::Confirm, 'paymentId' => $paymentId];
        }

        try {
            $data = $this->checkoutData($user);
        } catch (\Exception) {
            Session::put(['redirect' => ['id' => $id]]);

            return ['status' => CheckoutStatus::Collect, 'period' => $period];
        }

        return [
            'status' => CheckoutStatus::Ready,
            'data' => array_merge(['plan' => $plan, 'user' => $user], $data),
        ];
    }

    /**
     * Prepare the payment collection state before checkout.
     *
     * @param User $user
     * @param array|null $redirect
     * @return array
     */
    public function prepareCollect(User $user, ?array $redirect): array
    {
        if (! is_array($redirect)) {
            return ['status' => CheckoutStatus::Pricing];
        }

        $plan = $this->paidPlan($redirect['id']);

        try {
            $data = $this->collectData($user);
        } catch (\Exception $exception) {
            return [
                'status' => CheckoutStatus::PricingError,
                'error' => $exception->getMessage(),
            ];
        }

        return [
            'status' => CheckoutStatus::Ready,
            'data' => array_merge(['user' => $user, 'plan' => $plan], $data),
        ];
    }

    /**
     * Return a Cashier payment object for confirmation.
     *
     * @param int|string $id
     * @return Payment
     */
    public function confirmationPayment(int|string $id): Payment
    {
        try {
            return new Payment($this->retrievePaymentIntent($id));
        } catch (\Exception) {
            abort(404);
        }
    }

    /**
     * Build data required by the checkout plan selection page.
     *
     * @return array<string, mixed>
     */
    public function checkoutData(User $user): array
    {
        $paymentMethod = $user->defaultPaymentMethod();

        if (!$paymentMethod) {
            throw new \RuntimeException('Payment method required.');
        }

        return [
            'paymentMethod' => $paymentMethod,
            'customer' => $this->customer($user),
        ];
    }

    /**
     * Build data required by the payment collection page.
     *
     * @return array<string, mixed>
     */
    public function collectData(User $user): array
    {
        return [
            'customer' => $this->customer($user),
            'intent' => $user->createSetupIntent(),
        ];
    }

    /**
     * Start or resume a Stripe subscription checkout for a user.
     *
     * @param User $user
     * @param Plan $plan
     * @param string $period
     * @return string|null
     * @throws \Exception
     */
    public function subscribe(User $user, Plan $plan, string $period): ?string
    {
        try {
            if ($this->subscriptions->findForUserByName($user->id, $plan->name)?->valid()) {
                return null;
            }

            $paymentMethod = $user->defaultPaymentMethod();
            $selectedPlan = $period === 'yearly' ? $plan->plan_year : $plan->plan_month;
            $subscription = $user->newSubscription($plan->name, $selectedPlan);

            if ($plan->trial_days) {
                $subscription->trialDays($plan->trial_days);
            }

            $subscription->create($paymentMethod->id, ['email' => $user->email]);

            return null;
        } catch (IncompletePayment $exception) {
            return $exception->payment->id;
        }
    }

    /**
     * Run the subscription flow for a selected plan and return the controller state.
     *
     * @param User $user
     * @param int|string $id
     * @param string $period
     * @return array
     */
    public function subscribeForCheckout(User $user, int|string $id, string $period): array
    {
        $plan = $this->paidPlan($id);

        try {
            if ($user->subscribed($plan->name)) {
                return ['status' => CheckoutStatus::Pricing];
            }

            if ($paymentId = $this->subscribe($user, $plan, $period)) {
                return ['status' => CheckoutStatus::Confirm, 'paymentId' => $paymentId];
            }

            return ['status' => CheckoutStatus::Complete];
        } catch (\Exception $exception) {
            return [
                'status' => CheckoutStatus::Error,
                'error' => $exception->getMessage(),
            ];
        }
    }

    /**
     * Update billing and payment details for checkout.
     *
     * @param User $user
     * @param array $input
     * @return void
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function updatePaymentDetails(User $user, array $input): void
    {
        $paymentMethod = $input['payment_method'];

        $user->addPaymentMethod($paymentMethod);
        $user->updateDefaultPaymentMethod($paymentMethod);

        $this->updateCustomer($user, $input);
    }

    /**
     * Update the Stripe customer attached to a user.
     *
     * @param User $user
     * @param array $input
     * @return void
     * @throws \Stripe\Exception\ApiErrorException
     */
    private function updateCustomer(User $user, array $input): void
    {
        $this->setApiKey();

        $this->updateStripeCustomer($user->stripe_id, [
            'address' => [
                'city' => $input['city'] ?? null,
                'country' => $input['country'] ?? null,
                'line1' => $input['address'] ?? null,
                'postal_code' => $input['postal_code'] ?? null,
                'state' => $input['state'] ?? null,
            ],
            'name' => $input['name'] ?? null,
            'phone' => $input['phone'] ?? null,
        ]);
    }

    /**
     * Return the Stripe customer for a billable user.
     */
    private function customer(User $user): Customer
    {
        $this->setApiKey();

        return $this->retrieveCustomer($user->stripe_id);
    }

    /**
     * Configure the Stripe API key from application settings.
     */
    private function setApiKey(): void
    {
        Stripe::setApiKey(config('cashier.secret'));
    }

    /**
     * Retrieve a Stripe customer through the Stripe SDK.
     */
    protected function retrieveCustomer(string $customerId): Customer
    {
        return Customer::retrieve($customerId);
    }

    /**
     * Retrieve a Stripe payment intent through Cashier.
     */
    protected function retrievePaymentIntent(int|string $id): PaymentIntent
    {
        return Cashier::stripe()->paymentIntents->retrieve($id);
    }

    /**
     * Update a Stripe customer through the Stripe SDK.
     *
     * @param array<string, mixed> $attributes
     */
    protected function updateStripeCustomer(string $customerId, array $attributes): Customer
    {
        return Customer::update($customerId, $attributes);
    }
}
