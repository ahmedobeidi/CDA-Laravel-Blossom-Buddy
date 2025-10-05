<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\PlantController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Auth
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth:sanctum');
Route::post('/me', [AuthController::class, 'me'])->middleware('auth:sanctum'); // did not undrstand it


// Plants
Route::get('/plants', [PlantController::class, 'index']);
Route::post('/plants', [PlantController::class, 'store']);
Route::get('/plants/{name}', [PlantController::class, 'show']);
Route::delete('/plants/{id}', [PlantController::class, 'destroy']);