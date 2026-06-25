<?php

namespace App\Http\Controllers;

use App\Http\Requests\LinksController\CreateLinkRequest;
use App\Services\HomeService;
use App\Services\LinkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomeController extends Controller
{
    /**
     * Inject services used by public home and shortening actions.
     */
    public function __construct(
        private readonly HomeService $homeService,
        private readonly LinkService $linkService,
    ) {
    }

    /**
     * Display the landing page or redirect custom-domain home requests.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request): mixed
    {
        // If the user is logged-in, redirect to dashboard
        if (Auth::check()) {
            return redirect()->route('dashboard');
        }

        if (config('settings.index')) {
            return redirect()->to(config('settings.index'), 301);
        }

        if ($redirect = $this->homeService->domainIndexRedirect($request)) {
            return redirect()->to($redirect, 301);
        }

        $data = $this->homeService->landingData();

        return view('home.index', $data);
    }

    /**
     * Create a guest or authenticated short link from public form input.
     *
     * @param CreateLinkRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createLink(CreateLinkRequest $request): mixed
    {
        if (!config('settings.short_guest')) {
            abort(404);
        }

        $this->linkService->create($request->all());

        return redirect()->back()->with('link', $this->linkService->latestForUser(0, 1));
    }
}
