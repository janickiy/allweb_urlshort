<?php

namespace App\Http\Controllers;

use App\Enums\CheckoutStatus;
use App\Http\Requests\Settings\UpdateBillingRequest;
use App\Services\CheckoutService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\View\View;
use Laravel\Cashier\Http\Controllers\PaymentController as CashierPaymentController;

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
     * @return RedirectResponse|View
     */
    public function index(int|string $id, string $period): View|RedirectResponse
    {
        $state = $this->checkout->prepareCheckout(Auth::user(), $id, $period);

        return match ($state['status']) {
            CheckoutStatus::Pricing => redirect()->route('pricing'),
            CheckoutStatus::Confirm => redirect()->route('checkout.confirm', $state['paymentId']),
            CheckoutStatus::Collect => redirect()->route('checkout.collect', ['period' => $state['period']]),
            default => view('checkout.index', $state['data']),
        };
    }

    /**
     * Display the payment details form before returning to checkout.
     *
     * @return RedirectResponse|View
     */
    public function collect(): View|RedirectResponse
    {
        $redirect = Session::get('redirect');
        $state = $this->checkout->prepareCollect(Auth::user(), is_array($redirect) ? $redirect : null);

        return match ($state['status']) {
            CheckoutStatus::PricingError => redirect()->route('pricing')->with('error', $state['error']),
            CheckoutStatus::Pricing => redirect()->route('pricing'),
            default => view('checkout.collect', $state['data']),
        };
    }

    /**
     * Display the payment confirmation form for an incomplete payment intent.
     *
     * @param string $id
     * @return RedirectResponse|View
     */
    public function show($id): View|RedirectResponse
    {
        $payment = $this->checkout->confirmationPayment($id);

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
     * @return View
     */
    public function complete(): View
    {
        return view('checkout.complete');
    }

    /**
     * Display the checkout cancellation page after payment is canceled.
     *
     * @return View
     */
    public function cancelled(): View
    {
        return view('checkout.cancelled');
    }

    /**
     * Create or continue a subscription checkout for the selected plan.
     *
     * @param $id
     * @param $period
     * @return RedirectResponse
     */
    public function subscribe(int|string $id, string $period): RedirectResponse
    {
        $state = $this->checkout->subscribeForCheckout(Auth::user(), $id, $period);

        return match ($state['status']) {
            CheckoutStatus::Pricing => redirect()->route('pricing'),
            CheckoutStatus::Confirm => redirect()->route('checkout.confirm', $state['paymentId']),
            CheckoutStatus::Complete => redirect()->route('checkout.complete'),
            default => redirect()->route('checkout.index', ['id' => $id, 'period' => $period])->with('error', $state['error']),
        };
    }

    /**
     * Update customer payment details and return to checkout.
     *
     * @param UpdateBillingRequest $request
     * @param string $period
     * @return RedirectResponse
     */
    public function updatePaymentDetails(UpdateBillingRequest $request, string $period): RedirectResponse
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

}
