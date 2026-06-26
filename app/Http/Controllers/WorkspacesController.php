<?php

namespace App\Http\Controllers;

use App\Http\Requests\Workspaces\{CreateWorkspaceRequest, UpdateWorkspaceRequest};
use App\Repositories\WorkspaceRepository;
use App\Services\WorkspaceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class WorkspacesController extends Controller
{
    /**
     * Inject workspace repository and service dependencies.
     */
    public function __construct(
        private readonly WorkspaceRepository $workspaces,
        private readonly WorkspaceService $workspaceService,
    ) {
    }

    /**
     * Display the authenticated user workspace list with filters.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $sort = ($request->input('sort') == 'asc' ? 'asc' : 'desc');

        return view('workspaces.content', [
            'view' => 'list',
            'workspaces' => $this->workspaces->paginateForUser($user->id, $request->input('search'), $sort),
        ]);
    }

    /**
     * Display the form for creating a new workspace.
     *
     */
    public function workspacesNew(): View
    {
        return view('workspaces.content', ['view' => 'new']);
    }

    /**
     * Display the edit form for a workspace owned by the user.
     *
     * @param $id
     * @return View
     */
    public function workspacesEdit(int|string $id): View
    {
        $user = Auth::user();

        $workspace = $this->workspaces->findForUserOrFail($id, $user->id);

        return view('workspaces.content', ['view' => 'edit', 'workspace' => $workspace]);
    }

    /**
     * Create a workspace for the authenticated user.
     *
     * @param CreateWorkspaceRequest $request
     * @return RedirectResponse
     */
    public function createWorkspace(CreateWorkspaceRequest $request): RedirectResponse
    {
        $workspace = $this->workspaceService->create($request->validated(), Auth::user());

        return redirect()->route('workspaces')->with('success', __(':name has been created.', ['name' => $workspace->name]));
    }

    /**
     * Update a workspace owned by the authenticated user.
     *
     * @param UpdateWorkspaceRequest $request
     * @param $id
     * @return RedirectResponse
     */
    public function updateWorkspace(UpdateWorkspaceRequest $request, int|string $id): RedirectResponse
    {
        $this->workspaceService->updateForUser($id, Auth::user(), $request->validated());

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Delete a workspace owned by the authenticated user.
     *
     * @param $id
     * @return RedirectResponse
     * @throws \Exception
     */
    public function deleteWorkspace(int|string $id): RedirectResponse
    {
        $name = $this->workspaceService->deleteForUser($id, Auth::user());

        return redirect()->route('workspaces')->with('success', __(':name has been deleted.', ['name' => $name]));
    }
}
