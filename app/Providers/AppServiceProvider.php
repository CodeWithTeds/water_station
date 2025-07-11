<?php

namespace App\Providers;

use App\Repositories\CustomerRepository;
use App\Repositories\OrderRepository;
use App\Services\CustomerService;
use App\Services\LoyaltyService;
use App\Services\OrderService;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        // Register repositories
        $this->app->singleton(OrderRepository::class, function ($app) {
            return new OrderRepository();
        });
        
        $this->app->singleton(CustomerRepository::class, function ($app) {
            return new CustomerRepository();
        });
        
        // Register services
        $this->app->singleton(LoyaltyService::class, function ($app) {
            return new LoyaltyService(
                $app->make(CustomerRepository::class)
            );
        });
        
        $this->app->singleton(OrderService::class, function ($app) {
            return new OrderService(
                $app->make(OrderRepository::class),
                $app->make(CustomerRepository::class),
                $app->make(LoyaltyService::class)
            );
        });
        
        $this->app->singleton(CustomerService::class, function ($app) {
            return new CustomerService(
                $app->make(CustomerRepository::class),
                $app->make(OrderRepository::class),
                $app->make(LoyaltyService::class)
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
