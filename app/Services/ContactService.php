<?php

namespace App\Services;

use App\Mail\ContactMail;
use Illuminate\Support\Facades\Mail;

class ContactService
{
    /**
     * Send the public contact form message to the configured recipient.
     */
    public function send(): void
    {
        Mail::to(config('settings.contact_email'))->send(new ContactMail());
    }
}
