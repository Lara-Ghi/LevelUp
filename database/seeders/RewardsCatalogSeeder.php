<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class RewardsCatalogSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only seed if table is empty (prevents duplicates)
        if (DB::table('rewards_catalog')->count() == 0) {
            DB::table('rewards_catalog')->insert([
                [
                    'card_name' => 'Social Meeting',
                    'points_amount' => 0,
                    'card_description' => 'Your ticket to a casual catch-up with your supervisor.',
                    'card_image' => 'meeting-card.jpg',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'card_name' => '10 Coffee Card',
                    'points_amount' => 300,
                    'card_description' => 'Fuel your day at the best café in town.',
                    'card_image' => 'coffee-card.jpg',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'card_name' => '1 Month Spotify Subscription',
                    'points_amount' => 100,
                    'card_description' => 'Soundtrack your life with unlimited music.',
                    'card_image' => 'spotify-card.jpg',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'card_name' => '1 Month PureGym Subscription',
                    'points_amount' => 200,
                    'card_description' => 'Healthy body, healthy mind.',
                    'card_image' => 'gym-card.jpg',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'card_name' => '1 Month Netflix Subscription',
                    'points_amount' => 150,
                    'card_description' => 'Well-deserved chill time.',
                    'card_image' => 'netflix-card.jpg',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'card_name' => '2 Cinema Tickets (Kinorama)',
                    'points_amount' => 250,
                    'card_description' => 'Big screen, big moments.',
                    'card_image' => 'cinema-card.jpg',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'card_name' => '1 Month Audible Subscription',
                    'points_amount' => 150,
                    'card_description' => 'Expand your horizons.',
                    'card_image' => 'audible-card.jpg',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
                [
                    'card_name' => '1 Spa Entry at Sønderborg Alsik Spa',
                    'points_amount' => 500,
                    'card_description' => 'Pure relaxation awaits.',
                    'card_image' => 'spa-card.jpg',
                    'created_at' => now(),
                    'updated_at' => now(),
                ],
            ]);
        }
    }
}
