<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;


class AuthController extends Controller
{
    public function register(Request $request){
        $request->validate([
            'name'   => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6',
            'gender' => 'required|in:male,female,other',
            'age' => 'nullable|integer|min:18',
            'photo_base64' => 'required|string',
        ]);

        $photoData = $request->photo_base64;
        $photo = base64_decode($photoData);

        $fileName = 'verification_' . Str::random(10) . '.jpg';
        Storage::disk('public')->put('verification_photos/' . $fileName, $photo);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'gender' => $request->gender,
            'age' => $request->age,
            'verification_photo' => $fileName,
            'is_verified' => false, // Set false if verification pending
        ]);

        $token = $user->createToken('mobile-token')->plainTextToken;

        return response()->json([
            'error' => false,
            'reason' => 'listed',
            'response' => $user,
            'token' => $token
        ],201);
    }

    public function login(Request $request){
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return response()->json(['message' => 'Invalid email'], 401);
        }

        if (!Hash::check($request->password, $user->password)) {
            return response()->json(['message' => 'Invalid password'], 401);
        }

        $token = $user->createToken('mobile-token')->plainTextToken;

        return response()->json([
            'error' => false,
            'reason' => 'listed',
            'response' => $user,
            'token' => $token,
        ]);
    
    }

     public function me(Request $request)
    {
        return response()->json($request->user());
    }

    public function logout(Request $request)
    {
         $user = $request->user();
    if ($user) {
        $user->tokens()->delete();
        return response()->json([
            'error' => false,
            'reason' => 'success',
            'response' => null
        ]);
    }

    return response()->json([
        'error' => true,
        'reason' => 'User not authenticated',
        'response' => null
    ], 401);
    }
}
