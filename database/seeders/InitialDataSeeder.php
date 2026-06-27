<?php

namespace Database\Seeders;

use App\Models\Page;
use App\Models\Plan;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class InitialDataSeeder extends Seeder
{
    /**
     * Seed the minimum data required by a clean installation.
     */
    public function run(): void
    {
        $this->seedSettings();
        $this->seedLanguages();
        $this->seedDefaultPlan();
        $this->seedPages();
    }

    /**
     * Seed application settings used by middleware and layouts.
     */
    private function seedSettings(): void
    {
        $settings = [
            'captcha_contact' => '0',
            'captcha_registration' => '0',
            'captcha_secret_key' => '',
            'captcha_shorten' => '0',
            'captcha_site_key' => '',
            'contact_email' => env('MAIL_FROM_ADDRESS', 'support@example.test'),
            'email_address' => env('MAIL_FROM_ADDRESS', 'noreply@example.test'),
            'email_driver' => env('MAIL_MAILER', env('MAIL_DRIVER', 'log')),
            'email_encryption' => $this->mailEncryption(),
            'email_host' => env('MAIL_HOST', 'mailpit'),
            'email_password' => env('MAIL_PASSWORD', ''),
            'email_port' => env('MAIL_PORT', '1025'),
            'email_username' => env('MAIL_USERNAME', ''),
            'index' => '',
            'invoice_address' => '',
            'invoice_city' => '',
            'invoice_country' => '',
            'invoice_phone' => '',
            'invoice_postal_code' => '',
            'invoice_state' => '',
            'invoice_vat_number' => '',
            'invoice_vendor' => 'ShortLink Pro',
            'legal_cookie_url' => '/page/cookie-policy',
            'legal_privacy_url' => '/page/privacy-policy',
            'legal_terms_url' => '/page/terms-of-service',
            'license_key' => null,
            'license_type' => null,
            'registration_registration' => '1',
            'registration_verification' => '0',
            'short_bad_words' => 'admin,login,root',
            'short_guest' => '1',
            'social_facebook' => '',
            'social_instagram' => '',
            'social_twitter' => '',
            'social_youtube' => '',
            'stripe' => '0',
            'stripe_key' => '',
            'stripe_secret' => '',
            'stripe_wh_secret' => '',
            'tagline' => 'Create, organize, and track short links.',
            'theme' => '0',
            'timezone' => 'UTC',
            'title' => 'ShortLink Pro',
            'tracking_code' => '',
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
     * Seed the default language row retained by the database schema.
     */
    private function seedLanguages(): void
    {
        DB::table('languages')->updateOrInsert(
            ['code' => 'en'],
            ['name' => 'English', 'dir' => 'ltr', 'default' => 1],
        );
    }

    /**
     * Seed the free plan used for default feature limits.
     */
    private function seedDefaultPlan(): void
    {
        Plan::withTrashed()->updateOrCreate(
            ['name' => 'Default'],
            [
                'product' => 'default',
                'description' => 'Free plan available after installation.',
                'trial_days' => null,
                'currency' => 'USD',
                'decimals' => 2,
                'plan_month' => null,
                'plan_year' => null,
                'amount_month' => 0,
                'amount_year' => 0,
                'visibility' => 1,
                'color' => '#0ea5e9',
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
        );
    }

    /**
     * Seed public pages referenced by legal settings.
     */
    private function seedPages(): void
    {
        foreach ([
            [
                'slug' => 'terms-of-service',
                'title' => 'Terms of Service',
                'footer' => 1,
                'content' => '<p>Update these terms from the admin panel.</p>',
            ],
            [
                'slug' => 'privacy-policy',
                'title' => 'Privacy Policy',
                'footer' => 1,
                'content' => '<p>Update this privacy policy from the admin panel.</p>',
            ],
            [
                'slug' => 'cookie-policy',
                'title' => 'Cookie Policy',
                'footer' => 0,
                'content' => '<p>Update this cookie policy from the admin panel.</p>',
            ],
            [
                'slug' => 'about',
                'title' => 'About',
                'footer' => 1,
                'content' => '<p>ShortLink Pro helps teams manage branded short links.</p>',
            ],
        ] as $page) {
            Page::query()->updateOrCreate(['slug' => $page['slug']], $page);
        }
    }
}
