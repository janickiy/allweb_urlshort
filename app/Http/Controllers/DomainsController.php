<?php

namespace App\Http\Controllers;

use App\Http\Requests\Domains\CreateDomainRequest;
use App\Http\Requests\Domains\UpdateDomainRequest;
use App\Repositories\DomainRepository;
use App\Services\DomainService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class DomainsController extends Controller
{
    /**
     * Inject domain repository and service dependencies.
     */
    public function __construct(
        private readonly DomainRepository $domains,
        private readonly DomainService $domainService,
    ) {
    }

    /**
     * Display the authenticated user domain list with filters.
     *
     * @param Request $request
     * @return View
     */
    public function index(Request $request): View
    {
        $user = Auth::user();

        $sort = ($request->input('sort') == 'asc' ? 'asc' : 'desc');

        return view('domains.content', [
            'view' => 'list',
            'domains' => $this->domains->paginateForUser($user->id, $request->input('search'), $sort),
        ]);
    }

    /**
     * Display the form for creating a new custom domain.
     *
     * @return View
     */
    public function domainsNew(): View
    {
        return view('domains.content', ['view' => 'new']);
    }

    /**
     * Display the edit form for a domain owned by the user.
     *
     * @param $id
     * @return View
     */
    public function domainsEdit(int|string $id): View
    {
        $user = Auth::user();

        $domain = $this->domains->findForUserOrFail($id, $user->id);

        return view('domains.content', ['view' => 'edit', 'domain' => $domain]);
    }

    /**
     * Create a custom domain for the authenticated user.
     *
     * @param CreateDomainRequest $request
     * @return RedirectResponse
     */
    public function createDomain(CreateDomainRequest $request): RedirectResponse
    {
        $domain = $this->domainService->create($request->validated(), Auth::user());

        return redirect()->route('domains')->with('success', __(':name has been created.', ['name' => $domain->name]));
    }

    /**
     * Update a custom domain owned by the authenticated user.
     *
     * @param UpdateDomainRequest $request
     * @param $id
     * @return RedirectResponse
     */
    public function updateDomain(UpdateDomainRequest $request, int|string $id): RedirectResponse
    {
        $this->domainService->updateForUser($id, Auth::user(), $request->validated());

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Delete a custom domain owned by the authenticated user.
     *
     * @param $id
     * @return RedirectResponse
     * @throws \Exception
     */
    public function deleteDomain(int|string $id): RedirectResponse
    {
        $name = $this->domainService->deleteForUser($id, Auth::user());

        return redirect()->route('domains')->with('success', __(':name has been deleted.', ['name' => $name]));
    }
}
