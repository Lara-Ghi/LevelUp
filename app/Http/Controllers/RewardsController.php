<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class RewardsController extends Controller
{
    /**
     * Display the rewards page
     */
    public function index()
    {
        $user = Auth::user();
        
        // If no user logged in, use test user for development (matching HealthCycleController behavior)
        if (!$user) {
            $user = \App\Models\User::where('email', 'test@example.com')->first();
        }

        // For now, just pass the user's points to the view
        return view('rewards', [
            'totalPoints' => $user ? $user->total_points : 0,
        ]);
    }

    // You can add more methods later for:
    // public function redeem(Request $request) - Handle reward redemption
    // public function history() - Show redemption history
    // public function available() - List available rewards
}