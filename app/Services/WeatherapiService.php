<?php

namespace App\Services;

use App\Contracts\WeatherServiceInterface;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class WeatherapiService implements WeatherServiceInterface
{
    private string $apiKey;
    private string $baseUrl;
    private int $cacheDuration;
    private int $maxForecastDays;

    public function __construct()
    {
        $this->apiKey = config('services.weatherapi.key');
        $this->baseUrl = config('services.weatherapi.base_url');
        $this->cacheDuration = config('services.weatherapi.cache_duration');
        $this->maxForecastDays = config('services.weatherapi.max_forecast_days');
    }

    public function getForecast(string $city, int $days = 5): array
    {
        $days = min($days, $this->maxForecastDays);

        $cacheKey = "weather_forecast_{$city}_{$days}_days";

        $cachedData = Cache::get($cacheKey);

        if ($cachedData) {
            Log::info("Weather data retrieved from cache for {$city}");
            return $cachedData;
        }

        try {
            $response = Http::timeout(30)->get("{$this->baseUrl}/forecast.json", [
                'key' => $this->apiKey,
                'q' => $city,
                'days' => $days,
                'api' => 'no',
                'alerts' => 'no'
            ]);

            if (!$response->successful()) {
                Log::error("Error Weather API for {$city}, Status : {$response->status()}");
                return [
                    'error' => "Failed to fetch weather data for {$city}",
                    'status' => $response->status()
                ];
            }

            $weatherData = $response->json();

            if (!$weatherData || !isset($weatherData['forecast']['forecastday'])) {
                Log::warning("Empty or invalid data returned");
                return [
                    'error' => "Empty or invalid data returned"
                ];
            }

            return $weatherData;

        } catch (\Exception $e) {
            Log::error("Error retrieving weather for {$city}: {$e->getMessage()}");
            return [
                'error' => "Error retrieving weather for {$city}"
            ];
        }
    }

    public function determineForecastDays(array $wateringBenchmark): int
    {
        if (!isset($wateringBenchmark['value']) || !isset($wateringBenchmark['unit'])) {
            return $this->maxForecastDays; // Par défaut, 5 jours
        }

        $unit = strtolower($wateringBenchmark['unit']);
        $value = $wateringBenchmark['value'];

        // Si l'unité n'est pas en jours, retourner la valeur par défaut
        if ($unit !== 'days') {
            return $this->maxForecastDays;
        }

        // Nettoyer la valeur (enlever les guillemets et espaces)
        $cleanValue = trim($value, '"');
        
        // Gérer les cas comme "6-12", "7", etc.
        if (strpos($cleanValue, '-') !== false) {
            // Cas d'une plage comme "6-12"
            $range = explode('-', $cleanValue);
            $maxDays = intval(trim($range[1] ?? $range[0]));
        } else {
            // Cas d'une valeur unique comme "7"
            $maxDays = intval($cleanValue);
        }

        // Limiter au maximum autorisé par l'API (5 jours)
        return min($maxDays, $this->maxForecastDays);
    }
}
