<?php

namespace App\Providers;

use App\Interface\CartInterface;
use App\Interface\Repositories\CartRepositoryInterface;
use App\Repositories\CartRepository;
use App\Services\CartService;
use Illuminate\Support\Facades\Vite;
use Illuminate\Support\ServiceProvider;

use Barryvdh\Debugbar\ServiceProvider as DebugbarServiceProvider;
use Laravel\Telescope\TelescopeServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        if ($this->app->isLocal()) {
            $this->app->register(DebugbarServiceProvider::class);
            $this->app->register(TelescopeServiceProvider::class);
        }

        $this->app->singleton(CartInterface::class, function ($app) {
            return new CartService($app->make(CartRepositoryInterface::class));
        });

        $this->app->bind(CartRepositoryInterface::class, CartRepository::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Vite::prefetch(concurrency: 3);
    }
}
