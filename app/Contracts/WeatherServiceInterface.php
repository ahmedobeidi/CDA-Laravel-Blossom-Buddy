<?php

namespace App\Contracts;

interface WeatherServiceInterface
{
    public function getForecast(string $city, int $days = 5): array;

    public function determineForecastDays(array $wateringBenchmark): int;
}
