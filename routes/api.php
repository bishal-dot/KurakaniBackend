<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\FaceVerificationController;
use App\Http\Controllers\SearchController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminAuthController;


// Route::get('/user', function (Request $request) {
//     return $request->user();
// })->middleware('auth:sanctum');

Route::post('user/register',[AuthController::class,'register']);
Route::post('user/login',[AuthController::class,'login']);

Route::middleware('auth:sanctum')->group(function(){
    Route::post('/profile/setup', [AuthController::class, 'completeProfile']);
    Route::get('/me',[AuthController::class,'me']);
    Route::get('/logout', [AuthController::class, 'logout']);
    Route::post('/verifygender',[FaceVerificationController::class,'verifyGender']);
    Route::post('user/complete/profile', [UserController::class, 'completeProfile']);
    Route::get('user/profile',[UserController::class,'showProfile']);
    Route::post('/profile/update',[UserController::class,'updateProfile']);
    Route::post('/profile/photos', [UserController::class, 'uploadPhoto']);
    Route::delete('/profile/photos/{id}', [UserController::class, 'deletePhoto']);
    Route::get('/photos',[UserController::class,'getPhotos']);
    Route::get('/users/others',[UserController::class,'otherUsers']); //for home page
    Route::get('users/{id}', [UserController::class, 'getUser']); //for single user details 
    Route::get('/search/users',[UserController::class,'search']); //search users name

});
