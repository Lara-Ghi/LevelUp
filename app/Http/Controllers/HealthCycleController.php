<?php

namespace App\Http\Controllers;

use App\Models\HealthCycle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HealthCycleController extends Controller
{
    /**
     * Calculate health score based on LINAK 20:10 algorithm
     * 
     * @param int $sit - sitting time in minutes
     * @param int $stand - standing time in minutes
     * @return int - score from 0 to 100
     */
    private function calculateHealthScore($sit, $stand)
    {
        // Safety: avoid division errors
        if ($stand <= 0 || $sit <= 0) {
            return 0;
        }

        // Minimum cycle time check (15 minutes total)
        // Prevents gaming the system with tiny cycles
        $minCycleTime = 15; // minutes
        $total = $sit + $stand;
        
        if ($total < $minCycleTime) {
            return 0; // Cycle too short, no points
        }

        // Step 1: Calculate ratio accuracy
        // Ideal ratio = 2 (20 min sitting / 10 min standing)
        $idealRatio = 2.0;
        $userRatio = $sit / $stand;

        // The closer the ratio is to 2, the higher the score (0–1)
        $ratioScore = max(0, 1 - abs($userRatio - $idealRatio) / $idealRatio);

        // Step 2: Check total duration balance
        // Ideal total duration ~30 minutes (20 + 10)
        $durationScore = max(0, 1 - abs($total - 30) / 20);
        // → If user works 25–35 min total, full points.
        // → Drops slowly if cycle is much shorter or longer.

        // Step 3: Weighted final score
        // Ratio accuracy is more important (70%), total time (30%)
        $score = ($ratioScore * 0.7 + $durationScore * 0.3) * 100;

        return round($score);
    }

    /**
     * Convert health score to points
     * 
     * @param int $healthScore
     * @return array - ['points' => int, 'feedback' => string, 'color' => string]
     */
    private function scoreToPoints($healthScore)
    {
        if ($healthScore >= 90) {
            return [
                'points' => 10,
                'feedback' => '🟢 Perfect! Excellent sit–stand balance.',
                'color' => 'green'
            ];
        } elseif ($healthScore >= 70) {
            return [
                'points' => 7,
                'feedback' => '🟡 Good — keep this rhythm going.',
                'color' => 'yellow'
            ];
        } elseif ($healthScore >= 50) {
            return [
                'points' => 4,
                'feedback' => '🟠 Fair — try adjusting your times a bit.',
                'color' => 'orange'
            ];
        } else {
            return [
                'points' => 0,
                'feedback' => '🔴 Too much sitting or too short — no points this cycle.',
                'color' => 'red'
            ];
        }
    }

    /**
     * Complete a health cycle and award points
     */
    public function completeHealthCycle(Request $request)
    {
        $request->validate([
            'sitting_minutes' => 'required|integer|min:1',
            'standing_minutes' => 'required|integer|min:1',
            'cycle_number' => 'required|integer|min:1',
        ]);

        $user = Auth::user();
        
        // If no user is logged in, use the test user for development
        if (!$user) {
            $user = \App\Models\User::where('email', 'test@example.com')->first();
            
            if (!$user) {
                return response()->json([
                    'success' => false,
                    'message' => 'Test user not found. Please run: php artisan db:seed',
                    'health_score' => 0,
                    'points_earned' => 0,
                    'daily_points' => 0,
                    'total_points' => 0,
                ], 404);
            }
        }
        
        // Calculate health score
        $healthScore = $this->calculateHealthScore(
            $request->sitting_minutes,
            $request->standing_minutes
        );

        // Convert to points and get feedback
        $result = $this->scoreToPoints($healthScore);
        $pointsEarned = $result['points'];

        // Check if user can earn points today
        if (!$user->canEarnPoints()) {
            return response()->json([
                'success' => false,
                'message' => 'Daily limit reached! You\'ve earned 100 points today. Come back tomorrow!',
                'health_score' => $healthScore,
                'points_earned' => 0,
                'daily_points' => $user->daily_points,
                'total_points' => $user->total_points,
                'feedback' => 'Daily limit reached (100 points)',
                'color' => 'blue',
            ]);
        }

        // Add points to user (respecting daily limit)
        $actualPointsEarned = $user->addPoints($pointsEarned);

        // Save the health cycle
        $healthCycle = HealthCycle::create([
            'user_id' => $user->id,
            'sitting_minutes' => $request->sitting_minutes,
            'standing_minutes' => $request->standing_minutes,
            'cycle_number' => $request->cycle_number,
            'health_score' => $healthScore,
            'points_earned' => $actualPointsEarned,
            'completed_at' => now(),
        ]);

        // Check if user hit the daily limit with this cycle
        $message = $actualPointsEarned < $pointsEarned 
            ? "You earned {$actualPointsEarned} points (daily limit reached!)" 
            : "You earned {$actualPointsEarned} points!";

        return response()->json([
            'success' => true,
            'message' => $message,
            'health_score' => $healthScore,
            'points_earned' => $actualPointsEarned,
            'daily_points' => $user->daily_points,
            'total_points' => $user->total_points,
            'feedback' => $result['feedback'],
            'color' => $result['color'],
            'daily_limit_reached' => $user->daily_points >= 100,
        ]);
    }

    /**
     * Get user's points and daily status
     */
    public function getPointsStatus()
    {
        $user = Auth::user();
        
        // If no user logged in, use test user for development
        if (!$user) {
            $user = \App\Models\User::where('email', 'test@example.com')->first();
            
            if (!$user) {
                return response()->json([
                    'total_points' => 0,
                    'daily_points' => 0,
                    'can_earn_more' => false,
                    'points_remaining_today' => 0,
                    'message' => 'Test user not found',
                ]);
            }
        }
        
        $user->resetDailyPointsIfNeeded();

        return response()->json([
            'total_points' => $user->total_points,
            'daily_points' => $user->daily_points,
            'can_earn_more' => $user->canEarnPoints(),
            'points_remaining_today' => max(0, 100 - $user->daily_points),
        ]);
    }

    /**
     * Get user's health cycle history
     */
    public function getHistory(Request $request)
    {
        $user = Auth::user();
        
        // If no user logged in, return empty
        if (!$user) {
            return response()->json([
                'cycles' => [],
            ]);
        }
        
        $limit = $request->input('limit', 10);

        $cycles = $user->healthCycles()
            ->orderBy('completed_at', 'desc')
            ->limit($limit)
            ->get();

        return response()->json([
            'cycles' => $cycles,
        ]);
    }
}
