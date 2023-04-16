<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Laravel\Passport\Passport;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array<class-string, class-string>
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

        /* Creating routes for passport. */
        Passport::routes();

        /* Creating a scope for the token. */
        Passport::tokensCan([
            'user' => 'User Type',
            'admin' => 'Admin User Type',
        ]);

        /* This is a gate that is checking if the user is an super-admin. If the user is an super-admin, then it
        will grant the user the ability to do anything. */
        if (Auth::check('admin')) {
            $admin = Auth::guard('admin')->user();
            // Implicitly grant "Super-Admin" role all permission checks using can()
            Gate::before(function ($admin, $ability) {
                return $admin->hasRole('super-admin') ? true : null;
            });
        }
    }
}
