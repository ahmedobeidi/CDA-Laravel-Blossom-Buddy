<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * @OA\Schema(
 *     schema="Plant",
 *     type="object",
 *     title="Plant",
 *     required={"id","common_name","watering_general_benchmark"},
 *     @OA\Property(property="id", type="integer"),
 *     @OA\Property(property="common_name", type="string"),
 *     @OA\Property(
 *         property="watering_general_benchmark",
 *         type="object",
 *         @OA\Property(property="value", type="string"),
 *         @OA\Property(property="unit", type="string")
 *     )
 * )
 */
class Plant extends Model
{
   protected $fillable = [
      "api_id", 
      "common_name",
      'scientific_name',
      'family',
      'origin',
      'default_image',
      'watering_general_benchmark'
   ];

   protected $casts = [
        'scientific_name' => 'array',
        'origin' => 'array',
        'default_image' => 'array',
        'watering_general_benchmark' => 'array',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];
}
