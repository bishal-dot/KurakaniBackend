<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    // ---------------- SIGNUP ----------------
   public function register(Request $request)
{
    $request->validate([
        'username' => 'required|string|max:255|unique:users,username',
        'email'    => 'required|email|unique:users,email',
        'password' => 'required|string|min:6',
    ]);

    // Create user directly
    $user = User::create([
        'username'         => $request->username,
        'email'            => $request->email,
        'password'         => Hash::make($request->password),
        'profile_complete' => false,  // profile not completed yet
        'is_verified'      => false,  // face/gender not verified yet
    ]);

    $token = $user->createToken('mobile-token')->plainTextToken;

    return response()->json([
        'error'   => false,
        'message' => 'Signup successful. Complete your profile.',
        'user'    => [
            'id'               => $user->id,
            'username'         => $user->username,
            'email'            => $user->email,
            'profile_complete' => $user->profile_complete,
            'is_verified'      => $user->is_verified,
        ],
        'token' => $token,
    ], 200);
}

    // ---------------- LOGIN ----------------
    public function login(Request $request)
{
    Log::info($request->all());

    $request->validate([
        'email' => 'required|email',
        'password' => 'required|string'
    ]);

    $user = User::where('email', $request->email)->first();

    if (!$user || !Hash::check($request->password, $user->password)) {
        return response()->json(['error' => true, 'message' => 'Invalid credentials'], 401);
    }

    $token = $user->createToken('mobile-token')->plainTextToken;

    // check profile completion
    $profileComplete = !empty($user->fullname) && !empty($user->gender) && !empty($user->age);

    // Optionally keep DB column updated
    if ($user->profile_complete !== $profileComplete) {
        $user->profile_complete = $profileComplete;
        $user->save();
    }

    return response()->json([
        'error' => false,
        'message' => 'Login successful',
        'user' => $user,
        'token' => $token,
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

    public function sendResetOtp(Request $request)
{
    // Validate email
    $validator = Validator::make($request->all(), [
        'email' => 'required|email|exists:users,email',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'error' => true,
            'message' => $validator->errors()->first()
        ], 400);
    }

    $email = $request->email;

    // Generate 6-digit OTP
    $otp = rand(100000, 999999);

    // Save OTP to password_resets table (or update existing)
    DB::table('password_resets')->updateOrInsert(
        ['email' => $email],
        [
            'otp' => $otp,
            'expires_at' => now()->addMinutes(10),
            'created_at' => now(),
            'updated_at' => now(),
        ]
    );

    // TODO: Send OTP to user via email or SMS
    // Mail::to($email)->send(new ResetOtpMail($otp));

    return response()->json([
        'error' => false,
        'message' => 'OTP sent successfully',
        'otp' => $otp // for testing only; remove in production
    ]);
}

    public function verifyOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:users,email',
        'otp' => 'required|string',
    ]);

    $record = DB::table('password_resets')
        ->where('email', $request->email)
        ->where('otp', $request->otp)
        ->first();

    if (!$record) {
        return response()->json([
            'error' => true,
            'message' => 'Invalid OTP'
        ], 400);
    }

    if (now()->greaterThan($record->expires_at)) {
        return response()->json([
            'error' => true,
            'message' => 'OTP expired'
        ], 400);
    }

    return response()->json([
        'error' => false,
        'message' => 'OTP verified successfully'
    ]);
}


   public function resetPassword(Request $request)
{
    $request->validate([
        'email' => 'required|email|exists:users,email',
        'otp' => 'required|string',
        'new_password' => 'required|min:6|confirmed', // expects new_password_confirmation
    ]);

    // Check OTP
    $record = DB::table('password_resets')
        ->where('email', $request->email)
        ->where('otp', $request->otp)
        ->first();

    if (!$record) {
        return response()->json([
            'error' => true,
            'message' => 'Invalid OTP'
        ], 400);
    }

    if (now()->greaterThan($record->expires_at)) {
        return response()->json([
            'error' => true,
            'message' => 'OTP expired'
        ], 400);
    }

    // Update user password
    $user = User::where('email', $request->email)->first();
    $user->password = Hash::make($request->new_password);
    $user->save();

    // Delete OTP after successful reset
    DB::table('password_resets')->where('email', $request->email)->delete();

    return response()->json([
        'error' => false,
        'message' => 'Password reset successfully'
    ]);
}
}
