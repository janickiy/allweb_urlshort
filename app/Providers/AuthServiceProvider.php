<?php

namespace App\Providers;

use App\Models\Domain;
use App\Models\Link;
use App\Models\Workspace;
use App\Policies\DomainPolicy;
use App\Policies\LinkPolicy;
use App\Policies\WorkspacePolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Domain::class => DomainPolicy::class,
        Link::class => LinkPolicy::class,
        Workspace::class => WorkspacePolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot(): void
    {
        $this->registerPolicies();

        //
    }
}
