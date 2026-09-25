<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Vigilance\Vigilance;

class VigilanceServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Vigilance::auth(function ($request) {
            return app()->environment('local') || (
                $request->user() && (
                    $request->user()->email === 'cadastre@moncefdev.me'
                    || $request->user()->hasRole('super_admin')
                )
            );
        });
    }
}
