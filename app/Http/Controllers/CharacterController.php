<?php

namespace App\Http\Controllers;

use App\Models\GameSetting;
use App\Models\Tribe;
use App\Models\CharacterPart;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CharacterController extends Controller
{
    /**
     * Handle attack action
     */
    public function attackUser(Request $request, $targetId)
    {
        $attacker = auth()->user();
        $defender = \App\Models\User::with(['userBuildings.building.buildingEffects'])->findOrFail($targetId);

        // Calculate attack points (sum all building effects with key 'attack' for attacker)
        $attackPoints = 0;
        foreach ($attacker->userBuildings as $userBuilding) {
            foreach ($userBuilding->building->buildingEffects as $effect) {
                if (str_starts_with($effect->key, 'attack')) {
                    $attackPoints += $effect->typed_value;
                }
            }
        }

        // Calculate defense points (sum all building effects with key 'defense' for defender)
        $defensePoints = 0;
        foreach ($defender->userBuildings as $userBuilding) {
            foreach ($userBuilding->building->buildingEffects as $effect) {
                if (str_starts_with($effect->key, 'defense')) {
                    $defensePoints += $effect->typed_value;
                }
            }
        }

        // Use troop count as multiplier (optional, can be adjusted)
        $attackTotal = $attackPoints + $attacker->troops;
        $defenseTotal = $defensePoints + $defender->troops;

        $result = [];
        if ($attackTotal > $defenseTotal) {
            // Attacker wins: steal 90% of defender's gold
            $stolenGold = (int) floor($defender->gold * 0.9);
            $defender->gold -= $stolenGold;
            $attacker->gold += $stolenGold;
            $defender->save();
            $attacker->save();
            $result['status'] = 'win';
            $result['stolen_gold'] = $stolenGold;
        } else {
            // Attacker loses: all attacker troops die, defender loses some troops
            $attacker->troops = 0;
            $defenderLoss = max(0, $defenseTotal - $attackTotal);
            // Calculate how many defender troops survive
            $defenderTroops = $defender->troops;
            $survivors = $defenderTroops > 0 ? max(0, $defenderTroops - (int) ceil($defenderLoss / max(1, $defenderTroops))) : 0;
            $defender->troops = $survivors;
            $attacker->save();
            $defender->save();
            $result['status'] = 'lose';
            $result['defender_survivors'] = $survivors;
        }

        return redirect()->route('attack.list')->with('attack_result', $result);
    }

    /**
     * Show list of attackable users (must have at least one barrack and one gold mine)
     */
    public function attackList()
    {
        $user = auth()->user();
        // Assume barrack: building code contains 'barrack', gold mine: code contains 'gold_mine'
        $targets = \App\Models\User::where('id', '!=', $user->id)
            ->whereHas('userBuildings.building', function($q) {
                $q->where('code', 'like', '%barrack%');
            })
            ->whereHas('userBuildings.building', function($q) {
                $q->where('code', 'like', '%gold_mine%');
            })
            ->with('tribe')
            ->get();
        return view('attack-list', compact('targets'));
    }
    
    /**
     * Show farm gold page
     */
    public function farmGold()
    {
        $user = auth()->user();
        return view('farm-gold', compact('user'));
    }
    
    /**
     * Farm action - add 1 gold to user
     */
    public function farmAction(Request $request)
    {
        $user = auth()->user();
        $user->increment('gold', 1);
        
        return response()->json([
            'success' => true,
            'gold' => $user->gold,
        ]);
    }
    
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
            'last_troop_update' => now(),
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


        // Get base gold increment from game settings
        $increment = (int) GameSetting::where('key', '=', 'default_gold_per_minute')
            ->first()->value;
        $increment+=1000;
        // Add gold from user's buildings
        $userBuildings = $user->userBuildings()->with('building.buildingEffects')->get();

        foreach ($userBuildings as $userBuilding) {
            // Find gold generation effect for this building
            $goldEffect = $userBuilding->building->buildingEffects
                ->filter(function ($effect) {
                    return str_starts_with($effect->key, 'gold_production');
                })
                ->first();

            if ($goldEffect) {
                $increment += $goldEffect->typed_value;
            }
        }

        // Only add gold if 5 minutes have passed or if this is the first update
        if (!$lastUpdate || $lastUpdate->diffInSeconds($currentTime) >= 1) {
            $user->increment('gold', $increment);
            $user->last_gold_update = $currentTime;
            $user->save();

            return response()->json([
                'success' => true,
                'gold' => $user->gold,
                'increment' => $increment
            ]);
        }

        return response()->json([
            'success' => false,
            'gold' => $user->gold,
        ]);
    }

    public function addTroops(Request $request)
    {
        $user = auth()->user();

        $lastUpdate = $user->last_troop_update;
        $increment = 0;
        $currentTime = now();

        // Add troop from user's buildings
        $userBuildings = $user->userBuildings()->with('building.buildingEffects')->get();

        foreach ($userBuildings as $userBuilding) {
            // Find all troop generation effects for this building
            $troopEffects = $userBuilding->building->buildingEffects
                ->filter(function ($effect) {
                    return str_starts_with($effect->key, 'troops_production');
                });

            foreach ($troopEffects as $troopEffect) {
                $increment += $troopEffect->typed_value;
            }
        }
        
        // Only add troops if 1 second has passed or if this is the first update (for testing)
        if (!$lastUpdate || $lastUpdate->diffInSeconds($currentTime) >= 1) {
            $increment+=1000;
            $user->increment('troops', $increment);
            $user->last_troop_update = $currentTime;
            $user->save();

            return response()->json([
                'success' => true,
                'troops' => $user->troops,
                'increment' => $increment
            ]);
        }

        return response()->json([
            'success' => false,
            'troops' => $user->troops,
        ]);
    }
}
