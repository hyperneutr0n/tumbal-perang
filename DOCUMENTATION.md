# Tumbal Perang - Authentication with Google OAuth

## Complete Tutorial: Building from Scratch

This document provides a step-by-step guide to setting up Google OAuth authentication with character creation for the Tumbal Perang game.

---

## Table of Contents

1. [Project Setup](#project-setup)
2. [Google OAuth Configuration](#google-oauth-configuration)
3. [Database Setup](#database-setup)
4. [Models](#models)
5. [Controllers](#controllers)
6. [Routes](#routes)
7. [Views](#views)
8. [Testing the Flow](#testing-the-flow)
9. [Troubleshooting](#troubleshooting)

---

## Project Setup

### Prerequisites

- PHP 8.2+
- Composer
- Node.js & npm
- Laragon (or any local development environment)
- SQLite or MySQL

### Initial Installation

```bash
# Create a new Laravel project with Livewire starter kit
composer create-project laravel/livewire-starter-kit tumbal-perang
cd tumbal-perang

# Install Socialite for OAuth
composer require laravel/socialite

# Install npm dependencies
npm install
```

### Environment Configuration

Update your `.env` file:

```env
APP_NAME=Laravel
APP_ENV=local
APP_KEY=base64:YOUR_KEY_HERE
APP_DEBUG=true
APP_URL=http://localhost:8000

# Database (SQLite)
DB_CONNECTION=sqlite

# Session
SESSION_DRIVER=database
SESSION_LIFETIME=120

# Google OAuth
GOOGLE_CLIENT_ID=your_client_id.apps.googleusercontent.com
GOOGLE_CLIENT_SECRET=your_client_secret
GOOGLE_REDIRECT_URI=http://localhost:8000/auth/google/callback
```

---

## Google OAuth Configuration

### Step 1: Create Google OAuth Credentials

1. Go to [Google Cloud Console](https://console.cloud.google.com/)
2. Create a new project (name it "Tumbal Perang")
3. Go to **APIs & Services** → **Credentials**
4. Click **Create Credentials** → **OAuth 2.0 Client IDs**
5. Choose **Web application**
6. Add authorized redirect URIs:
   - `http://localhost:8000/auth/google/callback` (local development)
   - `https://yourdomain.com/auth/google/callback` (production)
7. Copy your **Client ID** and **Client Secret**

### Step 2: Configure Laravel

Update `config/services.php`:

```php
'google' => [
    'client_id' => env('GOOGLE_CLIENT_ID'),
    'client_secret' => env('GOOGLE_CLIENT_SECRET'),
    'redirect' => env('GOOGLE_REDIRECT_URI', 'http://localhost:8000/auth/google/callback'),
],
```

---

## Database Setup

### Step 1: Create Migrations

The application uses SQLite with the following key tables:

#### Users Table
Located in `database/migrations/0001_01_01_000000_create_users_table.php`

```php
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('username');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->rememberToken();
    
    // OAuth IDs
    $table->string('google_id')->nullable()->unique();
    $table->string('github_id')->nullable()->unique();
    
    // Game data
    $table->foreignId('tribe_id')
        ->nullable() // Nullable for new OAuth users
        ->constrained('tribes')
        ->onDelete('restrict');
    $table->bigInteger('gold')->default(0);
    $table->bigInteger('troops')->default(0);
    
    $table->timestamps();
    $table->index('tribe_id');
});
```

#### Tribes Table
Located in `database/migrations/2025_11_18_202244_create_tribes_table.php`

```php
Schema::create('tribes', function (Blueprint $table) {
    $table->id();
    $table->string('name', 50)->unique();
    $table->text('description');
    $table->integer('troops_per_minute')->default(5);
    $table->timestamps();
});
```

### Step 2: Run Migrations

```bash
php artisan migrate
```

### Step 3: Seed Dummy Data

Create `database/seeders/TribeSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Tribe;

class TribeSeeder extends Seeder
{
    public function run(): void
    {
        $tribes = [
            [
                'name' => 'Suku Merah',
                'description' => 'Suku Merah adalah suku yang terkenal dengan keberanian dan kekuatan dalam pertempuran.',
                'troops_per_minute' => 5
            ],
            [
                'name' => 'Suku Biru',
                'description' => 'Suku Biru adalah suku yang ahli dalam strategi dan diplomasi.',
                'troops_per_minute' => 5
            ],
            [
                'name' => 'Suku Hijau',
                'description' => 'Suku Hijau adalah suku yang berhubungan dengan alam dan memiliki sumber daya melimpah.',
                'troops_per_minute' => 5
            ],
            [
                'name' => 'Suku Kuning',
                'description' => 'Suku Kuning adalah suku yang kaya dan memiliki perdagangan yang kuat.',
                'troops_per_minute' => 5
            ],
        ];

        foreach ($tribes as $tribe) {
            Tribe::create($tribe);
        }
    }
}
```

Update `database/seeders/DatabaseSeeder.php`:

```php
<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $this->call([
            TribeSeeder::class,
        ]);
    }
}
```

Run the seeder:

```bash
php artisan migrate:fresh --seed
```

---

## Models

### User Model

Location: `app/Models/User.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'username',
        'email',
        'password',
        'google_id',
        'github_id',
        'tribe_id',
        'gold',
        'troops',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function tribe()
    {
        return $this->belongsTo(Tribe::class);
    }
}
```

### Tribe Model

Location: `app/Models/Tribe.php`

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tribe extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'description',
        'troops_per_minute',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
```

---

## Controllers

### AuthController

Location: `app/Http/Controllers/AuthController.php`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    /**
     * Show the login form
     */
    public function showLogin()
    {
        return view('auth.login');
    }

    /**
     * Handle logout
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect(route('login'));
    }
}
```

### SocialiteController

Location: `app/Http/Controllers/SocialiteController.php`

**Key Flow:**
1. User clicks "Sign in with Google" → `redirect()` method
2. Google redirects back → `callback()` method
3. Create or find user in database
4. Log user in
5. Redirect to character creation (if no tribe yet) or dashboard

```php
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
            
            // Find existing user by OAuth ID
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
            
            // Create new user if doesn't exist
            if (!$user) {
                $tempUsername = 'user_' . uniqid();
                
                $userData = [
                    'username' => $tempUsername,
                    'email' => $socialiteUser->getEmail(),
                    'password' => bcrypt('oauth_' . $socialiteUser->getId()),
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
            Auth::login($user);
            
            // Store socialite user data in session
            Session::put('socialite_name', $socialiteUser->getName());
            
            // Redirect to character creation if no tribe yet
            if (!$user->tribe_id) {
                return redirect(route('character.create'));
            }
            
            return redirect(route('dashboard'));
        } catch (\Exception $e) {
            return redirect(route('login'))->with('error', 'Failed to authenticate: ' . $e->getMessage());
        }
    }
}
```

### CharacterController

Location: `app/Http/Controllers/CharacterController.php`

**Purpose:** Handle character creation/nickname and tribe selection

```php
<?php

namespace App\Http\Controllers;

use App\Models\Tribe;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CharacterController extends Controller
{
    /**
     * Show character creation page
     */
    public function create()
    {
        $tribes = Tribe::all();
        return view('character.create', compact('tribes'));
    }

    /**
     * Store character (username + tribe selection)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|max:255|unique:users',
            'tribe_id' => 'required|exists:tribes,id',
        ]);

        $user = Auth::user();
        
        // Update user with chosen username, tribe, and starting resources
        $user->update([
            'username' => $validated['username'],
            'tribe_id' => $validated['tribe_id'],
            'gold' => 100,
            'troops' => 100,
        ]);

        return redirect()->route('dashboard');
    }
}
```

### DashboardController

Location: `app/Http/Controllers/DashboardController.php`

```php
<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard');
    }
}
```

---

## Routes

Location: `routes/web.php`

```php
<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\SocialiteController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\CharacterController;

// Public routes
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Guest-only routes (not logged in)
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
});

// OAuth routes
Route::get('/auth/{provider}/redirect', [SocialiteController::class, 'redirect'])
    ->name('socialite.redirect');
Route::get('/auth/{provider}/callback', [SocialiteController::class, 'callback'])
    ->name('socialite.callback');

// Protected routes (must be logged in)
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::get('/character/create', [CharacterController::class, 'create'])->name('character.create');
    Route::post('/character', [CharacterController::class, 'store'])->name('character.store');
});
```

---

## Views

### Login Page

Location: `resources/views/auth/login.blade.php`

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Tumbal Perang</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="flex items-center justify-center min-h-screen">
        <div class="bg-white p-8 rounded-lg shadow-lg w-full max-w-md">
            <h1 class="text-3xl font-bold text-center mb-8 text-gray-800">Tumbal Perang</h1>

            <!-- Google OAuth Button -->
            <a
                href="{{ route('socialite.redirect', 'google') }}"
                class="w-full flex items-center justify-center px-4 py-3 bg-white border border-gray-300 rounded-lg hover:bg-gray-50 transition duration-200 font-semibold text-gray-700"
            >
                <svg class="w-6 h-6" viewBox="0 0 24 24" fill="currentColor">
                    <!-- Google SVG icon here -->
                </svg>
                <span class="ml-3">Sign in with Google</span>
            </a>
        </div>
    </div>
</body>
</html>
```

### Character Creation Page

Location: `resources/views/character/create.blade.php`

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Character - Tumbal Perang</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-purple-900 to-indigo-900 min-h-screen">
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        <div class="bg-white rounded-lg shadow-2xl w-full max-w-2xl p-8">
            <h1 class="text-3xl font-bold text-center mb-2 text-gray-800">Welcome to Tumbal Perang</h1>
            <p class="text-center text-gray-600 mb-8">Create your character to get started</p>

            <form method="POST" action="{{ route('character.store') }}" class="space-y-6">
                @csrf

                <!-- Username/Nickname -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        Nickname
                    </label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-purple-500 @error('username') border-red-500 @enderror"
                        placeholder="Enter your nickname"
                        value="{{ old('username') }}"
                        required
                    >
                    @error('username')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tribe Selection -->
                <div>
                    <label for="tribe_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Select Tribe
                    </label>
                    <select
                        id="tribe_id"
                        name="tribe_id"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-purple-500 @error('tribe_id') border-red-500 @enderror"
                        required
                        onchange="updateTribeDescription()"
                    >
                        <option value="">Choose a tribe...</option>
                        @foreach($tribes as $tribe)
                            <option value="{{ $tribe->id }}" data-description="{{ $tribe->description }}">
                                {{ $tribe->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('tribe_id')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tribe Description -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p id="tribe-description" class="text-sm text-gray-600">
                        Select a tribe to see its description
                    </p>
                </div>

                <!-- Starting Resources Info -->
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <h3 class="font-semibold text-blue-900 mb-2">Starting Resources</h3>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>💰 Gold: <span class="font-bold">100</span></li>
                        <li>⚔️ Troops: <span class="font-bold">100</span></li>
                    </ul>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200"
                >
                    Create Character
                </button>
            </form>
        </div>
    </div>

    <script>
        function updateTribeDescription() {
            const select = document.getElementById('tribe_id');
            const selectedOption = select.options[select.selectedIndex];
            const description = selectedOption.dataset.description || 'Select a tribe to see its description';
            document.getElementById('tribe-description').textContent = description;
        }
    </script>
</body>
</html>
```

### Dashboard Page

Location: `resources/views/dashboard.blade.php`

```blade
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Tumbal Perang</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
                <h1 class="text-2xl font-bold text-gray-800">Tumbal Perang</h1>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-gray-600 hover:text-gray-900">Logout</button>
                </form>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-4xl font-bold text-gray-800">Dashboard</h1>
        </div>
    </div>
</body>
</html>
```

---

## Testing the Flow

### Complete Workflow

1. **Start the server:**
   ```bash
   php artisan serve
   ```

2. **Visit the login page:**
   - Navigate to `http://localhost:8000/login`

3. **Click "Sign in with Google":**
   - You'll be redirected to Google's login
   - Grant permissions to the application

4. **Character Creation:**
   - Enter a nickname (username)
   - Select a tribe
   - See the description update dynamically
   - Click "Create Character"

5. **Automatic Assignment:**
   - Gold: 100
   - Troops: 100
   - Tribe: Your selection

6. **Dashboard:**
   - You should now be logged in and see the dashboard
   - Click "Logout" to exit

### Database Verification

Check the database after completing the flow:

```bash
php artisan tinker

# Inside tinker:
>>> App\Models\User::latest()->first()
>>> App\Models\User::where('tribe_id', '!=', null)->first()
```

---

## Troubleshooting

### Issue: Looping Back to Login

**Cause:** User was created but missing `username` field

**Solution:** Ensure `username` is generated with a temporary value if not provided:
```php
$tempUsername = 'user_' . uniqid();
```

### Issue: Redirect URI Mismatch

**Error:** "Redirect URI mismatch"

**Solution:** 
1. Ensure `GOOGLE_REDIRECT_URI` matches exactly what's in Google Console
2. For localhost: `http://localhost:8000/auth/google/callback`
3. For production: Use your actual domain

### Issue: Sessions Not Working

**Solution:** Ensure `SESSION_DRIVER=database` in `.env` and run migrations

### Issue: Character Not Being Saved

**Check:**
1. Validate that `tribe_id` exists in database
2. Ensure all fillable fields are in User model
3. Check validation errors: `{{ $errors->first() }}`

---

## Production Deployment

### Before Going Live

1. **Update Environment Variables:**
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://yourdomain.com
   GOOGLE_REDIRECT_URI=https://yourdomain.com/auth/google/callback
   ```

2. **Generate App Key:**
   ```bash
   php artisan key:generate
   ```

3. **Cache Configuration:**
   ```bash
   php artisan config:cache
   php artisan route:cache
   php artisan view:cache
   ```

4. **Run Migrations:**
   ```bash
   php artisan migrate --force
   ```

5. **Update Google OAuth:**
   - Add `https://yourdomain.com/auth/google/callback` to authorized redirect URIs

---

## File Structure Summary

```
app/
├── Http/Controllers/
│   ├── AuthController.php
│   ├── CharacterController.php
│   ├── DashboardController.php
│   └── SocialiteController.php
├── Models/
│   ├── Tribe.php
│   └── User.php

database/
├── migrations/
│   ├── 0001_01_01_000000_create_users_table.php
│   ├── 2025_11_18_202244_create_tribes_table.php
│   └── 2025_11_29_000000_add_oauth_to_users.php
└── seeders/
    ├── DatabaseSeeder.php
    └── TribeSeeder.php

resources/views/
├── auth/
│   └── login.blade.php
├── character/
│   └── create.blade.php
└── dashboard.blade.php

routes/
└── web.php
```

---

## Key Concepts

### Authentication Flow

```
User clicks Google → Redirect to Google → User logs in → 
Google redirects back → SocialiteController.callback() → 
Create/find user → Auth::login() → 
Check if tribe_id exists → 
  If null → Redirect to character.create
  If set → Redirect to dashboard
```

### Character Creation Flow

```
Character.create view → 
Select nickname + tribe → 
POST to character.store → 
Validate inputs → 
Update user (username, tribe_id, gold=100, troops=100) → 
Redirect to dashboard
```

### Database Schema

- **Users**: Store authentication data and game state
- **Tribes**: Store tribe information
- **Relationships**: Users belong to Tribes (one-to-many)

---

## Next Steps

After completing this setup, you can extend with:

1. **Battle System** - Add battle mechanics
2. **Building System** - Create buildings and effects
3. **Leaderboard** - Rank players by stats
4. **Real-time Updates** - Use WebSockets for live updates
5. **Mobile App** - Use API to build mobile client

---

## Support & References

- [Laravel Documentation](https://laravel.com/docs)
- [Laravel Socialite](https://laravel.com/docs/socialite)
- [Google OAuth Documentation](https://developers.google.com/identity/protocols/oauth2)
- [Tailwind CSS](https://tailwindcss.com/)

---

**Created:** November 29, 2025
**Version:** 1.0
