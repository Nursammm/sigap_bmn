<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;
use App\Models\User;

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
        Blade::if('admin', function () {
        $u = auth()->user();
        return $u instanceof User && $u->role === User::ROLE_ADMIN;
    });

    Blade::if('pengelola', function () {
        $u = auth()->user();
        return $u instanceof User && $u->role === User::ROLE_PENGELOLA;
    });
    }
}
