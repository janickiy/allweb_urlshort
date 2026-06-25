<?php

namespace App\Http\Controllers;

use App\Http\Requests\LinksController\{CreateLinkRequest, UpdateLinkRequest};
use App\Repositories\DomainRepository;
use App\Repositories\LinkRepository;
use App\Repositories\SpaceRepository;
use App\Services\LinkService;
use App\Traits\UserFeaturesTrait;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
        private readonly SpaceRepository $spaces,
    ) {
    }

    /**
     * Display the authenticated user link list with filters.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request): mixed
    {
        $user = Auth::user();

        if (session('toast') == false) {
            session(['toast' => []]);
        }

        $links = $this->links->paginateForUser($user->id, $request->only([
            'search',
            'space',
            'domain',
            'type',
            'by',
            'sort',
        ]));

        return view('links.content', [
            'view' => 'list',
            'links' => $links,
            'spaces' => $this->spaces->forUser($user->id),
            'domains' => $this->domains->forUser($user->id),
        ]);
    }

    /**
     * Display the edit form for a link owned by the user.
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function linksEdit(mixed $id): mixed
    {
        $user = Auth::user();

        $link = $this->links->findForUserOrFail($id, $user->id);

        return view('links.content', [
            'view' => 'edit',
            'domains' => $this->domains->forUser($user->id),
            'spaces' => $this->spaces->forUser($user->id),
            'link' => $link,
        ]);
    }

    /**
     * Create one or more links for the authenticated user.
     *
     * @param CreateLinkRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createLink(CreateLinkRequest $request): mixed
    {
        $user = Auth::user();

        if ($request->multi_link) {
            $created = $this->linkService->createMany($request->all(), $user);

            return redirect()->back()->with('toast', $this->linkService->latestForUser($user->id, count($created)));
        }

        $this->linkService->create($request->all(), $user);

        return redirect()->back()->with('toast', $this->linkService->latestForUser($user->id, 1));
    }

    /**
     * Update a link owned by the authenticated user.
     *
     * @param UpdateLinkRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateLink(UpdateLinkRequest $request, mixed $id): mixed
    {
        $user = Auth::user();

        $link = $this->links->findForUserOrFail($id, $user->id);

        $this->linkService->update($link, $request->all());

        return redirect()->route('links.edit', $id)->with('success', __('Settings saved.'));
    }

    /**
     * Delete a link owned by the authenticated user.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function deleteLink(Request $request, mixed $id): mixed
    {
        $user = Auth::user();

        $link = $this->links->findForUserOrFail($id, $user->id);
        $name = $this->linkService->displayName($link);

        $this->linkService->delete($link);

        return redirect()->route('links')->with('success', __(':name has been deleted.', ['name' => $name]));
    }
}
