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
                throw new \Exception("Failed to fetch weather data for {$city}");
            }

            $weatherData = $response->json();

            if (!$weatherData || !isset($weatherData['forecast']['forecastday'])) {
                Log::warning("Empty or invalid data returned");
                throw new \Exception("Empty or invalid data returned");
            }

            $processedData = [
                'city' => $city,
                'days' => $days,
                'forecast' => $weatherData['forecast']['forecastday'],
                'daily_humidity' => $this->calculateDailyAverageHumidity($weatherData['forecast']['forecastday']),
                'retrieved_at' => now()->toISOString()
            ];

            // Mettre en cache pour 2 heures
            Cache::put($cacheKey, $processedData, now()->addMinutes($this->cacheDuration));

            Log::info("Weather data retrieved and cached for {$city}");

            return $processedData;
        } catch (\Exception $e) {
            Log::error("Error retrieving weather for {$city}: {$e->getMessage()}");
            throw $e;
        }
    }

    public function calculateDailyAverageHumidity(array $forecastData): array
    {
        $dailyHumidity = [];

        foreach ($forecastData as $day) {
            $date = $day['date'];
            $hourlyData = $day['hour'] ?? [];

            if (empty($hourlyData)) {
                // Si pas de données horaires, utiliser la moyenne du jour si disponible
                $dailyHumidity[$date] = $day['day']['avghumidity'] ?? null;
                continue;
            }

            // Calculer la moyenne des humidités horaires
            $totalHumidity = 0;
            $validHours = 0;

            foreach ($hourlyData as $hour) {
                if (isset($hour['humidity'])) {
                    $totalHumidity += $hour['humidity'];
                    $validHours++;
                }
            }

            $averageHumidity = $validHours > 0 ? round($totalHumidity / $validHours, 1) : null;
            $dailyHumidity[$date] = $averageHumidity;
        }

        return $dailyHumidity;
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
