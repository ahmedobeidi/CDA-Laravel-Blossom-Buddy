<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use \Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    public function register(Request $request): JsonResponse
    {
        try {
            $validatedData = $request->validate([
                'firstName' => 'required|string|max:55',
                'lastName' => 'required|string|max:55',
                'email' => 'required|email|unique:users',
                'password' => 'required|string|min:8'
            ]);
        } catch (ValidationException $e) {
            return response()->json([
                'message' => 'Validation failed',
                'errors' => $e->errors(),
            ], 422);
        }

        $user = User::create([
            'firstName' => $validatedData['firstName'],
            'lastName' => $validatedData['lastName'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password'])
        ]);

        $token = $user->createToken('access_token')->plainTextToken;

        return response()->json([
            'message' => 'User registered successfully',
            'access_token' => $token,
            'token_type' => 'Bearer'
        ], 201);
    }
}
