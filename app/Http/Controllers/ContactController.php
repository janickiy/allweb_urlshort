<?php

namespace App\Http\Controllers;

use App\Http\Requests\ContactMailRequest;
use App\Mail\ContactMail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Mail;

class ContactController extends Controller
{

    /**
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index()
    {
        return view('contact.index');
    }

    /**
     * @param ContactMailRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function sendMail(ContactMailRequest $request)
    {
        try {
            Mail::to(config('settings.contact_email'))->send(new ContactMail());
        } catch(\Exception $e) {
            return redirect()->route('contact')->with('error', $e->getMessage());
        }

        return redirect()->route('contact')->with('success', __('Thank you!').' '.__('We\'ve received your message.'));
    }
}
