<?php

namespace App\Providers;

use App\Contracts\PlantServiceInterface;
use App\Contracts\WateringCalculatorServiceInterface;
use App\Contracts\WeatherServiceInterface;
use App\Services\PerenualPlantService;
use App\Services\WateringCalculatorService;
use App\Services\WeatherapiService;
use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(PlantServiceInterface::class, PerenualPlantService::class);
        $this->app->bind(WeatherServiceInterface::class, WeatherapiService::class);
        $this->app->bind(WateringCalculatorServiceInterface::class, WateringCalculatorService::class);
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);
    }
}
