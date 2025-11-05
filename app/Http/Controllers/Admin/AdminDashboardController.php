<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class AdminDashboardController extends Controller
{
    public function index(Request $request)
    {
        $q = trim($request->get('q', ''));
        $editId = $request->integer('edit');
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

        return view('admin.dashboard', compact('users', 'q', 'editUser'));
    }
}