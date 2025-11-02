<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\HealthCycleController;
use App\Http\Controllers\RewardsController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\Auth\LoginController;

// Home Routes
Route::get('/', function () {
    return view('home');
})->name('home');

// Authentication Routes
Route::middleware('guest')->group(function () {
    // Login
    Route::get('/login', [LoginController::class, 'show'])->name('login');
    Route::post('/login', [LoginController::class, 'authenticate'])->name('login.perform');
});

// Logout (only accessible when authenticated)
Route::post('/logout', [LoginController::class, 'logout'])
    ->middleware('auth')
    ->name('logout');

// Password Reset Routes (placeholders for future implementation)
Route::get('/forgot-password', function() {
    return redirect()->route('login');
})->name('password.request');

// Protected Routes (require authentication)
Route::middleware('auth')->group(function () {
    // Profile Routes
    Route::get('/profile', [ProfileController::class, 'show'])->name('profile');
    Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

    // Statistics Routes
    Route::get('/statistics', [StatisticsController::class, 'statistics'])->name('statistics');
    
    // Rewards Routes
    Route::get('/rewards', [RewardsController::class, 'index'])->name('rewards');
});

// Health Cycle API routes (require authentication)
Route::middleware('auth')->group(function () {
    Route::post('/api/health-cycle/complete', [HealthCycleController::class, 'completeHealthCycle']);
    Route::get('/api/health-cycle/points-status', [HealthCycleController::class, 'getPointsStatus']);
    Route::get('/api/health-cycle/history', [HealthCycleController::class, 'getHistory']);
});