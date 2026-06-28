<?php

namespace App\Http\Controllers;

use App\Enums\RedirectDecision;
use App\Http\Requests\Redirect\ValidateLinkPasswordRequest;
use App\Services\RedirectService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class RedirectController extends Controller
{
    /**
     * Inject redirect service used to resolve short-link requests.
     */
    public function __construct(private readonly RedirectService $redirectService)
    {
    }

    /**
     * Resolve a short-link alias and return the appropriate redirect response.
     *
     * @param Request $request
     * @param int|string $id
     * @return RedirectResponse|View
     * @throws \MaxMind\Db\Reader\InvalidDatabaseException
     */
    public function index(Request $request, int|string $id): View|RedirectResponse
    {
        $result = $this->redirectService->resolve(
            $request,
            (string) $id,
            preview: $request->routeIs('link.preview')
        );

        return match ($result->decision) {
            RedirectDecision::Preview => view('redirect.preview', ['link' => $result->link]),
            RedirectDecision::Expired => view('redirect.expired', ['link' => $result->link]),
            RedirectDecision::Password => view('redirect.password', ['link' => $result->link]),
            RedirectDecision::Disabled => view('redirect.disabled', ['link' => $result->link]),
            RedirectDecision::Banned => view('redirect.banned', ['link' => $result->link]),
            RedirectDecision::NotFound => $result->target ? $this->redirectNoCache($result->target) : abort(404),
            RedirectDecision::Redirect => $this->redirectNoCache($result->target),
        };
    }

    /**
     * Resolve a local custom-domain short-link URL and return the redirect response.
     *
     * @param Request $request
     * @param string $domain
     * @param int|string $id
     * @return RedirectResponse|View
     * @throws \MaxMind\Db\Reader\InvalidDatabaseException
     */
    public function domain(Request $request, string $domain, int|string $id): View|RedirectResponse
    {
        $result = $this->redirectService->resolve(
            $request,
            (string) $id,
            $domain,
            $request->routeIs('link.domain.preview')
        );

        return match ($result->decision) {
            RedirectDecision::Preview => view('redirect.preview', ['link' => $result->link]),
            RedirectDecision::Expired => view('redirect.expired', ['link' => $result->link]),
            RedirectDecision::Password => view('redirect.password', ['link' => $result->link]),
            RedirectDecision::Disabled => view('redirect.disabled', ['link' => $result->link]),
            RedirectDecision::Banned => view('redirect.banned', ['link' => $result->link]),
            RedirectDecision::NotFound => $result->target ? $this->redirectNoCache($result->target) : abort(404),
            RedirectDecision::Redirect => $this->redirectNoCache($result->target),
        };
    }

    /**
     * Validate a password-protected link before redirecting to its target.
     *
     * @param Request $request
     * @return RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validatePassword(ValidateLinkPasswordRequest $request): RedirectResponse
    {
        return $this->redirectNoCache($request->link()->url);
    }

    /**
     * Create a redirect response with headers that prevent caching.
     */
    private function redirectNoCache(?string $target): RedirectResponse
    {
        return redirect()->to($target, 301)->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }
}
