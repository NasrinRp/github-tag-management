<?php

namespace App\Providers;

use App\Services\GitHubService;
use App\Services\tagService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(GitHubService::class, function ($app) {
            return new GitHubService();
        });

        $this->app->singleton(TagService::class, function ($app) {
            return new TagService();
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        //
    }
}
