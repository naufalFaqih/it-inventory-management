<?php

namespace App\Providers;

use App\Models\User;
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
        Paginator::useTailwind();

        Gate::define('admin-only', fn (User $user) => $user->isAdmin());
        Gate::define('manage-users', fn (User $user) => $user->isAdmin());
        Gate::define('view-audit-logs', fn (User $user) => $user->isAdmin());

        Gate::define('manage-inventory', fn (User $user) => $user->isAuthorizedStaff());
        Gate::define('manage-master-data', fn (User $user) => $user->isAdmin());
        Gate::define('manage-assets', fn (User $user) => $user->isAuthorizedStaff());
        Gate::define('manage-loans', fn (User $user) => $user->isAuthorizedStaff());
        Gate::define('manage-maintenance', fn (User $user) => $user->isAuthorizedStaff());
        Gate::define('view-reports', fn (User $user) => $user->isAuthorizedStaff());
    }
}
