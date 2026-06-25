<?php

namespace App\Http\Controllers;

use Laravel\Cashier\Http\Controllers\WebhookController as CashierWebhookController;

class WebhookController extends CashierWebhookController
{
    /**
     * Handle a Stripe invoice payment succeeded webhook event.
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function handleInvoicePaymentSucceeded(): \Symfony\Component\HttpFoundation\Response
    {
        // Handle The Event
        return $this->successMethod();
    }
}
