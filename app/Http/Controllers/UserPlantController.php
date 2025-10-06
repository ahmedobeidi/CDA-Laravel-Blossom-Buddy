<?php

namespace App\Http\Controllers;

use App\Models\Plant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class UserPlantController extends Controller
{

    public function show(Request $request): JsonResponse
    {
        $user = $request->user();

        $plants = $user->plants;

        return response()->json($plants, 200);
    }

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

    public function destroy(Request $request, int $id): JsonResponse
    {
        /**
         * @var \App\Models\User $user
         */
        $user = $request->user();

        $relation = $user->plants()->wherePivot('id', $id)->first();

        if (!$relation) {
            return response()->json(['error' => 'Plant not found in user'], 404);
        }

        $user->plants()->wherePivot('id', $id)->detach();

        return response()->json(['message' => 'Plant deleted from user successfully'], 200);
    }
}
