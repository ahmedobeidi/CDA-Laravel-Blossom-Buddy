<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Models\Plant;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="Plants",
 *     description="Endpoints for managing global plant resources"
 * )
 */
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
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Plant not found"
     *     )
     * )
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => Plant::all()
        ], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/plants/{common_name}",
     *     summary="Get one plant by its common name",
     *     tags={"Plants"},
     *     @OA\Parameter(
     *         name="common_name",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Plant found",
     *         @OA\JsonContent(ref="#/components/schemas/Plant")
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Plant not found"
     *     )
     * )
     */
    public function show(string $common_name): JsonResponse
    {
        $plant = Plant::where('common_name', 'LIKE', '%' . $common_name . '%')->first();

        if (!$plant) {
            return response()->json([
                'message' => 'Plant not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Plant found',
            $plant
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/plants",
     *     summary="Create a new plant",
     *     tags={"Plants"},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"common_name","watering_general_benchmark"},
     *             @OA\Property(property="common_name", type="string"),
     *             @OA\Property(
     *                 property="watering_general_benchmark",
     *                 type="object",
     *                 @OA\Property(property="value", type="string"),
     *                 @OA\Property(property="unit", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Plant created successfully",
     *         @OA\JsonContent(ref="#/components/schemas/Plant")
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
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

        return response()->json([
            'message' => 'Plant stored successfully',
            $plant
        ], 201);
    }

    /**
     * @OA\Patch(
     *     path="/api/plants/{id}",
     *     summary="Update an existing plant",
     *     tags={"Plants"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=false,
     *         @OA\JsonContent(
     *             @OA\Property(property="common_name", type="string"),
     *             @OA\Property(
     *                 property="watering_general_benchmark",
     *                 type="object",
     *                 @OA\Property(property="value", type="string"),
     *                 @OA\Property(property="unit", type="string")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Resource updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="data", ref="#/components/schemas/Plant")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Plant not found"
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
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
            'data' => $plant,
        ], 200);
    }

    /**
     * @OA\Delete(
     *     path="/api/plants/{id}",
     *     summary="Delete a plant",
     *     tags={"Plants"},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=204,
     *         description="Plant deleted successfully"
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Plant not found"
     *     )
     * )
     */
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
