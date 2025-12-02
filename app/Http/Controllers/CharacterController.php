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
}
