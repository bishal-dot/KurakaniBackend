<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\FaceVerificationController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\Admin\AdminAuthController;
use App\Http\Controllers\MatchController;
use App\Http\Controllers\MatchesController;


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('user/register',[AuthController::class,'register']);
Route::post('user/login',[AuthController::class,'login'])->name('login');
Route::post('/password/send-otp',[AuthController::class,'sendResetOtp']);
Route::post('/password/verify-otp', [AuthController::class, 'verifyOtp']);
Route::post('/password/reset', [AuthController::class, 'resetPassword']);

Route::middleware('auth:sanctum')->group(function(){
    Route::post('/profile/setup', [AuthController::class, 'completeProfile']);
    Route::get('/me',[AuthController::class,'me']);
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::post('/profile/create', [UserController::class, 'createProfile']);
    Route::post('/profile/verify', [FaceVerificationController::class, 'verifyGender']);
    Route::get('user/profile',[UserController::class,'showProfile']);
    Route::post('/profile/update',[UserController::class,'updateProfile']);
    Route::post('/user/profilephoto',[UserController::class,'uploadProfilePhoto']);
    Route::post('/profile/photos', [UserController::class, 'uploadPhoto']);
    Route::delete('/profile/photos/{id}', [UserController::class, 'deletePhoto']);
    Route::get('/photos',[UserController::class,'getPhotos']);
    Route::get('/users/others',[UserController::class,'otherUsers']); //for home page
    Route::get('users/{id}', [UserController::class, 'getUser']); //for single user details 
    Route::get('/search/users',[UserController::class,'search']); //search users name
    Route::post('user/changepassword',[UserController::class,'changePassword']);

});

// message and search users in chat
Route::middleware('auth:sanctum')->group(function () {
    
    // Send a message to a user
    Route::post('messages/send', [MessageController::class, 'send']);

    // Fetch messages between current user and another user
    Route::get('/messages/{userId}', [MessageController::class, 'getMessages'])
        ->whereNumber('userId'); // ✅ Enforces numeric userId

    // Get all users the current user has chatted with 
    Route::get('messages', [MessageController::class, 'getChatUsers']);

    // Search users by name/username (for the search dropdown)
    Route::get('messages/search', [MessageController::class, 'searchUsers']);
});

// Match Notifications
Route::post('/send-match', [MatchController::class, 'sendMatch']);
Route::middleware('auth:sanctum')->post('/update-fcm-token', [UserController::class, 'updateFcmToken']);

Route::middleware('auth:sanctum')->post('/match', [MatchController::class, 'sendMatch']);

Route::get('/matches', [MatchesController::class, 'index']);