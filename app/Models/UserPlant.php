<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\Pivot;

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
