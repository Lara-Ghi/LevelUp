<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Admin\AdminDashboardController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Admin\AdminRewardsController; // ADD THIS LINE
use App\Http\Middleware\IsAdmin;

return function () {
    // Admin Dashboard & User Management (admin only)
    Route::middleware(['auth', IsAdmin::class])
        ->prefix('admin')
        ->name('admin.')
        ->group(function () {
            Route::get('/dashboard', [AdminDashboardController::class, 'index'])->name('dashboard');
            Route::post('/users', [AdminUserController::class, 'store'])->name('users.store');
            Route::patch('/users/{user}', [AdminUserController::class, 'update'])->name('users.update');
            Route::patch('/users/{user}/promote', [AdminUserController::class, 'promote'])->name('users.promote');
            Route::patch('/users/{user}/demote', [AdminUserController::class, 'demote'])->name('users.demote');
            Route::delete('/users/{user}', [AdminUserController::class, 'destroy'])->name('users.destroy');
            
            // Reward Management Routes
            Route::post('/rewards', [AdminRewardsController::class, 'store'])->name('rewards.store');
            Route::put('/rewards/{reward}', [AdminRewardsController::class, 'update'])->name('rewards.update');
            Route::patch('/rewards/{reward}/archive', [AdminRewardsController::class, 'archive'])->name('rewards.archive');
            Route::patch('/rewards/{reward}/unarchive', [AdminRewardsController::class, 'unarchive'])->name('rewards.unarchive');
            Route::delete('/rewards/{reward}', [AdminRewardsController::class, 'destroy'])->name('rewards.destroy');
        });
};

