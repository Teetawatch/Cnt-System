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
        // Override public path for shared hosting subfolder deployment
        
        // Correct Public Path on Server:
        // /home/nassacth/domains/nass.ac.th/public_html/workcnt
        
        $productionPublicPath = '/home/nassacth/domains/nass.ac.th/public_html/workcnt';
        
        if (is_dir($productionPublicPath)) {
            $this->app->bind('path.public', function () use ($productionPublicPath) {
                return $productionPublicPath;
            });
        }
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}


