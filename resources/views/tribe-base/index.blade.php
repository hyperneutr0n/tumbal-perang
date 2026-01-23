<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tribe Base - Tumbal Perang</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Tumbal Perang - Tribe Base</h2>
                    <div class="flex gap-4 mt-2 text-sm text-gray-600">
                        <span>👤 {{ auth()->user()->username }}</span>
                        <span>🏹 Tribe: <span id="tribe">{{ optional(auth()->user()->tribe)->name ?? 'None' }}</span></span>
                        <span>💰 Gold: <span id="gold">{{ auth()->user()->gold }}</span></span>
                        <span>⚔️ Troops: <span id="troops">{{ auth()->user()->troops }}</span></span>
                    </div>
                </div>
                <div class="flex gap-4 items-center">
                    <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800">Dashboard</a>
                    <a href="{{ route('store.index') }}" class="text-blue-600 hover:text-blue-800">Store</a>
                    <a href="{{ route('tribe-base.index') }}" class="text-blue-600 hover:text-blue-800 font-semibold">Tribe Base</a>
                    <a href="{{ route('attack.list') }}" class="text-blue-600 hover:text-blue-800">Attack</a>
                    <a href="{{ route('farm.gold') }}" class="text-blue-600 hover:text-blue-800">Farm Gold</a>
                    <a href="{{ route('dictionary') }}" class="text-blue-600 hover:text-blue-800">Dictionary</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-gray-900">Logout</button>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <div class="flex justify-between items-center mb-8">
                <h1 class="text-4xl font-bold text-gray-800">Your Buildings</h1>
                <a href="{{ route('store.index') }}" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition">
                    🏪 Visit Store
                </a>
            </div>

            @if($userBuildings->count() > 0)
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($userBuildings as $userBuilding)
                <div class="bg-white rounded-lg shadow-md p-6">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-xl font-bold text-gray-800">{{ $userBuilding->building->name }}</h3>
                        <span class="bg-blue-100 text-blue-800 text-xs px-2 py-1 rounded">Lvl {{ $userBuilding->level }}</span>
                    </div>
                    
                    <p class="text-gray-600 text-sm mb-4">{{ $userBuilding->building->description }}</p>
                    
                    <!-- Building Effects -->
                    @if($userBuilding->building->buildingEffects->count() > 0)
                    <div class="bg-green-50 rounded p-3 mb-4">
                        <h4 class="font-semibold text-sm text-green-900 mb-2">Active Effects:</h4>
                        @foreach($userBuilding->building->buildingEffects as $effect)
                        <div class="text-sm text-green-800">
                            ✨ {{ $effect->description }}: +{{ $effect->value }}
                        </div>
                        @endforeach
                    </div>
                    @endif
                    
                    <!-- Built At -->
                    <div class="text-xs text-gray-500 pt-4 border-t">
                        Built: {{ $userBuilding->built_at->diffForHumans() }}
                    </div>
                </div>
                @endforeach
            </div>

            <!-- Building Summary -->
            <div class="mt-8 bg-white rounded-lg shadow p-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-4">Building Summary</h2>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    <div class="bg-yellow-50 p-4 rounded">
                        <div class="text-yellow-800 font-semibold">Total Buildings</div>
                        <div class="text-3xl font-bold text-yellow-900">{{ $userBuildings->count() }}</div>
                    </div>
                    <div class="bg-green-50 p-4 rounded">
                        <div class="text-green-800 font-semibold">Gold Production Bonus</div>
                        <div class="text-3xl font-bold text-green-900">
                            +{{ $userBuildings->sum(function($ub) {
                                return $ub->building->buildingEffects
                                    ->filter(function($effect) {
                                        return str_contains($effect->key, 'gold_production');
                                    })
                                    ->sum('value');
                            }) }} /interval
                        </div>
                    </div>
                    <div class="bg-blue-50 p-4 rounded">
                        <div class="text-blue-800 font-semibold">Troops Production Bonus</div>
                        <div class="text-3xl font-bold text-blue-900">
                            +{{ $userBuildings->sum(function($ub) {
                                return $ub->building->buildingEffects
                                    ->filter(function($effect) {
                                        return str_contains($effect->key, 'troops_production');
                                    })
                                    ->sum('value');
                            }) }} /minute
                        </div>
                    </div>
                </div>
            </div>
            @else
            <div class="bg-white rounded-lg shadow-md p-12 text-center">
                <div class="text-6xl mb-4">🏗️</div>
                <h3 class="text-2xl font-bold text-gray-800 mb-2">No Buildings Yet</h3>
                <p class="text-gray-600 mb-6">Start building your kingdom by purchasing buildings from the store!</p>
                <a href="{{ route('store.index') }}" class="inline-block bg-blue-600 hover:bg-blue-700 text-white px-8 py-3 rounded-lg transition">
                    Visit Store
                </a>
            </div>
            @endif
        </div>
    </div>

    <script>
        // Update gold every 30 seconds
        setInterval(async () => {
            try {
                const response = await fetch('{{ route('add.gold') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                
                const data = await response.json();
                if (data.success) {
                    document.getElementById('gold').textContent = data.gold;
                }
            } catch (error) {
                console.error('Error updating gold:', error);
            }
        }, 30000);
    </script>
</body>
</html>
