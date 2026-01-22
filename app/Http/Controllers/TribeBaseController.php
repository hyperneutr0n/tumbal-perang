<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TribeBaseController extends Controller
{
    public function index()
    {
        $user = auth()->user();
        $userBuildings = $user->userBuildings()
            ->with('building.buildingEffects')
            ->orderBy('built_at', 'desc')
            ->get();
        
        return view('tribe-base.index', compact('userBuildings', 'user'));
    }
}
