<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;
use Illuminate\Support\Carbon;
class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        // 'App\Models\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        if (! $this->app->routesAreCached()) {
            Passport::routes();

            Passport::personalAccessTokensExpireIn(Carbon::now()->addDays(7));
            Passport::refreshTokensExpireIn(Carbon::now()->addDays(31));

            // Mandatory to define Scope
            Passport::tokensCan([
                'banned'=> 'Cannot login',
                'admin' => 'Add/Edit/Delete Users',
                'user' => 'Add/Edit Users',
                'guest' => 'List Users'
            ]);

            Passport::setDefaultScope([
                'guest'
            ]);
        }
    }
}
