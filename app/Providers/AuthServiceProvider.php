<?php

namespace App\Providers;

use App\Models\Domain;
use App\Models\Link;
use App\Models\User;
use App\Models\Workspace;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * Models whose creation is limited by subscription features.
     */
    private const LIMITED_MODELS = [
        Domain::class,
        Link::class,
        Workspace::class,
    ];

    /**
     * Link feature abilities checked by validation rules and Blade views.
     */
    private const FEATURE_ABILITIES = [
        'api',
        'disabled',
        'domains',
        'expiration',
        'geo',
        'password',
        'platform',
        'stats',
        'utm',
        'workspaces',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(): void
    {
        Gate::guessPolicyNamesUsing(fn (string $modelClass): array => []);

        $this->registerLimitGates();
        $this->registerFeatureGates();
    }

    /**
     * Register subscription limit checks for model creation.
     */
    private function registerLimitGates(): void
    {
        Gate::define('create', function (User $user, mixed $modelClass = null, mixed $limit = null): bool {
            if (!is_string($modelClass) || !in_array($modelClass, self::LIMITED_MODELS, true)) {
                return false;
            }

            if ((int) $limit === -1) {
                return true;
            }

            if ((int) $limit <= 0) {
                return false;
            }

            return $modelClass::query()
                ->where('user_id', $user->id)
                ->count() < (int) $limit;
        });
    }

    /**
     * Register simple on/off checks for link features.
     */
    private function registerFeatureGates(): void
    {
        foreach (self::FEATURE_ABILITIES as $ability) {
            Gate::define($ability, fn (User $user, mixed $modelClass = null, mixed $limit = null): bool => (bool) $limit);
        }
    }
}
