<?php

namespace Tests\Feature;

use App\Models\Domain;
use App\Models\Link;
use App\Models\Page;
use App\Models\Plan;
use App\Models\Workspace;
use App\Models\Stat;
use App\Models\Subscription;
use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

trait ControllerTestHelpers
{
    private function user(array $attributes = []): User
    {
        return User::factory()->create(array_merge([
            'name' => 'Test User',
            'email' => 'user-'.Str::random(8).'@example.test',
            'email_verified_at' => now(),
            'password' => Hash::make('password'),
            'timezone' => 'UTC',
            'role' => 0,
            'api_token' => null,
            'stripe_id' => 'cus_test_'.Str::random(8),
        ], $attributes));
    }

    private function admin(array $attributes = []): User
    {
        return $this->user(array_merge(['role' => 1], $attributes));
    }

    private function workspace(User $user, array $attributes = []): Workspace
    {
        return Workspace::forceCreate(array_merge([
            'user_id' => $user->id,
            'name' => 'Workspace '.Str::random(6),
            'color' => 1,
            'created_at' => now(),
            'updated_at' => now(),
        ], $attributes));
    }

    private function domain(User $user, array $attributes = []): Domain
    {
        return Domain::forceCreate(array_merge([
            'user_id' => $user->id,
            'name' => 'http://example-'.Str::random(8).'.test',
            'index_page' => null,
            'not_found_page' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ], $attributes));
    }

    private function link(?User $user = null, array $attributes = []): Link
    {
        return Link::forceCreate(array_merge([
            'user_id' => $user?->id,
            'alias' => 'alias-'.Str::random(8),
            'url' => 'https://example.com',
            'title' => 'Example',
            'geo_target' => null,
            'platform_target' => null,
            'password' => null,
            'disabled' => 0,
            'public' => 0,
            'expiration_url' => null,
            'clicks' => 1,
            'workspace_id' => null,
            'domain_id' => null,
            'ends_at' => null,
            'created_at' => now(),
            'updated_at' => now(),
        ], $attributes));
    }

    private function page(array $attributes = []): Page
    {
        return Page::forceCreate(array_merge([
            'title' => 'Page '.Str::random(6),
            'slug' => 'page-'.Str::random(6),
            'footer' => 1,
            'content' => '<p>Page content</p>',
            'created_at' => now(),
            'updated_at' => now(),
        ], $attributes));
    }

    private function paidPlan(array $attributes = []): Plan
    {
        return Plan::forceCreate(array_merge([
            'product' => 'prod_test',
            'name' => 'Pro '.Str::random(6),
            'description' => 'Paid plan',
            'trial_days' => 0,
            'currency' => 'USD',
            'decimals' => 2,
            'plan_month' => 'price_month_'.Str::random(6),
            'plan_year' => 'price_year_'.Str::random(6),
            'amount_month' => 1000,
            'amount_year' => 10000,
            'visibility' => 1,
            'color' => '#ef698b',
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
        ], $attributes));
    }

    private function subscription(User $user, Plan $plan, array $attributes = []): Subscription
    {
        return Subscription::forceCreate(array_merge([
            'user_id' => $user->id,
            'name' => $plan->name,
            'stripe_id' => '',
            'stripe_status' => 'emulated',
            'stripe_plan' => $plan->plan_month,
            'quantity' => 1,
            'trial_ends_at' => Carbon::now()->addDays(10),
            'ends_at' => Carbon::now()->addDays(10),
            'created_at' => now(),
            'updated_at' => now(),
        ], $attributes));
    }

    private function stat(Link $link, array $attributes = []): Stat
    {
        return Stat::forceCreate(array_merge([
            'link_id' => $link->id,
            'user_id' => $link->user_id,
            'referrer' => 'l.facebook.com',
            'platform' => 'Windows',
            'browser' => 'Chrome',
            'device' => 'desktop',
            'country' => 'US',
            'language' => 'en',
            'created_at' => now(),
        ], $attributes));
    }

    private function planPayload(array $overrides = []): array
    {
        return array_merge([
            'name' => 'Plan '.Str::random(6),
            'description' => 'Plan description',
            'trial_days' => 0,
            'currency' => 'USD',
            'amount_month' => 1000,
            'amount_year' => 10000,
            'visibility' => 1,
            'color' => '#ef698b',
            'option_links' => -1,
            'option_workspaces' => -1,
            'option_domains' => -1,
            'option_password' => 1,
            'option_expiration' => 1,
            'option_stats' => 1,
            'option_geo' => 1,
            'option_platform' => 1,
            'option_disabled' => 1,
            'option_api' => 1,
            'option_utm' => 1,
        ], $overrides);
    }

    private function paymentMethod(string $id = 'pm_test'): object
    {
        return (object) [
            'id' => $id,
            'card' => (object) [
                'brand' => 'visa',
                'last4' => '4242',
                'exp_month' => 12,
                'exp_year' => 2030,
            ],
        ];
    }

    private function customer(): object
    {
        return (object) [
            'name' => 'Test Customer',
            'phone' => '+10000000000',
            'address' => (object) [
                'line1' => 'Main Street',
                'city' => 'New York',
                'state' => 'NY',
                'postal_code' => '10001',
                'country' => 'US',
            ],
        ];
    }

    private function setupIntent(): object
    {
        return (object) ['client_secret' => 'seti_test_secret'];
    }
}
