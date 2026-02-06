<?php

namespace App\Providers;

use App\Services\PostFetchService;
use Illuminate\Support\ServiceProvider;
use App\Contracts\PostFetchServiceInterface;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(
            PostFetchServiceInterface::class,
            PostFetchService::class
        );
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
