<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Desk;
use App\Services\Wifi2BleSimulatorClient;
use Illuminate\Support\Facades\Log;

class DesksSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Only seed if table is empty (prevents duplicates)
        if (Desk::query()->exists()) {
            return;
        }

        try {
            /** @var Wifi2BleSimulatorClient $client */
            $client = app(Wifi2BleSimulatorClient::class);

            // GET /api/v2/<api_key>/desks returns an array of IDs
            $simulatorDeskIds = $client->listDesks();
        } catch (\Throwable $e) {
            Log::error('Failed to load desks from simulator', ['exception' => $e]);
            $simulatorDeskIds = [];
        }

        foreach ($simulatorDeskIds as $deskId) {
            Desk::create([
                'name'               => null,
                'desk_model'         => 'Linak Desk',
                'serial_number'      => $deskId,
            ]);
        }
    }
}
