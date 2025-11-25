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

        // Only register half of the desks
        $totalDesks = count($simulatorDeskIds);

        if ($totalDesks === 0) {
            return;
        }

        $halfCount = (int) floor($totalDesks / 2);

        // If there is only 1 desk, still seed that one
        if ($halfCount === 0) {
            $halfCount = 1;
        }

        // Take the first half of the list
        $deskIdsToSeed = array_slice($simulatorDeskIds, 0, $halfCount);

        foreach ($deskIdsToSeed as $deskId) {
            Desk::create([
                'name'          => null,
                'desk_model'    => 'Linak Desk',
                'serial_number' => $deskId,
            ]);
        }
    }
}
