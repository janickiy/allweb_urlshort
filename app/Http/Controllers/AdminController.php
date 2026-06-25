<?php

namespace App\Http\Controllers;

use App\Http\Requests\AdminController\{
    CreateLanguageRequest,
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
use App\Http\Requests\DomainsController\UpdateDomainRequest;
use App\Http\Requests\LinksController\UpdateLinkRequest;
use App\Http\Requests\SpacesController\UpdateSpaceRequest;
use App\Http\Requests\SettingsController\{
    UpdateUserRequest
};
use App\Repositories\DomainRepository;
use App\Repositories\LinkRepository;
use App\Repositories\SpaceRepository;
use App\Services\AdminService;
use App\Services\DomainService;
use App\Services\LinkService;
use App\Services\SettingsService;
use App\Services\SpaceService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;

class AdminController extends Controller
{
    /**
     * Inject repositories and services used by admin controller actions.
     */
    public function __construct(
        private readonly AdminService $adminService,
        private readonly DomainRepository $domainRepository,
        private readonly DomainService $domainService,
        private readonly LinkRepository $linkRepository,
        private readonly LinkService $linkService,
        private readonly SettingsService $settingsService,
        private readonly SpaceRepository $spaceRepository,
        private readonly SpaceService $spaceService,
    ) {
    }

    /**
     * Display the admin dashboard with aggregated platform metrics.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function dashboard(Request $request): mixed
    {
        return view('admin.dashboard.content', $this->adminService->dashboardData(Auth::user()));
    }

    /**
     * Display the general settings screen in the admin panel.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function settingsGeneral(): mixed
    {
        return view('admin.content', ['view' => 'admin.settings.general']);
    }

    /**
     * Display the appearance settings screen in the admin panel.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function settingsAppearance(): mixed
    {
        return view('admin.content', ['view' => 'admin.settings.appearance']);
    }

    /**
     * Display the email settings screen in the admin panel.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function settingsEmail(): mixed
    {
        return view('admin.content', ['view' => 'admin.settings.email']);
    }

    /**
     * Display the social links settings screen in the admin panel.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function settingsSocial(): mixed
    {
        return view('admin.content', ['view' => 'admin.settings.social']);
    }

    /**
     * Display the payment settings screen in the admin panel.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function settingsPayment(): mixed
    {
        return view('admin.content', ['view' => 'admin.settings.payment']);
    }

    /**
     * Display the invoice settings screen in the admin panel.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function settingsInvoice(): mixed
    {
        return view('admin.content', ['view' => 'admin.settings.invoice']);
    }

    /**
     * Display the registration settings screen in the admin panel.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function settingsRegistration(): mixed
    {
        return view('admin.content', ['view' => 'admin.settings.registration']);
    }

    /**
     * Display the legal settings screen in the admin panel.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function settingsLegal(): mixed
    {
        return view('admin.content', ['view' => 'admin.settings.legal']);
    }

    /**
     * Display the contact settings screen in the admin panel.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function settingsContact(): mixed
    {
        return view('admin.content', ['view' => 'admin.settings.contact']);
    }

    /**
     * Display the captcha settings screen in the admin panel.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function settingsCaptcha(): mixed
    {
        return view('admin.content', ['view' => 'admin.settings.captcha']);
    }

    /**
     * Display the shortener settings screen in the admin panel.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function settingsShortener(): mixed
    {
        return view('admin.content', ['view' => 'admin.settings.shortener']);
    }

    /**
     * Display the paginated language list in the admin panel.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function languages(Request $request): mixed
    {
        return view('admin.content', $this->adminService->languagesListData($request));
    }

    /**
     * Display the form for uploading a new language file.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function languagesNew(): mixed
    {
        return view('admin.content', ['view' => 'admin.languages.new']);
    }

    /**
     * Display the language edit form for the selected language.
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function languagesEdit(mixed $id): mixed
    {
        return view('admin.content', $this->adminService->languageEditData($id));
    }

    /**
     * Display the paginated user list in the admin panel.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function users(Request $request): mixed
    {
        return view('admin.content', $this->adminService->usersListData($request));
    }

    /**
     * Display the user edit form and account statistics.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function usersEdit(Request $request, mixed $id): mixed
    {
        return view('admin.content', $this->adminService->userEditData($id));
    }

    /**
     * Display the paginated link list in the admin panel.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function links(Request $request): mixed
    {
        return view('admin.content', $this->adminService->linksListData($request));
    }

    /**
     * Display the admin link edit form for the selected link.
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function linksEdit(mixed $id): mixed
    {
        return view('admin.content', $this->adminService->linkEditData($id, Auth::user()));
    }

    /**
     * Display the paginated space list in the admin panel.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function spaces(Request $request): mixed
    {
        return view('admin.content', $this->adminService->spacesListData($request));
    }

    /**
     * Display the admin space edit form for the selected space.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function spacesEdit(Request $request, mixed $id): mixed
    {
        return view('admin.content', $this->adminService->spaceEditData($id));
    }

    /**
     * Display the paginated domain list in the admin panel.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function domains(Request $request): mixed
    {
        return view('admin.content', $this->adminService->domainsListData($request));
    }

    /**
     * Display the admin domain edit form for the selected domain.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function domainsEdit(Request $request, mixed $id): mixed
    {
        return view('admin.content', $this->adminService->domainEditData($id));
    }

    /**
     * Display the paginated page list in the admin panel.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function pages(Request $request): mixed
    {
        return view('admin.content', $this->adminService->pagesListData($request));
    }

    /**
     * Display the form for creating a new static page.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function pagesNew(): mixed
    {
        return view('admin.content', ['view' => 'admin.pages.new']);
    }

    /**
     * Display the edit form for the selected static page.
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function pagesEdit(mixed $id): mixed
    {
        return view('admin.content', $this->adminService->pageEditData($id));
    }

    /**
     * Display the paginated subscription plan list in the admin panel.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function plans(Request $request): mixed
    {
        return view('admin.content', $this->adminService->plansListData($request));
    }

    /**
     * Display the form for creating a new subscription plan.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function plansNew(): mixed
    {
        return view('admin.content', ['view' => 'admin.plans.new']);
    }

    /**
     * Display the edit form for the selected subscription plan.
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function plansEdit(mixed $id): mixed
    {
        return view('admin.content', $this->adminService->planEditData($id));
    }

    /**
     * Display the paginated subscription list in the admin panel.
     *
     * @param Request $request
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function subscriptions(Request $request): mixed
    {
        return view('admin.content', $this->adminService->subscriptionsListData($request));
    }

    /**
     * Display the form for creating an emulated subscription.
     *
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function subscriptionsNew(): mixed
    {
        return view('admin.content', $this->adminService->subscriptionNewData());
    }

    /**
     * Display the edit form for the selected subscription.
     *
     * @param $id
     * @return \Illuminate\Contracts\Foundation\Application|\Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function subscriptionsEdit(mixed $id): mixed
    {
        return view('admin.content', $this->adminService->subscriptionEditData($id));
    }

    /**
     * Persist general settings submitted from the admin panel.
     *
     * @param UpdateSettingsGeneralRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSettingsGeneral(UpdateSettingsGeneralRequest $request): mixed
    {
        // The rows to be updated
        $rows = ['title', 'tagline', 'index', 'timezone', 'tracking_code'];

        $this->settingsService->updateKeys($rows, $request->all());

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Persist registration settings submitted from the admin panel.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSettingsRegistration(UpdateSettingsRegistrationRequest $request): mixed
    {
        // The rows to be updated
        $rows = ['registration_registration', 'registration_captcha', 'registration_verification'];

        $this->settingsService->updateKeys($rows, $request->all());

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Persist contact settings submitted from the admin panel.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSettingsContact(UpdateSettingsContactRequest $request): mixed
    {
        // The rows to be updated
        $rows = ['contact_captcha', 'contact_email'];

        $this->settingsService->updateKeys($rows, $request->all());

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Persist captcha settings submitted from the admin panel.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSettingsCaptcha(UpdateSettingsCaptchaRequest $request): mixed
    {
        // The rows to be updated
        $rows = ['captcha_site_key', 'captcha_secret_key', 'captcha_registration', 'captcha_contact', 'captcha_shorten'];

        $this->settingsService->updateKeys($rows, $request->all());

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Persist shortener settings submitted from the admin panel.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSettingsShortener(UpdateSettingsShortenerRequest $request): mixed
    {
        // The rows to be updated
        $rows = ['short_guest', 'short_bad_words'];

        $this->settingsService->updateKeys($rows, $request->all());

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Persist legal settings submitted from the admin panel.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSettingsLegal(UpdateSettingsLegalRequest $request): mixed
    {
        // The rows to be updated
        $rows = ['legal_terms_url', 'legal_privacy_url', 'legal_cookie_url'];

        $this->settingsService->updateKeys($rows, $request->all());

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Persist appearance settings and uploaded branding assets.
     *
     * @param UpdateSettingsAppearanceRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSettingsAppearance(UpdateSettingsAppearanceRequest $request): mixed
    {
        if ($request->validated()) {
            // The rows to be updated
            $rows = ['logo', 'favicon'];

            foreach ($rows as $row) {
                if ($request->has($row)) {
                    if ($request->hasFile($row)) {
                        $fileName = $request->file($row)->hashName();

                        // Check if the file exists
                        if (file_exists(public_path('uploads/brand/' . config('settings.' . $row)))) {
                            unlink(public_path('uploads/brand/' . config('settings.' . $row)));
                        }

                        // Save the file
                        $request->file($row)->move(public_path('uploads/brand'), $fileName);
                    }

                    $this->settingsService->updateKeys([$row], [$row => $fileName]);

                    session()->flash('success', __('Settings saved.'));
                }
            }

            // The rows to be updated
            $rows = ['theme'];

            $this->settingsService->updateKeys($rows, $request->all());
        }

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Persist email settings submitted from the admin panel.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSettingsEmail(UpdateSettingsEmailRequest $request): mixed
    {
        // The rows to be updated
        $rows = ['email_driver', 'email_host', 'email_port', 'email_encryption', 'email_address', 'email_username', 'email_password'];

        $this->settingsService->updateKeys($rows, $request->all());

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Persist social profile settings submitted from the admin panel.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSettingsSocial(UpdateSettingsSocialRequest $request): mixed
    {
        // The rows to be updated
        $rows = ['social_facebook', 'social_twitter', 'social_instagram', 'social_youtube'];

        $this->settingsService->updateKeys($rows, $request->all());

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Persist payment provider settings submitted from the admin panel.
     *
     * @param UpdateSettingsPaymentRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSettingsPayment(UpdateSettingsPaymentRequest $request): mixed
    {
        // The rows to be updated
        $rows = ['stripe', 'stripe_key', 'stripe_secret', 'stripe_wh_secret'];

        $this->settingsService->updateKeys($rows, $request->all());

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Persist invoice settings submitted from the admin panel.
     *
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSettingsInvoice(UpdateSettingsInvoiceRequest $request): mixed
    {
        // The rows to be updated
        $rows = ['invoice_vendor', 'invoice_address', 'invoice_city', 'invoice_state', 'invoice_postal_code', 'invoice_country', 'invoice_phone', 'invoice_vat_number'];

        $this->settingsService->updateKeys($rows, $request->all());

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Create an emulated subscription for a user from admin input.
     *
     * @param CreateSubscriptionRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createSubscription(CreateSubscriptionRequest $request): mixed
    {
        try {
            $name = $this->adminService->createSubscription($request->validated());
        } catch (\Exception $e) {
            return back()->with('error', $e->getMessage());
        }

        return redirect()->route('admin.subscriptions')->with('success', __(':name has been created.', ['name' => $name]));
    }

    /**
     * Delete an emulated subscription and redirect back with feedback.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteSubscription(mixed $id): mixed
    {
        $name = $this->adminService->deleteEmulatedSubscription($id);

        return redirect()->route('admin.subscriptions')->with('success', __(':name has been deleted.', ['name' => $name]));
    }

    /**
     * Upload and register a new language file from admin input.
     *
     * @param CreateLanguageRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createLanguage(CreateLanguageRequest $request): mixed
    {
        if ($request->validated()) {

            $file = $this->readLanguage($request);
            $this->uploadLanguage($request, $file);

            $name = $this->adminService->syncLanguage($file);

            session()->flash('success', __(':name language uploaded.', ['name' => $name]));
        }

        return redirect()->route('admin.languages');
    }

    /**
     * Read and decode the uploaded language JSON payload.
     *
     * @param Request $request
     * @return mixed
     */
    private function readLanguage(Request $request): mixed
    {
        $uploadedFile = file_get_contents($request->file('language'));
        $file = json_decode($uploadedFile);

        return $file;
    }

    /**
     * Store the uploaded language JSON file on the language disk.
     *
     * @param Request $request
     * @param $file
     */
    private function uploadLanguage(Request $request, mixed $file): mixed
    {
        Storage::disk('languages')->put($file->lang_code . '.json', File::get($request->file('language')));
    }

    /**
     * Update language metadata and default-language state.
     *
     * @param Request $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateLanguage(Request $request, mixed $id): mixed
    {
        $this->adminService->updateLanguageDefault($id, $request->has('default'));

        return redirect()->route('admin.languages.edit', $id)->with('success', __('Settings saved.'));
    }

    /**
     * Delete a language after validating default-language constraints.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteLanguage(mixed $id): mixed
    {
        try {
            $name = $this->adminService->deleteLanguage($id);
            Storage::disk('languages')->delete($id . '.json');
        } catch (\Exception $e) {
            return redirect()->route('admin.languages')->with('error', $e->getMessage());
        }

        return redirect()->route('admin.languages')->with('success', __(':name has been deleted.', ['name' => $name]));
    }

    /**
     * Create a static page from validated admin input.
     *
     * @param CreatePageRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createPage(CreatePageRequest $request): mixed
    {
        $name = $this->adminService->createPage($request->all());

        return redirect()->route('admin.pages')->with('success', __(':name has been created.', ['name' => $name]));
    }

    /**
     * Update the selected static page from validated admin input.
     *
     * @param UpdatePageRequest $request
     * @param $id
     * @return mixed
     */
    public function updatePage(UpdatePageRequest $request, mixed $id): mixed
    {
        $this->adminService->updatePage($id, $request->all());

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Delete the selected static page and redirect with feedback.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deletePage(mixed $id): mixed
    {
        $name = $this->adminService->deletePage($id);

        return redirect()->route('admin.pages')->with('success', __(':name has been deleted.', ['name' => $name]));
    }

    /**
     * Create a subscription plan and its payment provider records.
     *
     * @param CreatePlanRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function createPlan(CreatePlanRequest $request): mixed
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
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updatePlan(UpdatePlanRequest $request, mixed $id): mixed
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
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function disablePlan(mixed $id): mixed
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
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restorePlan(mixed $id): mixed
    {
        $this->adminService->restorePlan($id);

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Update a user profile from admin input while enforcing role safeguards.
     *
     * @param UpdateUserRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateUser(UpdateUserRequest $request, mixed $id): mixed
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
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function deleteUser(mixed $id): mixed
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
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function disableUser(mixed $id): mixed
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
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function restoreUser(mixed $id): mixed
    {
        $this->adminService->restoreUser($id);

        return redirect()->route('admin.users.edit', $id)->with('success', __('Settings saved.'));
    }

    /**
     * Update the selected link from validated admin input.
     *
     * @param UpdateLinkRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateLink(UpdateLinkRequest $request, mixed $id): mixed
    {
        $link = $this->linkRepository->findOrFail($id);

        $this->linkService->update($link, $request->all());

        return redirect()->route('admin.links.edit', $id)->with('success', __('Settings saved.'));
    }

    /**
     * Delete the selected link and redirect with feedback.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function deleteLink(mixed $id): mixed
    {
        $link = $this->linkRepository->findOrFail($id);
        $name = $this->linkService->displayName($link);
        $this->linkService->delete($link);

        return redirect()->route('admin.links')->with('success', __(':name has been deleted.', ['name' => $name]));
    }

    /**
     * Update the selected space from validated admin input.
     *
     * @param UpdateSpaceRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateSpace(UpdateSpaceRequest $request, mixed $id): mixed
    {
        $space = $this->spaceRepository->findOrFail($id);

        $this->spaceService->update($space, $request->validated());

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Delete the selected space and redirect with feedback.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function deleteSpace(mixed $id): mixed
    {
        $space = $this->spaceRepository->findOrFail($id);
        $this->spaceService->delete($space);

        return redirect()->route('admin.spaces')->with('success', __(':name has been deleted.', ['name' => $space->name]));
    }

    /**
     * Update the selected domain from validated admin input.
     *
     * @param UpdateDomainRequest $request
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     */
    public function updateDomain(UpdateDomainRequest $request, mixed $id): mixed
    {
        $domain = $this->domainRepository->findOrFail($id);

        $this->domainService->update($domain, $request->validated());

        return back()->with('success', __('Settings saved.'));
    }

    /**
     * Delete the selected domain and redirect with feedback.
     *
     * @param $id
     * @return \Illuminate\Http\RedirectResponse
     * @throws \Exception
     */
    public function deleteDomain(mixed $id): mixed
    {
        $domain = $this->domainRepository->findOrFail($id);
        $this->domainService->delete($domain);

        return redirect()->route('admin.domains')->with('success', __(':name has been deleted.', ['name' => str_replace(['http://', 'https://'], '', $domain->name)]));
    }


}
