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
        // Fix users who have more than 100 total points due to the daily limit bug
        // This should only affect test users during development
        
        $usersWithExcessPoints = \App\Models\User::where('total_points', '>', 100)->get();
        
        foreach ($usersWithExcessPoints as $user) {
            $user->total_points = 100; // Cap at maximum possible daily points
            $user->save();
            
            echo "Fixed user {$user->email}: Set total_points to 100\n";
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
