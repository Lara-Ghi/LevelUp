<?php

namespace App\Http\Controllers;

use App\Models\HealthCycle;
use Illuminate\Http\Request;
use Illuminate\Routing\Controller as BaseController;

class StatisticsController extends BaseController
{
    // Special method of PHP
    public function __construct()
    {
        $this->middleware("auth");
    }
    public function statistics()
    {

        // Get user Id
        $userId = auth()->id();

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
