<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create or update test user with default points
        User::updateOrCreate(
            ['email' => 'test@example.com'],
            [
                'name' => 'Test User',
                'password' => bcrypt('password'), // password: password
                'total_points' => 0,
                'daily_points' => 0,
                'last_points_date' => now()->toDateString(),
            ]
        );
        
        // Optionally create more test users
        // User::factory(5)->create();
    }
}
