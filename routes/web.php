<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\StatisticsController;
use App\Http\Controllers\HealthCycleController;
use App\Http\Controllers\RewardsController;

Route::get('/', function () {
    return view('home');
});

Route::get('/profile', function () {
    return view('profile');
});

// Use Controller to get the Statistics Page
// This is a better practice in MVC framework
Route::get('/', [StatisticsController::class, 'statistics'])->name('statistics');

// Health Cycle API routes
// TODO: Add authentication middleware when auth system is ready
// For now, these work without auth but require a logged-in user to earn points
Route::post('/api/health-cycle/complete', [HealthCycleController::class, 'completeHealthCycle']);
Route::get('/api/health-cycle/points-status', [HealthCycleController::class, 'getPointsStatus']);
Route::get('/api/health-cycle/history', [HealthCycleController::class, 'getHistory']);
Route::get('/rewards', [RewardsController::class, 'index'])->name('rewards');