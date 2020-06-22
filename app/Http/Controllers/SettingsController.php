<?php

namespace App\Http\Controllers;

use App\Http\Requests\DeleteUserAccountRequest;
use App\Http\Requests\UpdateBillingRequest;
use App\Http\Requests\UpdateUserRequest;
use App\Http\Requests\UpdateUserSecurityRequest;
use App\Plan;
use App\Subscription;
use App\Traits\UserTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Laravel\Cashier\Invoice;
use Laravel\Cashier\PaymentMethod;
use Stripe;

class SettingsController extends Controller
{
    use UserTrait;

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        $user = Auth::user();

        return view('settings.content', ['view' => 'index', 'user' => $user]);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function account()
    {
        $user = Auth::user();

        return view('settings.content', ['view' => 'account', 'user' => $user]);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function security()
    {
        $user = Auth::user();

        return view('settings.content', ['view' => 'security', 'user' => $user]);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function subscriptions()
    {
        $user = Auth::user();

        $subscriptions = $user->subscriptions;

        return view('settings.content', ['view' => 'payments.subscriptions.list', 'user' => $user, 'subscriptions' => $subscriptions]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function subscriptionsEdit($id)
    {
        $user = Auth::user();

        $subscription = Subscription::where([['id', $id], ['user_id', $user->id]])->firstOrFail();
        $plan = Plan::withTrashed()->where('name', $subscription->name)->firstOrFail();

        return view('settings.content', ['view' => 'payments.subscriptions.edit', 'user' => $user, 'subscription' => $subscription, 'plan' => $plan]);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function paymentMethods()
    {
        $user = Auth::user();

        try {
            $defaultPaymentMethod = $user->defaultPaymentMethod();
            $paymentMethods = $user->paymentMethods();
        } catch (\Exception $e) {
            $defaultPaymentMethod = null;
            $paymentMethods = null;
        }

        return view('settings.content', ['view' => 'payments.methods.list', 'user' => $user, 'defaultPaymentMethod' => $defaultPaymentMethod, 'paymentMethods' => $paymentMethods]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function paymentMethodsNew(Request $request)
    {
        $user = Auth::user();

        $hasDefaultPaymentMethod = $user->hasDefaultPaymentMethod();

        try {
            $intent = $user->createSetupIntent();
        } catch (\Exception $e) {
            return redirect()->route('settings.payments.methods')->with('error', $e->getMessage());
        }

        return view('settings.content', ['view' => 'payments.methods.new', 'intent' => $intent, 'hasDefaultPaymentMethod' => $hasDefaultPaymentMethod]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function paymentMethodsEdit($id)
    {
        $user = Auth::user();

        \Stripe\Stripe::setApiKey(config('cashier.secret'));

        // Retrieve the payment method details
        try {
            $defaultPaymentMethod = $user->defaultPaymentMethod();
            $paymentMethod = \Stripe\PaymentMethod::retrieve($id);
        } catch (\Exception $e) {
            // If the payment method does not exist
            abort(404);
        }

        // If the payment method does not belong to the customer
        if ($user->stripe_id != $paymentMethod->customer) {
            abort(404);
        }

        return view('settings.content', ['view' => 'payments.methods.edit', 'id' => $id, 'defaultPaymentMethod' => $defaultPaymentMethod, 'paymentMethod' => $paymentMethod, 'intent' => $user->createSetupIntent()]);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function billing(Request $request)
    {
        $user = Auth::user();

        try {
            \Stripe\Stripe::setApiKey(config('cashier.secret'));
            $customer = \Stripe\Customer::retrieve($user->stripe_id);
        } catch (\Exception $e) {
            return redirect()->route('settings.payments.billing')->with('error', $e->getMessage());
        }

        return view('settings.content', ['view' => 'payments.billing', 'user' => $user, 'customer' => $customer]);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function invoices()
    {
        $user = Auth::user();

        $invoices = $user->invoices();

        return view('settings.content', ['view' => 'payments.invoices', 'user' => $user, 'invoices' => $invoices]);
    }

    /**
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     */
    public function invoice($id)
    {
        $user = Auth::user();

        try {
            \Stripe\Stripe::setApiKey(config('cashier.secret'));
            $invoice = new Invoice($user, \Stripe\Invoice::retrieve($id));
        } catch (\Exception $e) {
            return redirect()->route('settings.payments.invoices')->with('error', $e->getMessage());
        }

        return view('settings.payments.invoice', [
            'user' => $user,
            'invoice' => $invoice,
            'owner' => $user,
            'product' => __('Subscription')

        ]);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function api()
    {
        $user = Auth::user();

        return view('settings.content', ['view' => 'api', 'user' => $user]);
    }

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function delete()
    {
        $user = Auth::user();

        return view('settings.content', ['view' => 'delete', 'user' => $user]);
    }

    /**
     * @param UpdateUserRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateAccount(UpdateUserRequest $request)
    {
        $user = Auth::user();

        $this->userUpdate($request, $user);

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * @param UpdateUserSecurityRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSecurity(UpdateUserSecurityRequest $request)
    {
        $user = Auth::user();

        $user->password = Hash::make($request->input('password'));
        $user->save();

        Auth::logoutOtherDevices($request->input('password'));

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createPaymentMethod(Request $request)
    {
        $user = Auth::user();

        try {
            $paymentMethod = $user->addPaymentMethod($request->input('payment_method'));

            // If the user marked his payment method as default, or if there's no default payment
            if ($request->input('default') || !$user->hasDefaultPaymentMethod()) {
                $user->updateDefaultPaymentMethod($request->input('payment_method'));
            }
        } catch (\Exception $e) {
            return redirect()->route('settings.payments.methods.new')->with('error', $e->getMessage());
        }

        return redirect()->route('settings.payments.methods')->with('success', __(':name has been added.', ['name' => $paymentMethod->card->last4]));
    }

    /**
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePaymentMethod(Request $request, $id)
    {
        $user = Auth::user();

        \Stripe\Stripe::setApiKey(config('cashier.secret'));

        // Retrieve the payment method details
        try {
            $paymentMethod = \Stripe\PaymentMethod::retrieve($id);
        } catch (\Exception $e) {
            // If the payment method does not exist
            abort(404);
        }

        // If the payment method does not belong to the customer
        if ($user->stripe_id != $paymentMethod->customer) {
            abort(404);
        }

        // Update the payment method details
        try {
            if ($request->input('default')) {
                $user->updateDefaultPaymentMethod($id);
            }
        } catch (\Exception $e) {
            $request->flash();
            return redirect()->route('settings.payments.methods.edit', $id)->with('error', $e->getMessage());
        }

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deletePaymentMethod($id)
    {
        $user = Auth::user();

        \Stripe\Stripe::setApiKey(config('cashier.secret'));

        // Retrieve the payment method details
        try {
            $defaultPaymentMethod = $user->defaultPaymentMethod();
            $paymentMethod = \Stripe\PaymentMethod::retrieve($id);
        } catch (\Exception $e) {
            // If the payment method does not exist
            abort(404);
        }

        // If the payment method does not belong to the customer
        if ($user->stripe_id != $paymentMethod->customer) {
            abort(404);
        }

        // If the payment method is the default one
        if ($defaultPaymentMethod->id == $paymentMethod->id) {

            // Iterate over the user's subscriptions
            foreach ($user->subscriptions as $subscription) {
                // If the user has an active subscription
                if ($subscription) {
                    // Check if the subscription is Recurring or on Trial (and not already cancelled)
                    if ($user->subscription($subscription->name)->recurring() || ($user->subscription($subscription->name)->onTrial() && !$user->subscription($subscription->name)->onGracePeriod())) {
                        return redirect()->route('settings.payments.methods')->with('error', __('The default payment method can\'t be deleted while a subscription plan is active.'));
                    }
                }
            }
        }

        try {
            // If the deleted payment method is the default one
            if ($defaultPaymentMethod->id == $paymentMethod->id) {
                // Iterate over the other available payment methods
                foreach ($user->paymentMethods() as $stripePaymentMethod) {
                    // If the selected payment method is not the same with the available one
                    if ($paymentMethod->id != $stripePaymentMethod->id) {
                        // Attempt to update the default payment method to another available payment method
                        if ($user->updateDefaultPaymentMethod($stripePaymentMethod->id)) {
                            break;
                        }
                    }
                }

                // Delete the default payment method
                $paymentMethod->detach();
            } else {
                $paymentMethod->detach();
            }
        } catch (\Exception $e) {
            return redirect()->route('settings.payments.methods')->with('error', $e->getMessage());
        }

        return redirect()->route('settings.payments.methods')->with('success', __(':name has been deleted.', ['name' => $paymentMethod->card->last4]));
    }

    /**
     * @param UpdateBillingRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateBilling(UpdateBillingRequest $request)
    {
        $user = Auth::user();

        \Stripe\Stripe::setApiKey(config('cashier.secret'));

        // Update the payment method details
        try {
            \Stripe\Customer::update($user->stripe_id, [
                'address' => [
                    'city' => $request->input('city'),
                    'country' => $request->input('country'),
                    'line1' => $request->input('address'),
                    'postal_code' => $request->input('postal_code'),
                    'state' => $request->input('state'),
                ],
                'name' => $request->input('name'),
                'phone' => $request->input('phone')
            ]);
        } catch (\Exception $e) {
            return redirect()->route('settings.payments.billing')->with('error', $e->getMessage());
        }

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * @param $subscription
     * @return \Illuminate\Http\RedirectResponse
     */
    public function cancelSubscription($subscription)
    {
        $user = Auth::user();

        // Get the user's subscription
        $subscription = $user->subscription($subscription);

        if ($subscription) {
            try {
                // Cancel the subscription
                if ($subscription->hasIncompletePayment()) {
                    $subscription->cancelNow();
                } else {
                    $subscription->cancel();
                }
            } catch (\Exception  $e) {
                return back()->with('error', $e->getMessage());
            }
        }

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * @param $subscription
     * @return \Illuminate\Http\RedirectResponse
     */
    public function resumeSubscription($subscription)
    {
        $user = Auth::user();

        // If the user has no payment method
        if (!$user->hasDefaultPaymentMethod()) {
            return back()->with('error', __('Your subscription can\'t be resumed without a payment method.'));
        }

        if ($user->hasIncompletePayment($subscription)) {
            abort(403);
        }

        // Get the user's subscription
        $subscription = $user->subscription($subscription);

        // If there's a subscription
        if ($subscription) {
            try {
                // Resume the subscription
                $subscription->resume();
            } catch (\Exception $e) {
                return back()->with('error', $e->getMessage());
            }
        }

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateApi()
    {
        $user = Auth::user();

        $user->api_token = Str::random(60);
        $user->save();

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * @param DeleteUserAccountRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteAccount(DeleteUserAccountRequest $request)
    {
        $user = Auth::user();
        $user->forceDelete();

        return redirect()->route('home');
    }
}
