<?php

namespace App\Services;

use App\DTO\PageData;
use App\DTO\PlanData;
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
use RuntimeException;

class AdminService
{
    use UserFeaturesTrait;

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
     * @return array<string, mixed>
     */
    public function languagesListData(Request $request): array
    {
        return [
            'view' => 'admin.languages.list',
            'languages' => $this->languages->paginateForAdmin($request->input('search'), $this->sort($request)),
        ];
    }

    /**
     * @return array<string, mixed>
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

    public function deleteEmulatedSubscription(int|string $id): string
    {
        $subscription = $this->subscriptions->emulatedOrFail($id);
        $name = $subscription->name;

        $this->subscriptions->delete($id);

        return $name;
    }

    public function syncLanguage(object $file): string
    {
        $this->languages->updateOrCreateByCode($file->lang_code, [
            'name' => $file->lang_name,
            'dir' => $file->lang_dir,
        ]);

        return $file->lang_name;
    }

    public function updateLanguageDefault(int|string $id, bool $makeDefault): void
    {
        $language = $this->languages->findOrFail($id);

        if ($language->default == 0 && $makeDefault) {
            $this->languages->makeDefault($id);
        }
    }

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

        return $name;
    }

    /**
     * @param array<string, mixed> $input
     */
    public function createPage(array $input): string
    {
        $this->pages->createFromDto(PageData::fromArray($this->pageAttributes($input)));

        return $input['title'];
    }

    /**
     * @param array<string, mixed> $input
     */
    public function updatePage(int|string $id, array $input): void
    {
        $this->pages->updateFromDto($id, PageData::fromArray($this->pageAttributes($input)));
    }

    public function deletePage(int|string $id): string
    {
        $page = $this->pages->findOrFail($id);
        $title = $page->title;

        $this->pages->delete($id);

        return $title;
    }

    /**
     * @param array<string, mixed> $input
     */
    public function createPlan(array $input): string
    {
        \Stripe\Stripe::setApiKey(config('cashier.secret'));
        $stripeProduct = \Stripe\Product::create([
            'name' => $input['name'],
            'type' => 'service',
        ]);

        $monthlyPlan = \Stripe\Plan::create([
            'product' => $stripeProduct->id,
            'amount' => $input['amount_month'],
            'interval' => 'month',
            'currency' => $input['currency'],
        ]);

        $yearlyPlan = \Stripe\Plan::create([
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
     * @param array<string, mixed> $input
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
            \Stripe\Product::update($plan->product, ['name' => $input['name']]);

            $this->subscriptions->renamePlan($plan->name, $input['name']);
            $attributes['trial_days'] = $input['trial_days'];
            $attributes['visibility'] = $input['visibility'];
        }

        $this->plans->updateFromDto($id, PlanData::fromArray($attributes));
    }

    public function disablePlan(int|string $id): void
    {
        $plan = $this->plans->findOrFail($id);

        if ($plan->amount_month == 0 && $plan->amount_year == 0) {
            throw new RuntimeException(__('The default plan can\'t be deleted.'));
        }

        $this->plans->delete($id);
    }

    public function restorePlan(int|string $id): void
    {
        $this->plans->restore($id);
    }

    /**
     * @param array<string, mixed> $input
     */
    public function updateUser(int|string $id, array $input, int $currentUserId): void
    {
        $user = $this->users->withTrashedFindOrFail($id);

        if ($currentUserId == $user->id && ($input['role'] ?? null) == 0) {
            throw new RuntimeException(__('Operation denied.'));
        }

        $this->userSettings->updateProfile($user, $input, true);
    }

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

    public function disableUser(int|string $id, int $currentUserId): void
    {
        $user = $this->users->findOrFail($id);

        if ($currentUserId == $user->id && $user->role == 1) {
            throw new RuntimeException(__('Operation denied.'));
        }

        $this->users->softDelete($user);
    }

    public function restoreUser(int|string $id): void
    {
        $this->users->restore($this->users->withTrashedFindOrFail($id));
    }

    /**
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

    private function sort(Request $request): string
    {
        return $request->input('sort') === 'asc' ? 'asc' : 'desc';
    }

    /**
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
}
