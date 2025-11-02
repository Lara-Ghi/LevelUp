<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class ProfileController extends Controller
{
    /**
     * Display the user's profile.
     */
    public function show()
    {
        $user = Auth::user();
        
        return view('profile', compact('user'));
    }

    /**
     * Update the user's profile information.
     */
    public function update(Request $request)
    {
        $user = Auth::user();
        
        $request->validate([
            'name' => 'required|string|max:100',
            'surname' => 'nullable|string|max:100',
            'date_of_birth' => 'nullable|date',
            'sitting_position' => 'nullable|integer|min:40|max:120',
            'standing_position' => 'nullable|integer|min:60|max:200',
        ]);

        $user->update([
            'name' => $request->name,
            'surname' => $request->surname,
            'date_of_birth' => $request->date_of_birth,
            'sitting_position' => $request->sitting_position,
            'standing_position' => $request->standing_position,
        ]);

        return redirect()->route('profile')->with('success', 'Profile updated successfully!');
    }
}