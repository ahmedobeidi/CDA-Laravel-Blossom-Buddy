<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Models\Plant;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PlantController extends Controller
{
    public function index(): JsonResponse
    {
        return response()->json(Plant::all(), 200);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'common_name' => 'required|string',
                'watering_general_benchmark.value' => 'required|string',
                'watering_general_benchmark.unit' => 'required|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        $plant = Plant::create($validatedData);

        return response()->json($plant, 201);
    }
}
