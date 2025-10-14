<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        // Reset daily points for new day (simulate timezone fix)
        $users = \App\Models\User::all();
        
        foreach ($users as $user) {
            $user->daily_points = 0;
            $user->last_points_date = now()->toDateString();
            $user->save();
            
            echo "Reset user {$user->email}: daily_points = 0, date = " . now()->toDateString() . "\n";
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
