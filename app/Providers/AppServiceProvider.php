<?php

namespace App\Providers;

use App\Models\Domain;
use App\Models\Link;
use App\Observers\DomainObserver;
use App\Observers\LinkObserver;
use App\Observers\WorkspaceObserver;
use App\Observers\UserObserver;
use App\Models\Workspace;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Database\Schema\Builder as SchemaBuilder;
use Illuminate\Support\Facades\Event;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register(): void
    {
        // Fix for utf8mb migration @https://laravel.com/docs/master/migrations#creating-indexes
        SchemaBuilder::defaultStringLength(191);
    }

    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot(): void
    {
        Event::listen(Registered::class, SendEmailVerificationNotification::class);

        Workspace::observe(WorkspaceObserver::class);
        Link::observe(LinkObserver::class);
        Domain::observe(DomainObserver::class);
        User::observe(UserObserver::class);
    }
}
