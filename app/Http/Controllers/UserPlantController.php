<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserPlantController extends Controller
{
    public function store(Request $request): JsonResponse
    {
        try {
            $validatedPlantData = $request->validate([
                'common_name' => 'required|string|max:255',
                'watering_general_benchmark' => 'required|array',
                'watering_general_benchmark.value' => 'required|string',
                'watering_general_benchmark.unit' => 'required|string',
            ]);
            $validatedUserPlantData = $request->validate([
                'city' => 'required|string|max:255',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        $user = $request->user();

        $plant = Plant::create($validatedPlantData);

        $user->plants()->attach($plant->id, [
            'city' => $validatedUserPlantData['city'],
        ]);

        return response()->json([
            'message' => 'User plant added successfully',
            'data' => [
                'common_name' => $validatedPlantData['common_name'],
                'city' => $validatedUserPlantData['city'],
                'value' => $validatedPlantData['watering_general_benchmark']['value'],
                'unit'  => $validatedPlantData['watering_general_benchmark']['unit'],
            ]
        ], 201);
    }
}
