<?php

namespace App\Services;

use App\Models\Plan;
use App\Models\User;
use App\Repositories\PlanRepository;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Stripe\Customer;
use Stripe\Stripe;

class CheckoutService
{
    public function __construct(private readonly PlanRepository $plans)
    {
    }

    public function paidPlan(int|string $id): Plan
    {
        return $this->plans->paidByIdOrFail($id);
    }

    public function incompletePaymentId(User $user, Plan $plan): ?string
    {
        $subscription = $user->subscription($plan->name);

        return $subscription && $subscription->hasIncompletePayment()
            ? $subscription->latestPayment()->id
            : null;
    }

    /**
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
     * @return array<string, mixed>
     */
    public function collectData(User $user): array
    {
        return [
            'customer' => $this->customer($user),
            'intent' => $user->createSetupIntent(),
        ];
    }

    public function subscribe(User $user, Plan $plan, string $period): ?string
    {
        try {
            if ($user->subscribed($plan->name)) {
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
     * @param array<string, mixed> $input
     */
    public function updatePaymentDetails(User $user, array $input): void
    {
        $paymentMethod = $input['payment_method'];

        $user->addPaymentMethod($paymentMethod);
        $user->updateDefaultPaymentMethod($paymentMethod);

        $this->updateCustomer($user, $input);
    }

    /**
     * @param array<string, mixed> $input
     */
    private function updateCustomer(User $user, array $input): void
    {
        $this->setApiKey();

        Customer::update($user->stripe_id, [
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

    private function customer(User $user): Customer
    {
        $this->setApiKey();

        return Customer::retrieve($user->stripe_id);
    }

    private function setApiKey(): void
    {
        Stripe::setApiKey(config('cashier.secret'));
    }
}
