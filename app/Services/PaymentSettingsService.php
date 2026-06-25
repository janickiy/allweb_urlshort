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
    public function __construct(
        private readonly SubscriptionRepository $subscriptions,
        private readonly PlanRepository $plans,
    ) {
    }

    /**
     * @return array<string, mixed>
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
     * @return array<string, mixed>
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

    public function billingCustomer(User $user): Customer
    {
        $this->setApiKey();

        return Customer::retrieve($user->stripe_id);
    }

    public function invoice(User $user, string $id): CashierInvoice
    {
        $this->setApiKey();

        return new CashierInvoice($user, StripeInvoice::retrieve($id));
    }

    public function addPaymentMethod(User $user, string $paymentMethodId, bool $makeDefault): object
    {
        $paymentMethod = $user->addPaymentMethod($paymentMethodId);

        if ($makeDefault || !$user->hasDefaultPaymentMethod()) {
            $user->updateDefaultPaymentMethod($paymentMethodId);
        }

        return $paymentMethod;
    }

    public function updatePaymentMethod(User $user, string $id, bool $makeDefault): void
    {
        $this->ownedPaymentMethod($user, $id);

        if ($makeDefault) {
            $user->updateDefaultPaymentMethod($id);
        }
    }

    public function deletePaymentMethod(User $user, string $id): object
    {
        $defaultPaymentMethod = $user->defaultPaymentMethod();
        $paymentMethod = $this->ownedPaymentMethod($user, $id);

        if ($defaultPaymentMethod && $defaultPaymentMethod->id === $paymentMethod->id) {
            foreach ($user->subscriptions as $subscription) {
                if ($subscription && ($user->subscription($subscription->name)->recurring() || ($user->subscription($subscription->name)->onTrial() && !$user->subscription($subscription->name)->onGracePeriod()))) {
                    throw new RuntimeException(__('The default payment method can\'t be deleted while a subscription plan is active.'));
                }
            }

            foreach ($user->paymentMethods() as $stripePaymentMethod) {
                if ($paymentMethod->id !== $stripePaymentMethod->id && $user->updateDefaultPaymentMethod($stripePaymentMethod->id)) {
                    break;
                }
            }
        }

        $paymentMethod->detach();

        return $paymentMethod;
    }

    /**
     * @param array<string, mixed> $input
     */
    public function updateBilling(User $user, array $input): void
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

    public function cancelSubscription(User $user, string $name): void
    {
        $subscription = $user->subscription($name);

        if (!$subscription) {
            return;
        }

        if ($subscription->hasIncompletePayment()) {
            $subscription->cancelNow();
            return;
        }

        $subscription->cancel();
    }

    public function resumeSubscription(User $user, string $name): void
    {
        if (!$user->hasDefaultPaymentMethod()) {
            throw new RuntimeException(__('Your subscription can\'t be resumed without a payment method.'));
        }

        if ($user->hasIncompletePayment($name)) {
            abort(403);
        }

        $subscription = $user->subscription($name);

        if ($subscription) {
            $subscription->resume();
        }
    }

    private function ownedPaymentMethod(User $user, string $id): PaymentMethod
    {
        $this->setApiKey();

        try {
            $paymentMethod = PaymentMethod::retrieve($id);
        } catch (\Exception) {
            abort(404);
        }

        if ($user->stripe_id !== $paymentMethod->customer) {
            abort(404);
        }

        return $paymentMethod;
    }

    private function setApiKey(): void
    {
        Stripe::setApiKey(config('cashier.secret'));
    }
}
