<?php

namespace App\Http\Controllers;

use App\Http\Requests\Spaces\{CreateSpaceRequest, UpdateSpaceRequest};
use App\Repositories\SpaceRepository;
use App\Services\SpaceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

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
     * @return View
     */
    public function index(Request $request): View
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
     */
    public function spacesNew(): View
    {
        return view('spaces.content', ['view' => 'new']);
    }

    /**
     * Display the edit form for a space owned by the user.
     *
     * @param $id
     * @return View
     */
    public function spacesEdit(int|string $id): View
    {
        $user = Auth::user();

        $space = $this->spaces->findForUserOrFail($id, $user->id);

        return view('spaces.content', ['view' => 'edit', 'space' => $space]);
    }

    /**
     * Create a space for the authenticated user.
     *
     * @param CreateSpaceRequest $request
     * @return RedirectResponse
     */
    public function createSpace(CreateSpaceRequest $request): RedirectResponse
    {
        $space = $this->spaceService->create($request->validated(), Auth::user());

        return redirect()->route('spaces')->with('success', __(':name has been created.', ['name' => $space->name]));
    }

    /**
     * Update a space owned by the authenticated user.
     *
     * @param UpdateSpaceRequest $request
     * @param $id
     * @return RedirectResponse
     */
    public function updateSpace(UpdateSpaceRequest $request, int|string $id): RedirectResponse
    {
        $this->spaceService->updateForUser($id, Auth::user(), $request->validated());

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Delete a space owned by the authenticated user.
     *
     * @param $id
     * @return RedirectResponse
     * @throws \Exception
     */
    public function deleteSpace(int|string $id): RedirectResponse
    {
        $name = $this->spaceService->deleteForUser($id, Auth::user());

        return redirect()->route('spaces')->with('success', __(':name has been deleted.', ['name' => $name]));
    }
}
