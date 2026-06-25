<?php

namespace App\Http\Controllers;

use App\Http\Requests\Contact\ContactMailRequest;
use App\Services\ContactService;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class ContactController extends Controller
{
    /**
     * Inject the service that sends public contact messages.
     */
    public function __construct(private readonly ContactService $contacts)
    {
    }

    /**
     * Display the public contact form.
     *
     * @return View
     */
    public function index(): View
    {
        return view('contact.index');
    }

    /**
     * Send the contact form message to the configured contact email.
     *
     * @param ContactMailRequest $request
     * @return RedirectResponse
     */
    public function sendMail(ContactMailRequest $request): RedirectResponse
    {
        try {
            $this->contacts->send();
        } catch (\Exception $e) {
            return redirect()->route('contact')->with('error', $e->getMessage());
        }

        return redirect()->route('contact')->with('success', __('Thank you!').' '.__('We\'ve received your message.'));
    }
}
