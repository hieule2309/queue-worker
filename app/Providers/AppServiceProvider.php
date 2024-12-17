<?php

namespace App\Providers;

use App\Services\ImageService;
use App\Services\ProductService;
use App\Repositories\Product\ProductRepositoryInterface;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(ImageService::class, function ($app) {
            return new ImageService();
        });

        $this->app->singleton(ProductService::class, function ($app) {
            return new ProductService(
                $app->make(ProductRepositoryInterface::class),
                $app->make(ImageService::class)
            );
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
