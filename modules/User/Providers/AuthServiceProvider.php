<?php

namespace Modules\User\Providers;

// use Modules\Document\Models\Document;
// use Modules\Document\Policies\DocumentPolicy;

use Illuminate\Auth\Notifications\ResetPassword;

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
        //Document::class => DocumentPolicy::class
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        // /**
        //  * Customize the 
        //  */
        // ResetPassword::createUrlUsing(function ($user, string $token) {
        //     return 'https://example.com/reset-password?token='.$token;
        // });
    }
}
