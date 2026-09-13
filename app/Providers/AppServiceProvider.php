<?php

namespace App\Providers;

use App\Models\User;
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
        // nonaktif: tolak semua ability (lapis kedua selain canAccessPanel)
        Gate::before(function ($user, string $ability): ?bool {
            if ($user instanceof User && ! (bool) $user->is_active) {
                return false;
            }

            return null;
        });
    }
}
