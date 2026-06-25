<?php

namespace App\Http\Controllers;

use App\Http\Requests\RedirectController\ValidateLinkPasswordRequest;
use App\Services\RedirectDecision;
use App\Services\RedirectService;
use Illuminate\Http\Request;

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
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\Http\RedirectResponse|\Illuminate\View\View
     * @throws \MaxMind\Db\Reader\InvalidDatabaseException
     */
    public function index(Request $request, mixed $id): mixed
    {
        $decision = $this->redirectService->resolve($request, $id);

        return match ($decision->type) {
            RedirectDecision::TYPE_PREVIEW => view('redirect.preview', ['link' => $decision->link]),
            RedirectDecision::TYPE_EXPIRED => view('redirect.expired', ['link' => $decision->link]),
            RedirectDecision::TYPE_PASSWORD => view('redirect.password', ['link' => $decision->link]),
            RedirectDecision::TYPE_DISABLED => view('redirect.disabled', ['link' => $decision->link]),
            RedirectDecision::TYPE_BANNED => view('redirect.banned', ['link' => $decision->link]),
            RedirectDecision::TYPE_NOT_FOUND => $decision->target ? $this->redirectNoCache($decision->target) : abort(404),
            default => $this->redirectNoCache($decision->target),
        };
    }

    /**
     * Validate a password-protected link before redirecting to its target.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Illuminate\Validation\ValidationException
     */
    public function validatePassword(ValidateLinkPasswordRequest $request, mixed $id): mixed
    {
        return $this->redirectNoCache($request->link()->url);
    }

    /**
     * Create a redirect response with headers that prevent caching.
     */
    private function redirectNoCache(?string $target): mixed
    {
        return redirect()->to($target, 301)->header('Cache-Control', 'no-store, no-cache, must-revalidate');
    }
}
