<?php

namespace App\Http\Controllers;

use App\Models\User;
use Laravel\Socialite\Facades\Socialite;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class SocialiteController extends Controller
{
    /**
     * Redirect to OAuth provider
     */
    public function redirect($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * Handle OAuth callback
     */
    public function callback($provider)
    {
        try {
            $socialiteUser = Socialite::driver($provider)->user();
            
            // Find or create user by OAuth ID first
            $user = null;
            
            if ($provider === 'google') {
                $user = User::where('google_id', $socialiteUser->getId())->first();
            } elseif ($provider === 'github') {
                $user = User::where('github_id', $socialiteUser->getId())->first();
            }
            
            // If not found by OAuth ID, try by email
            if (!$user) {
                $user = User::where('email', $socialiteUser->getEmail())->first();
            }
            
            $isNewUser = !$user;
            
            if (!$user) {
                // Create new user - temporary, without tribe_id
                // Generate a temporary username (will be updated during character creation)
                $tempUsername = 'user_' . uniqid();
                
                $userData = [
                    'username' => $tempUsername,
                    'email' => $socialiteUser->getEmail(),
                    'password' => bcrypt('oauth_' . $socialiteUser->getId()),
                    'gold' => 0,
                    'troops' => 0,
                ];
                
                // Add provider-specific ID
                if ($provider === 'google') {
                    $userData['google_id'] = $socialiteUser->getId();
                } elseif ($provider === 'github') {
                    $userData['github_id'] = $socialiteUser->getId();
                }
                
                $user = User::create($userData);
            }
            
            // Log the user in
            Auth::login($user);
            
            // Store socialite user data in session for character creation
            Session::put('socialite_name', $socialiteUser->getName());
            
            // Always redirect to character creation if no tribe yet
            if (!$user->tribe_id) {
                return redirect(route('character.create'));
            }
            
            return redirect(route('dashboard'));
        } catch (\Exception $e) {
            return redirect(route('login'))->with('error', 'Failed to authenticate with ' . $provider . ': ' . $e->getMessage());
        }
    }
}
