<?php

namespace Tests\Feature;

use App\DTO\DomainData;
use App\DTO\LinkData;
use App\DTO\PageData;
use App\DTO\SettingData;
use App\DTO\SubscriptionData;
use App\DTO\UserData;
use App\Models\Page;
use App\Repositories\DomainRepository;
use App\Repositories\LinkRepository;
use App\Repositories\PageRepository;
use App\Repositories\PlanRepository;
use App\Repositories\RepositoryInterface;
use App\Repositories\SettingRepository;
use App\Repositories\WorkspaceRepository;
use App\Repositories\StatRepository;
use App\Repositories\SubscriptionRepository;
use App\Repositories\UserRepository;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use InvalidArgumentException;
use Tests\TestCase;

class RepositoryMethodsTest extends TestCase
{
    use ControllerTestHelpers;
    use RefreshDatabase;

    public function test_base_repository_methods_through_page_repository(): void
    {
        /** @var PageRepository $pages */
        $pages = app(PageRepository::class);

        $this->assertInstanceOf(RepositoryInterface::class, $pages);
        $this->assertInstanceOf(Builder::class, $pages->query());

        $created = $pages->createFromDto(PageData::fromArray([
            'title' => 'Repository Page',
            'slug' => 'repository-page',
            'footer' => 1,
            'content' => 'Repository content',
        ]));

        $this->assertSame('Repository Page', $created->title);
        $this->assertGreaterThanOrEqual(1, $pages->all()->count());
        $this->assertSame($created->id, $pages->find($created->id)->id);
        $this->assertSame($created->id, $pages->findOrFail($created->id)->id);

        $this->assertTrue($pages->updateFromDto($created->id, PageData::fromArray(['title' => 'Repository Page Updated'])));
        $this->assertSame('Repository Page Updated', $pages->findOrFail($created->id)->title);

        $this->assertSame($created->id, $pages->findBySlugOrFail('repository-page')->id);
        $this->assertInstanceOf(LengthAwarePaginator::class, $pages->paginateForAdmin('Repository', 'asc'));

        $this->assertTrue($pages->delete($created->id));
        $this->assertFalse($pages->delete(999999));

        $this->page(['slug' => 'truncate-page']);
        $pages->truncate();
        $this->assertSame(0, Page::query()->count());
    }

    public function test_domain_and_workspace_repository_methods(): void
    {
        $user = $this->user();
        $otherUser = $this->user();

        /** @var DomainRepository $domains */
        $domains = app(DomainRepository::class);
        $domain = $domains->createFromDto(DomainData::fromArray([
            'name' => 'http://repository-domain.test',
            'index_page' => 'https://example.com',
            'not_found_page' => 'https://example.com/404',
            'user_id' => $user->id,
        ]));
        $this->domain($otherUser, ['name' => 'http://other-domain.test']);

        $this->assertInstanceOf(Builder::class, $domains->query());
        $this->assertSame(1, $domains->forUser($user->id)->count());
        $this->assertSame($domain->id, $domains->findForUserOrFail($domain->id, $user->id)->id);
        $this->assertSame($domain->id, $domains->findByHost('https://repository-domain.test/path')->id);
        $this->assertGreaterThanOrEqual($domain->id, $domains->maxId());
        $this->assertSame(1, $domains->countForUser($user->id));
        $this->assertInstanceOf(LengthAwarePaginator::class, $domains->paginateForUser($user->id, 'repository', 'asc'));
        $this->assertInstanceOf(LengthAwarePaginator::class, $domains->paginateForAdmin($user->id, 'repository', 'desc'));

        /** @var WorkspaceRepository $workspaces */
        $workspaces = app(WorkspaceRepository::class);
        $workspace = $this->workspace($user, ['name' => 'Repository Workspace']);
        $this->workspace($otherUser, ['name' => 'Other Workspace']);

        $this->assertInstanceOf(Builder::class, $workspaces->query());
        $this->assertSame(1, $workspaces->forUser($user->id)->where('id', $workspace->id)->count());
        $this->assertSame($workspace->id, $workspaces->findForUserOrFail($workspace->id, $user->id)->id);
        $this->assertGreaterThanOrEqual($workspace->id, $workspaces->maxId());
        $this->assertGreaterThanOrEqual(1, $workspaces->countForUser($user->id));
        $this->assertInstanceOf(LengthAwarePaginator::class, $workspaces->paginateForUser($user->id, 'Repository', 'asc'));
        $this->assertInstanceOf(LengthAwarePaginator::class, $workspaces->paginateForAdmin($user->id, 'Repository', 'desc'));
    }

    public function test_setting_repository_methods(): void
    {
        /** @var SettingRepository $settings */
        $settings = app(SettingRepository::class);
        $this->assertTrue($settings->updateByName('title', SettingData::fromArray(['value' => 'Repository Title'])));
        $this->assertDatabaseHas('settings', ['name' => 'title', 'value' => 'Repository Title']);
        $this->assertFalse($settings->updateByName('missing_setting', SettingData::fromArray(['value' => 'Missing'])));
    }

    public function test_link_repository_methods(): void
    {
        $user = $this->user();
        $otherUser = $this->user();
        $workspace = $this->workspace($user);
        $domain = $this->domain($user, ['name' => 'http://repository-link-domain.test']);

        /** @var LinkRepository $links */
        $links = app(LinkRepository::class);

        $first = $this->link($user, [
            'alias' => 'repo-first',
            'url' => 'https://example.com/first',
            'title' => 'Repository First',
            'clicks' => 10,
            'workspace_id' => $workspace->id,
            'domain_id' => $domain->id,
        ]);
        $second = $this->link($user, [
            'alias' => 'repo-second',
            'url' => 'https://example.com/second',
            'title' => 'Repository Second',
            'clicks' => 2,
            'workspace_id' => $workspace->id,
            'domain_id' => null,
            'ends_at' => Carbon::now()->subDay(),
        ]);
        $otherLink = $this->link($otherUser, ['alias' => 'repo-other']);

        $this->assertInstanceOf(Builder::class, $links->query());
        $this->assertInstanceOf(LengthAwarePaginator::class, $links->paginateForUser($user->id, ['search' => 'Repository', 'by' => 'title', 'sort' => 'max']));
        $this->assertInstanceOf(LengthAwarePaginator::class, $links->paginateForUser($user->id, ['domain' => $domain->id, 'workspace' => $workspace->id, 'type' => 1, 'sort' => 'min']));
        $this->assertInstanceOf(LengthAwarePaginator::class, $links->paginateForAdmin(['search' => 'repo', 'by' => 'alias', 'user_id' => $user->id, 'workspace_id' => $workspace->id, 'domain_id' => $domain->id, 'type' => 1, 'sort' => 'asc']));

        $this->assertSame($first->id, $links->findForUser($first->id, $user->id)->id);
        $this->assertNull($links->findForUser($first->id, $otherUser->id));
        $this->assertSame($first->id, $links->findForUserOrFail($first->id, $user->id)->id);
        $this->assertSame($second->id, $links->latestForUser($user->id, 1)->first()->id);
        $this->assertInstanceOf(LengthAwarePaginator::class, $links->paginateLatestForUser($user->id));
        $this->assertSame($otherLink->id, $links->latest(1)->first()->id);
        $this->assertGreaterThanOrEqual($second->id, $links->maxId());

        $this->assertSame(2, $links->countForUser($user->id));
        $this->assertSame(2, $links->countForWorkspace($user->id, $workspace->id));
        $this->assertSame(1, $links->countForDomain($user->id, $domain->id));
        $this->assertSame($first->id, $links->findByAliasForDomain('repo-first', $domain->id)->id);

        $this->assertSame('http://repository-link-domain.test', $links->domainName($first->fresh()));
        $loaded = $first->fresh()->load('domain');
        $this->assertSame('http://repository-link-domain.test', $links->domainName($loaded));
        $this->assertNull($links->domainName($second->fresh()));

        $this->assertTrue($links->aliasExists('repo-first', $domain->id));
        $this->assertFalse($links->aliasExists('repo-first', $domain->id, $first->id));
        $this->assertTrue($links->bulkInsertFromDtos([
            LinkData::fromArray([
                'user_id' => $user->id,
                'alias' => 'repo-bulk',
                'url' => 'https://example.com/bulk',
                'title' => 'Bulk',
            ]),
        ]));
        $this->assertTrue($links->bulkInsertFromDtos([]));
        $this->assertDatabaseHas('links', ['alias' => 'repo-bulk']);

        $this->assertTrue($links->incrementClicks($first->fresh()));
        $this->assertSame(11, (int) $first->fresh()->clicks);
    }

    public function test_plan_repository_methods(): void
    {
        /** @var PlanRepository $plans */
        $plans = app(PlanRepository::class);
        $paid = $this->paidPlan(['name' => 'Repository Paid Plan', 'visibility' => 1]);
        $hidden = $this->paidPlan(['name' => 'Repository Hidden Plan', 'visibility' => 0]);

        $this->assertInstanceOf(Builder::class, $plans->query());
        $this->assertGreaterThanOrEqual(1, $plans->visible()->where('id', $paid->id)->count());
        $this->assertSame('Default', $plans->free()->name);
        $this->assertGreaterThanOrEqual(2, $plans->paid()->count());
        $this->assertSame($paid->id, $plans->paidByIdOrFail($paid->id)->id);
        $this->assertSame($paid->id, $plans->withTrashedByNameOrFail($paid->name)->id);
        $this->assertSame($paid->id, $plans->withTrashedFindOrFail($paid->id)->id);
        $this->assertGreaterThanOrEqual(3, $plans->withTrashedCount());

        config(['settings.stripe' => 1]);
        $this->assertInstanceOf(LengthAwarePaginator::class, $plans->paginateForAdmin('Repository', 1, 0, 'asc'));
        config(['settings.stripe' => 0]);
        $this->assertInstanceOf(LengthAwarePaginator::class, $plans->paginateForAdmin(null, null, null, 'desc'));

        $this->assertSame($paid->id, $plans->findByStripePlanOrFail($paid->plan_month)->id);
        $plans->delete($hidden->id);
        $this->assertNotNull($plans->withTrashedFindOrFail($hidden->id)->deleted_at);
        $this->assertTrue($plans->restore($hidden->id));
        $this->assertNull($plans->withTrashedFindOrFail($hidden->id)->deleted_at);
    }

    public function test_stat_repository_methods(): void
    {
        $user = $this->user();
        $link = $this->link($user, ['clicks' => 3]);
        $old = $this->stat($link, ['country' => 'US', 'browser' => 'Chrome', 'created_at' => Carbon::now()->subDays(10)]);
        $recent = $this->stat($link, ['country' => 'CA', 'browser' => 'Firefox', 'created_at' => Carbon::now()]);

        /** @var StatRepository $stats */
        $stats = app(StatRepository::class);

        $this->assertSame($recent->id, $stats->latestForUser($user->id, 1)->first()->id);
        $this->assertGreaterThanOrEqual($recent->id, $stats->maxId());
        $this->assertInstanceOf(LengthAwarePaginator::class, $stats->paginateForLink($link->id));
        $this->assertSame(1, $stats->countForLinkSince($link->id, Carbon::now()->subDay()));
        $this->assertSame(1, $stats->countForLinkBetween($link->id, Carbon::now()->subDays(11), Carbon::now()->subDays(9)));

        $countryGroups = $stats->groupForLink($link->id, 'country', null, false);
        $countries = $countryGroups->pluck('country')->all();
        sort($countries);
        $this->assertSame(['CA', 'US'], $countries);
        $this->assertInstanceOf(LengthAwarePaginator::class, $stats->groupForLink($link->id, 'browser'));

        $filtered = $stats->groupForLink($link->id, 'country', ['US'], false);
        $this->assertSame(['US'], $filtered->pluck('country')->all());

        $this->expectException(InvalidArgumentException::class);
        $stats->groupForLink($link->id, 'unsupported_column');
    }

    public function test_subscription_repository_methods(): void
    {
        $user = $this->user();
        $otherUser = $this->user();
        $plan = $this->paidPlan(['name' => 'Repository Subscription Plan']);
        $subscription = $this->subscription($user, $plan, ['stripe_status' => 'emulated']);
        $active = $this->subscription($otherUser, $plan, ['stripe_status' => 'active']);

        /** @var SubscriptionRepository $subscriptions */
        $subscriptions = app(SubscriptionRepository::class);

        $this->assertSame($subscription->id, $subscriptions->findForUserOrFail($subscription->id, $user->id)->id);
        $this->assertSame(1, $subscriptions->forUser($user->id)->count());
        $this->assertSame($subscription->id, $subscriptions->findForUserByName($user->id, $plan->name)->id);
        $this->assertNull($subscriptions->findForUserByName($user->id, 'missing'));
        $this->assertSame($active->id, $subscriptions->recent(1)->first()->id);
        $this->assertGreaterThanOrEqual(2, $subscriptions->count());
        $this->assertSame(1, $subscriptions->countForUser($user->id));
        $this->assertSame($subscription->id, $subscriptions->emulatedOrFail($subscription->id)->id);

        $renamed = $subscriptions->renamePlan($plan->name, SubscriptionData::fromArray(['name' => 'Renamed Repository Plan']));
        $this->assertGreaterThanOrEqual(2, $renamed);
        $this->assertDatabaseHas('subscriptions', ['id' => $subscription->id, 'name' => 'Renamed Repository Plan']);

        $this->assertInstanceOf(LengthAwarePaginator::class, $subscriptions->paginateForAdmin([
            'status' => 'active',
            'plan' => 'Renamed Repository Plan',
            'user_id' => $user->id,
            'sort' => 'asc',
        ]));
    }

    public function test_user_repository_methods(): void
    {
        /** @var UserRepository $users */
        $users = app(UserRepository::class);

        $created = $users->createFromDto(UserData::fromArray([
            'name' => 'Repository User',
            'email' => 'repository-user@example.test',
            'password' => Hash::make('password'),
            'timezone' => 'UTC',
            'role' => 0,
        ]));
        $this->assertSame('Repository User', $created->name);

        $this->assertTrue($users->updateFromDto($created->id, UserData::fromArray(['name' => 'Repository User Updated'])));
        $this->assertDatabaseHas('users', ['id' => $created->id, 'name' => 'Repository User Updated']);
        $this->assertTrue($users->updateLocale($created->fresh(), 'fr'));
        $this->assertSame('fr', $created->fresh()->locale);
        $this->assertTrue($users->updatePassword($created->fresh(), 'new-password'));
        $this->assertTrue(Hash::check('new-password', $created->fresh()->password));
        $this->assertTrue($users->regenerateApiToken($created->fresh()));
        $this->assertNotNull($created->fresh()->api_token);
        $this->assertGreaterThanOrEqual($created->id, $users->maxId());

        $softDelete = $this->user(['email' => 'repository-soft-delete@example.test']);
        $this->assertTrue($users->softDelete($softDelete));
        $this->assertNotNull($users->withTrashedFindOrFail($softDelete->id)->deleted_at);
        $this->assertTrue($users->restore($softDelete->fresh()));
        $this->assertNull($users->withTrashedFindOrFail($softDelete->id)->deleted_at);

        $this->assertGreaterThanOrEqual(1, $users->recentWithTrashed(5)->count());
        $this->assertGreaterThanOrEqual(1, $users->withTrashedCount());
        $this->assertSame($created->id, $users->findByEmailOrFail('repository-user@example.test')->id);
        $this->assertInstanceOf(LengthAwarePaginator::class, $users->paginateForAdmin([
            'search' => 'repository-user@example.test',
            'by' => 'email',
            'role' => 0,
            'sort' => 'asc',
        ]));
        $this->assertInstanceOf(LengthAwarePaginator::class, $users->paginateForAdmin([
            'search' => 'Repository User',
            'by' => 'name',
            'sort' => 'desc',
        ]));

        $forceDelete = $this->user(['email' => 'repository-force-delete@example.test']);
        $this->assertTrue($users->forceDelete($forceDelete));
        $this->assertDatabaseMissing('users', ['id' => $forceDelete->id]);
    }
}
