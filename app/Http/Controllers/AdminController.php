<?php

namespace App\Http\Controllers;

use App\Http\Requests\Admin\{
    CreatePageRequest,
    CreatePlanRequest,
    CreateSubscriptionRequest,
    UpdateSettingsCaptchaRequest,
    UpdateSettingsContactRequest,
    UpdateSettingsEmailRequest,
    UpdateSettingsGeneralRequest,
    UpdateSettingsAppearanceRequest,
    UpdateSettingsInvoiceRequest,
    UpdateSettingsLegalRequest,
    UpdatePageRequest,
    UpdatePlanRequest,
    UpdateSettingsPaymentRequest,
    UpdateSettingsRegistrationRequest,
    UpdateSettingsShortenerRequest,
    UpdateSettingsSocialRequest
};
use App\Http\Requests\Domains\UpdateDomainRequest;
use App\Http\Requests\Links\UpdateLinkRequest;
use App\Http\Requests\Workspaces\UpdateWorkspaceRequest;
use App\Http\Requests\Settings\{
    UpdateUserRequest
};
use App\Services\AdminService;
use App\Services\DomainService;
use App\Services\LinkService;
use App\Services\SettingsService;
use App\Services\WorkspaceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class AdminController extends Controller
{
    /**
     * Inject repositories and services used by admin controller actions.
     */
    public function __construct(
        private readonly AdminService $adminService,
        private readonly DomainService $domainService,
        private readonly LinkService $linkService,
        private readonly SettingsService $settingsService,
        private readonly WorkspaceService $workspaceService,
    ) {
    }

    /**
     * Display the admin dashboard with aggregated platform metrics.
     *
     * @return View
     */
    public function dashboard(): View
    {
        return view('admin.dashboard.content', $this->adminService->dashboardData(Auth::user()));
    }

    /**
     * Display the general settings screen in the admin panel.
     *
     * @return View
     */
    public function settingsGeneral(): View
    {
        return view('admin.content', ['view' => 'admin.settings.general']);
    }

    /**
     * Display the appearance settings screen in the admin panel.
     *
     * @return View
     */
    public function settingsAppearance(): View
    {
        return view('admin.content', ['view' => 'admin.settings.appearance']);
    }

    /**
     * Display the email settings screen in the admin panel.
     *
     * @return View
     */
    public function settingsEmail(): View
    {
        return view('admin.content', ['view' => 'admin.settings.email']);
    }

    /**
     * Display the social links settings screen in the admin panel.
     *
     * @return View
     */
    public function settingsSocial(): View
    {
        return view('admin.content', ['view' => 'admin.settings.social']);
    }

    /**
     * Display the payment settings screen in the admin panel.
     *
     * @return View
     */
    public function settingsPayment(): View
    {
        return view('admin.content', ['view' => 'admin.settings.payment']);
    }

    /**
     * Display the invoice settings screen in the admin panel.
     *
     * @return View
     */
    public function settingsInvoice(): View
    {
        return view('admin.content', ['view' => 'admin.settings.invoice']);
    }

    /**
     * Display the registration settings screen in the admin panel.
     *
     * @return View
     */
    public function settingsRegistration(): View
    {
        return view('admin.content', ['view' => 'admin.settings.registration']);
    }

    /**
     * Display the legal settings screen in the admin panel.
     *
     * @return View
     */
    public function settingsLegal(): View
    {
        return view('admin.content', ['view' => 'admin.settings.legal']);
    }

    /**
     * Display the contact settings screen in the admin panel.
     *
     * @return View
     */
    public function settingsContact(): View
    {
        return view('admin.content', ['view' => 'admin.settings.contact']);
    }

    /**
     * Display the captcha settings screen in the admin panel.
     *
     * @return View
     */
    public function settingsCaptcha(): View
    {
        return view('admin.content', ['view' => 'admin.settings.captcha']);
    }

    /**
     * Display the shortener settings screen in the admin panel.
     *
     * @return View
     */
    public function settingsShortener(): View
    {
        return view('admin.content', ['view' => 'admin.settings.shortener']);
    }

    /**
     * Display the paginated user list in the admin panel.
     *
     * @param Request $request
     * @return View
     */
    public function users(Request $request): View
    {
        return view('admin.content', $this->adminService->usersListData($request));
    }

    /**
     * Display the user edit form and account statistics.
     *
     * @param $id
     * @return View
     */
    public function usersEdit(int|string $id): View
    {
        return view('admin.content', $this->adminService->userEditData($id));
    }

    /**
     * Display the paginated link list in the admin panel.
     *
     * @param Request $request
     * @return View
     */
    public function links(Request $request): View
    {
        return view('admin.content', $this->adminService->linksListData($request));
    }

    /**
     * Display the admin link edit form for the selected link.
     *
     * @param $id
     * @return View
     */
    public function linksEdit(int|string $id): View
    {
        return view('admin.content', $this->adminService->linkEditData($id, Auth::user()));
    }

    /**
     * Display the paginated workspace list in the admin panel.
     *
     * @param Request $request
     * @return View
     */
    public function workspaces(Request $request): View
    {
        return view('admin.content', $this->adminService->workspacesListData($request));
    }

    /**
     * Display the admin workspace edit form for the selected workspace.
     *
     * @param $id
     * @return View
     */
    public function workspacesEdit(int|string $id): View
    {
        return view('admin.content', $this->adminService->workspaceEditData($id));
    }

    /**
     * Display the paginated domain list in the admin panel.
     *
     * @param Request $request
     * @return View
     */
    public function domains(Request $request): View
    {
        return view('admin.content', $this->adminService->domainsListData($request));
    }

    /**
     * Display the admin domain edit form for the selected domain.
     *
     * @param $id
     * @return View
     */
    public function domainsEdit(int|string $id): View
    {
        return view('admin.content', $this->adminService->domainEditData($id));
    }

    /**
     * Display the paginated page list in the admin panel.
     *
     * @param Request $request
     * @return View
     */
    public function pages(Request $request): View
    {
        return view('admin.content', $this->adminService->pagesListData($request));
    }

    /**
     * Display the form for creating a new static page.
     *
     * @return View
     */
    public function pagesNew(): View
    {
        return view('admin.content', ['view' => 'admin.pages.new']);
    }

    /**
     * Display the edit form for the selected static page.
     *
     * @param $id
     * @return View
     */
    public function pagesEdit(int|string $id): View
    {
        return view('admin.content', $this->adminService->pageEditData($id));
    }

    /**
     * Display the paginated subscription plan list in the admin panel.
     *
     * @param Request $request
     * @return View
     */
    public function plans(Request $request): View
    {
        return view('admin.content', $this->adminService->plansListData($request));
    }

    /**
     * Display the form for creating a new subscription plan.
     *
     * @return View
     */
    public function plansNew(): View
    {
        return view('admin.content', ['view' => 'admin.plans.new']);
    }

    /**
     * Display the edit form for the selected subscription plan.
     *
     * @param $id
     * @return View
     */
    public function plansEdit(int|string $id): View
    {
        return view('admin.content', $this->adminService->planEditData($id));
    }

    /**
     * Display the paginated subscription list in the admin panel.
     *
     * @param Request $request
     * @return View
     */
    public function subscriptions(Request $request): View
    {
        return view('admin.content', $this->adminService->subscriptionsListData($request));
    }

    /**
     * Display the form for creating an emulated subscription.
     *
     * @return View
     */
    public function subscriptionsNew(): View
    {
        return view('admin.content', $this->adminService->subscriptionNewData());
    }

    /**
     * Display the edit form for the selected subscription.
     *
     * @param $id
     * @return View
     */
    public function subscriptionsEdit(int|string $id): View
    {
        return view('admin.content', $this->adminService->subscriptionEditData($id));
    }

    /**
     * Persist general settings submitted from the admin panel.
     *
     * @param UpdateSettingsGeneralRequest $request
     * @return RedirectResponse
     */
    public function updateSettingsGeneral(UpdateSettingsGeneralRequest $request): RedirectResponse
    {
        $this->settingsService->updateGeneral($request->all());

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Persist registration settings submitted from the admin panel.
     *
     * @param UpdateSettingsRegistrationRequest $request
     * @return RedirectResponse
     */
    public function updateSettingsRegistration(UpdateSettingsRegistrationRequest $request): RedirectResponse
    {
        $this->settingsService->updateRegistration($request->all());

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Persist contact settings submitted from the admin panel.
     *
     * @param UpdateSettingsContactRequest $request
     * @return RedirectResponse
     */
    public function updateSettingsContact(UpdateSettingsContactRequest $request): RedirectResponse
    {
        $this->settingsService->updateContact($request->all());

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Persist captcha settings submitted from the admin panel.
     *
     * @param UpdateSettingsCaptchaRequest $request
     * @return RedirectResponse
     */
    public function updateSettingsCaptcha(UpdateSettingsCaptchaRequest $request): RedirectResponse
    {
        $this->settingsService->updateCaptcha($request->all());

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Persist shortener settings submitted from the admin panel.
     *
     * @param UpdateSettingsShortenerRequest $request
     * @return RedirectResponse
     */
    public function updateSettingsShortener(UpdateSettingsShortenerRequest $request): RedirectResponse
    {
        $this->settingsService->updateShortener($request->all());

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Persist legal settings submitted from the admin panel
     *
     * @param UpdateSettingsLegalRequest $request
     * @return RedirectResponse
     */
    public function updateSettingsLegal(UpdateSettingsLegalRequest $request): RedirectResponse
    {
        $this->settingsService->updateLegal($request->all());

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Persist appearance settings and uploaded branding assets.
     *
     * @param UpdateSettingsAppearanceRequest $request
     * @return RedirectResponse
     */
    public function updateSettingsAppearance(UpdateSettingsAppearanceRequest $request): RedirectResponse
    {
        $this->settingsService->updateAppearance(
            $request->all(),
            $request->file('logo'),
            $request->file('favicon')
        );

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Persist email settings submitted from the admin panel.
     *
     * @param UpdateSettingsEmailRequest $request
     * @return RedirectResponse
     */
    public function updateSettingsEmail(UpdateSettingsEmailRequest $request): RedirectResponse
    {
        $this->settingsService->updateEmail($request->all());

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Persist social profile settings submitted from the admin panel.
     *
     * @param UpdateSettingsSocialRequest $request
     * @return RedirectResponse
     */
    public function updateSettingsSocial(UpdateSettingsSocialRequest $request): RedirectResponse
    {
        $this->settingsService->updateSocial($request->all());

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Persist payment provider settings submitted from the admin panel.
     *
     * @param UpdateSettingsPaymentRequest $request
     * @return RedirectResponse
     */
    public function updateSettingsPayment(UpdateSettingsPaymentRequest $request): RedirectResponse
    {
        $this->settingsService->updatePayment($request->all());

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Persist invoice settings submitted from the admin panel.
     *
     * @param UpdateSettingsInvoiceRequest $request
     * @return RedirectResponse
     */
    public function updateSettingsInvoice(UpdateSettingsInvoiceRequest $request): RedirectResponse
    {
        $this->settingsService->updateInvoice($request->all());

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Create an emulated subscription for a user from admin input.
     *
     * @param CreateSubscriptionRequest $request
     * @return RedirectResponse
     */
    public function createSubscription(CreateSubscriptionRequest $request): RedirectResponse
    {
        try {
            $name = $this->adminService->createSubscription($request->validated());
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.subscriptions')->with('success', __(':name has been created.', ['name' => $name]));
    }

    /**
     * Delete an emulated subscription and redirect back with feedbac
     *
     * @param int|string $id
     * @return RedirectResponse
     */
    public function deleteSubscription(int|string $id): RedirectResponse
    {
        $name = $this->adminService->deleteEmulatedSubscription($id);

        return redirect()->route('admin.subscriptions')->with('success', __(':name has been deleted.', ['name' => $name]));
    }

    /**
     * Create a static page from validated admin input.
     *
     * @param CreatePageRequest $request
     * @return RedirectResponse
     */
    public function createPage(CreatePageRequest $request): RedirectResponse
    {
        $name = $this->adminService->createPage($request->all());

        return redirect()->route('admin.pages')->with('success', __(':name has been created.', ['name' => $name]));
    }


    /**
     * Update the selected static page from validated admin input.
     *
     * @param UpdatePageRequest $request
     * @param int|string $id
     * @return RedirectResponse
     */
    public function updatePage(UpdatePageRequest $request, int|string $id): RedirectResponse
    {
        $this->adminService->updatePage($id, $request->all());

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Delete the selected static page and redirect with feedback.
     *
     * @param int|string $id
     * @return RedirectResponse
     */
    public function deletePage(int|string $id): RedirectResponse
    {
        $name = $this->adminService->deletePage($id);

        return redirect()->route('admin.pages')->with('success', __(':name has been deleted.', ['name' => $name]));
    }

    /**
     * Create a subscription plan and its payment provider records.
     *
     * @param CreatePlanRequest $request
     * @return RedirectResponse
     */
    public function createPlan(CreatePlanRequest $request): RedirectResponse
    {
        try {
            $name = $this->adminService->createPlan($request->validated());
        } catch (\Exception $e) {
            $request->flash();
            return redirect()->route('admin.plans.new')->with('error', $e->getMessage());
        }

        return redirect()->route('admin.plans')->with('success', __(':name has been created.', ['name' => $name]));
    }

    /**
     * Update the selected subscription plan and related provider metadata.
     *
     * @param UpdatePlanRequest $request
     * @param int|string $id
     * @return RedirectResponse
     */
    public function updatePlan(UpdatePlanRequest $request, int|string $id): RedirectResponse
    {
        try {
            $this->adminService->updatePlan($id, $request->all());
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Soft-delete the selected subscription plan when allowed.
     *
     * @param int|string $id
     * @return RedirectResponse
     */
    public function disablePlan(int|string $id): RedirectResponse
    {
        try {
            $this->adminService->disablePlan($id);
        } catch (\Exception $e) {
            return redirect()->route('admin.plans')->with('error', $e->getMessage());
        }

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Restore a previously disabled subscription plan.
     *
     * @param int|string $id
     * @return RedirectResponse
     */
    public function restorePlan(int|string $id): RedirectResponse
    {
        $this->adminService->restorePlan($id);

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Update a user profile from admin input while enforcing role safeguards.
     *
     * @param UpdateUserRequest $request
     * @param int|string $id
     * @return RedirectResponse
     */
    public function updateUser(UpdateUserRequest $request, int|string $id): RedirectResponse
    {
        try {
            $this->adminService->updateUser($id, $request->validated(), Auth::id());
        } catch (\Exception $e) {
            return back()->with('error', __('Operation denied.'));
        }

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Permanently delete a user account after permission checks.
     *
     * @param int|string $id
     * @return RedirectResponse
     */
    public function deleteUser(int|string $id): RedirectResponse
    {
        try {
            $name = $this->adminService->deleteUser($id, Auth::id());
        } catch (\Exception $e) {
            return back()->with('error', __('Operation denied.'));
        }

        return redirect()->route('admin.users')->with('success', __(':name has been deleted.', ['name' => $name]));
    }

    /**
     * Soft-delete a user account after permission checks.
     *
     * @param int|string $id
     * @return RedirectResponse
     * @throws \Exception
     */
    public function disableUser(int|string $id): RedirectResponse
    {
        try {
            $this->adminService->disableUser($id, Auth::id());
        } catch (\Exception $e) {
            return back()->with('error', __('Operation denied.'));
        }

        return redirect()->route('admin.users.edit', $id)->with('success', __('Settings saved.'));
    }

    /**
     * Restore a previously disabled user account.
     *
     * @param int|string $id
     * @return RedirectResponse
     */
    public function restoreUser(int|string $id): RedirectResponse
    {
        $this->adminService->restoreUser($id);

        return redirect()->route('admin.users.edit', $id)->with('success', __('Settings saved.'));
    }

    /**
     * Update the selected link from validated admin input.
     *
     * @param UpdateLinkRequest $request
     * @param int|string $id
     * @return RedirectResponse
     */
    public function updateLink(UpdateLinkRequest $request, int|string $id): RedirectResponse
    {
        $this->linkService->updateById($id, $request->all());

        return redirect()->route('admin.links.edit', $id)->with('success', __('Settings saved.'));
    }

    /**
     * Delete the selected link and redirect with feedback.
     *
     * @param int|string $id
     * @return RedirectResponse
     * @throws \Exception
     */
    public function deleteLink(int|string $id): RedirectResponse
    {
        $name = $this->linkService->deleteById($id);

        return redirect()->route('admin.links')->with('success', __(':name has been deleted.', ['name' => $name]));
    }

    /**
     * Update the selected workspace from validated admin input.
     *
     * @param UpdateWorkspaceRequest $request
     * @param int|string $id
     * @return RedirectResponse
     */
    public function updateWorkspace(UpdateWorkspaceRequest $request, int|string $id): RedirectResponse
    {
        $this->workspaceService->updateById($id, $request->validated());

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Delete the selected workspace and redirect with feedback.
     *
     * @param int|string $id
     * @return RedirectResponse
     * @throws \Exception
     */
    public function deleteWorkspace(int|string $id): RedirectResponse
    {
        $name = $this->workspaceService->deleteById($id);

        return redirect()->route('admin.workspaces')->with('success', __(':name has been deleted.', ['name' => $name]));
    }

    /**
     * Update the selected domain from validated admin input.
     *
     * @param UpdateDomainRequest $request
     * @param int|string $id
     * @return RedirectResponse
     */
    public function updateDomain(UpdateDomainRequest $request, int|string $id): RedirectResponse
    {
        $this->domainService->updateById($id, $request->validated());

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Delete the selected domain and redirect with feedback.
     *
     * @param int|string $id
     * @return RedirectResponse
     * @throws \Exception
     */
    public function deleteDomain(int|string $id): RedirectResponse
    {
        $name = $this->domainService->deleteById($id);

        return redirect()->route('admin.domains')->with('success', __(':name has been deleted.', ['name' => $name]));
    }


}
