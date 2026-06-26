<?php

namespace Database\Seeders;

use App\Models\Domain;
use App\Models\Link;
use App\Models\Page;
use App\Models\Plan;
use App\Models\Workspace;
use App\Models\Stat;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class TestDataSeeder extends Seeder
{
    private const PASSWORD = 'password';

    /**
     * Seed deterministic test data across all application entities.
     */
    public function run(): void
    {
        Model::unguarded(function (): void {
            $this->seedSettings();
            $users = $this->seedUsers();
            $plans = $this->seedPlans();
            $this->seedPages();
            $workspaces = $this->seedWorkspaces($users);
            $domains = $this->seedDomains($users);
            $links = $this->seedLinks($users, $workspaces, $domains);
            $this->seedStats($links);
            $this->seedSubscriptions($users, $plans);
        });
    }

    /**
     * Add realistic values to configurable site settings.
     */
    private function seedSettings(): void
    {
        $settings = [
            'contact_email' => env('MAIL_FROM_ADDRESS', 'support@example.test'),
            'email_address' => env('MAIL_FROM_ADDRESS', 'noreply@example.test'),
            'email_driver' => env('MAIL_MAILER', env('MAIL_DRIVER', 'log')),
            'email_encryption' => $this->mailEncryption(),
            'email_host' => env('MAIL_HOST', 'mailpit'),
            'email_port' => env('MAIL_PORT', '1025'),
            'email_username' => env('MAIL_USERNAME', 'test'),
            'email_password' => env('MAIL_PASSWORD', 'password'),
            'invoice_address' => '100 Demo Street',
            'invoice_city' => 'Testville',
            'invoice_country' => 'US',
            'invoice_phone' => '+1 555 0100',
            'invoice_postal_code' => '10001',
            'invoice_state' => 'NY',
            'invoice_vat_number' => 'DEMO-VAT-001',
            'invoice_vendor' => 'ShortLink Pro Demo LLC',
            'legal_cookie_url' => '/page/cookie-policy',
            'legal_privacy_url' => '/page/privacy-policy',
            'legal_terms_url' => '/page/terms-of-service',
            'registration_registration' => '1',
            'registration_verification' => '0',
            'short_bad_words' => 'admin,login,root',
            'short_guest' => '1',
            'social_facebook' => 'https://facebook.com/shortlink-pro-demo',
            'social_instagram' => 'https://instagram.com/shortlink-pro-demo',
            'social_twitter' => 'https://x.com/shortlink-pro-demo',
            'social_youtube' => 'https://youtube.com/@shortlink-pro-demo',
            'stripe' => '0',
            'stripe_key' => 'pk_test_demo',
            'stripe_secret' => 'sk_test_demo',
            'stripe_wh_secret' => 'whsec_test_demo',
            'tagline' => 'Demo URL shortening workspace with seeded data.',
            'timezone' => 'UTC',
            'title' => 'ShortLink Pro',
        ];

        foreach ($settings as $name => $value) {
            DB::table('settings')->updateOrInsert(['name' => $name], ['value' => $value]);
        }
    }

    /**
     * Resolve the legacy email encryption setting from current environment values.
     */
    private function mailEncryption(): string
    {
        $encryption = (string) env('MAIL_ENCRYPTION', '');

        if ($encryption !== '') {
            return $encryption;
        }

        return env('MAIL_SCHEME') === 'smtps' ? 'ssl' : '';
    }

    /**
     * Create demo users, including an archived account for admin filters.
     *
     * @return array<string, User>
     */
    private function seedUsers(): array
    {
        $now = Carbon::now();
        $password = Hash::make(self::PASSWORD);
        $rows = [
            'owner' => [
                'name' => 'Demo Owner',
                'email' => 'demo.owner@example.test',
                'role' => 0,
                'locale' => 'en',
                'timezone' => 'UTC',
                'api_token' => 'test-owner-api-token',
                'stripe_id' => 'cus_test_owner',
                'card_brand' => 'visa',
                'card_last_four' => '4242',
                'trial_ends_at' => $now->copy()->addDays(14),
                'deleted_at' => null,
            ],
            'marketer' => [
                'name' => 'Demo Marketer',
                'email' => 'demo.marketer@example.test',
                'role' => 0,
                'locale' => 'es',
                'timezone' => 'Europe/Madrid',
                'api_token' => 'test-marketer-api-token',
                'stripe_id' => 'cus_test_marketer',
                'card_brand' => 'mastercard',
                'card_last_four' => '4444',
                'trial_ends_at' => null,
                'deleted_at' => null,
            ],
            'manager' => [
                'name' => 'Demo Manager',
                'email' => 'demo.manager@example.test',
                'role' => 1,
                'locale' => 'en',
                'timezone' => 'America/New_York',
                'api_token' => 'test-manager-api-token',
                'stripe_id' => 'cus_test_manager',
                'card_brand' => 'amex',
                'card_last_four' => '0005',
                'trial_ends_at' => null,
                'deleted_at' => null,
            ],
            'archived' => [
                'name' => 'Demo Archived',
                'email' => 'demo.archived@example.test',
                'role' => 0,
                'locale' => 'de',
                'timezone' => 'Europe/Berlin',
                'api_token' => 'test-archived-api-token',
                'stripe_id' => 'cus_test_archived',
                'card_brand' => null,
                'card_last_four' => null,
                'trial_ends_at' => null,
                'deleted_at' => $now->copy()->subDays(7),
            ],
        ];

        $users = [];
        foreach ($rows as $key => $data) {
            $user = User::withTrashed()->updateOrCreate(
                ['email' => $data['email']],
                array_merge($data, [
                    'email_verified_at' => $now,
                    'password' => $password,
                ])
            );

            $users[$key] = $user->fresh(['subscriptions']) ?? $user;
        }

        return $users;
    }

    /**
     * Create visible, hidden, and archived plans for pricing and admin screens.
     *
     * @return array<string, Plan>
     */
    private function seedPlans(): array
    {
        $rows = [
            'default' => [
                'product' => 'prod_test_default',
                'name' => 'Default',
                'description' => 'Free demo plan with unlimited local testing.',
                'trial_days' => null,
                'currency' => 'USD',
                'decimals' => 2,
                'plan_month' => 'price_test_default_month',
                'plan_year' => 'price_test_default_year',
                'amount_month' => 0,
                'amount_year' => 0,
                'visibility' => 1,
                'color' => '#0d6efd',
                'option_api' => 1,
                'option_links' => -1,
                'option_workspaces' => -1,
                'option_domains' => -1,
                'option_stats' => 1,
                'option_geo' => 1,
                'option_platform' => 1,
                'option_expiration' => 1,
                'option_password' => 1,
                'option_disabled' => 1,
                'option_utm' => 1,
                'deleted_at' => null,
            ],
            'starter' => [
                'product' => 'prod_test_starter',
                'name' => 'Starter Test',
                'description' => 'Entry-level plan for seeded customer accounts.',
                'trial_days' => 7,
                'currency' => 'USD',
                'decimals' => 2,
                'plan_month' => 'price_test_starter_month',
                'plan_year' => 'price_test_starter_year',
                'amount_month' => 900,
                'amount_year' => 9000,
                'visibility' => 1,
                'color' => '#20c997',
                'option_api' => 1,
                'option_links' => 100,
                'option_workspaces' => 10,
                'option_domains' => 3,
                'option_stats' => 1,
                'option_geo' => 1,
                'option_platform' => 1,
                'option_expiration' => 1,
                'option_password' => 1,
                'option_disabled' => 1,
                'option_utm' => 1,
                'deleted_at' => null,
            ],
            'growth' => [
                'product' => 'prod_test_growth',
                'name' => 'Growth Test',
                'description' => 'Team plan with larger seeded limits.',
                'trial_days' => 14,
                'currency' => 'USD',
                'decimals' => 2,
                'plan_month' => 'price_test_growth_month',
                'plan_year' => 'price_test_growth_year',
                'amount_month' => 2900,
                'amount_year' => 29000,
                'visibility' => 1,
                'color' => '#6f42c1',
                'option_api' => 1,
                'option_links' => 1000,
                'option_workspaces' => 50,
                'option_domains' => 20,
                'option_stats' => 1,
                'option_geo' => 1,
                'option_platform' => 1,
                'option_expiration' => 1,
                'option_password' => 1,
                'option_disabled' => 1,
                'option_utm' => 1,
                'deleted_at' => null,
            ],
            'archived' => [
                'product' => 'prod_test_archived',
                'name' => 'Archived Test',
                'description' => 'Soft-deleted plan used to verify admin status filters.',
                'trial_days' => null,
                'currency' => 'USD',
                'decimals' => 2,
                'plan_month' => 'price_test_archived_month',
                'plan_year' => 'price_test_archived_year',
                'amount_month' => 4900,
                'amount_year' => 49000,
                'visibility' => 0,
                'color' => '#6c757d',
                'option_api' => 0,
                'option_links' => 25,
                'option_workspaces' => 2,
                'option_domains' => 1,
                'option_stats' => 0,
                'option_geo' => 0,
                'option_platform' => 0,
                'option_expiration' => 0,
                'option_password' => 0,
                'option_disabled' => 1,
                'option_utm' => 0,
                'deleted_at' => Carbon::now()->subDays(30),
            ],
        ];

        $plans = [];
        foreach ($rows as $key => $data) {
            $plans[$key] = Plan::withTrashed()->updateOrCreate(['name' => $data['name']], $data);
        }

        return $plans;
    }

    /**
     * Create public content pages used by legal and footer settings.
     */
    private function seedPages(): void
    {
        foreach ([
            [
                'slug' => 'about-demo',
                'title' => 'About the Demo',
                'footer' => 1,
                'content' => '<p>This seeded page demonstrates public CMS content.</p>',
            ],
            [
                'slug' => 'terms-of-service',
                'title' => 'Terms of Service',
                'footer' => 1,
                'content' => '<p>Demo terms for local development and QA workflows.</p>',
            ],
            [
                'slug' => 'privacy-policy',
                'title' => 'Privacy Policy',
                'footer' => 1,
                'content' => '<p>Demo privacy policy content for test data.</p>',
            ],
            [
                'slug' => 'cookie-policy',
                'title' => 'Cookie Policy',
                'footer' => 0,
                'content' => '<p>Demo cookie policy for legal settings.</p>',
            ],
        ] as $page) {
            Page::query()->updateOrCreate(['slug' => $page['slug']], $page);
        }
    }

    /**
     * Create workspaces for each active demo user.
     *
     * @param array<string, User> $users
     * @return array<string, Workspace>
     */
    private function seedWorkspaces(array $users): array
    {
        $rows = [
            'owner-product' => ['user' => 'owner', 'name' => 'Product Campaigns', 'color' => 1],
            'owner-social' => ['user' => 'owner', 'name' => 'Social Media', 'color' => 4],
            'marketer-sales' => ['user' => 'marketer', 'name' => 'Sales Funnels', 'color' => 2],
            'manager-ops' => ['user' => 'manager', 'name' => 'Operations', 'color' => 5],
        ];

        $workspaces = [];
        foreach ($rows as $key => $data) {
            $workspaces[$key] = Workspace::query()->updateOrCreate(
                ['user_id' => $users[$data['user']]->id, 'name' => $data['name']],
                ['color' => $data['color']]
            );
        }

        return $workspaces;
    }

    /**
     * Create test domains owned by demo users.
     *
     * @param array<string, User> $users
     * @return array<string, Domain>
     */
    private function seedDomains(array $users): array
    {
        $rows = [
            'owner-main' => [
                'user' => 'owner',
                'name' => 'go-demo.example.test',
                'index_page' => 'https://example.com/demo',
                'not_found_page' => 'https://example.com/404',
            ],
            'owner-events' => [
                'user' => 'owner',
                'name' => 'events-demo.example.test',
                'index_page' => 'https://example.org/events',
                'not_found_page' => 'https://example.org/not-found',
            ],
            'marketer-main' => [
                'user' => 'marketer',
                'name' => 'mkt-demo.example.test',
                'index_page' => 'https://example.net',
                'not_found_page' => 'https://example.net/404',
            ],
        ];

        $domains = [];
        foreach ($rows as $key => $data) {
            $domains[$key] = Domain::query()->updateOrCreate(
                ['name' => $data['name']],
                [
                    'user_id' => $users[$data['user']]->id,
                    'index_page' => $data['index_page'],
                    'not_found_page' => $data['not_found_page'],
                ]
            );
        }

        return $domains;
    }

    /**
     * Create links with normal, protected, disabled, and expired states.
     *
     * @param array<string, User> $users
     * @param array<string, Workspace> $workspaces
     * @param array<string, Domain> $domains
     * @return array<string, Link>
     */
    private function seedLinks(array $users, array $workspaces, array $domains): array
    {
        $rows = [
            'launch' => [
                'user' => 'owner',
                'alias' => 'demo-launch',
                'url' => 'https://example.com/product-launch?utm_source=seed',
                'title' => 'Product Launch',
                'workspace' => 'owner-product',
                'domain' => 'owner-main',
                'public' => 1,
                'disabled' => 0,
                'password' => null,
                'expiration_url' => null,
                'ends_at' => null,
                'geo_target' => [
                    ['key' => 'US', 'value' => 'https://example.com/us-launch'],
                    ['key' => 'DE', 'value' => 'https://example.com/de-launch'],
                ],
                'platform_target' => [
                    ['key' => 'iOS', 'value' => 'https://example.com/app-store'],
                    ['key' => 'Android', 'value' => 'https://example.com/play-store'],
                ],
            ],
            'pricing' => [
                'user' => 'owner',
                'alias' => 'demo-pricing',
                'url' => 'https://example.com/pricing',
                'title' => 'Pricing Page',
                'workspace' => 'owner-product',
                'domain' => 'owner-main',
                'public' => 1,
                'disabled' => 0,
                'password' => null,
                'expiration_url' => null,
                'ends_at' => Carbon::now()->addDays(45),
                'geo_target' => null,
                'platform_target' => null,
            ],
            'webinar' => [
                'user' => 'owner',
                'alias' => 'demo-webinar',
                'url' => 'https://example.org/webinar',
                'title' => 'Private Webinar',
                'workspace' => 'owner-social',
                'domain' => 'owner-events',
                'public' => 0,
                'disabled' => 0,
                'password' => Hash::make('secret'),
                'expiration_url' => 'https://example.org/webinar-ended',
                'ends_at' => Carbon::now()->addDays(10),
                'geo_target' => null,
                'platform_target' => null,
            ],
            'expired' => [
                'user' => 'marketer',
                'alias' => 'demo-expired',
                'url' => 'https://example.net/old-offer',
                'title' => 'Expired Offer',
                'workspace' => 'marketer-sales',
                'domain' => 'marketer-main',
                'public' => 0,
                'disabled' => 0,
                'password' => null,
                'expiration_url' => 'https://example.net/new-offer',
                'ends_at' => Carbon::now()->subDays(3),
                'geo_target' => null,
                'platform_target' => null,
            ],
            'disabled' => [
                'user' => 'manager',
                'alias' => 'demo-disabled',
                'url' => 'https://example.com/internal',
                'title' => 'Disabled Internal Link',
                'workspace' => 'manager-ops',
                'domain' => null,
                'public' => 0,
                'disabled' => 1,
                'password' => null,
                'expiration_url' => null,
                'ends_at' => null,
                'geo_target' => null,
                'platform_target' => null,
            ],
            'guest' => [
                'user' => 'owner',
                'alias' => 'demo-public',
                'url' => 'https://example.com/public-report',
                'title' => 'Public Report',
                'workspace' => null,
                'domain' => null,
                'public' => 1,
                'disabled' => 0,
                'password' => null,
                'expiration_url' => null,
                'ends_at' => null,
                'geo_target' => null,
                'platform_target' => null,
            ],
        ];

        $links = [];
        foreach ($rows as $key => $data) {
            $links[$key] = Link::query()->updateOrCreate(
                [
                    'alias' => $data['alias'],
                    'domain_id' => $data['domain'] ? $domains[$data['domain']]->id : null,
                ],
                [
                    'user_id' => $users[$data['user']]->id,
                    'url' => $data['url'],
                    'title' => $data['title'],
                    'workspace_id' => $data['workspace'] ? $workspaces[$data['workspace']]->id : null,
                    'password' => $data['password'],
                    'disabled' => $data['disabled'],
                    'public' => $data['public'],
                    'expiration_url' => $data['expiration_url'],
                    'ends_at' => $data['ends_at'],
                    'geo_target' => $data['geo_target'],
                    'platform_target' => $data['platform_target'],
                ]
            );
        }

        return $links;
    }

    /**
     * Create click statistics and synchronize cached link click counters.
     *
     * @param array<string, Link> $links
     */
    private function seedStats(array $links): void
    {
        $profiles = [
            ['referrer' => 'https://google.com', 'platform' => 'Windows', 'browser' => 'Chrome', 'device' => 'desktop', 'country' => 'US', 'language' => 'en'],
            ['referrer' => 'https://bing.com', 'platform' => 'macOS', 'browser' => 'Safari', 'device' => 'desktop', 'country' => 'DE', 'language' => 'de'],
            ['referrer' => 'https://facebook.com', 'platform' => 'iOS', 'browser' => 'Safari', 'device' => 'mobile', 'country' => 'ES', 'language' => 'es'],
            ['referrer' => 'https://linkedin.com', 'platform' => 'Android', 'browser' => 'Chrome', 'device' => 'mobile', 'country' => 'FR', 'language' => 'fr'],
            ['referrer' => null, 'platform' => 'Linux', 'browser' => 'Firefox', 'device' => 'desktop', 'country' => 'GB', 'language' => 'en'],
        ];
        $volumes = ['launch' => 18, 'pricing' => 10, 'webinar' => 7, 'expired' => 4, 'disabled' => 1, 'guest' => 12];

        foreach ($volumes as $key => $count) {
            $link = $links[$key]->fresh();
            Stat::query()->where('link_id', $link->id)->delete();

            for ($index = 0; $index < $count; $index++) {
                $profile = $profiles[$index % count($profiles)];
                Stat::query()->create(array_merge($profile, [
                    'link_id' => $link->id,
                    'user_id' => $link->user_id,
                    'created_at' => Carbon::now()->subHours($index * 3)->subDays($index % 8),
                ]));
            }

            $link->forceFill(['clicks' => $count])->save();
        }
    }

    /**
     * Create subscriptions and matching Cashier subscription items.
     *
     * @param array<string, User> $users
     * @param array<string, Plan> $plans
     */
    private function seedSubscriptions(array $users, array $plans): void
    {
        $rows = [
            [
                'user' => 'owner',
                'plan' => 'growth',
                'name' => $plans['growth']->name,
                'stripe_id' => 'sub_test_owner_growth',
                'stripe_status' => 'active',
                'stripe_plan' => $plans['growth']->plan_month,
                'quantity' => 1,
                'trial_ends_at' => null,
                'ends_at' => null,
            ],
            [
                'user' => 'marketer',
                'plan' => 'starter',
                'name' => $plans['starter']->name,
                'stripe_id' => 'sub_test_marketer_starter',
                'stripe_status' => 'trialing',
                'stripe_plan' => $plans['starter']->plan_month,
                'quantity' => 1,
                'trial_ends_at' => Carbon::now()->addDays(7),
                'ends_at' => null,
            ],
            [
                'user' => 'manager',
                'plan' => 'default',
                'name' => $plans['default']->name,
                'stripe_id' => 'sub_test_manager_default',
                'stripe_status' => 'emulated',
                'stripe_plan' => $plans['default']->plan_month,
                'quantity' => 1,
                'trial_ends_at' => null,
                'ends_at' => null,
            ],
            [
                'user' => 'archived',
                'plan' => 'archived',
                'name' => $plans['archived']->name,
                'stripe_id' => 'sub_test_archived_canceled',
                'stripe_status' => 'canceled',
                'stripe_plan' => $plans['archived']->plan_month,
                'quantity' => 1,
                'trial_ends_at' => null,
                'ends_at' => Carbon::now()->subDays(5),
            ],
        ];

        foreach ($rows as $row) {
            $subscription = Subscription::query()->updateOrCreate(
                ['stripe_id' => $row['stripe_id']],
                [
                    'user_id' => $users[$row['user']]->id,
                    'name' => $row['name'],
                    'stripe_status' => $row['stripe_status'],
                    'stripe_plan' => $row['stripe_plan'],
                    'quantity' => $row['quantity'],
                    'trial_ends_at' => $row['trial_ends_at'],
                    'ends_at' => $row['ends_at'],
                ]
            );

            DB::table('subscription_items')->updateOrInsert(
                [
                    'subscription_id' => $subscription->id,
                    'stripe_plan' => $row['stripe_plan'],
                ],
                [
                    'stripe_id' => 'si_' . $row['stripe_id'],
                    'quantity' => $row['quantity'],
                    'created_at' => Carbon::now(),
                    'updated_at' => Carbon::now(),
                ]
            );
        }
    }
}
