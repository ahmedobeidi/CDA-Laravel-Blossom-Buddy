<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @OA\Schema(
 *     schema="UserPlant",
 *     type="object",
 *     title="User Plant Relation",
 *     description="Pivot table connecting User and Plant",
 *     @OA\Property(property="id", type="integer", example=5),
 *     @OA\Property(property="city", type="string", example="Paris"),
 *     @OA\Property(property="created_at", type="string", format="date-time", example="2025-10-15T10:00:00Z"),
 *     @OA\Property(property="updated_at", type="string", format="date-time", example="2025-10-15T10:00:00Z")
 * )
 */
class UserPlant extends Pivot
{
    protected $fillable = [
        'city'
    ];

    protected $hidden = [
        'user_id',
        'plant_id'
    ];
}
