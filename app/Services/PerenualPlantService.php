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
        $this->apiUrl = config('services.perenual.base_url');
        $this->apiKey = config('services.perenual.key');
    }

    public function fetchAndStorePlants(int $maxRequests): array
    {
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
                'scientific_name' => $data['scientific_name'] ?? [],
                'family' => $data['family'] ?? null,
                'origin' => $data['origin'] ?? [],
                'default_image' => $data['default_image'] ?? [],
                'watering_general_benchmark' => $data['watering_general_benchmark'] ?? ['value' => null, 'unit' => null],
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

                sleep(1);
            } catch (\Exception $e) {
                $stats['errors']++;
                Log::error("Failed to save plant ID {$i}: " . $e->getMessage());
            }
        }

        return $stats;
    }
}
