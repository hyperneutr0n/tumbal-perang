<?php

namespace App\Http\Controllers;

use App\Models\Building;
use App\Models\UserBuilding;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class StoreController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $buildings = Building::with('buildingEffects')->get();
        $userBuildings = $user->userBuildings()->pluck('building_id')->toArray();
        
        return view('store.index', compact('buildings', 'user', 'userBuildings'));
    }

    public function purchase(Request $request, Building $building)
    {
        $user = auth()->user();
        

        if ($user->gold < $building->price) { //ada uang ngak user nya
            return response()->json([
                'success' => false,
                'message' => 'Not enough gold!'
            ], 400);
        }
        

        if ($building->is_unique) {
            $exists = UserBuilding::where('user_id', $user->id)
                ->where('building_id', $building->id)
                ->exists();
            
            if ($exists) {
                return response()->json([
                    'success' => false,
                    'message' => 'You already own this unique building!'
                ], 400);
            }
        }
        
    
        if ($building->max_quantity) {
            $count = UserBuilding::where('user_id', $user->id)
                ->where('building_id', $building->id)
                ->count();
            
            if ($count >= $building->max_quantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Maximum quantity reached for this building!'
                ], 400);
            }
        }
        
        DB::beginTransaction();
        try {
        
            $user->decrement('gold', $building->price);
            
        
            UserBuilding::create([
                'user_id' => $user->id,
                'building_id' => $building->id,
                'built_at' => now(),
                'level' => 1,
            ]);
            
            DB::commit();
            
            return response()->json([
                'success' => true,
                'message' => $building->name . ' purchased successfully!',
                'gold' => $user->fresh()->gold
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Purchase failed: ' . $e->getMessage()
            ], 500);
        }
    }
}
