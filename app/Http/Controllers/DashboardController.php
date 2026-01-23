<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user = auth()->user()->load(['tribe', 'head', 'body', 'arm', 'leg']);
        return view('dashboard', compact('user'));
    }
}
