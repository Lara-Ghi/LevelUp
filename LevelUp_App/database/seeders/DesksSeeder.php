<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Desk;
use Illuminate\Support\Facades\DB;

class DesksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only seed if table is empty (prevents duplicates)
        if (DB::table('desks')->count() == 0) {
            // Create 10 desks
            for ($i = 1; $i <= 10; $i++) {
                Desk::create([
                    'desk_model' => 'Linak Desk',
                    'serial_number' => 'LNK' . str_pad($i, 3, '0', STR_PAD_LEFT),
                ]);
            }
        }
    }
}
