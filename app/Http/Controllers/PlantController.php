<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use App\Models\Plant; 

class PlantController extends Controller 
{
    public function index(): JsonResponse
    {
        return response()->json([Plant::all()]);
    }
}