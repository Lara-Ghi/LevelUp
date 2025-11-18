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
            // Simulator desk IDs (all 7 desks)
            $simulatorDeskIds = [
                'cd:fb:1a:53:fb:e6',
                'ee:62:5b:b8:73:1d',
                '70:9e:d5:e7:8c:98',
                '00:ec:eb:50:c2:c8',
                'f1:50:c2:b8:bf:22',
                'ce:38:a6:30:af:1d',
                '91:17:a4:3b:f4:4d',
            ];

            foreach ($simulatorDeskIds as $deskId) {
                Desk::create([
                    'desk_model' => 'Linak Desk',
                    'serial_number' => $deskId,
                ]);
            }
        }
    }
}
