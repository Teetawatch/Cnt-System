<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Override public path for shared hosting where document root (public_html/)
        // is different from Laravel's default public/ folder.
        //
        // Priority:
        //   1. PUBLIC_PATH env var (set in .env for explicit control)
        //   2. $_SERVER['DOCUMENT_ROOT'] (auto-detected on shared hosting)
        //   3. Default Laravel public/ (local development)

        $customPath = env('PUBLIC_PATH');

        if (!$customPath && !empty($_SERVER['DOCUMENT_ROOT'])) {
            $docRoot = rtrim($_SERVER['DOCUMENT_ROOT'], '/');
            // Only override if DOCUMENT_ROOT differs from default public/
            if ($docRoot !== $this->app->basePath('public')) {
                $customPath = $docRoot;
            }
        }

        if ($customPath && is_dir($customPath)) {
            $this->app->bind('path.public', fn() => $customPath);
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Carbon\Carbon::setLocale('th');
    }
}


