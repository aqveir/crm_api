<?php

namespace Modules\Core\Providers;

use Modules\Core\Models\Role\Role;
use Modules\Core\Models\Organization\Organization;

use Modules\Core\Policies\RolePolicy;
use Modules\Core\Policies\OrganizationPolicy;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        Role::class => RolePolicy::class,
        Organization::class => OrganizationPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();
    }
}