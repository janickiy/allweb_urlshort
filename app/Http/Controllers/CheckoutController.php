<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateBillingRequest;
use App\Http\Requests\updatePaymentDetails;
use App\Plan;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Exceptions\IncompletePayment;
use Laravel\Cashier\Http\Controllers\PaymentController as CashierPaymentController;
use Laravel\Cashier\Payment;
use Stripe;
use Stripe\PaymentIntent as StripePaymentIntent;

class CheckoutController extends CashierPaymentController
{
    /**
     * Display the checkout form.
     *
     * @param $id
     * @param $period
     * @return Factory|RedirectResponse|View
     */
    public function index($id, $period)
    {
        Session::forget('redirect');

        $plan = Plan::where([['id', '=', $id], ['amount_month', '>', 0], ['amount_year', '>', 0]])->firstOrFail();
        $user = Auth::user();

        // If the user is already subscribed to the selected plan
        if ($user->subscribed($plan->name)) {
            return redirect()->route('pricing');
        }

        $subscription = $user->subscription($plan->name);

        // If the user has an incomplete payment
        if ($subscription && $subscription->hasIncompletePayment()) {
            // Redirect the user to confirm his payment
            return redirect()->route('checkout.confirm', $subscription->latestPayment()->id);
        }

        $redirect = ['redirect' => ['id' => $id]];

        try {
            $paymentMethod = $user->defaultPaymentMethod();

            if (!$paymentMethod) {
                Session::put($redirect);
                return redirect()->route('checkout.collect', ['period' => $period]);
            }

            \Stripe\Stripe::setApiKey(config('cashier.secret'));
            $customer = \Stripe\Customer::retrieve($user->stripe_id);
        } catch (\Exception $e) {
            Session::put($redirect);
            return redirect()->route('checkout.collect', ['period' => $period]);
        }

        return view('checkout.index', ['plan' => $plan, 'user' => $user, 'customer' => $customer, 'paymentMethod' => $paymentMethod]);
    }

    /**
     * Display the Payment Details form.
     *
     * @param Request $request
     * @param $period
     * @return Factory|RedirectResponse|View
     */
    public function collect(Request $request, $period)
    {
        $user = Auth::user();

        $redirect = Session::get('redirect');

        if (!is_array($redirect)) {
            return redirect()->route('pricing');
        }

        $plan = $plan = Plan::where([['id', '=', $redirect['id']], ['amount_month', '>', 0], ['amount_year', '>', 0]])->firstOrFail();

        try {
            \Stripe\Stripe::setApiKey(config('cashier.secret'));
            $customer = \Stripe\Customer::retrieve($user->stripe_id);

            $intent = $user->createSetupIntent();
        } catch (\Exception $e) {
            return redirect()->route('pricing')->with('error', $e->getMessage());
        }

        return view('checkout.collect', ['user' => $user, 'intent' => $intent, 'customer' => $customer, 'plan' => $plan]);
    }

    /**
     * Display the form to gather additional payment verification for the given payment.
     *
     * @param string $id
     * @return Factory|View
     */
    public function show($id)
    {
        try {
            $payment = new Payment(
                StripePaymentIntent::retrieve($id, Cashier::stripeOptions())
            );
        } catch (\Exception $e) {
            abort(404);
        }

        if ($payment->isSucceeded()) {
            return redirect()->route('checkout.complete');
        }

        if ($payment->isCancelled()) {
            return redirect()->route('checkout.cancelled');
        }

        return view('checkout.confirm', [
            'stripeKey' => config('cashier.key'),
            'payment' => $payment,
            'redirect' => request('redirect')
        ]);
    }

    /**
     * Display the Payment complete page.
     *
     * @return Factory|View
     */
    public function complete()
    {
        return view('checkout.complete');
    }

    /**
     * Display the Payment cancelled page.
     *
     * @return Factory|View
     */
    public function cancelled()
    {
        return view('checkout.cancelled');
    }

    /**
     * @param Request $request
     * @param $id
     * @param $period
     * @return RedirectResponse
     */
    public function subscribe(Request $request, $id, $period)
    {
        $plan = Plan::where([['id', '=', $id], ['amount_month', '>', 0], ['amount_year', '>', 0]])->firstOrFail();
        $user = Auth::user();

        try {
            // If the user is already subscribed to the selected plan
            if ($user->subscribed($plan->name)) {
                return redirect()->route('pricing');
            }

            $paymentMethod = $user->defaultPaymentMethod();

            if ($period == 'yearly') {
                $selectedPlan = $plan->plan_year;
            } else {
                $selectedPlan = $plan->plan_month;
            }

            if ($plan->trial_days) {
                $user->newSubscription($plan->name, $selectedPlan)->trialDays($plan->trial_days)->create($paymentMethod->id, [
                    'email' => $user->email
                ]);
            } else {
                $user->newSubscription($plan->name, $selectedPlan)->create($paymentMethod->id, [
                    'email' => $user->email
                ]);
            }

            return redirect()->route('checkout.complete');
        } catch (IncompletePayment $exception) {
            return redirect()->route('checkout.confirm', $exception->payment->id);
        } catch (\Exception $e) {
            return redirect()->route('checkout.index', ['id' => $id, 'period' => $period])->with('error', $e->getMessage());
        }
    }

    /**
     * @param UpdateBillingRequest $request
     * @param $period
     * @return RedirectResponse
     */
    public function updatePaymentDetails(UpdateBillingRequest $request, $period)
    {
        $user = Auth::user();

        try {
            $user->addPaymentMethod($request->input('payment_method'));

            // If the user marked his payment method as default, or if there's no default payment
            $user->updateDefaultPaymentMethod($request->input('payment_method'));

            \Stripe\Stripe::setApiKey(config('cashier.secret'));
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
            return redirect()->route('pricing')->with('error', $e->getMessage());
        }

        $redirect = Session::get('redirect');

        // Redirect back to the checkout page
        return redirect()->route('checkout.index', ['id' => $redirect['id'], 'period' => $period]);
    }

    /**
     * @return \string[][]
     */
    private function menu()
    {
        /**
         * key => [icon, title, route]
         */
        $menu = [
            'dashboard' => ['dashboard', 'Dashboard', 'dashboard'],
            'links' => ['link', 'Links', 'links'],
            'spaces' => ['space', 'Spaces', 'spaces'],
            'domains' => ['domain', 'Domains', 'domains'],
        ];

        return $menu;
    }
}
