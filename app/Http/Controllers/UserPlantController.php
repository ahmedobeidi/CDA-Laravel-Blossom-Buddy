<?php

namespace App\Http\Controllers;

use App\Contracts\WateringCalculatorServiceInterface;
use App\Contracts\WeatherServiceInterface;
use App\Models\Plant;
use Illuminate\Auth\Events\Validated;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

/**
 * @OA\Tag(
 *     name="User Plants",
 *     description="Endpoints for managing plants associated with the authenticated user"
 * )
 */
class UserPlantController extends Controller
{
    /**
     * @OA\Get(
     *     path="/api/user/plants",
     *     summary="Get all plants belonging to the authenticated user",
     *     tags={"User Plants"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Response(
     *         response=200,
     *         description="List of user's plants",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/Plant")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function show(Request $request): JsonResponse
    {
        $user = $request->user();
        $plants = $user->plants;

        return response()->json($plants, 200);
    }

    /**
     * @OA\Post(
     *     path="/api/user/plants",
     *     summary="Add a new plant to the authenticated user",
     *     tags={"User Plants"},
     *     security={{"bearerAuth":{}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"common_name", "watering_general_benchmark", "city"},
     *             @OA\Property(property="common_name", type="string", example="Aloe Vera"),
     *             @OA\Property(
     *                 property="watering_general_benchmark",
     *                 type="object",
     *                 @OA\Property(property="value", type="string", example="Low"),
     *                 @OA\Property(property="unit", type="string", example="liters/week")
     *             ),
     *             @OA\Property(property="city", type="string", example="Paris")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Plant successfully added to the user",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="message", type="string", example="User plant added successfully"),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="common_name", type="string", example="Aloe Vera"),
     *                 @OA\Property(property="city", type="string", example="Paris"),
     *                 @OA\Property(property="value", type="string", example="Low"),
     *                 @OA\Property(property="unit", type="string", example="liters/week")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation failed",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Validation failed"),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function store(Request $request, WeatherServiceInterface $weatherService, WateringCalculatorServiceInterface $wateringCalculatorService): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'plant_name' => 'required|string|max:255',
                'city' => 'required|string|max:255',
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors()
            ], 422);
        }

        $plantName = $validatedData['plant_name'];
        $city = $validatedData['city'];

        $plant = Plant::where('common_name', 'LIKE', '%' . $plantName . '%')->first();

        if (!$plant) {
            return response()->json([
                'message' => 'Plant not found'
            ], 404);
        }

        $user = $request->user();

        $user->plants()->attach($plant->id, [
            'city' => $city,
        ]);

        try {
            $forecastDays = $weatherService->determineForecastDays($plant->watering_general_benchmark);

            $weatherData = $weatherService->getForecast($city, $forecastDays);

            $wateringCalculation = $wateringCalculatorService->calculateNextWatering(
                $plant->watering_general_benchmark,
                $weatherData['daily_humidity']
            );

            return response()->json([
                'message' => 'Plant added to user successfully',
                'weather_info' => [
                    'city' => $weatherData['city'],
                    'days' => $weatherData['days'],
                    'daily_humidity' => $weatherData['daily_humidity'],
                    'retrieved_at' => $weatherData['retrieved_at']
                ],
                'watering_calculation' => $wateringCalculation
            ], 200);
            
        } catch (\Exception $e) {
            Log::error("Plant added to user successfully, but weather/watering calculation unavailable {$city}: " . $e->getMessage());

            // La plante est déjà ajoutée, mais on informe que les calculs ne sont pas disponibles
            return response()->json([
                'message' => 'Plant added to user successfully, but weather/watering calculation unavailable',
                'error' => 'Weather/watering calculation could not be completed: ' . $e->getMessage()
            ], 200);
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/user/plants/{id}",
     *     summary="Delete a specific plant from the authenticated user's collection",
     *     tags={"User Plants"},
     *     security={{"bearerAuth":{}}},
     *     @OA\Parameter(
     *         name="id",
     *         in="path",
     *         description="The pivot ID (user_plant relation ID) linking the user and plant",
     *         required=true,
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Plant deleted successfully from user",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Plant deleted from user successfully")
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Plant not found for user",
     *         @OA\JsonContent(
     *             @OA\Property(property="error", type="string", example="Plant not found in user")
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthorized"
     *     )
     * )
     */
    public function destroy(Request $request, int $id): JsonResponse
    {
        /** @var \App\Models\User $user */
        $user = $request->user();
        $relation = $user->plants()->wherePivot('id', $id)->first();

        if (!$relation) {
            return response()->json(['error' => 'Plant not found in user'], 404);
        }

        $user->plants()->wherePivot('id', $id)->detach();

        return response()->json(['message' => 'Plant deleted from user successfully'], 200);
    }
}
