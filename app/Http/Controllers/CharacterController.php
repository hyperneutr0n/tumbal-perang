<?php

namespace App\Http\Controllers;

use App\Models\Tribe;
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
        
        // Update user with username, tribe, and starting resources
        $user->update([
            'username' => $validated['username'],
            'tribe_id' => $validated['tribe_id'],
            'gold' => 100,
            'troops' => 100,
        ]);

        return redirect()->route('dashboard');
    }

    /**
     * Add gold to the authenticated user (5 gold every 5 minutes)
     */
    public function addGold(Request $request)
    {
        $user = auth()->user();
        
        // Check if 5 minutes have passed since last gold update using session
        $lastUpdate = session('last_gold_update');
        $currentTime = now();
        
        // Only add gold if 5 minutes have passed or if this is the first update
        if (!$lastUpdate || $currentTime->diffInMinutes($lastUpdate) >= 5) {
            $user->increment('gold', 5);
            session(['last_gold_update' => $currentTime]);
        }
        
        return response()->json([
            'success' => true,
            'gold' => $user->gold
        ]);
    }
}
