<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminUserController;

Route::get('/', function () {
    return view('admin.login');
});

Route::prefix('admin')->group(function () {

    // Show login page
    Route::get('/login', [AdminController::class, 'showLogin'])->name('admin.login');

    // Handle login submission
    Route::post('/login', [AdminController::class, 'login'])->name('admin.login.submit');

    // Admin dashboard (protected by auth + admin middleware)
    Route::middleware(['auth', 'admin'])->group(function () {
        Route::get('/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
    });
});






