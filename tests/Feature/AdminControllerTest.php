<?php

namespace Tests\Feature;

use App\Http\Controllers\AdminController;
use App\Http\Middleware\VerifyPaymentEnabled;
use App\Http\Requests\Admin\UpdateSettingsPaymentRequest;
use App\Models\Language;
use App\Models\Plan;
use App\Services\AdminService;
use App\Services\UrlMetadataService;
use Illuminate\Foundation\Http\Middleware\PreventRequestsDuringMaintenance;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Mockery\MockInterface;
use Tests\TestCase;

class AdminControllerTest extends TestCase
{
    use ControllerTestHelpers;
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();

        $this->withoutMiddleware(PreventRequestForgery::class);
        $this->withoutMiddleware(VerifyPaymentEnabled::class);
        $this->withoutMiddleware(PreventRequestsDuringMaintenance::class);

        $this->mock(UrlMetadataService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('parse')->andReturn([])->byDefault();
        });
    }

    public function test_admin_get_controller_methods_render(): void
    {
        $admin = $this->admin(['email' => 'admin-get@example.test']);
        $user = $this->user(['email' => 'admin-get-user@example.test']);
        $space = $this->space($user, ['name' => 'Admin GET space']);
        $domain = $this->domain($user, ['name' => 'http://admin-get-domain.test']);
        $link = $this->link($user, [
            'alias' => 'admin-get-link',
            'space_id' => $space->id,
            'domain_id' => $domain->id,
        ]);
        $page = $this->page(['slug' => 'admin-get-page']);
        $plan = $this->paidPlan(['name' => 'Admin GET Plan']);
        $subscription = $this->subscription($user, $plan);

        $this->actingAs($admin);

        $routes = [
            route('admin.dashboard'),
            route('admin.settings.general'),
            route('admin.settings.appearance'),
            route('admin.settings.email'),
            route('admin.settings.social'),
            route('admin.settings.payment'),
            route('admin.settings.invoice'),
            route('admin.settings.registration'),
            route('admin.settings.contact'),
            route('admin.settings.legal'),
            route('admin.settings.captcha'),
            route('admin.settings.shortener'),
            route('admin.languages'),
            route('admin.languages.new'),
            route('admin.languages.edit', 'en'),
            route('admin.users'),
            route('admin.users.edit', $user->id),
            route('admin.links'),
            route('admin.links.edit', $link->id),
            route('admin.spaces'),
            route('admin.spaces.edit', $space->id),
            route('admin.domains'),
            route('admin.domains.edit', $domain->id),
            route('admin.pages'),
            route('admin.pages.new'),
            route('admin.pages.edit', $page->id),
            route('admin.plans'),
            route('admin.plans.new'),
            route('admin.plans.edit', $plan->id),
            route('admin.subscriptions'),
            route('admin.subscriptions.new'),
            route('admin.subscriptions.edit', $subscription->id),
        ];

        foreach ($routes as $url) {
            $this->get($url)->assertOk();
        }
    }

    public function test_admin_settings_update_controller_methods(): void
    {
        $this->actingAs($this->admin(['email' => 'admin-settings@example.test']));

        $settingsPayloads = [
            'admin.settings.general.update' => [
                'title' => 'AllWeb Tests',
                'tagline' => 'Feature test tagline',
                'index' => 'https://example.com',
                'timezone' => 'UTC',
                'tracking_code' => '',
            ],
            'admin.settings.appearance.update' => [
                'theme' => 1,
            ],
            'admin.settings.email.update' => [
                'email_driver' => 'smtp',
                'email_host' => 'smtp.example.test',
                'email_port' => 587,
                'email_encryption' => 'tls',
                'email_address' => 'mail@example.test',
                'email_username' => 'mailer',
                'email_password' => 'secret',
            ],
            'admin.settings.social.update' => [
                'social_facebook' => 'https://facebook.com/allweb',
                'social_twitter' => 'https://twitter.com/allweb',
                'social_instagram' => 'https://instagram.com/allweb',
                'social_youtube' => 'https://youtube.com/allweb',
            ],
            'admin.settings.invoice.update' => [
                'invoice_vendor' => 'AllWeb',
                'invoice_address' => 'Main Street',
                'invoice_city' => 'New York',
                'invoice_state' => 'NY',
                'invoice_postal_code' => '10001',
                'invoice_country' => 'US',
                'invoice_phone' => '+10000000000',
                'invoice_vat_number' => 'VAT-1',
            ],
            'admin.settings.registration.update' => [
                'registration_registration' => 1,
                'registration_captcha' => 0,
                'registration_verification' => 1,
            ],
            'admin.settings.contact.update' => [
                'contact_email' => 'contact@example.test',
                'contact_captcha' => 0,
            ],
            'admin.settings.legal.update' => [
                'legal_terms_url' => 'https://example.com/terms',
                'legal_privacy_url' => 'https://example.com/privacy',
                'legal_cookie_url' => 'https://example.com/cookies',
            ],
            'admin.settings.captcha.update' => [
                'captcha_site_key' => 'site-key',
                'captcha_secret_key' => 'secret-key',
                'captcha_registration' => 0,
                'captcha_contact' => 0,
                'captcha_shorten' => 0,
            ],
            'admin.settings.shortener.update' => [
                'short_guest' => 1,
                'short_bad_words' => 'badword',
            ],
        ];

        foreach ($settingsPayloads as $routeName => $payload) {
            $this->post(route($routeName), $payload)->assertRedirect();
        }

        $this->assertDatabaseHas('settings', ['name' => 'title', 'value' => 'AllWeb Tests']);
        $this->assertDatabaseHas('settings', ['name' => 'theme', 'value' => '1']);
        $this->assertDatabaseHas('settings', ['name' => 'short_bad_words', 'value' => 'badword']);

        request()->headers->set('referer', route('admin.settings.payment'));
        $request = Mockery::mock(UpdateSettingsPaymentRequest::class);
        $request->shouldReceive('all')->andReturn([
            'stripe' => 0,
            'stripe_key' => 'pk_test',
            'stripe_secret' => 'sk_test',
            'stripe_wh_secret' => 'whsec_test',
        ]);

        $response = $this->app->make(AdminController::class)->updateSettingsPayment($request);

        $this->assertSame(302, $response->getStatusCode());
        $this->assertDatabaseHas('settings', ['name' => 'stripe_secret', 'value' => 'sk_test']);
    }

    public function test_admin_language_page_subscription_user_link_space_domain_and_plan_methods(): void
    {
        Storage::fake('languages');

        $admin = $this->admin(['email' => 'admin-resources@example.test']);
        $user = $this->user(['email' => 'resource-user@example.test']);
        $space = $this->space($user, ['name' => 'Resource space']);
        $domain = $this->domain($user, ['name' => 'http://resource-domain.test']);
        $link = $this->link($user, [
            'alias' => 'resource-link',
            'space_id' => $space->id,
            'domain_id' => $domain->id,
        ]);
        $plan = $this->paidPlan(['name' => 'Resource Plan', 'plan_month' => 'price_resource_month']);

        $this->actingAs($admin);

        $languageFile = UploadedFile::fake()->createWithContent('fr.json', json_encode([
            'lang_code' => 'fr',
            'lang_name' => 'French',
            'lang_dir' => 'ltr',
        ]));

        $this->post(route('admin.languages.create'), ['language' => $languageFile])
            ->assertRedirect(route('admin.languages'));
        $this->assertDatabaseHas('languages', ['code' => 'fr', 'name' => 'French']);
        Storage::disk('languages')->assertExists('fr.json');

        $this->post(route('admin.languages.update', 'fr'), ['default' => 1])
            ->assertRedirect(route('admin.languages.edit', 'fr'));
        $this->assertDatabaseHas('languages', ['code' => 'fr', 'default' => 1]);

        Language::forceCreate(['code' => 'de', 'name' => 'German', 'dir' => 'ltr', 'default' => 0]);
        Storage::disk('languages')->put('de.json', '{}');
        $this->post(route('admin.languages.delete', 'de'))->assertRedirect(route('admin.languages'));
        $this->assertDatabaseMissing('languages', ['code' => 'de']);
        Storage::disk('languages')->assertMissing('de.json');

        $this->post(route('admin.pages.create'), [
            'title' => 'Admin Page',
            'slug' => 'admin-page',
            'footer' => 1,
            'content' => '<p>Admin content</p>',
        ])->assertRedirect(route('admin.pages'));
        $this->assertDatabaseHas('pages', ['slug' => 'admin-page', 'footer' => 1]);

        $page = $this->page(['title' => 'Existing Page', 'slug' => 'existing-page']);
        $this->from(route('admin.pages.edit', $page->id))
            ->post(route('admin.pages.update', $page->id), [
                'title' => 'Updated Page',
                'slug' => 'updated-page',
                'content' => '<p>Updated</p>',
            ])->assertRedirect(route('admin.pages.edit', $page->id));
        $this->assertDatabaseHas('pages', ['id' => $page->id, 'slug' => 'updated-page', 'footer' => 0]);

        $pageToDelete = $this->page(['slug' => 'delete-page']);
        $this->post(route('admin.pages.delete', $pageToDelete->id))->assertRedirect(route('admin.pages'));
        $this->assertDatabaseMissing('pages', ['id' => $pageToDelete->id]);

        $this->post(route('admin.subscriptions.create'), [
            'email' => $user->email,
            'plan' => $plan->plan_month,
            'trial_days' => 5,
        ])->assertRedirect(route('admin.subscriptions'));
        $this->assertDatabaseHas('subscriptions', [
            'user_id' => $user->id,
            'name' => $plan->name,
            'stripe_status' => 'emulated',
        ]);

        $subscription = $this->subscription($user, $plan, ['stripe_plan' => $plan->plan_month]);
        $this->post(route('admin.subscriptions.delete', $subscription->id))->assertRedirect(route('admin.subscriptions'));
        $this->assertDatabaseMissing('subscriptions', ['id' => $subscription->id]);

        $this->from(route('admin.subscriptions.update', $user->id))
            ->post(route('admin.subscriptions.update', $user->id), [
                'name' => 'Updated Resource User',
                'email' => 'updated-resource-user@example.test',
                'timezone' => 'UTC',
                'role' => 0,
            ])->assertRedirect(route('admin.users.edit', $user->id));
        $this->assertDatabaseHas('users', ['id' => $user->id, 'email' => 'updated-resource-user@example.test']);

        $disableUser = $this->user(['email' => 'disable-user@example.test']);
        $this->post(route('admin.users.disable', $disableUser->id))->assertRedirect(route('admin.users.edit', $disableUser->id));
        $this->assertSoftDeleted('users', ['id' => $disableUser->id]);
        $this->post(route('admin.users.restore', $disableUser->id))->assertRedirect(route('admin.users.edit', $disableUser->id));
        $this->assertDatabaseHas('users', ['id' => $disableUser->id, 'deleted_at' => null]);

        $deleteUser = $this->user(['email' => 'delete-user@example.test']);
        $this->post(route('admin.users.delete', $deleteUser->id))->assertRedirect(route('admin.users'));
        $this->assertDatabaseMissing('users', ['id' => $deleteUser->id]);

        $this->from(route('admin.links.edit', $link->id))
            ->post(route('admin.links.update', $link->id), [
                'url' => 'https://example.com/admin-updated',
                'alias' => 'admin-updated-link',
            ])->assertRedirect(route('admin.links.edit', $link->id));
        $this->assertDatabaseHas('links', ['id' => $link->id, 'alias' => 'admin-updated-link']);

        $linkToDelete = $this->link($user, ['alias' => 'admin-delete-link']);
        $this->post(route('admin.links.delete', $linkToDelete->id))->assertRedirect(route('admin.links'));
        $this->assertDatabaseMissing('links', ['id' => $linkToDelete->id]);

        $this->from(route('admin.spaces.edit', $space->id))
            ->post(route('admin.spaces.update', $space->id), [
                'name' => 'Updated Resource Space',
                'color' => 3,
                'user_id' => $user->id,
            ])->assertRedirect(route('admin.spaces.edit', $space->id));
        $this->assertDatabaseHas('spaces', ['id' => $space->id, 'name' => 'Updated Resource Space']);

        $spaceToDelete = $this->space($user, ['name' => 'Admin delete space']);
        $this->post(route('admin.spaces.delete', $spaceToDelete->id))->assertRedirect(route('admin.spaces'));
        $this->assertDatabaseMissing('spaces', ['id' => $spaceToDelete->id]);

        $this->from(route('admin.domains.edit', $domain->id))
            ->post(route('admin.domains.update', $domain->id), [
                'index_page' => 'https://example.com',
                'not_found_page' => 'https://example.com/404',
            ])->assertRedirect(route('admin.domains.edit', $domain->id));
        $this->assertDatabaseHas('domains', ['id' => $domain->id, 'not_found_page' => 'https://example.com/404']);

        $domainToDelete = $this->domain($user, ['name' => 'http://admin-delete-domain.test']);
        $this->post(route('admin.domains.delete', $domainToDelete->id))->assertRedirect(route('admin.domains'));
        $this->assertDatabaseMissing('domains', ['id' => $domainToDelete->id]);

        $defaultPlan = Plan::query()->where('name', 'Default')->firstOrFail();
        $this->from(route('admin.plans.edit', $defaultPlan->id))
            ->post(route('admin.plans.update', $defaultPlan->id), $this->planPayload([
                'name' => 'Default',
                'description' => 'Updated default plan',
                'amount_month' => 0,
                'amount_year' => 0,
            ]))->assertRedirect(route('admin.plans.edit', $defaultPlan->id));
        $this->assertDatabaseHas('plans', ['id' => $defaultPlan->id, 'description' => 'Updated default plan']);

        $planToDisable = $this->paidPlan(['name' => 'Disable Plan']);
        $this->post(route('admin.plans.disable', $planToDisable->id))->assertRedirect();
        $this->assertSoftDeleted('plans', ['id' => $planToDisable->id]);
        $this->post(route('admin.plans.restore', $planToDisable->id))->assertRedirect();
        $this->assertDatabaseHas('plans', ['id' => $planToDisable->id, 'deleted_at' => null]);
    }

    public function test_admin_create_plan_controller_method_uses_service(): void
    {
        $this->actingAs($this->admin(['email' => 'admin-plan@example.test']));

        $this->mock(AdminService::class, function (MockInterface $mock): void {
            $mock->shouldReceive('createPlan')
                ->once()
                ->andReturn('Mocked Paid Plan');
        });

        $this->post(route('admin.plans.create'), $this->planPayload(['name' => 'Mocked Paid Plan']))
            ->assertRedirect(route('admin.plans'));
    }
}
