<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\PlanRepository;
use App\Repositories\SubscriptionRepository;
use Laravel\Cashier\Invoice as CashierInvoice;
use RuntimeException;
use Stripe\Customer;
use Stripe\Invoice as StripeInvoice;
use Stripe\PaymentMethod;
use Stripe\Stripe;

class PaymentSettingsService
{
    /**
     * Inject dependencies used by payment settings operations.
     */
    public function __construct(
        private readonly SubscriptionRepository $subscriptions,
        private readonly PlanRepository $plans,
    ) {
    }

    /**Build data required to edit a user subscription.
     *
     *
     * @param User $user
     * @param int|string $id
     * @return array
     */
    public function subscriptionEditData(User $user, int|string $id): array
    {
        $subscription = $this->subscriptions->findForUserOrFail($id, $user->id);

        return [
            'subscription' => $subscription,
            'plan' => $this->plans->withTrashedByNameOrFail($subscription->name),
        ];
    }

    /**
     * Return saved payment methods for a user.
     *
     * @return array<string, mixed>
     */
    public function paymentMethods(User $user): array
    {
        try {
            return [
                'defaultPaymentMethod' => $user->defaultPaymentMethod(),
                'paymentMethods' => $user->paymentMethods(),
            ];
        } catch (\Exception) {
            return [
                'defaultPaymentMethod' => null,
                'paymentMethods' => null,
            ];
        }
    }

    /**
     * Build data required to add a payment method.
     *
     * @return array<string, mixed>
     */
    public function newPaymentMethodData(User $user): array
    {
        return [
            'intent' => $user->createSetupIntent(),
            'hasDefaultPaymentMethod' => $user->hasDefaultPaymentMethod(),
        ];
    }


    /**
     * Build data required to edit a saved payment method.
     *
     * @param User $user
     * @param string $id
     * @return array
     */
    public function editPaymentMethodData(User $user, string $id): array
    {
        $paymentMethod = $this->ownedPaymentMethod($user, $id);

        return [
            'id' => $id,
            'defaultPaymentMethod' => $user->defaultPaymentMethod(),
            'paymentMethod' => $paymentMethod,
            'intent' => $user->createSetupIntent(),
        ];
    }

    /**
     * Return billing customer data for a user.
     */
    public function billingCustomer(User $user): Customer
    {
        $this->setApiKey();

        return $this->retrieveCustomer($user->stripe_id);
    }

    /**
     * Return a user invoice by identifier.
     *
     * @param User $user
     * @param string $id
     * @return CashierInvoice
     * @throws \Laravel\Cashier\Exceptions\InvalidInvoice
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function invoice(User $user, string $id): CashierInvoice
    {
        $this->setApiKey();

        return new CashierInvoice($user, $this->retrieveInvoice($id));
    }

    /**
     * Attach a payment method to a user and optionally make it default.
     *
     * @param User $user
     * @param string $paymentMethodId
     * @param bool $makeDefault
     * @return object|\Laravel\Cashier\PaymentMethod
     */
    public function addPaymentMethod(User $user, string $paymentMethodId, bool $makeDefault): object
    {
        $paymentMethod = $user->addPaymentMethod($paymentMethodId);

        if ($makeDefault || !$user->hasDefaultPaymentMethod()) {
            $user->updateDefaultPaymentMethod($paymentMethodId);
        }

        return $paymentMethod;
    }

    /**
     * Update the default payment method for a user.
     *
     * @param User $user
     * @param string $id
     * @param bool $makeDefault
     * @return void
     */
    public function updatePaymentMethod(User $user, string $id, bool $makeDefault): void
    {
        $this->ownedPaymentMethod($user, $id);

        if ($makeDefault) {
            $user->updateDefaultPaymentMethod($id);
        }
    }

    /**
     * Detach a saved payment method from a user.
     *
     * @param User $user
     * @param string $id
     * @return object|PaymentMethod
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function deletePaymentMethod(User $user, string $id): object
    {
        $defaultPaymentMethod = $user->defaultPaymentMethod();
        $paymentMethod = $this->ownedPaymentMethod($user, $id);

        if ($defaultPaymentMethod && $defaultPaymentMethod->id === $paymentMethod->id) {
            foreach ($this->subscriptions->forUser($user->id) as $subscription) {
                if ($subscription->recurring() || ($subscription->onTrial() && !$subscription->onGracePeriod())) {
                    throw new RuntimeException(__('The default payment method can\'t be deleted while a subscription plan is active.'));
                }
            }

            foreach ($user->paymentMethods() as $stripePaymentMethod) {
                if ($paymentMethod->id !== $stripePaymentMethod->id && $user->updateDefaultPaymentMethod($stripePaymentMethod->id)) {
                    break;
                }
            }
        }

        $this->detachPaymentMethod($paymentMethod);

        return $paymentMethod;
    }

    /**
     * Update billing details for a user.
     *
     * @param User $user
     * @param array $input
     * @return void
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function updateBilling(User $user, array $input): void
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
     * Cancel an active subscription for a user.
     *
     * @param User $user
     * @param string $name
     * @return void
     */
    public function cancelSubscription(User $user, string $name): void
    {
        $subscription = $this->subscriptions->findForUserByName($user->id, $name);

        if (!$subscription) {
            return;
        }

        if ($subscription->hasIncompletePayment()) {
            $subscription->cancelNow();
            return;
        }

        $subscription->cancel();
    }


    /**
     * Resume a canceled subscription for a user.
     *
     * @param User $user
     * @param string $name
     * @return void
     */
    public function resumeSubscription(User $user, string $name): void
    {
        if (!$user->hasDefaultPaymentMethod()) {
            throw new RuntimeException(__('Your subscription can\'t be resumed without a payment method.'));
        }

        $subscription = $this->subscriptions->findForUserByName($user->id, $name);

        if ($subscription) {
            if ($subscription->hasIncompletePayment()) {
                abort(403);
            }

            $subscription->resume();
        }
    }

    /**
     * Return a payment method owned by a user or throw.
     *
     * @param User $user
     * @param string $id
     * @return PaymentMethod
     */
    private function ownedPaymentMethod(User $user, string $id): PaymentMethod
    {
        $this->setApiKey();

        try {
            $paymentMethod = $this->retrievePaymentMethod($id);
        } catch (\Exception) {
            abort(404);
        }

        if ($user->stripe_id !== $paymentMethod->customer) {
            abort(404);
        }

        return $paymentMethod;
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
     * Retrieve a Stripe invoice through the Stripe SDK.
     */
    protected function retrieveInvoice(string $invoiceId): StripeInvoice
    {
        return StripeInvoice::retrieve($invoiceId);
    }

    /**
     * Retrieve a Stripe payment method through the Stripe SDK.
     */
    protected function retrievePaymentMethod(string $paymentMethodId): PaymentMethod
    {
        return PaymentMethod::retrieve($paymentMethodId);
    }

    /**
     * Detach a Stripe payment method through the Stripe SDK.
     */
    protected function detachPaymentMethod(PaymentMethod $paymentMethod): object
    {
        return $paymentMethod->detach();
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
