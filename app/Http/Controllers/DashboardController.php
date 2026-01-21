<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user()->load('tribe');
        return view('dashboard', compact('user'));
    }

    public function addGold(Request $request)
    {
        $user = auth()->user();
        $user->increment('gold', 5);
        
        return response()->json([
            'success' => true,
            'gold' => $user->gold
        ]);
    }
}
