<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class AuthController extends Controller
{
    // ---------------- SIGNUP ----------------
    public function register(Request $request)
    {
        $request->validate([
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
        ]);

        // Create user
        $user = User::create([
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        // Generate token
        $token = $user->createToken('mobile-token')->plainTextToken;

        return response()->json([
            'error' => false,
            'message' => 'Signup successful',
            'user' => $user,
            'token' => $token,
            'profile_complete' => false,  // Always false after signup
        ], 201);
    }

    // ---------------- LOGIN ----------------
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid credentials'], 401);
        }

        $token = $user->createToken('mobile-token')->plainTextToken;

        $profileComplete = !empty($user->fullname) && !empty($user->gender) && !empty($user->age);

        return response()->json([
            'error' => false,
            'message' => 'Login successful',
            'user' => $user,
            'token' => $token,
            'profile_complete' => $profileComplete
        ]);
    }

    // ---------------- CURRENT USER ----------------
    public function me(Request $request)
    {
        return response()->json($request->user());
    }

    // ---------------- LOGOUT ----------------
    public function logout(Request $request)
    {
        $user = $request->user();
        if ($user) {
            $user->tokens()->delete();
            return response()->json([
                'error' => false,
                'message' => 'Logged out successfully',
            ]);
        }

        return response()->json([
            'error' => true,
            'message' => 'User not authenticated',
        ], 401);
    }
}
