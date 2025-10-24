<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\HealthCycle;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create or update test user with default points
        $user = User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'), // password: password
                'total_points' => 0,
                'daily_points' => 0,
                'last_points_date' => now()->toDateString(),
            ]
        );

        // Create test health cycles for the user
        HealthCycle::create([
            'user_id' => $user->id,
            'sitting_minutes' => 50,
            'standing_minutes' => 35,
            'cycle_number' => 1,
            'health_score' => 80,
            'points_earned' => 100,
            'completed_at' => now()
        ]);
        
        // Optionally create more test users
        // User::factory(5)->create();
    }
}
