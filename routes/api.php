<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlantController;
use App\Http\Controllers\UserPlantController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/me', [AuthController::class, 'me'])->middleware('auth:sanctum');


// Plant
Route::get('/plants', [PlantController::class, 'index']);
Route::get('/plants/{name}', [PlantController::class, 'show']);
Route::post('/plants', [PlantController::class, 'store']);
Route::patch('/plants/{id}', [PlantController::class, 'update']);
Route::delete('/plants/{id}', [PlantController::class, 'destroy']);

// User_Plant
Route::get('/user/plants', [UserPlantController::class, 'show'])->middleware('auth:sanctum');
Route::post('/user/plants', [UserPlantController::class, 'store'])->middleware('auth:sanctum');
Route::delete('/user/plants/{id}', [UserPlantController::class, 'destroy'])->middleware('auth:sanctum');