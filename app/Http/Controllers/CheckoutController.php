<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingsController\UpdateBillingRequest;
use App\Services\CheckoutService;
use Illuminate\Contracts\View\Factory;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Laravel\Cashier\Cashier;
use Laravel\Cashier\Http\Controllers\PaymentController as CashierPaymentController;
use Laravel\Cashier\Payment;
use Stripe\PaymentIntent as StripePaymentIntent;

class CheckoutController extends CashierPaymentController
{
    /**
     * Inject checkout services used by payment flow actions.
     */
    public function __construct(private readonly CheckoutService $checkout)
    {
    }

    /**
     * Display the checkout form for the selected paid plan and billing period.
     *
     * @param $id
     * @param $period
     * @return Factory|RedirectResponse|View
     */
    public function index(mixed $id, mixed $period): mixed
    {
        Session::forget('redirect');

        $plan = $this->checkout->paidPlan($id);
        $user = Auth::user();

        // If the user is already subscribed to the selected plan
        if ($user->subscribed($plan->name)) {
            return redirect()->route('pricing');
        }

        if ($paymentId = $this->checkout->incompletePaymentId($user, $plan)) {
            // Redirect the user to confirm his payment
            return redirect()->route('checkout.confirm', $paymentId);
        }

        $redirect = ['redirect' => ['id' => $id]];

        try {
            $data = $this->checkout->checkoutData($user);
        } catch (\Exception $e) {
            Session::put($redirect);
            return redirect()->route('checkout.collect', ['period' => $period]);
        }

        return view('checkout.index', array_merge(['plan' => $plan, 'user' => $user], $data));
    }

    /**
     * Display the payment details form before returning to checkout.
     *
     * @param Request $request
     * @param $period
     * @return Factory|RedirectResponse|View
     */
    public function collect(Request $request, mixed $period): mixed
    {
        $user = Auth::user();

        $redirect = Session::get('redirect');

        if (!is_array($redirect)) {
            return redirect()->route('pricing');
        }

        $plan = $this->checkout->paidPlan($redirect['id']);

        try {
            $data = $this->checkout->collectData($user);
        } catch (\Exception $e) {
            return redirect()->route('pricing')->with('error', $e->getMessage());
        }

        return view('checkout.collect', array_merge(['user' => $user, 'plan' => $plan], $data));
    }

    /**
     * Display the payment confirmation form for an incomplete payment intent.
     *
     * @param string $id
     * @return Factory|View
     */
    public function show(mixed $id): mixed
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
     * Display the checkout completion page after payment succeeds.
     *
     * @return Factory|View
     */
    public function complete(): mixed
    {
        return view('checkout.complete');
    }

    /**
     * Display the checkout cancellation page after payment is canceled.
     *
     * @return Factory|View
     */
    public function cancelled(): mixed
    {
        return view('checkout.cancelled');
    }

    /**
     * Create or continue a subscription checkout for the selected plan.
     *
     * @param Request $request
     * @param $id
     * @param $period
     * @return RedirectResponse
     */
    public function subscribe(Request $request, mixed $id, mixed $period): mixed
    {
        $plan = $this->checkout->paidPlan($id);
        $user = Auth::user();

        try {
            // If the user is already subscribed to the selected plan
            if ($user->subscribed($plan->name)) {
                return redirect()->route('pricing');
            }

            if ($paymentId = $this->checkout->subscribe($user, $plan, $period)) {
                return redirect()->route('checkout.confirm', $paymentId);
            }

            return redirect()->route('checkout.complete');
        } catch (\Exception $e) {
            return redirect()->route('checkout.index', ['id' => $id, 'period' => $period])->with('error', $e->getMessage());
        }
    }

    /**
     * Update customer payment details and return to checkout.
     *
     * @param UpdateBillingRequest $request
     * @param $period
     * @return RedirectResponse
     */
    public function updatePaymentDetails(UpdateBillingRequest $request, mixed $period): mixed
    {
        $user = Auth::user();

        try {
            $this->checkout->updatePaymentDetails($user, $request->all());
        } catch (\Exception $e) {
            return redirect()->route('pricing')->with('error', $e->getMessage());
        }

        $redirect = Session::get('redirect');

        // Redirect back to the checkout page
        return redirect()->route('checkout.index', ['id' => $redirect['id'], 'period' => $period]);
    }

    /**
     * Return the dashboard menu metadata used by checkout views.
     *
     * @return \string[][]
     */
    private function menu(): mixed
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
