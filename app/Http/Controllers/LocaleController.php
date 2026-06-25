<?php

namespace App\Http\Controllers;

use App\Services\LocaleService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;

class LocaleController extends Controller
{
    /**
     * Inject locale service used to change the active locale.
     */
    public function __construct(private readonly LocaleService $locales)
    {
    }

    /**
     * Store the selected locale and redirect back to the previous page.
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function index(Request $request): RedirectResponse
    {
        $this->locales->select($request->input('locale'));

        return redirect()->back();
    }
}
