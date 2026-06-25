<?php

namespace App\Http\Controllers;

use App\Http\Requests\DomainsController\CreateDomainRequest;
use App\Http\Requests\DomainsController\UpdateDomainRequest;
use App\Repositories\DomainRepository;
use App\Services\DomainService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request): mixed
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
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function domainsNew(): mixed
    {
        return view('domains.content', ['view' => 'new']);
    }

    /**
     * Display the edit form for a domain owned by the user.
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function domainsEdit(mixed $id): mixed
    {
        $user = Auth::user();

        $domain = $this->domains->findForUserOrFail($id, $user->id);

        return view('domains.content', ['view' => 'edit', 'domain' => $domain]);
    }

    /**
     * Create a custom domain for the authenticated user.
     *
     * @param CreateDomainRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createDomain(CreateDomainRequest $request): mixed
    {
        $domain = $this->domainService->create($request->validated(), Auth::user());

        return redirect()->route('domains')->with('success', __(':name has been created.', ['name' => $domain->name]));
    }

    /**
     * Update a custom domain owned by the authenticated user.
     *
     * @param UpdateDomainRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateDomain(UpdateDomainRequest $request, mixed $id): mixed
    {
        $user = Auth::user();

        $domain = $this->domains->findForUserOrFail($id, $user->id);

        $this->domainService->update($domain, $request->validated());

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Delete a custom domain owned by the authenticated user.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function deleteDomain(mixed $id): mixed
    {
        $user = Auth::user();

        $domain = $this->domains->findForUserOrFail($id, $user->id);

        $this->domainService->delete($domain);

        return redirect()->route('domains')->with('success', __(':name has been deleted.', ['name' => str_replace(['http://', 'https://'], '', $domain->name)]));
    }
}
