<?php

namespace App\Http\Controllers;

use App\Models\User;
use Hash;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SocialiteController extends Controller
{
    
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }


    public function callback($provider)
    {
        try {
            $socialiteUser = Socialite::driver($provider)->user();
            

            $user = null;
            
            if ($provider === 'google') {
                $user = User::where('google_id', $socialiteUser->getId())->first();
            } elseif ($provider === 'github') {
                $user = User::where('github_id', $socialiteUser->getId())->first();
            }
        
            if (!$user) {
                $user = User::where('email', $socialiteUser->getEmail())->first();
            }
            
            $isNewUser = !$user;
            
            if (!$user) {
        
                $tempUsername = 'user_' . uniqid();
                
                $userData = [
                    'username' => $tempUsername,
                    'email' => $socialiteUser->getEmail(),
                    'password' => Hash::make('oauth_' . $socialiteUser->getId()),
                    'gold' => 0,
                    'troops' => 0,
                ];
                
        
                if ($provider === 'google') {
                    $userData['google_id'] = $socialiteUser->getId();
                } elseif ($provider === 'github') {
                    $userData['github_id'] = $socialiteUser->getId();
                }
                
                $user = User::create($userData);
            }
            
            // Log the user in
            Auth::login($user, true);
            
            // Regenerate session to prevent fixation and ensure auth persists
            request()->session()->regenerate();
            
            Session::put('socialite_name', $socialiteUser->getName());
            

            if (!$user->tribe_id) {
                return redirect(route('character.create'));
            }
            
            return redirect(route('dashboard'));
        } catch (\Exception $e) {
            return redirect(route('login'))->with('error', 'Failed to authenticate with ' . $provider . ': ' . $e->getMessage());
        }
    }
}
