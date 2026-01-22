<?php

namespace App\Http\Controllers;

use App\Models\Tribe;
use App\Models\CharacterPart;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CharacterController extends Controller
{
    /**
     * Show character selection/creation page for first-time users
     */
    public function create()
    {
        $tribes = Tribe::all();
        return view('character.create', compact('tribes'));
    }

    /**
     * Store the selected character
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'tribe_id' => 'required|exists:tribes,id',
        ]);

        $user = Auth::user();

        // Get default character parts for the selected tribe
        $defaultParts = CharacterPart::where('tribe_id', $validated['tribe_id'])
            ->where('is_default', true)
            ->get()
            ->keyBy('part_type');

        // Update user with username, tribe, starting resources, and default character parts
        $user->update([
            'username' => $validated['username'],
            'tribe_id' => $validated['tribe_id'],
            'gold' => 100,
            'last_gold_update' => now(),
            'troops' => 100,
            'head_id' => $defaultParts->get('head')->id ?? null,
            'body_id' => $defaultParts->get('body')->id ?? null,
            'arm_id' => $defaultParts->get('arm')->id ?? null,
            'leg_id' => $defaultParts->get('leg')->id ?? null,
        ]);

        return redirect()->route('dashboard');
    }

    /**
     * Add gold to the authenticated user (5 gold every 5 minutes)
     */
    public function addGold(Request $request)
    {
        $user = auth()->user();

        $lastUpdate = $user->last_gold_update;
        $currentTime = now();

        // Only add gold if 5 minutes have passed or if this is the first update
        if (!$lastUpdate || $lastUpdate->diffInSeconds($currentTime) >= 1) {
            $user->increment('gold', 5);
            $user->last_gold_update = $currentTime;
            $user->save();

            return response()->json([
                'success' => true,
                'gold' => $user->gold
            ]);
        }

        return response()->json([
            'success' => false,
            'gold' => $user->gold
        ]);
    }
}
