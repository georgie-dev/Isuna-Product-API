<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class AuthController extends Controller
{
    /**
     * Register a new user
     *
     * Creates a new user account and returns an API token.
     *
     * @bodyParam name string required The user's full name. Example: George
     * @bodyParam email string required The user's email address. Example: george@test.com
     * @bodyParam password string required The user's password (min 8 characters). Example: password
     * @bodyParam password_confirmation string required Must match the password field. Example: password
     *
     * @response 201 scenario="Registration successful" {
     *   "user": {
     *     "id": 1,
     *     "name": "George",
     *     "email": "george@test.com",
     *     "created_at": "2026-05-16 17:00:00",
     *     "updated_at": "2026-05-16 17:00:00"
     *   },
     *   "token": "1|ezFqbCLoX6kwfITwUb9rCrHyXkOhHLh2Y5BW9ojE491acf71"
     * }
     * @response 422 scenario="Validation error" {
     *   "message": "Validation failed.",
     *   "errors": {
     *     "email": ["The email has already been taken."],
     *     "password": ["The password field confirmation does not match."]
     *   }
     * }
     */
    public function register(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
        ]);

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ], 201);
    }

    /**
     * Login
     *
     * Authenticates a user and returns an API token.
     *
     * @bodyParam email string required The user's email address. Example: george@test.com
     * @bodyParam password string required The user's password. Example: password
     *
     * @response 200 scenario="Login successful" {
     *   "user": {
     *     "id": 1,
     *     "name": "George",
     *     "email": "george@test.com",
     *     "created_at": "2026-05-16 17:00:00",
     *     "updated_at": "2026-05-16 17:00:00"
     *   },
     *   "token": "1|ezFqbCLoX6kwfITwUb9rCrHyXkOhHLh2Y5BW9ojE491acf71"
     * }
     * @response 422 scenario="Invalid credentials" {
     *   "message": "Validation failed.",
     *   "errors": {
     *     "email": ["The provided credentials are incorrect."]
     *   }
     * }
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string',
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials are incorrect.'],
            ]);
        }

        $token = $user->createToken('api-token')->plainTextToken;

        return response()->json([
            'user' => $user,
            'token' => $token,
        ]);
    }

    /**
     * Logout
     *
     * Revokes the current user's API token.
     *
     * @authenticated
     *
     * @response 200 scenario="Logout successful" {
     *   "message": "Logged out successfully."
     * }
     * @response 401 scenario="Unauthenticated" {
     *   "message": "Unauthenticated"
     * }
     */
    public function logout(Request $request)
    {
        $request->user()->currentAccessToken()->delete();

        return response()->json(['message' => 'Logged out successfully.']);
    }
}