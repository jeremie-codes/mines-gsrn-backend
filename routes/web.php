<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\MerchantController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\AuthTokenController;
use App\Http\Controllers\ConfigurationController;
use App\Http\Controllers\MessageController;
use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\ProfileController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

Route::middleware('guest')->group(function () {
    // Authentication Routes
    Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
    Route::post('/login', [LoginController::class, 'login']);
});

// Protected Routes
Route::middleware('auth')->group(function () {
    Route::get('/', [DashboardController::class, 'index'])->name('dashboard');

    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Merchants
    Route::resource('merchants', MerchantController::class);

    // Users
    Route::resource('users', UserController::class);

    // Auths
    Route::resource('auths', AuthController::class);

    // Auth Tokens
    Route::resource('auth-tokens', AuthTokenController::class);
    Route::get('/auth-tokens/generate-token', [AuthTokenController::class, 'generateToken'])->name('auth-tokens.generate-token');

    // Configurations
    Route::resource('configurations', ConfigurationController::class);

    // Messages
    Route::resource('messages', MessageController::class);

    // Logout
    Route::post('/logout', [LoginController::class, 'logout'])->name('logout');
});
