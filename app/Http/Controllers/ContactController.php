<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactController\ContactMailRequest;
use App\Mail\ContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{

    /**
     * Display the public contact form.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(): mixed
    {
        return view('contact.index');
    }

    /**
     * Send the contact form message to the configured contact email.
     *
     * @param ContactMailRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendMail(ContactMailRequest $request): mixed
    {
        try {
            Mail::to(config('settings.contact_email'))->send(new ContactMail());
        } catch(\Exception $e) {
            return redirect()->route('contact')->with('error', $e->getMessage());
        }

        return redirect()->route('contact')->with('success', __('Thank you!').' '.__('We\'ve received your message.'));
    }
}
