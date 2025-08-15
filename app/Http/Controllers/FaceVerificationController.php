<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;

class FaceVerificationController extends Controller
{
    public function verifyGender(Request $request)
    {
        $request->validate([
            'user_gender' => 'required|in:male,female',
            'photo' => 'required|image|max:5120', // max 5MB
        ]);

        $userGender = $request->input('user_gender');
        $photo = $request->file('photo');

        $apiKey = env('FACEPP_API_KEY');
        $apiSecret = env('FACEPP_API_SECRET');

        $response = Http::attach(
            'image_file', file_get_contents($photo->getRealPath()), $photo->getClientOriginalName()
        )->post('https://api-us.faceplusplus.com/facepp/v3/detect', [
            'api_key' => $apiKey,
            'api_secret' => $apiSecret,
            'return_attributes' => 'gender',
        ]);

        if ($response->failed()) {
            return response()->json([
                'error' => true,
                'message' => 'Face++ API error: ' . $response->body(),
            ], 500);
        }

        $data = $response->json();

        if (empty($data['faces'])) {
            return response()->json([
                'error' => true,
                'message' => 'No face detected in the photo',
            ], 400);
        }

        $detectedGender = strtolower($data['faces'][0]['attributes']['gender']['value']); // "male" or "female"

        if ($detectedGender !== strtolower($userGender)) {
            return response()->json([
                'error' => true,
                'message' => 'Gender does not match the live photo',
                'detected_gender' => $detectedGender,
                'user_gender' => $userGender,
            ], 403);
        }

        return response()->json([
            'error' => false,
            'message' => 'Gender verified successfully',
        ]);
    }
}
