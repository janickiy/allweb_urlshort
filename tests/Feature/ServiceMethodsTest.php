<?php

namespace Tests\Feature;

use App\DTO\RedirectResult;
use App\Enums\CheckoutStatus;
use App\Enums\RedirectDecision;
use App\Mail\ContactMail;
use App\Models\Link;
use App\Models\Plan;
use App\Models\User;
use App\Repositories\DomainRepository;
use App\Repositories\LinkRepository;
use App\Repositories\PageRepository;
use App\Repositories\PlanRepository;
use App\Repositories\WorkspaceRepository;
use App\Repositories\SubscriptionRepository;
use App\Repositories\UserRepository;
use App\Services\AdminService;
use App\Services\AliasGenerator;
use App\Services\CheckoutService;
use App\Services\ContactService;
use App\Services\DashboardService;
use App\Services\DomainService;
use App\Services\HomeService;
use App\Services\LinkService;
use App\Services\LocaleService;
use App\Services\PaymentSettingsService;
use App\Services\RedirectService;
use App\Services\SettingsService;
use App\Services\WorkspaceService;
use App\Services\StatsService;
use App\Services\UrlMetadataService;
use App\Services\UserRegistrationService;
use App\Services\UserSettingsService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Laravel\Cashier\Invoice as CashierInvoice;
use Laravel\Cashier\Payment;
use Laravel\Cashier\PaymentMethod as CashierPaymentMethod;
use Mockery;
use Mockery\MockInterface;
use RuntimeException;
use Stripe\Customer as StripeCustomer;
use Stripe\Invoice as StripeInvoice;
use Stripe\PaymentIntent;
use Stripe\PaymentMethod;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Tests\TestCase;

class ServiceMethodsTest extends TestCase
{
    use ControllerTestHelpers;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        config([
            'app.locales' => ['en' => 'English', 'fr' => 'French'],
            'app.url' => 'http://localhost',
            'cashier.secret' => 'sk_test_service',
            'settings.registration_registration' => 1,
            'settings.registration_verification' => 0,
            'settings.short_guest' => 1,
            'settings.short_bad_words' => '',
            'settings.stripe' => 0,
            'settings.index' => '',
            'settings.title' => 'Service Test',
            'settings.contact_email' => 'owner@example.test',
            'settings.theme' => 0,
        ]);
    }

    public function test_workspace_domain_and_link_service_methods(): void
    {
        $user = $this->user();

        $this->mock(UrlMetadataService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('parse')->andReturn(['title' => ' Parsed title '])->byDefault();
        });

        $workspaces = app(WorkspaceService::class);
        $createdWorkspace = $workspaces->create(['name' => 'Workspace', 'color' => 999], $user);
        $this->assertSame(1, (int) $createdWorkspace->color);

        $updatedWorkspace = $workspaces->update($createdWorkspace, ['name' => 'Workspace updated', 'color' => 2]);
        $this->assertSame('Workspace updated', $updatedWorkspace->name);
        $this->assertSame($updatedWorkspace->id, $workspaces->updateForUser($updatedWorkspace->id, $user, ['name' => 'Workspace user', 'color' => 3])->id);
        $this->assertSame($updatedWorkspace->id, $workspaces->updateById($updatedWorkspace->id, ['name' => 'Workspace admin', 'color' => 4])->id);

        $workspaceForUserDelete = $this->workspace($user, ['name' => 'Delete user workspace']);
        $this->assertSame('Delete user workspace', $workspaces->deleteForUser($workspaceForUserDelete->id, $user));
        $workspaceForAdminDelete = $this->workspace($user, ['name' => 'Delete admin workspace']);
        $this->assertSame('Delete admin workspace', $workspaces->deleteById($workspaceForAdminDelete->id));
        $workspaceForDirectDelete = $this->workspace($user, ['name' => 'Delete direct workspace']);
        $this->assertTrue($workspaces->delete($workspaceForDirectDelete));

        $domains = app(DomainService::class);
        $createdDomain = $domains->create([
            'name' => 'https://example-domain.test/path',
            'index_page' => 'https://example.com',
            'not_found_page' => null,
        ], $user);
        $this->assertSame('example-domain.test', $createdDomain->name);
        $this->assertSame('plain-domain.test', $domains->normalizeName('plain-domain.test'));
        $this->assertSame('domain.test', $domains->displayName($this->domain($user, ['name' => 'https://domain.test'])));

        $updatedDomain = $domains->update($createdDomain, ['index_page' => null, 'not_found_page' => 'https://example.com/404']);
        $this->assertSame('https://example.com/404', $updatedDomain->not_found_page);
        $this->assertSame($updatedDomain->id, $domains->updateForUser($updatedDomain->id, $user, ['index_page' => 'https://home.test'])->id);
        $this->assertSame($updatedDomain->id, $domains->updateById($updatedDomain->id, ['not_found_page' => 'https://nf.test'])->id);

        $domainForUserDelete = $this->domain($user, ['name' => 'http://delete-user-domain.test']);
        $this->assertSame('delete-user-domain.test', $domains->deleteForUser($domainForUserDelete->id, $user));
        $domainForAdminDelete = $this->domain($user, ['name' => 'https://delete-admin-domain.test']);
        $this->assertSame('delete-admin-domain.test', $domains->deleteById($domainForAdminDelete->id));
        $domainForDirectDelete = $this->domain($user, ['name' => 'direct-delete-domain.test']);
        $this->assertTrue($domains->delete($domainForDirectDelete));

        /** @var LinkService $links */
        $links = app(LinkService::class);
        $createdLink = $links->create([
            'url' => 'https://example.com/a',
            'alias' => 'service-create',
            'workspace' => $updatedWorkspace->id,
            'domain' => $updatedDomain->id,
            'disabled' => true,
            'public' => true,
            'expiration_url' => 'https://example.com/expired',
            'expiration_date' => '2030-01-01',
            'expiration_time' => '12:00',
            'password' => 'secret',
            'geo' => [['key' => 'US', 'value' => 'https://us.example.com']],
            'platform' => [['key' => 'Windows', 'value' => 'https://win.example.com']],
        ], $user);
        $this->assertSame('service-create', $createdLink->alias);
        $this->assertSame('Parsed title', $createdLink->title);
        $this->assertNotNull($createdLink->password);

        $latest = $links->createForUser(['url' => 'https://example.com/user', 'alias' => 'service-user'], $user);
        $this->assertCount(1, $latest);

        $guest = $links->createForGuest(['url' => 'https://example.com/guest', 'alias' => 'service-guest']);
        $this->assertCount(1, $guest);

        $many = $links->createMany(['urls' => "https://a.test\nhttps://b.test", 'workspace' => $updatedWorkspace->id], $user);
        $this->assertCount(2, $many);

        $updatedLink = $links->update($createdLink, [
            'url' => 'https://example.com/updated',
            'alias' => 'service-updated',
            'password' => $createdLink->password,
            'geo' => [['key' => '', 'value' => ''], ['key' => 'CA', 'value' => 'https://ca.example.com']],
            'platform' => null,
        ]);
        $this->assertSame('service-updated', $updatedLink->alias);
        $this->assertSame($createdLink->password, $updatedLink->password);

        $this->assertSame($updatedLink->id, $links->updateForUser($updatedLink->id, $user, ['alias' => 'service-user-update'])->id);
        $this->assertSame($updatedLink->id, $links->updateById($updatedLink->id, ['alias' => 'service-admin-update'])->id);
        $this->assertSame($updatedLink->id, $links->findForUser($updatedLink->id, $user)->id);
        $this->assertGreaterThanOrEqual(1, $links->latestForUser($user->id, 2)->count());
        $this->assertGreaterThanOrEqual(1, $links->paginateLatestForUser($user->id)->total());
        $this->assertStringContainsString('service-admin-update', $links->displayName($updatedLink->fresh()));

        $apiLink = $this->link($user, ['alias' => 'api-delete']);
        $this->assertSame($apiLink->id, $links->deleteForApiUser($apiLink->id, $user)->id);
        $this->assertNull($links->deleteForApiUser(999999, $user));

        $linkForUserDelete = $this->link($user, ['alias' => 'user-delete']);
        $this->assertStringContainsString('user-delete', $links->deleteForUser($linkForUserDelete->id, $user));
        $linkForAdminDelete = $this->link($user, ['alias' => 'admin-delete']);
        $this->assertStringContainsString('admin-delete', $links->deleteById($linkForAdminDelete->id));
        $linkForDirectDelete = $this->link($user, ['alias' => 'direct-delete']);
        $this->assertTrue($links->delete($linkForDirectDelete));

        $this->assertIsString(app(AliasGenerator::class)->generate(null));
    }

    public function test_content_user_settings_locale_home_dashboard_stats_redirect_and_contact_services(): void
    {
        Mail::fake();

        $registration = app(UserRegistrationService::class);
        config(['settings.registration_registration' => 0]);
        $this->assertNull($registration->createPublicUser([
            'name' => 'Blocked',
            'email' => 'blocked@example.test',
            'password' => 'password',
        ]));

        config(['settings.registration_registration' => 1, 'settings.registration_verification' => 0]);
        $registered = $registration->createPublicUser([
            'name' => 'Registered User',
            'email' => 'registered@example.test',
            'password' => 'password',
        ]);
        $this->assertInstanceOf(User::class, $registered);
        $this->assertNotNull($registered->email_verified_at);

        $admin = $registration->createInstallerAdmin([
            'name' => 'Installer Admin',
            'email' => 'installer@example.test',
            'password' => 'password',
        ]);
        $this->assertSame(1, (int) $admin->role);

        $settings = app(SettingsService::class);
        $settings->updateGeneral(['title' => 'Updated title', 'tagline' => 'Tag', 'index' => '', 'timezone' => 'UTC', 'tracking_code' => 'code']);
        $settings->updateRegistration(['registration_registration' => 1, 'registration_captcha' => 0, 'registration_verification' => 0]);
        $settings->updateContact(['contact_captcha' => 0, 'contact_email' => 'owner@example.test']);
        $settings->updateCaptcha(['captcha_site_key' => 'site', 'captcha_secret_key' => 'secret', 'captcha_registration' => 0, 'captcha_contact' => 0, 'captcha_shorten' => 0]);
        $settings->updateShortener(['short_guest' => 1, 'short_bad_words' => 'badword']);
        $settings->updateLegal(['legal_terms_url' => '/terms', 'legal_privacy_url' => '/privacy', 'legal_cookie_url' => '/cookies']);
        $settings->updateAppearance(['theme' => 1]);
        $settings->updateEmail(['email_driver' => 'log', 'email_host' => 'smtp.test', 'email_port' => 587, 'email_encryption' => 'tls', 'email_address' => 'mail@example.test', 'email_username' => 'user', 'email_password' => 'pass']);
        $settings->updateSocial(['social_facebook' => 'fb', 'social_twitter' => 'tw', 'social_instagram' => 'ig', 'social_youtube' => 'yt']);
        $settings->updatePayment(['stripe' => 0, 'stripe_key' => 'pk', 'stripe_secret' => 'sk', 'stripe_wh_secret' => 'wh']);
        $settings->updateInvoice(['invoice_vendor' => 'Vendor', 'invoice_address' => 'Address', 'invoice_city' => 'City', 'invoice_state' => 'State', 'invoice_postal_code' => '10001', 'invoice_country' => 'US', 'invoice_phone' => '100', 'invoice_vat_number' => 'VAT']);
        $this->assertDatabaseHas('settings', ['name' => 'title', 'value' => 'Updated title']);

        $userSettings = app(UserSettingsService::class);
        $user = $this->user(['email' => 'settings-user@example.test']);
        $this->assertTrue($userSettings->updateProfile($user, ['name' => 'Profile Updated', 'email' => 'profile@example.test', 'timezone' => 'Europe/Moscow']));
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Profile Updated']);
        $this->assertTrue($userSettings->regenerateApiToken($user->fresh()));
        $this->assertNotNull($user->fresh()->api_token);
        Auth::login($user->fresh());
        $this->assertTrue($userSettings->updatePassword($user->fresh(), 'new-password'));

        $deleteUser = $this->user(['email' => 'delete-account@example.test']);
        $this->assertTrue($userSettings->deleteAccount($deleteUser));
        $this->assertDatabaseMissing('users', ['id' => $deleteUser->id]);

        $locale = app(LocaleService::class);
        $this->assertFalse($locale->select('missing'));
        Auth::login($user->fresh());
        $this->assertTrue($locale->select('fr'));
        $this->assertSame('fr', $user->fresh()->locale);
        $this->assertNotNull(Cookie::queued('locale'));

        request()->merge([
            'email' => 'sender@example.test',
            'subject' => 'Contact subject',
            'message' => 'Contact body',
        ]);
        app(ContactService::class)->send();
        Mail::assertSent(ContactMail::class);

        $home = app(HomeService::class);
        $customDomain = $this->domain($user, ['name' => 'http://custom-home.test', 'index_page' => 'https://landing.example.test']);
        $localRequest = Request::create('/', 'GET', [], [], [], [
            'HTTP_HOST' => 'localhost',
            'HTTP_USER_AGENT' => 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 Chrome/120.0 Safari/537.36',
            'HTTP_ACCEPT_LANGUAGE' => 'en-US,en;q=0.9',
        ]);
        $remoteRequest = Request::create('/', 'GET', [], [], [], ['HTTP_HOST' => 'custom-home.test']);
        $this->assertNull($home->domainIndexRedirect($localRequest));
        $this->assertSame($customDomain->index_page, $home->domainIndexRedirect($remoteRequest));
        $this->assertSame(302, $home->landingRedirect($localRequest, true)['status']);
        config(['settings.index' => 'https://configured.example.test']);
        $this->assertSame('https://configured.example.test', $home->landingRedirect($localRequest, false)['url']);
        config(['settings.index' => '', 'settings.stripe' => 1]);
        $this->assertArrayHasKey('stats', $home->landingData());

        $plan = Plan::query()->where('name', 'Default')->firstOrFail();
        $link = $this->link($user, ['alias' => 'stats-service', 'public' => 1, 'clicks' => 10]);
        $this->stat($link, ['country' => 'US', 'referrer' => 'l.facebook.com']);
        $subscription = $this->subscription($user, $plan, ['stripe_status' => 'active', 'ends_at' => null]);

        $dashboard = app(DashboardService::class)->dataFor($user);
        $this->assertSame($user->id, $dashboard['user']->id);
        $this->assertSame($subscription->id, $dashboard['subscriptions'][0]->id);

        Auth::login($user);
        $stats = app(StatsService::class);
        $this->assertSame('general', $stats->general($link->id)['view']);
        $this->assertSame('geographic', $stats->geographic($link->id)['view']);
        $this->assertSame('browser', $stats->grouped($link->id, 'browser', 'browser', 'browsers')['view']);
        $this->assertSame('social', $stats->social($link->id)['view']);

        $redirects = app(RedirectService::class);
        $this->assertSame(RedirectDecision::NotFound, $redirects->resolve($localRequest, 'missing')->decision);
        $this->assertSame(RedirectDecision::Preview, $redirects->resolve(Request::create('/stats-service/preview', 'GET', [], [], [], ['HTTP_HOST' => 'localhost']), 'stats-service')->decision);
        $this->assertSame(RedirectDecision::Redirect, $redirects->resolve($localRequest, 'stats-service')->decision);
        $this->assertSame(RedirectDecision::Password, $redirects->resolve($localRequest, $this->link($user, ['alias' => 'password-service', 'password' => Hash::make('secret')])->alias)->decision);
        $this->assertSame(RedirectDecision::Disabled, $redirects->resolve($localRequest, $this->link($user, ['alias' => 'disabled-service', 'disabled' => 1])->alias)->decision);
        config(['settings.short_bad_words' => 'blocked.test']);
        $this->assertSame(RedirectDecision::Banned, $redirects->resolve($localRequest, $this->link($user, ['alias' => 'banned-service', 'url' => 'https://blocked.test'])->alias)->decision);
        $this->assertSame(RedirectDecision::Expired, $redirects->resolve($localRequest, $this->link($user, ['alias' => 'expired-service', 'ends_at' => Carbon::now()->subDay()])->alias)->decision);
        $expiredRedirect = $redirects->resolve($localRequest, $this->link($user, ['alias' => 'expired-target-service', 'ends_at' => Carbon::now()->subDay(), 'expiration_url' => 'https://expired.example.test'])->alias);
        $this->assertSame(RedirectDecision::Redirect, $expiredRedirect->decision);
        $this->assertSame('https://expired.example.test', $expiredRedirect->target);
        $this->assertInstanceOf(RedirectResult::class, $expiredRedirect);
    }

    public function test_admin_service_methods(): void
    {
        config(['settings.stripe' => 1]);

        $admin = $this->admin(['email' => 'service-admin@example.test']);
        $user = $this->user(['email' => 'service-user@example.test']);
        $workspace = $this->workspace($user, ['name' => 'Admin service workspace']);
        $domain = $this->domain($user, ['name' => 'http://admin-service-domain.test']);
        $link = $this->link($user, ['alias' => 'admin-service-link', 'workspace_id' => $workspace->id, 'domain_id' => $domain->id]);
        $page = $this->page(['title' => 'Admin Service Page', 'slug' => 'admin-service-page']);
        $plan = $this->paidPlan(['name' => 'Admin Service Plan']);
        $subscription = $this->subscription($user, $plan, ['stripe_status' => 'emulated']);

        $service = $this->adminServicePartial();
        $request = Request::create('/admin', 'GET', [
            'search' => 'service',
            'sort' => 'asc',
            'user_id' => $user->id,
            'workspace_id' => $workspace->id,
            'domain_id' => $domain->id,
            'type' => 1,
            'by' => 'alias',
        ]);

        $this->assertArrayHasKey('stats', $service->dashboardData($admin));
        $this->assertSame('admin.users.list', $service->usersListData($request)['view']);
        $this->assertSame('settings.account', $service->userEditData($user->id)['view']);
        $this->assertSame('admin.links.list', $service->linksListData($request)['view']);
        $this->assertSame('links.edit', $service->linkEditData($link->id, $admin)['view']);
        $this->assertSame('admin.workspaces.list', $service->workspacesListData($request)['view']);
        $this->assertSame('workspaces.edit', $service->workspaceEditData($workspace->id)['view']);
        $this->assertSame('admin.domains.list', $service->domainsListData($request)['view']);
        $this->assertSame('domains.edit', $service->domainEditData($domain->id)['view']);
        $this->assertSame('admin.pages.list', $service->pagesListData($request)['view']);
        $this->assertSame('admin.pages.edit', $service->pageEditData($page->id)['view']);
        $this->assertSame('admin.plans.list', $service->plansListData($request)['view']);
        $this->assertSame('admin.plans.edit', $service->planEditData($plan->id)['view']);
        $this->assertSame('admin.subscriptions.list', $service->subscriptionsListData($request)['view']);
        $this->assertSame('admin.subscriptions.new', $service->subscriptionNewData()['view']);
        $this->assertSame('settings.payments.subscriptions.edit', $service->subscriptionEditData($subscription->id)['view']);

        $createdSubscriptionName = $service->createSubscription([
            'email' => $user->email,
            'plan' => $plan->plan_month,
            'trial_days' => 7,
        ]);
        $this->assertSame($plan->name, $createdSubscriptionName);
        $emulated = $this->subscription($user, $plan, ['stripe_status' => 'emulated']);
        $this->assertSame($plan->name, $service->deleteEmulatedSubscription($emulated->id));

        $this->assertSame('Created Page', $service->createPage(['title' => 'Created Page', 'slug' => 'created-page', 'footer' => 1, 'content' => 'Body']));
        $createdPage = \App\Models\Page::query()->where('slug', 'created-page')->firstOrFail();
        $service->updatePage($createdPage->id, ['title' => 'Updated Page', 'slug' => 'updated-page', 'footer' => 0, 'content' => 'Updated']);
        $this->assertDatabaseHas('pages', ['id' => $createdPage->id, 'title' => 'Updated Page']);
        $this->assertSame('Updated Page', $service->deletePage($createdPage->id));

        $service->shouldReceive('createStripeProduct')->once()->andReturn((object) ['id' => 'prod_service']);
        $service->shouldReceive('createStripePlan')->twice()->andReturn((object) ['id' => 'price_service_month'], (object) ['id' => 'price_service_year']);
        $this->assertSame('Created Paid Plan', $service->createPlan($this->planPayload(['name' => 'Created Paid Plan'])));
        $this->assertDatabaseHas('plans', ['name' => 'Created Paid Plan', 'product' => 'prod_service']);

        $service->shouldReceive('updateStripeProduct')->once()->with($plan->product, ['name' => 'Renamed Paid Plan'])->andReturn((object) ['id' => $plan->product]);
        $service->updatePlan($plan->id, $this->planPayload(['name' => 'Renamed Paid Plan']));
        $this->assertDatabaseHas('plans', ['id' => $plan->id, 'name' => 'Renamed Paid Plan']);

        $defaultPlan = Plan::query()->where('name', 'Default')->firstOrFail();
        $service->updatePlan($defaultPlan->id, $this->planPayload(['name' => 'Default', 'description' => 'Updated Default', 'amount_month' => 0, 'amount_year' => 0]));
        $this->assertDatabaseHas('plans', ['id' => $defaultPlan->id, 'description' => 'Updated Default']);

        $this->expectException(RuntimeException::class);
        $service->disablePlan($defaultPlan->id);
    }

    public function test_admin_service_user_and_restore_methods(): void
    {
        $admin = $this->admin(['email' => 'admin-restore@example.test']);
        $user = $this->user(['email' => 'admin-restore-user@example.test']);
        $service = $this->adminServicePartial();

        try {
            $service->updateUser($admin->id, ['role' => 0], $admin->id);
            $this->fail('Expected self-demotion to be denied.');
        } catch (RuntimeException $exception) {
            $this->assertSame(__('Operation denied.'), $exception->getMessage());
        }

        $service->updateUser($user->id, ['name' => 'Admin Updated', 'email' => $user->email, 'timezone' => 'UTC', 'role' => 1], $admin->id);
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Admin Updated', 'role' => 1]);

        try {
            $service->deleteUser($admin->id, $admin->id);
            $this->fail('Expected self-delete to be denied.');
        } catch (RuntimeException $exception) {
            $this->assertSame(__('Operation denied.'), $exception->getMessage());
        }

        $deleteUser = $this->user(['name' => 'Delete From Admin']);
        $this->assertSame('Delete From Admin', $service->deleteUser($deleteUser->id, $admin->id));
        $this->assertDatabaseMissing('users', ['id' => $deleteUser->id]);

        try {
            $service->disableUser($admin->id, $admin->id);
            $this->fail('Expected self-disable to be denied.');
        } catch (RuntimeException $exception) {
            $this->assertSame(__('Operation denied.'), $exception->getMessage());
        }

        $disableUser = $this->user(['email' => 'disable-admin-service@example.test']);
        $service->disableUser($disableUser->id, $admin->id);
        $this->assertSoftDeleted('users', ['id' => $disableUser->id]);
        $service->restoreUser($disableUser->id);
        $this->assertDatabaseHas('users', ['id' => $disableUser->id, 'deleted_at' => null]);

        $paid = $this->paidPlan(['name' => 'Restore Service Plan']);
        $service->disablePlan($paid->id);
        $this->assertSoftDeleted('plans', ['id' => $paid->id]);
        $service->restorePlan($paid->id);
        $this->assertDatabaseHas('plans', ['id' => $paid->id, 'deleted_at' => null]);
    }

    public function test_payment_settings_and_checkout_service_methods(): void
    {
        $user = $this->billableMockUser();
        $plan = $this->paidPlan(['name' => 'Payment Service Plan']);
        $subscription = $this->subscription($this->user(['email' => 'subscription-owner@example.test']), $plan, ['stripe_status' => 'active', 'ends_at' => null]);

        $payments = $this->paymentSettingsServicePartial();
        $payments->shouldReceive('retrievePaymentMethod')->andReturn($this->stripePaymentMethod('pm_service', $user->stripe_id))->byDefault();
        $payments->shouldReceive('retrieveCustomer')->andReturn($this->stripeCustomer($user->stripe_id))->byDefault();
        $payments->shouldReceive('retrieveInvoice')->andReturn($this->stripeInvoice($user))->byDefault();
        $payments->shouldReceive('updateStripeCustomer')->andReturn($this->stripeCustomer($user->stripe_id))->byDefault();
        $payments->shouldReceive('detachPaymentMethod')->andReturn($this->stripePaymentMethod('pm_service', $user->stripe_id))->byDefault();

        $user->shouldReceive('defaultPaymentMethod')->andReturn((object) ['id' => 'pm_default'])->byDefault();
        $user->shouldReceive('paymentMethods')->andReturn(collect([(object) ['id' => 'pm_service']]))->byDefault();
        $user->shouldReceive('createSetupIntent')->andReturn((object) ['client_secret' => 'seti_secret'])->byDefault();
        $user->shouldReceive('hasDefaultPaymentMethod')->andReturn(false)->byDefault();
        $user->shouldReceive('addPaymentMethod')->andReturn($this->cashierPaymentMethod($user, 'pm_service'))->byDefault();
        $user->shouldReceive('updateDefaultPaymentMethod')->andReturn($this->cashierPaymentMethod($user, 'pm_service'))->byDefault();

        $this->assertSame($subscription->id, $payments->subscriptionEditData($subscription->user, $subscription->id)['subscription']->id);
        $this->assertArrayHasKey('paymentMethods', $payments->paymentMethods($user));
        $this->assertFalse($payments->newPaymentMethodData($user)['hasDefaultPaymentMethod']);
        $this->assertSame('pm_service', $payments->editPaymentMethodData($user, 'pm_service')['paymentMethod']->id);
        $this->assertSame($user->stripe_id, $payments->billingCustomer($user)->id);
        $this->assertInstanceOf(CashierInvoice::class, $payments->invoice($user, 'in_service'));
        $this->assertSame('pm_service', $payments->addPaymentMethod($user, 'pm_service', true)->id);
        $payments->updatePaymentMethod($user, 'pm_service', true);
        $this->assertSame('pm_service', $payments->deletePaymentMethod($user, 'pm_service')->id);
        $payments->updateBilling($user, ['name' => 'Payment Service', 'address' => 'Main', 'city' => 'City']);
        $payments->cancelSubscription($user, 'missing');
        $resumeUser = $this->billableMockUser(['id' => 4242, 'stripe_id' => 'cus_resume']);
        $resumeUser->shouldReceive('hasDefaultPaymentMethod')->andReturn(true);
        $payments->resumeSubscription($resumeUser, 'missing');

        $noPaymentUser = $this->billableMockUser(['stripe_id' => 'cus_no_payment']);
        $noPaymentUser->shouldReceive('hasDefaultPaymentMethod')->andReturn(false);
        $this->expectException(RuntimeException::class);
        $payments->resumeSubscription($noPaymentUser, 'missing');
    }

    public function test_checkout_service_methods(): void
    {
        $plan = $this->paidPlan(['name' => 'Checkout Service Plan']);
        $user = $this->user(['email' => 'checkout-service@example.test', 'stripe_id' => null]);
        $checkout = $this->checkoutServicePartial();

        $this->assertSame($plan->id, $checkout->paidPlan($plan->id)->id);
        $this->assertNull($checkout->incompletePaymentId($user, $plan));
        $this->assertSame(CheckoutStatus::Collect, $checkout->prepareCheckout($user, $plan->id, 'monthly')['status']);
        $this->assertSame(CheckoutStatus::Pricing, $checkout->prepareCollect($user, null)['status']);

        $checkout->shouldReceive('retrieveCustomer')->andReturn($this->stripeCustomer('cus_checkout'))->byDefault();
        $checkout->shouldReceive('retrievePaymentIntent')->andReturn(PaymentIntent::constructFrom(['id' => 'pi_service', 'status' => 'requires_payment_method']))->byDefault();
        $checkout->shouldReceive('updateStripeCustomer')->andReturn($this->stripeCustomer('cus_checkout'))->byDefault();

        $billable = $this->billableMockUser(['id' => $user->id, 'stripe_id' => 'cus_checkout']);
        $billable->shouldReceive('defaultPaymentMethod')->andReturn((object) ['id' => 'pm_checkout'])->byDefault();
        $billable->shouldReceive('createSetupIntent')->andReturn((object) ['client_secret' => 'seti_checkout'])->byDefault();
        $billable->shouldReceive('addPaymentMethod')->andReturn($this->cashierPaymentMethod($billable, 'pm_checkout'))->byDefault();
        $billable->shouldReceive('updateDefaultPaymentMethod')->andReturn($this->cashierPaymentMethod($billable, 'pm_checkout'))->byDefault();

        $this->assertInstanceOf(Payment::class, $checkout->confirmationPayment('pi_service'));
        $this->assertSame('pm_checkout', $checkout->checkoutData($billable)['paymentMethod']->id);
        $this->assertSame('seti_checkout', $checkout->collectData($billable)['intent']->client_secret);
        $this->assertSame(CheckoutStatus::Ready, $checkout->prepareCollect($billable, ['id' => $plan->id])['status']);

        $this->subscription($user, $plan, ['stripe_status' => 'active', 'ends_at' => null]);
        $this->assertNull($checkout->subscribe($user->fresh(), $plan, 'yearly'));
        $this->assertSame(CheckoutStatus::Complete, $checkout->subscribeForCheckout($user->fresh(), $plan->id, 'yearly')['status']);
        $checkout->updatePaymentDetails($billable, ['payment_method' => 'pm_checkout', 'name' => 'Checkout', 'address' => 'Main']);
    }

    private function adminServicePartial(): AdminService&MockInterface
    {
        return Mockery::mock(AdminService::class, [
            app(DomainRepository::class),
            app(LinkRepository::class),
            app(PageRepository::class),
            app(PlanRepository::class),
            app(WorkspaceRepository::class),
            app(SubscriptionRepository::class),
            app(UserRepository::class),
            app(UserSettingsService::class),
        ])->makePartial()->shouldAllowMockingProtectedMethods();
    }

    private function paymentSettingsServicePartial(): PaymentSettingsService&MockInterface
    {
        return Mockery::mock(PaymentSettingsService::class, [
            app(SubscriptionRepository::class),
            app(PlanRepository::class),
        ])->makePartial()->shouldAllowMockingProtectedMethods();
    }

    private function checkoutServicePartial(): CheckoutService&MockInterface
    {
        return Mockery::mock(CheckoutService::class, [
            app(PlanRepository::class),
            app(SubscriptionRepository::class),
        ])->makePartial()->shouldAllowMockingProtectedMethods();
    }

    private function billableMockUser(array $attributes = []): User&MockInterface
    {
        $user = Mockery::mock(User::class)->makePartial();
        $user->forceFill(array_merge([
            'id' => random_int(1000, 9999),
            'name' => 'Billable User',
            'email' => 'billable-' . Str::random(8) . '@example.test',
            'timezone' => 'UTC',
            'role' => 0,
            'stripe_id' => 'cus_service',
        ], $attributes));
        $user->exists = true;

        return $user;
    }

    private function stripeCustomer(string $id): StripeCustomer
    {
        return StripeCustomer::constructFrom([
            'id' => $id,
            'name' => 'Stripe Customer',
            'phone' => '+10000000000',
            'address' => [
                'line1' => 'Main Street',
                'city' => 'New York',
                'state' => 'NY',
                'postal_code' => '10001',
                'country' => 'US',
            ],
        ]);
    }

    private function stripeInvoice(User $user): StripeInvoice
    {
        return StripeInvoice::constructFrom([
            'id' => 'in_service',
            'number' => 'INV-SERVICE',
            'customer' => $user->stripe_id,
            'created' => Carbon::now()->timestamp,
            'currency' => 'usd',
            'subtotal' => 1000,
            'tax' => 0,
            'total' => 1000,
            'discounts' => [],
            'total_discount_amounts' => [],
            'starting_balance' => 0,
        ]);
    }

    private function stripePaymentMethod(string $id, string $customer): PaymentMethod
    {
        return PaymentMethod::constructFrom([
            'id' => $id,
            'customer' => $customer,
            'type' => 'card',
            'card' => [
                'brand' => 'visa',
                'last4' => '4242',
                'exp_month' => 12,
                'exp_year' => 2030,
            ],
        ]);
    }

    private function cashierPaymentMethod(User $user, string $id): CashierPaymentMethod
    {
        return new CashierPaymentMethod($user, $this->stripePaymentMethod($id, $user->stripe_id));
    }
}
