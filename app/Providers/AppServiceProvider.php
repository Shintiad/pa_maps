<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Pagination\Paginator;
use Illuminate\Support\Facades\Storage;

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
        Paginator::useBootstrap();
        // Atau jika Anda menggunakan Tailwind:
        // Paginator::useTailwind();
        if (!Storage::disk('public')->exists('upload')) {
            Storage::disk('public')->makeDirectory('upload');
        }
    }
}
