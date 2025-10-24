<?php

namespace App\Http\Controllers;

use App\Models\HealthCycle;
use Illuminate\Http\Request;

class StatisticsController extends Controller
{
    public function statistics()
    {
        // Correct way to check the user.
        // Needs log-in implemented in order to work.
        //$userId = auth()->id();

        // Test user ID
        $userId = 1;

        // Last 7 days of data for Bar Chart
        $healthCycle = HealthCycle::where('user_id', $userId)
            ->whereDate('completed_at', now())
            ->first();

        // Totals for Pie Chart
        $totalSitting = HealthCycle::where('user_id', $userId)->sum('sitting_minutes');
        $totalStanding = HealthCycle::where('user_id', $userId)->sum('standing_minutes');
  
        
        return view('statistics', compact('healthCycle', 'totalSitting', 'totalStanding'));
    }
}
