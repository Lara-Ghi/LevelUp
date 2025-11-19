<?php

namespace App\Http\Controllers;

use App\Models\Reward;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Gate;

class RewardsController extends Controller
{
    /**
     * Display the rewards page
     */
    public function index()
    {
        $user = Auth::user();

        if (!$user) {
            $user = \App\Models\User::where('email', 'test@example.com')->first();
        }

        $rewards = Reward::where('archived', false)->get();
        $savedRewardIds = $user ? $user->favoriteRewards()->pluck('card_id')->toArray() : [];

        return view('rewards.rewards', [
            'rewards' => $rewards,
            'savedRewardIds' => $savedRewardIds,
        ]);
    }

    // Redeem rewards
    public function redeem(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $rewardId = $request->input('reward_id');
        $reward = Reward::find($rewardId);

        if (!$reward) {
            return response()->json(['error' => 'Reward not found'], 404);
        }

        // Check if user has enough points
        if ($user->total_points < $reward->points_amount) {
            return response()->json([
                'error' => 'Insufficient points',
                'required' => $reward->points_amount,
                'available' => $user->total_points
            ], 400);
        }

        // Deduct points
        $user->total_points -= $reward->points_amount;
        $user->save();

        // Create redemption record
        $user->redeemedRewards()->attach($rewardId, [
            'redeemed_at' => now()
        ]);

        return response()->json([
            'success' => true,
            'new_points' => $user->total_points,
            'reward_name' => $reward->card_name
        ]);
    }

    // Toggle save reward
    public function toggleSave(Request $request)
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        $rewardId = $request->input('reward_id');

        if ($user->favoriteRewards()->where('card_id', $rewardId)->exists()) {
            $user->favoriteRewards()->detach($rewardId);
            return response()->json(['saved' => false]);
        } else {
            $user->favoriteRewards()->attach($rewardId);
            return response()->json(['saved' => true]);
        }
    }

    // Get saved rewards
    public function getSavedRewards()
    {
        $user = Auth::user();
        if (!$user) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return response()->json([
            'savedRewardIds' => $user->favoriteRewards()->pluck('card_id')->toArray()
        ]);
    }

    // Admin: Show create form
    public function create()
    {
        if (!Gate::allows('admin')) {
            abort(403);
        }
        return view('rewards.create');
    }

    // Admin: Store new reward
    public function store(Request $request)
    {
        if (!Gate::allows('admin')) {
            abort(403);
        }

        $validated = $request->validate([
            'card_name' => 'required|string|max:255',
            'points_amount' => 'required|integer|min:0',
            'card_description' => 'required|string',
            'card_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('card_image')) {
            $path = $request->file('card_image')->store('rewards', 'public');
            $validated['card_image'] = $path;
        }

        Reward::create($validated);

        return redirect()->route('rewards.index')->with('success', 'Reward created successfully!');
    }

    // Admin: Show edit form
    public function edit(Reward $reward)
    {
        if (!Gate::allows('admin')) {
            abort(403);
        }
        return view('rewards.edit', compact('reward'));
    }

    // Admin: Update reward
    public function update(Request $request, Reward $reward)
    {
        if (!Gate::allows('admin')) {
            abort(403);
        }

        $validated = $request->validate([
            'card_name' => 'required|string|max:255',
            'points_amount' => 'required|integer|min:0',
            'card_description' => 'required|string',
            'card_image' => 'nullable|image|mimes:jpeg,png,jpg,gif|max:2048',
        ]);

        if ($request->hasFile('card_image')) {
            if ($reward->card_image) {
                Storage::disk('public')->delete($reward->card_image);
            }
            $path = $request->file('card_image')->store('rewards', 'public');
            $validated['card_image'] = $path;
        }

        $reward->update($validated);

        return redirect()->route('rewards.index')->with('success', 'Reward updated successfully!');
    }

    // Admin: Delete reward
    public function destroy(Reward $reward)
    {
        if (!Gate::allows('admin')) {
            abort(403);
        }

        if ($reward->card_image) {
            Storage::disk('public')->delete($reward->card_image);
        }

        $reward->delete();

        return redirect()->route('rewards.index')->with('success', 'Reward deleted successfully!');
    }
}