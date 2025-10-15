<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Models\Plant;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class PlantController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/plants",
     *     summary="Get all plants",
     *     tags={"Plants"},
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Plant")
     *         )
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        return response()->json(Plant::all(), 200);
    }

    public function show(string $common_name): JsonResponse
    {
        $plant = Plant::where('common_name', 'LIKE', '%' . $common_name . '%')->first();

        if (!$plant) {
            return response()->json([
                'message' => 'Plant not found'
            ], 404);
        }

        return response()->json($plant, 200);
    }

    public function store(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'common_name' => 'required|string|max:255',
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

    public function update(Request $request, int $id): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'common_name' => 'sometimes|string|max:255',
                'watering_general_benchmark' => 'sometimes|array',
                'watering_general_benchmark.value' => 'sometimes|string',
                'watering_general_benchmark.unit' => 'sometimes|string',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        $plant = Plant::find($id);

        if (!$plant) {
            return response()->json([
                'message' => 'Plant not found'
            ], 404);
        }

        if (isset($validatedData['watering_general_benchmark'])) {
            $existing = $plant->watering_general_benchmark ?? [];
            $validatedData['watering_general_benchmark'] = array_merge($existing, $validatedData['watering_general_benchmark']);
        }

        $plant->fill($validatedData);
        $plant->save();

        return response()->json([
            'message' => 'Resource updated successfully',
            'date' => $plant,
        ], 200);
    }

    public function destroy(int $id): JsonResponse
    {
        $plant = Plant::find($id);

        if (!$plant) {
            return response()->json([
                "message" => "Plant not found",
            ], 404);
        }

        $plant->delete();

        return response()->json(null, 204);
    }
}
