<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class FaceVerificationController extends Controller
{
    public function verifyGender(Request $request)
{
    $request->validate([
        'photo' => 'required|image|max:5120',
    ]);

    // Get the authenticated user via Sanctum token
    $user = Auth::guard('sanctum')->user();

    if (!$user) {
        return response()->json([
            'error' => true,
            'message' => 'Invalid or missing token.'
        ], 401);
    }

    $userGender = $user->gender;

    if (!$userGender) {
        return response()->json([
            'error' => true,
            'message' => 'User gender not set. Complete profile first.'
        ], 400);
    }

    // Store verification photo
    $photoPath = $request->file('photo')->store('verification', 'public');
    $user->verification_photo = $photoPath;

    // Face++ API keys
    $apiKey = env('FACEPP_API_KEY');
    $apiSecret = env('FACEPP_API_SECRET');

    $response = Http::attach(
        'image_file',
        file_get_contents($request->file('photo')->getRealPath()),
        $request->file('photo')->getClientOriginalName()
    )->post('https://api-us.faceplusplus.com/facepp/v3/detect', [
        'api_key' => $apiKey,
        'api_secret' => $apiSecret,
        'return_attributes' => 'gender',
    ]);

    if ($response->failed()) {
        $user->is_verified = false;
        $user->save();
        return response()->json([
            'error' => true,
            'message' => 'Face++ API error: ' . $response->body(),
        ], 500);
    }

    $data = $response->json();

    if (empty($data['faces']) || count($data['faces']) != 1) {
        $user->is_verified = false;
        $user->save();
        return response()->json([
            'error' => true,
            'message' => 'Submit a clear photo with only your face.',
        ], 400);
    }

    $detectedGender = strtolower($data['faces'][0]['attributes']['gender']['value']);

    if ($detectedGender === strtolower($userGender)) {
        $user->is_verified = true;
        $user->save();

        return response()->json([
            'error' => false,
            'message' => 'Verification successful.',
            'detected_gender' => $detectedGender,
            'user_gender' => $userGender,
        ]);
    } else {
        $user->is_verified = false;
        $user->save();

        return response()->json([
            'error' => true,
            'message' => 'Verification failed. Gender mismatch.',
            'detected_gender' => $detectedGender,
            'user_gender' => $userGender,
        ], 403);
    }
}
}