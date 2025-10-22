<?php

namespace App\Services;

use App\Contracts\PlantServiceInterface;
use App\Models\Plant;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class PerenualPlantService implements PlantServiceInterface
{
    private string $apiUrl;
    private string $apiKey;

    public function __construct()
    {
        $this->apiUrl = 'https://perenual.com/api/v2/species/details';
        $this->apiKey = config('services.perenual.key');
    }

    public function fetchAndStorePlants(?int $maxRequests = null): array
    {
        if ($maxRequests === null) {
            $maxRequests = 1;
        }

        $stats = [
            'processed' => 0,
            'created' => 0,
            'updated' => 0,
            'errors' => 0,
        ];

        for ($i = 1; $i <= $maxRequests; $i++) {
            $response = Http::timeout(30)->get("{$this->apiUrl}/{$i}", [
                'key' => $this->apiKey
            ]);

            $stats['processed']++;

            if (!$response->successful()) {
                $stats['errors']++;
                Log::error("Plant ID {$i} API request failed with status {$response->status()}");
                continue;
            }

            $data = $response->json();

            if (!$data || !isset($data['id'])) {
                $stats['errors']++;
                Log::warning("Plant ID {$i} returned empty or invalid data");
                continue;
            }

            $plantData = [
                'api_id' => $data['id'],
                'common_name' => $data['common_name'] ?? 'Unknown',
                'scientific_name' => is_array($data['scientific_name']) ? implode(', ', $data['scientific_name']) : $data['scientific_name'],
                'family' => $data['family'] ?? null,
                'origin' => is_array($data['origin']) ? implode(', ', $data['origin']) : $data['origin'],
                'default_image' => is_array($data['default_image']) ? implode(', ', $data['default_image']) : $data['default_image'],
                'watering_general_benchmark' => is_array($data['watering_general_benchmark']) ? implode(', ', $data['watering_general_benchmark']) : $data['watering_general_benchmark'],
            ];

            try {
                $plant = Plant::updateOrCreate(
                    ['api_id' => $data['id']],
                    $plantData
                );

                if ($plant->wasRecentlyCreated) {
                    $stats['created']++;
                } else {
                    $stats['updated']++;
                }
            } catch (\Exception $e) {
                $stats['errors']++;
                Log::error("Failed to save plant ID {$i}: " . $e->getMessage());
            }
        }

        return $stats;
    }
}