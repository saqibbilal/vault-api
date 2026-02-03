<?php

namespace App\Providers;

use App\Contracts\FileStorageInterface;
use App\Services\LocalFileService;
use Illuminate\Support\ServiceProvider;
use App\Models\Document;
use App\Observers\DocumentObserver;


class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(FileStorageInterface::class, LocalFileService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Register the observer
        Document::observe(DocumentObserver::class);
    }
}
