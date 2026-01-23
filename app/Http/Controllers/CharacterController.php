<?php

namespace App\Http\Controllers;

use App\Models\GameSetting;
use App\Models\Terrain;
use App\Models\Tribe;
use App\Models\CharacterPart;
use App\Models\User;
use DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CharacterController extends Controller
{
    public function getRandomTerrain() {
        $terrain = Terrain::inRandomOrder()->first();
        return response()->json(['terrain' => $terrain->name]);
    }

    /**
     * Handle attack action
     */
    public function attackUser(Request $request, $targetId)
    {
        $attacker = auth()->user()->load(['tribe.tribeStats.statType', 'userBuildings.building.buildingEffects']);
        $defender = User::with(['tribe.tribeStats.statType', 'userBuildings.building.buildingEffects'])->findOrFail($targetId);
        $terrain = $request->terrain;

        $terrainBoost = [
            'range_attack' => 1, 
            'magic_attack' => 1, 
            'melee_attack' => 1,
            'range_defense' => 1, 
            'magic_defense' => 1, 
            'melee_defense' => 1
            ];

        switch ($terrain) {
            case 'Plains':
                $terrainBoost['melee_attack'] = 5;
                $terrainBoost['melee_defense'] = 5;
                break;
            case 'Forest':
                $terrainBoost['magic_attack'] = 5;
                $terrainBoost['magic_defense'] = 5;
                break;
            case 'Mountains':
                $terrainBoost['range_attack'] = 5;
                $terrainBoost['range_defense'] = 5;
                break;
        }

        // Calculate attack points from tribe stats (magic + range + melee attack)
        $attackPoints = 0;
        if ($attacker->tribe) {
            foreach ($attacker->tribe->tribeStats as $tribeStat) {
                if ($tribeStat->statType->category === 'attack') {
                    $attackPoints += $tribeStat->value * $terrainBoost[$tribeStat->statType->code];
                }
            }
        }
        
        // Add building effects with key 'attack'
        foreach ($attacker->userBuildings as $userBuilding) {
            foreach ($userBuilding->building->buildingEffects as $effect) {
                if (str_starts_with($effect->key, 'attack')) {
                    $attackPoints += $effect->typed_value;
                }
            }
        }

        // Calculate defense points from tribe stats (magic + range + melee defense)
        $defensePoints = 0;
        if ($defender->tribe) {
            foreach ($defender->tribe->tribeStats as $tribeStat) {
                if ($tribeStat->statType->category === 'defense') {
                    $defensePoints += $tribeStat->value * $terrainBoost[$tribeStat->statType->code];
                }
            }
        }
        
        // Add building effects with key 'defense'
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
            $result['terrain'] = $terrain;
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
            $result['terrain'] = $terrain;
        }

        return response()->json($result);
    }

    
    public function attackList()
    {
        $user = auth()->user();
        //cek ada barrack dan gold mine?
        $targets = User::where('id', '!=', $user->id)
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
    

    public function farmGold()
    {
        $user = auth()->user();
        return view('farm-gold', compact('user'));
    }
    
  //autocliiiiiiiiiiiiiiiick
    public function farmAction(Request $request)
    {
        $user = auth()->user();
        $user->increment('gold', 1);
        
        return response()->json([
            'success' => true,
            'gold' => $user->gold,
        ]);
    }
    

    public function dictionary()
    {
        $tribes = Tribe::with(['tribeStats.statType'])->get();
        return view('dictionary', compact('tribes'));
    }
    

    public function create()
    {
        $tribes = Tribe::all();
        return view('character.create', compact('tribes'));
    }


    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'tribe_id' => 'required|exists:tribes,id',
        ]);

        $user = Auth::user();

        $defaultParts = CharacterPart::where('tribe_id', $validated['tribe_id'])
            ->where('is_default', true)
            ->get()
            ->keyBy('part_type');

    
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

    
    public function addGold(Request $request)
    {
        $user = auth()->user();

        $lastUpdate = $user->last_gold_update;
        $currentTime = now();

        $increment = (int) GameSetting::where('key', '=', 'default_gold_per_minute')
            ->first()->value;
        $increment+=1000; //sek blm diubah
        $userBuildings = $user->userBuildings()->with('building.buildingEffects')->get();

        foreach ($userBuildings as $userBuilding) {
            
            $goldEffect = $userBuilding->building->buildingEffects
                ->filter(function ($effect) {
                    return str_starts_with($effect->key, 'gold_production');
                })
                ->first();

            if ($goldEffect) {
                $increment += $goldEffect->typed_value;
            }
        }

        if (!$lastUpdate || $lastUpdate->diffInSeconds($currentTime) >= 1) { //hrs e 5 or 5 * 60
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

        $userBuildings = $user->userBuildings()->with('building.buildingEffects')->get();

        foreach ($userBuildings as $userBuilding) {
      
            $troopEffects = $userBuilding->building->buildingEffects
                ->filter(function ($effect) {
                    return str_starts_with($effect->key, 'troops_production');
                });

            foreach ($troopEffects as $troopEffect) {
                $increment += $troopEffect->typed_value;
            }
        }
        
        if (!$lastUpdate || $lastUpdate->diffInSeconds($currentTime) >= 60) {
         
            if ($increment > 0) {
                $user->increment('troops', $increment);
                $user->last_troop_update = $currentTime;
                $user->save();

                return response()->json([
                    'success' => true,
                    'troops' => $user->troops,
                    'increment' => $increment
                ]);
            }
        }

        return response()->json([
            'success' => false,
            'troops' => $user->troops,
        ]);
    }
}
