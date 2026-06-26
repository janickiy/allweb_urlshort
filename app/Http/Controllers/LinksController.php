<?php

namespace App\Http\Controllers;

use App\Http\Requests\Links\{CreateLinkRequest, UpdateLinkRequest};
use App\Repositories\DomainRepository;
use App\Repositories\LinkRepository;
use App\Repositories\WorkspaceRepository;
use App\Services\LinkService;
use App\Traits\UserFeaturesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class LinksController extends Controller
{
    use UserFeaturesTrait;

    /**
     * Inject link dependencies used by authenticated link actions.
     */
    public function __construct(
        private readonly DomainRepository $domains,
        private readonly LinkRepository $links,
        private readonly LinkService $linkService,
        private readonly WorkspaceRepository $workspaces,
    ) {
    }

    /**
     * Display the authenticated user link list with filters.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        if (session('toast') == false) {
            session(['toast' => []]);
        }

        $links = $this->links->paginateForUser($user->id, $request->only([
            'search',
            'workspace',
            'domain',
            'type',
            'by',
            'sort',
        ]));

        return view('links.content', [
            'view' => 'list',
            'links' => $links,
            'workspaces' => $this->workspaces->forUser($user->id),
            'domains' => $this->domains->forUser($user->id),
        ]);
    }

    /**
     * Display the edit form for a link owned by the user.
     *
     * @param int|string $id
     * @return View
     */
    public function linksEdit(int|string $id): View
    {
        $user = Auth::user();

        $link = $this->links->findForUserOrFail($id, $user->id);

        return view('links.content', [
            'view' => 'edit',
            'domains' => $this->domains->forUser($user->id),
            'workspaces' => $this->workspaces->forUser($user->id),
            'link' => $link,
        ]);
    }

    /**
     * Create one or more links for the authenticated user.
     *
     * @param CreateLinkRequest $request
     * @return RedirectResponse
     */
    public function createLink(CreateLinkRequest $request): RedirectResponse
    {
        return redirect()->back()->with('toast', $this->linkService->createForUser($request->validated(), Auth::user()));
    }

    /**
     * Update a link owned by the authenticated user.
     *
     * @param UpdateLinkRequest $request
     * @param int|string $id
     * @return RedirectResponse
     */
    public function updateLink(UpdateLinkRequest $request, int|string $id): RedirectResponse
    {
        $this->linkService->updateForUser($id, Auth::user(), $request->validated());

        return redirect()->route('links.edit', $id)->with('success', __('ui.messages.settings_saved'));
    }

    /**
     * Delete a link owned by the authenticated user.
     *
     * @param int|string $id
     * @return RedirectResponse
     * @throws \Exception
     */
    public function deleteLink(int|string $id): RedirectResponse
    {
        $name = $this->linkService->deleteForUser($id, Auth::user());

        return redirect()->route('links')->with('success', __('ui.messages.deleted', ['name' => $name]));
    }
}
