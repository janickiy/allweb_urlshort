<?php

namespace App\Services;

use App\DTO\PageData;
use App\DTO\PlanData;
use App\DTO\LanguageData;
use App\DTO\SubscriptionData;
use App\Models\User;
use App\Repositories\DomainRepository;
use App\Repositories\LanguageRepository;
use App\Repositories\LinkRepository;
use App\Repositories\PageRepository;
use App\Repositories\PlanRepository;
use App\Repositories\SpaceRepository;
use App\Repositories\SubscriptionRepository;
use App\Repositories\UserRepository;
use App\Traits\UserFeaturesTrait;
use Illuminate\Support\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use RuntimeException;

class AdminService
{
    use UserFeaturesTrait;

    /**
     * Inject dependencies used by admin service operations.
     */
    public function __construct(
        private readonly DomainRepository $domains,
        private readonly LanguageRepository $languages,
        private readonly LinkRepository $links,
        private readonly PageRepository $pages,
        private readonly PlanRepository $plans,
        private readonly SpaceRepository $spaces,
        private readonly SubscriptionRepository $subscriptions,
        private readonly UserRepository $users,
        private readonly UserSettingsService $userSettings,
    ) {
    }

    /**
     * Build the data required for the admin dashboard.
     *
     * @return array<string, mixed>
     */
    public function dashboardData(User $user): array
    {
        return [
            'user' => $user,
            'stats' => [
                'users' => $this->users->withTrashedCount(),
                'subscriptions' => $this->subscriptions->count(),
                'plans' => $this->plans->withTrashedCount(),
                'links' => $this->links->maxId(),
                'spaces' => $this->spaces->maxId(),
                'domains' => $this->domains->maxId(),
            ],
            'users' => $this->users->recentWithTrashed(10),
            'subscriptions' => config('settings.stripe') ? $this->subscriptions->recent(10) : null,
            'links' => config('settings.stripe') ? null : $this->links->latest(10),
        ];
    }

    /**
     * Build the data required for the admin languages list.
     *
     * @param Request $request
     * @return array
     */
    public function languagesListData(Request $request): array
    {
        return [
            'view' => 'admin.languages.list',
            'languages' => $this->languages->paginateForAdmin($request->input('search'), $this->sort($request)),
        ];
    }

    /**
     * Build the data required to edit a language.
     *
     * @return array<string, mixed>
     */

    /**
     * @param string $id
     * @return array
     */
    public function languageEditData(string $id): array
    {
        return [
            'view' => 'admin.languages.edit',
            'id' => $id,
            'languages' => $this->languages->all(),
            'language' => $this->languages->findByCodeOrFail($id),
        ];
    }

    /**
     * Build the data required for the admin users list.
     *
     * @return array<string, mixed>
     */
    public function usersListData(Request $request): array
    {
        return [
            'view' => 'admin.users.list',
            'users' => $this->users->paginateForAdmin([
                'search' => $request->input('search'),
                'role' => $request->input('role'),
                'sort' => $request->input('sort'),
                'by' => $request->input('by'),
            ]),
        ];
    }

    /**
     * Build the data required to edit a user from the admin panel.
     *
     * @return array<string, mixed>
     */
    public function userEditData(int|string $id): array
    {
        $user = $this->users->withTrashedFindOrFail($id);

        return [
            'view' => 'settings.account',
            'admin' => true,
            'user' => $user,
            'stats' => [
                'subscriptions' => $this->subscriptions->countForUser($user->id),
                'links' => $this->links->countForUser($user->id),
                'spaces' => $this->spaces->countForUser($user->id),
                'domains' => $this->domains->countForUser($user->id),
            ],
        ];
    }

    /**
     * Build the data required for the admin links list.
     *
     * @return array<string, mixed>
     */
    public function linksListData(Request $request): array
    {
        $filters = [
            'user_id' => $request->input('user_id'),
            'space_id' => $request->input('space_id'),
            'domain_id' => $request->input('domain_id'),
            'search' => $request->input('search'),
            'type' => $request->input('type'),
            'sort' => $request->input('sort'),
            'by' => $request->input('by'),
        ];

        return [
            'view' => 'admin.links.list',
            'links' => $this->links->paginateForAdmin($filters),
            'filters' => $this->namesForFilters($filters),
        ];
    }

    /**
     * Build the data required to edit a link from the admin panel.
     *
     * @return array<string, mixed>
     */
    public function linkEditData(int|string $id, User $admin): array
    {
        $link = $this->links->findOrFail($id);

        return [
            'view' => 'links.edit',
            'admin' => true,
            'domains' => $this->domains->forUser($link->user_id),
            'spaces' => $this->spaces->forUser($link->user_id),
            'link' => $link,
            'features' => $this->getFeatures($admin),
        ];
    }

    /**
     * Build the data required for the admin spaces list.
     *
     * @return array<string, mixed>
     */
    public function spacesListData(Request $request): array
    {
        $userId = $request->input('user_id');

        return [
            'view' => 'admin.spaces.list',
            'spaces' => $this->spaces->paginateForAdmin($userId ? (int) $userId : null, $request->input('search'), $this->sort($request)),
            'filters' => $this->namesForFilters(['user_id' => $userId]),
        ];
    }

    /**
     * Build the data required to edit a space from the admin panel.
     *
     * @return array<string, mixed>
     */
    public function spaceEditData(int|string $id): array
    {
        $space = $this->spaces->findOrFail($id);

        return [
            'view' => 'spaces.edit',
            'admin' => true,
            'space' => $space,
            'stats' => [
                'links' => $this->links->countForSpace($space->user_id, $space->id),
            ],
        ];
    }

    /**
     * Build the data required for the admin domains list.
     *
     * @return array<string, mixed>
     */
    public function domainsListData(Request $request): array
    {
        $userId = $request->input('user_id');

        return [
            'view' => 'admin.domains.list',
            'domains' => $this->domains->paginateForAdmin($userId ? (int) $userId : null, $request->input('search'), $this->sort($request)),
            'filters' => $this->namesForFilters(['user_id' => $userId]),
        ];
    }

    /**
     * Build the data required to edit a domain from the admin panel.
     *
     * @return array<string, mixed>
     */
    public function domainEditData(int|string $id): array
    {
        $domain = $this->domains->findOrFail($id);

        return [
            'view' => 'domains.edit',
            'admin' => true,
            'domain' => $domain,
            'stats' => [
                'links' => $this->links->countForDomain($domain->user_id, $domain->id),
            ],
        ];
    }

    /**
     * Build the data required for the admin pages list.
     *
     * @return array<string, mixed>
     */
    public function pagesListData(Request $request): array
    {
        return [
            'view' => 'admin.pages.list',
            'pages' => $this->pages->paginateForAdmin($request->input('search'), $this->sort($request)),
        ];
    }

    /**
     * Build the data required to edit a page.
     *
     * @return array<string, mixed>
     */
    public function pageEditData(int|string $id): array
    {
        return [
            'view' => 'admin.pages.edit',
            'page' => $this->pages->findOrFail($id),
        ];
    }

    /**
     * Build the data required for the admin plans list.
     *
     * @return array<string, mixed>
     */
    public function plansListData(Request $request): array
    {
        return [
            'view' => 'admin.plans.list',
            'plans' => $this->plans->paginateForAdmin(
                $request->input('search'),
                $request->input('visibility'),
                $request->input('status'),
                $this->sort($request)
            ),
        ];
    }

    /**
     * Build the data required to edit a plan.
     *
     * @return array<string, mixed>
     */
    public function planEditData(int|string $id): array
    {
        return [
            'view' => 'admin.plans.edit',
            'plan' => $this->plans->withTrashedFindOrFail($id),
        ];
    }

    /**
     * Build the data required for the admin subscriptions list.
     *
     * @return array<string, mixed>
     */
    public function subscriptionsListData(Request $request): array
    {
        $userId = $request->input('user_id');

        return [
            'view' => 'admin.subscriptions.list',
            'subscriptions' => $this->subscriptions->paginateForAdmin([
                'search' => $request->input('search'),
                'status' => $request->input('status'),
                'plan' => $request->input('plan'),
                'user_id' => $userId,
                'sort' => $request->input('sort'),
            ]),
            'plans' => $this->plans->paid(),
            'filters' => $this->namesForFilters(['user_id' => $userId]),
        ];
    }

    /**
     * Build the data required to create an emulated subscription.
     *
     * @return array<string, mixed>
     */
    public function subscriptionNewData(): array
    {
        return [
            'view' => 'admin.subscriptions.new',
            'plans' => $this->plans->paid(),
        ];
    }

    /**
     * Build the data required to edit a subscription.
     *
     * @return array<string, mixed>
     */
    public function subscriptionEditData(int|string $id): array
    {
        $subscription = $this->subscriptions->findOrFail($id);

        return [
            'view' => 'settings.payments.subscriptions.edit',
            'admin' => true,
            'subscription' => $subscription,
            'plan' => $this->plans->withTrashedByNameOrFail($subscription->name),
            'user' => $this->users->withTrashedFindOrFail($subscription->user_id),
        ];
    }

    /**
     * Create an emulated subscription for a user.
     *
     * @param array<string, mixed> $input
     */
    public function createSubscription(array $input): string
    {
        $user = $this->users->findByEmailOrFail($input['email']);
        $plan = $this->plans->findByStripePlanOrFail($input['plan']);
        $endDate = Carbon::now()->add($input['trial_days'], 'day');

        $this->subscriptions->createFromDto(SubscriptionData::fromArray([
            'user_id' => $user->id,
            'name' => $plan->name,
            'stripe_status' => 'emulated',
            'stripe_id' => '',
            'stripe_plan' => $input['plan'],
            'quantity' => 1,
            'trial_ends_at' => $endDate,
            'ends_at' => $endDate,
        ]));

        return $plan->name;
    }

    /**
     * Delete an emulated subscription and return its plan name.
     */
    public function deleteEmulatedSubscription(int|string $id): string
    {
        $subscription = $this->subscriptions->emulatedOrFail($id);
        $name = $subscription->name;

        $this->subscriptions->delete($id);

        return $name;
    }

    /**
     * Synchronize a language definition from a translation file object.
     */
    public function syncLanguage(object $file): string
    {
        $this->languages->updateOrCreateByCode($file->lang_code, LanguageData::fromArray([
            'name' => $file->lang_name,
            'dir' => $file->lang_dir,
        ]));

        return $file->lang_name;
    }

    /**
     * Import an uploaded language JSON file and return its display name.
     */
    public function importLanguage(UploadedFile $language): string
    {
        $file = $this->readLanguageFile($language);

        Storage::disk('languages')->put($file->lang_code . '.json', $this->languageFileContents($language));

        return $this->syncLanguage($file);
    }

    /**
     * Mark a language as default when requested.
     */
    public function updateLanguageDefault(int|string $id, bool $makeDefault): void
    {
        $language = $this->languages->findOrFail($id);

        if ($language->default == 0 && $makeDefault) {
            $this->languages->makeDefault($id);
        }
    }

    /**
     * Delete a non-default language and return its name.
     */
    public function deleteLanguage(int|string $id): string
    {
        if ($this->languages->count() <= 1) {
            throw new RuntimeException(__('The default language can\'t be deleted.'));
        }

        $language = $this->languages->findOrFail($id);

        if ($language->default) {
            throw new RuntimeException(__('The default language can\'t be deleted.'));
        }

        $name = $language->name;
        $this->languages->delete($id);
        Storage::disk('languages')->delete($id . '.json');

        return $name;
    }

    /**
     * Decode a language JSON upload into an object.
     */
    private function readLanguageFile(UploadedFile $language): object
    {
        $file = json_decode($this->languageFileContents($language));

        if (! is_object($file)) {
            throw new RuntimeException(__('Invalid language file.'));
        }

        return $file;
    }

    /**
     * Read the uploaded language JSON file contents.
     */
    private function languageFileContents(UploadedFile $language): string
    {
        $path = $language->getRealPath();

        if (! is_string($path)) {
            throw new RuntimeException(__('Invalid language file.'));
        }

        $contents = file_get_contents($path);

        if ($contents === false) {
            throw new RuntimeException(__('Invalid language file.'));
        }

        return $contents;
    }

    /**
     * Create a page and return its title.
     *
     * @param array<string, mixed> $input
     */
    public function createPage(array $input): string
    {
        $this->pages->createFromDto(PageData::fromArray($this->pageAttributes($input)));

        return $input['title'];
    }

    /**
     * Update a page with validated attributes.
     *
     * @param array<string, mixed> $input
     */
    public function updatePage(int|string $id, array $input): void
    {
        $this->pages->updateFromDto($id, PageData::fromArray($this->pageAttributes($input)));
    }

    /**
     * Delete a page and return its title.
     */
    public function deletePage(int|string $id): string
    {
        $page = $this->pages->findOrFail($id);
        $title = $page->title;

        $this->pages->delete($id);

        return $title;
    }

    /**
     * Create a Stripe-backed plan and store it locally.
     *
     * @param array<string, mixed> $input
     */
    public function createPlan(array $input): string
    {
        \Stripe\Stripe::setApiKey(config('cashier.secret'));
        $stripeProduct = $this->createStripeProduct([
            'name' => $input['name'],
            'type' => 'service',
        ]);

        $monthlyPlan = $this->createStripePlan([
            'product' => $stripeProduct->id,
            'amount' => $input['amount_month'],
            'interval' => 'month',
            'currency' => $input['currency'],
        ]);

        $yearlyPlan = $this->createStripePlan([
            'product' => $stripeProduct->id,
            'amount' => $input['amount_year'],
            'interval' => 'year',
            'currency' => $input['currency'],
        ]);

        $this->plans->createFromDto(PlanData::fromArray(array_merge(
            $this->planAttributes($input),
            [
                'product' => $stripeProduct->id,
                'plan_month' => $monthlyPlan->id,
                'plan_year' => $yearlyPlan->id,
                'amount_month' => $input['amount_month'],
                'amount_year' => $input['amount_year'],
                'currency' => $input['currency'],
            ]
        )));

        return $input['name'];
    }

    /**
     * Update a plan and synchronize Stripe metadata when needed.
     *
     * @param int|string $id
     * @param array $input
     * @return void
     * @throws \Stripe\Exception\ApiErrorException
     */
    public function updatePlan(int|string $id, array $input): void
    {
        $plan = $this->plans->withTrashedFindOrFail($id);

        if (!config('settings.stripe') && ($plan->amount_month > 0 || $plan->amount_year > 0)) {
            abort(404);
        }

        $attributes = $this->planAttributes($input);

        if ($plan->amount_month && $plan->amount_year) {
            \Stripe\Stripe::setApiKey(config('cashier.secret'));
            $this->updateStripeProduct($plan->product, ['name' => $input['name']]);

            $this->subscriptions->renamePlan($plan->name, SubscriptionData::fromArray(['name' => $input['name']]));
            $attributes['trial_days'] = $input['trial_days'];
            $attributes['visibility'] = $input['visibility'];
        }

        $this->plans->updateFromDto($id, PlanData::fromArray($attributes));
    }

    /**
     * Soft-delete a paid plan after validating admin rules.
     */
    public function disablePlan(int|string $id): void
    {
        $plan = $this->plans->findOrFail($id);

        if ($plan->amount_month == 0 && $plan->amount_year == 0) {
            throw new RuntimeException(__('The default plan can\'t be deleted.'));
        }

        $this->plans->delete($id);
    }

    /**
     * Restore a soft-deleted plan.
     */
    public function restorePlan(int|string $id): void
    {
        $this->plans->restore($id);
    }


    /**
     * Update a user from the admin panel.
     *
     * @param int|string $id
     * @param array $input
     * @param int $currentUserId
     * @return void
     */
    public function updateUser(int|string $id, array $input, int $currentUserId): void
    {
        $user = $this->users->withTrashedFindOrFail($id);

        if ($currentUserId == $user->id && ($input['role'] ?? null) == 0) {
            throw new RuntimeException(__('Operation denied.'));
        }

        $this->userSettings->updateProfile($user, $input, true);
    }

    /**
     * Permanently delete a user from the admin panel.
     *
     * @param int|string $id
     * @param int $currentUserId
     * @return string
     */
    public function deleteUser(int|string $id, int $currentUserId): string
    {
        $user = $this->users->withTrashedFindOrFail($id);

        if ($currentUserId == $user->id && $user->role == 1) {
            throw new RuntimeException(__('Operation denied.'));
        }

        $name = $user->name;
        $this->users->forceDelete($user);

        return $name;
    }

    /**
     * Soft-delete a user from the admin panel.
     *
     * @param int|string $id
     * @param int $currentUserId
     * @return void
     */
    public function disableUser(int|string $id, int $currentUserId): void
    {
        $user = $this->users->findOrFail($id);

        if ($currentUserId == $user->id && $user->role == 1) {
            throw new RuntimeException(__('Operation denied.'));
        }

        $this->users->softDelete($user);
    }

    /**
     * Restore a soft-deleted user from the admin panel.
     */
    public function restoreUser(int|string $id): void
    {
        $this->users->restore($this->users->withTrashedFindOrFail($id));
    }

    /**
     * Resolve display names for active admin filters.
     *
     * @param array<string, mixed> $filters
     * @return array<string, string>
     */
    private function namesForFilters(array $filters): array
    {
        $names = [];

        if (!empty($filters['user_id']) && $user = $this->users->find($filters['user_id'])) {
            $names['user'] = $user->name;
        }

        if (!empty($filters['space_id']) && $space = $this->spaces->find($filters['space_id'])) {
            $names['space'] = $space->name;
        }

        if (!empty($filters['domain_id']) && $domain = $this->domains->find($filters['domain_id'])) {
            $names['domain'] = str_replace(['http://', 'https://'], '', $domain->name);
        }

        return $names;
    }

    /**
     * Normalize the requested sort direction.
     */
    private function sort(Request $request): string
    {
        return $request->input('sort') === 'asc' ? 'asc' : 'desc';
    }

    /**
     * Map page input into repository attributes.
     *
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    private function pageAttributes(array $input): array
    {
        return [
            'title' => $input['title'],
            'slug' => $input['slug'],
            'footer' => ($input['footer'] ?? null) == 1 ? 1 : 0,
            'content' => $input['content'],
        ];
    }

    /**
     * Map plan input into repository attributes.
     *
     * @param array<string, mixed> $input
     * @return array<string, mixed>
     */
    private function planAttributes(array $input): array
    {
        return [
            'name' => $input['name'],
            'description' => $input['description'],
            'trial_days' => $input['trial_days'] ?? 0,
            'visibility' => $input['visibility'] ?? 0,
            'color' => $input['color'],
            'option_links' => $input['option_links'],
            'option_spaces' => $input['option_spaces'],
            'option_domains' => $input['option_domains'],
            'option_password' => $input['option_password'],
            'option_expiration' => $input['option_expiration'],
            'option_stats' => $input['option_stats'],
            'option_geo' => $input['option_geo'],
            'option_platform' => $input['option_platform'],
            'option_disabled' => $input['option_disabled'],
            'option_utm' => $input['option_utm'] ?? 0,
            'option_api' => $input['option_api'],
        ];
    }

    /**
     * Create a Stripe product through the Stripe SDK.
     *
     * @param array<string, mixed> $attributes
     */
    protected function createStripeProduct(array $attributes): object
    {
        return \Stripe\Product::create($attributes);
    }

    /**
     * Create a Stripe plan through the Stripe SDK.
     *
     * @param array<string, mixed> $attributes
     */
    protected function createStripePlan(array $attributes): object
    {
        return \Stripe\Plan::create($attributes);
    }

    /**
     * Update a Stripe product through the Stripe SDK.
     *
     * @param array<string, mixed> $attributes
     */
    protected function updateStripeProduct(string $productId, array $attributes): object
    {
        return \Stripe\Product::update($productId, $attributes);
    }
}
