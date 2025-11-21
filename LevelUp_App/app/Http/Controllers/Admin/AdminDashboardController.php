<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Reward;
use App\Models\Desk;
use App\Services\Wifi2BleSimulatorClient;
use Illuminate\Http\Request;
use Throwable;

class AdminDashboardController extends Controller
{
    public function __construct(
        private readonly Wifi2BleSimulatorClient $simClient
    ) {}

    public function index(Request $request, Wifi2BleSimulatorClient $simClient)
    {
        $tab = $request->query('tab', 'users');

        // ----- USERS TAB -----
        $q        = trim($request->get('q', ''));
        $editId   = $request->integer('edit');
        $editUser = $editId ? User::find($editId) : null;

        $users = User::query()
            ->when($q, function ($query) use ($q) {
                $query->where(function ($sub) use ($q) {
                    $sub->where('name', 'like', "%{$q}%")
                        ->orWhere('surname', 'like', "%{$q}%")
                        ->orWhere('username', 'like', "%{$q}%");
                });
            })
            ->orderBy('name')
            ->orderBy('surname')
            ->orderBy('username')
            ->paginate(15)
            ->withQueryString();

        // ----- REWARDS TAB -----
        $activeRewards   = Reward::where('archived', false)->orderBy('card_name')->get();
        $archivedRewards = Reward::where('archived', true)->orderBy('card_name')->get();
        $editRewardId    = $request->integer('edit_reward');
        $editReward      = $editRewardId ? Reward::find($editRewardId) : null;

        // ---------- DESKS & CLEANING TAB DATA ----------
        $desks            = collect();
        $availableDeskIds = [];
        $editDesk         = null;
        $deskStates       = [];
        $allManagedDesks  = collect();

        if (in_array($tab, ['desks', 'desk-cleaning'], true)) {
            $allManagedDesks = Desk::orderBy('name')
                ->orderBy('serial_number')
                ->get();

            if ($tab === 'desks') {
                $deskSearch = trim($request->get('q', ''));

                $deskQuery = Desk::query();
                if ($deskSearch !== '') {
                    $deskQuery->where(function ($q) use ($deskSearch) {
                        $q->where('name', 'like', "%{$deskSearch}%")
                          ->orWhere('serial_number', 'like', "%{$deskSearch}%");
                    });
                }

                $desks = $deskQuery
                    ->orderBy('name')
                    ->orderBy('serial_number')
                    ->paginate(10) // showing only the first 10 desks per page
                    ->withQueryString();

                $managedSerials = $allManagedDesks->pluck('serial_number')->all();

                // available simulator desks (not yet registered)
                try {
                    $simIds           = $simClient->listDesks();
                    $availableDeskIds = array_values(array_diff($simIds, $managedSerials));
                } catch (Throwable $e) {
                    $availableDeskIds = [];
                }

                // currently edited desk
                $editDeskId = $request->integer('edit_desk');
                $editDesk   = $editDeskId ? Desk::find($editDeskId) : null;
            }

            // fetch config/state for each managed desk
            foreach ($allManagedDesks as $desk) {
                try {
                    $data = $simClient->getDesk($desk->serial_number);

                    $deskStates[$desk->serial_number] = [
                        'config_name' => data_get($data, 'config.name'),
                        'position_cm' => isset($data['state']['position_mm'])
                            ? (int) round($data['state']['position_mm'] / 10)
                            : null,
                        'status'      => data_get($data, 'state.status'),
                    ];
                } catch (Throwable $e) {
                    $deskStates[$desk->serial_number] = [
                        'config_name' => null,
                        'position_cm' => null,
                        'status'      => 'Unavailable',
                    ];
                }
            }
        }

        return view('admin.dashboard', compact(
            'users',
            'q',
            'editUser',
            'activeRewards',
            'archivedRewards',
            'editReward',
            'desks',
            'availableDeskIds',
            'editDesk',
            'deskStates',
            'allManagedDesks'
        ));
    }
}