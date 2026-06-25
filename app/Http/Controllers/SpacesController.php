<?php

namespace App\Http\Controllers;

use App\Http\Requests\SpacesController\{CreateSpaceRequest, UpdateSpaceRequest};
use App\Repositories\SpaceRepository;
use App\Services\SpaceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SpacesController extends Controller
{
    /**
     * Inject space repository and service dependencies.
     */
    public function __construct(
        private readonly SpaceRepository $spaces,
        private readonly SpaceService $spaceService,
    ) {
    }

    /**
     * Display the authenticated user space list with filters.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request): mixed
    {
        $user = Auth::user();

        $sort = ($request->input('sort') == 'asc' ? 'asc' : 'desc');

        return view('spaces.content', [
            'view' => 'list',
            'spaces' => $this->spaces->paginateForUser($user->id, $request->input('search'), $sort),
        ]);
    }

    /**
     * Display the form for creating a new space.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function spacesNew(): mixed
    {
        return view('spaces.content', ['view' => 'new']);
    }

    /**
     * Display the edit form for a space owned by the user.
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function spacesEdit(mixed $id): mixed
    {
        $user = Auth::user();

        $space = $this->spaces->findForUserOrFail($id, $user->id);

        return view('spaces.content', ['view' => 'edit', 'space' => $space]);
    }

    /**
     * Create a space for the authenticated user.
     *
     * @param CreateSpaceRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createSpace(CreateSpaceRequest $request): mixed
    {
        $this->spaceService->create($request->validated(), Auth::user());

        return redirect()->route('spaces')->with('success', __(':name has been created.', ['name' => $request->input('name')]));
    }

    /**
     * Update a space owned by the authenticated user.
     *
     * @param UpdateSpaceRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSpace(UpdateSpaceRequest $request, mixed $id): mixed
    {
        $user = Auth::user();

        $space = $this->spaces->findForUserOrFail($id, $user->id);

        $this->spaceService->update($space, $request->validated());

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Delete a space owned by the authenticated user.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function deleteSpace(mixed $id): mixed
    {
        $user = Auth::user();

        $space = $this->spaces->findForUserOrFail($id, $user->id);

        $this->spaceService->delete($space);

        return redirect()->route('spaces')->with('success', __(':name has been deleted.', ['name' => $space->name]));
    }
}
