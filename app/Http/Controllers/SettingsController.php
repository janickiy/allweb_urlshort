<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingsController\{
    DeleteUserAccountRequest,
    UpdateBillingRequest,
    UpdateUserRequest,
    UpdateUserSecurityRequest
};
use App\Services\PaymentSettingsService;
use App\Services\UserSettingsService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SettingsController extends Controller
{
    /**
     * Inject account and payment settings services.
     */
    public function __construct(
        private readonly UserSettingsService $users,
        private readonly PaymentSettingsService $payments,
    ) {
    }

    /**
     * Redirect the settings landing route to account settings.
     */
    public function index(): mixed
    {
        return view('settings.content', ['view' => 'index', 'user' => Auth::user()]);
    }

    /**
     * Display the authenticated user account settings form.
     */
    public function account(): mixed
    {
        return view('settings.content', ['view' => 'account', 'user' => Auth::user()]);
    }

    /**
     * Display the authenticated user password settings form.
     */
    public function security(): mixed
    {
        return view('settings.content', ['view' => 'security', 'user' => Auth::user()]);
    }

    /**
     * Display the authenticated user subscription list.
     */
    public function subscriptions(): mixed
    {
        $user = Auth::user();

        return view('settings.content', [
            'view' => 'payments.subscriptions.list',
            'user' => $user,
            'subscriptions' => $user->subscriptions,
        ]);
    }

    /**
     * Display the selected subscription details for the authenticated user.
     */
    public function subscriptionsEdit(mixed $id): mixed
    {
        $user = Auth::user();

        return view('settings.content', array_merge([
            'view' => 'payments.subscriptions.edit',
            'user' => $user,
        ], $this->payments->subscriptionEditData($user, $id)));
    }

    /**
     * Display the authenticated user payment methods.
     */
    public function paymentMethods(): mixed
    {
        $user = Auth::user();

        return view('settings.content', array_merge([
            'view' => 'payments.methods.list',
            'user' => $user,
        ], $this->payments->paymentMethods($user)));
    }

    /**
     * Display the form for adding a new payment method.
     */
    public function paymentMethodsNew(Request $request): mixed
    {
        try {
            return view('settings.content', array_merge([
                'view' => 'payments.methods.new',
            ], $this->payments->newPaymentMethodData(Auth::user())));
        } catch (\Exception $e) {
            return redirect()->route('settings.payments.methods')->with('error', $e->getMessage());
        }
    }

    /**
     * Display the form for editing an existing payment method.
     */
    public function paymentMethodsEdit(mixed $id): mixed
    {
        try {
            return view('settings.content', array_merge([
                'view' => 'payments.methods.edit',
            ], $this->payments->editPaymentMethodData(Auth::user(), $id)));
        } catch (\Exception $e) {
            return redirect()->route('settings.payments.methods')->with('error', $e->getMessage());
        }
    }

    /**
     * Display the authenticated user billing information form.
     */
    public function billing(Request $request): mixed
    {
        $user = Auth::user();

        try {
            $customer = $this->payments->billingCustomer($user);
        } catch (\Exception $e) {
            return redirect()->route('settings.payments.billing')->with('error', $e->getMessage());
        }

        return view('settings.content', [
            'view' => 'payments.billing',
            'user' => $user,
            'customer' => $customer,
        ]);
    }

    /**
     * Display the authenticated user invoice list.
     */
    public function invoices(): mixed
    {
        $user = Auth::user();

        return view('settings.content', [
            'view' => 'payments.invoices',
            'user' => $user,
            'invoices' => $user->invoices(),
        ]);
    }

    /**
     * Display a single invoice for the authenticated user.
     */
    public function invoice(mixed $id): mixed
    {
        $user = Auth::user();

        try {
            $invoice = $this->payments->invoice($user, $id);
        } catch (\Exception $e) {
            return redirect()->route('settings.payments.invoices')->with('error', $e->getMessage());
        }

        return view('settings.payments.invoice', [
            'user' => $user,
            'invoice' => $invoice,
            'owner' => $user,
            'product' => __('Subscription'),
        ]);
    }

    /**
     * Display the API settings page for the authenticated user.
     */
    public function api(): mixed
    {
        return view('settings.content', ['view' => 'api', 'user' => Auth::user()]);
    }

    /**
     * Display the account deletion confirmation page.
     */
    public function delete(): mixed
    {
        return view('settings.content', ['view' => 'delete', 'user' => Auth::user()]);
    }

    /**
     * Update the authenticated user profile settings.
     */
    public function updateAccount(UpdateUserRequest $request): mixed
    {
        $this->users->updateProfile(Auth::user(), $request->validated());

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Update the authenticated user password.
     */
    public function updateSecurity(UpdateUserSecurityRequest $request): mixed
    {
        $this->users->updatePassword(Auth::user(), $request->input('password'));

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Attach a new payment method to the authenticated user.
     */
    public function createPaymentMethod(Request $request): mixed
    {
        try {
            $paymentMethod = $this->payments->addPaymentMethod(
                Auth::user(),
                $request->input('payment_method'),
                (bool) $request->input('default')
            );
        } catch (\Exception $e) {
            return redirect()->route('settings.payments.methods.new')->with('error', $e->getMessage());
        }

        return redirect()
            ->route('settings.payments.methods')
            ->with('success', __(':name has been added.', ['name' => $paymentMethod->card->last4]));
    }

    /**
     * Update an existing payment method for the authenticated user.
     */
    public function updatePaymentMethod(Request $request, mixed $id): mixed
    {
        try {
            $this->payments->updatePaymentMethod(Auth::user(), $id, (bool) $request->input('default'));
        } catch (\Exception $e) {
            $request->flash();

            return redirect()->route('settings.payments.methods.edit', $id)->with('error', $e->getMessage());
        }

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Delete an existing payment method for the authenticated user.
     */
    public function deletePaymentMethod(mixed $id): mixed
    {
        try {
            $paymentMethod = $this->payments->deletePaymentMethod(Auth::user(), $id);
        } catch (\Exception $e) {
            return redirect()->route('settings.payments.methods')->with('error', $e->getMessage());
        }

        return redirect()
            ->route('settings.payments.methods')
            ->with('success', __(':name has been deleted.', ['name' => $paymentMethod->card->last4]));
    }

    /**
     * Update the authenticated user billing details.
     */
    public function updateBilling(UpdateBillingRequest $request): mixed
    {
        try {
            $this->payments->updateBilling(Auth::user(), $request->validated());
        } catch (\Exception $e) {
            return redirect()->route('settings.payments.billing')->with('error', $e->getMessage());
        }

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Cancel an active subscription for the authenticated user.
     */
    public function cancelSubscription(mixed $subscription): mixed
    {
        try {
            $this->payments->cancelSubscription(Auth::user(), $subscription);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Resume a canceled subscription for the authenticated user.
     */
    public function resumeSubscription(mixed $subscription): mixed
    {
        try {
            $this->payments->resumeSubscription(Auth::user(), $subscription);
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Regenerate the authenticated user API token.
     */
    public function updateApi(): mixed
    {
        $this->users->regenerateApiToken(Auth::user());

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Delete the authenticated user account after password confirmation.
     */
    public function deleteAccount(DeleteUserAccountRequest $request): mixed
    {
        $this->users->deleteAccount(Auth::user());

        return redirect()->route('home');
    }
}
