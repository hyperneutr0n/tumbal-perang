<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CharacterController;
use App\Http\Controllers\StoreController;
use App\Http\Controllers\TribeBaseController;

Route::get('/', function () {
    return redirect('dashboard');
})->name('home');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

Route::post('/logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Socialite OAuth Routes
Route::get('/auth/{provider}/redirect', [SocialiteController::class, 'redirect'])->name('socialite.redirect');
Route::get('/auth/{provider}/callback', [SocialiteController::class, 'callback'])->name('socialite.callback');

// Protected Routes (require authentication)
Route::middleware('auth')->group(function () {
        // Attack Routes
        Route::get('/attack', [CharacterController::class, 'attackList'])->name('attack.list');
        Route::get('/attack/terrain', [CharacterController::class, 'getRandomTerrain'])->name('attack.terrain');
        Route::post('/attack/{target}', [CharacterController::class, 'attackUser'])->name('attack.user');
        
        // Farm Gold Routes
        Route::get('/farm-gold', [CharacterController::class, 'farmGold'])->name('farm.gold');
        Route::post('/farm-gold/farm', [CharacterController::class, 'farmAction'])->name('farm.action');
        
        // Dictionary Routes
        Route::get('/dictionary', [CharacterController::class, 'dictionary'])->name('dictionary');
        
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/character/create', [CharacterController::class, 'create'])->name('character.create');
    Route::post('/character', [CharacterController::class, 'store'])->name('character.store');
    Route::post('/add-gold', [CharacterController::class, 'addGold'])->name('add.gold');
    Route::post('/add-troops', [CharacterController::class, 'addTroops'])->name('add.troops');
    
    // Store Routes
    Route::get('/store', [StoreController::class, 'index'])->name('store.index');
    Route::post('/store/purchase/{building}', [StoreController::class, 'purchase'])->name('store.purchase');
    
    // Tribe Base Routes
    Route::get('/tribe-base', [TribeBaseController::class, 'index'])->name('tribe-base.index');
});
