<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\AdminUserController;

Route::get('/', function () {
    return view('admin.login');
});

// Admin Login Routes
// This route displays the admin login form.
Route::get('admin/login', [AdminController::class, 'showLogin'])->name('admin.login');

// This route handles the login form submission.
Route::post('admin/login', [AdminController::class, 'login'])->name('admin.login.submit');

// Admin Dashboard Route
// This route displays the main dashboard and should be protected.
Route::middleware(['auth'])->group(function () {
    Route::get('admin/dashboard', [AdminController::class, 'dashboard'])->name('admin.dashboard');
});

Route::prefix('admin')->middleware('auth')->group(function () {
    Route::get('users', [AdminUserController::class, 'index'])->name('admin.users.index');
    Route::get('users/{user}/edit', [AdminUserController::class, 'edit'])->name('admin.users.edit');
    Route::put('users/{user}', [AdminUserController::class, 'update'])->name('admin.users.update');
    Route::put('users/{user}/suspend', [AdminUserController::class, 'suspend'])->name('admin.users.suspend');
    Route::delete('users/{user}/delete', [AdminUserController::class, 'destroy'])->name('admin.users.destroy');
});
Route::prefix('admin')->name('admin.')->group(function () {
    Route::resource('users', AdminUserController::class);
});






