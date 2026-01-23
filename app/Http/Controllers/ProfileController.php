<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CharacterPart;
use Illuminate\Support\Facades\Auth;

class ProfileController extends Controller
{

    public function edit()
    {
        $user = Auth::user();
        
        
        $characterParts = [
            'heads' => CharacterPart::where('part_type', 'head') //load dr assets
                ->with('tribe')
                ->get(),
            'bodies' => CharacterPart::where('part_type', 'body')
                ->with('tribe')
                ->get(),
            'arms' => CharacterPart::where('part_type', 'arm')
                ->with('tribe')
                ->get(),
            'legs' => CharacterPart::where('part_type', 'leg')
                ->with('tribe')
                ->get(),
        ];

        return view('profile.edit', compact('user', 'characterParts'));
    }


    public function update(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'head_id' => 'required|exists:character_parts,id',
            'body_id' => 'required|exists:character_parts,id',
            'arm_id' => 'required|exists:character_parts,id',
            'leg_id' => 'required|exists:character_parts,id',
        ]);

        //validatae ada ngk part e di datavbaase
        $partIds = [$validated['head_id'], $validated['body_id'], $validated['arm_id'], $validated['leg_id']];
        $validParts = CharacterPart::whereIn('id', $partIds)->count();

        if ($validParts !== 4) {
            return back()->withErrors(['error' => 'Invalid character parts selected.']);
        }

        $user->update([
            'head_id' => $validated['head_id'],
            'body_id' => $validated['body_id'],
            'arm_id' => $validated['arm_id'],
            'leg_id' => $validated['leg_id'],
        ]);

        return redirect()->route('profile.edit')->with('success', 'Profile updated successfully!');
    }
}
