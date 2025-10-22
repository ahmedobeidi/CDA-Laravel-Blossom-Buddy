<?php

namespace App\Contracts;

interface PlantServiceInterface
{
    /**
     * Fetch multiple plants from the external API and store them in the database
     *
     * @param int|null $maxRequests Optional limit of API requests
     * @return array Summary of synced plants
     */
    public function fetchAndStorePlants(?int $maxRequests = null): array;
}
