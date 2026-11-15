<?php

namespace App\Providers;

use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Gate::define('isAdmin', function ($user) {
            return $user->role === 'admin';
        });

        Gate::define('isSupplier', function ($user) {
            return $user->role === 'supplier';
        });

        Gate::define('isCustomer', function ($user) {
            return $user->role === 'customer';
        });

        Paginator::useBootstrapFive();
    }
}
