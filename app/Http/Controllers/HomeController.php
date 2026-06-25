<?php

namespace App\Http\Controllers;

use App\Http\Requests\Links\CreateLinkRequest;
use App\Services\HomeService;
use App\Services\LinkService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

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
     * @return RedirectResponse|View;
     */
    public function index(Request $request): RedirectResponse|View
    {
        if ($redirect = $this->homeService->landingRedirect($request, Auth::check())) {
            return redirect()->to($redirect['url'], $redirect['status']);
        }

        $data = $this->homeService->landingData();

        return view('home.index', $data);
    }

    /**
     * Create a guest or authenticated short link from public form input.
     *
     * @param CreateLinkRequest $request
     * @return RedirectResponse
     */
    public function createLink(CreateLinkRequest $request): RedirectResponse
    {
        return redirect()->back()->with('link', $this->linkService->createForGuest($request->validated()));
    }
}
