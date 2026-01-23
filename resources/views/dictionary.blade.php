<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dictionary - Tumbal Perang</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>

<body class="bg-gray-100">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
                <div class="flex items-center gap-8">
                    <h1 class="text-2xl font-bold text-gray-800">Tumbal Perang</h1>
                    <div class="flex gap-6">
                        <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800">Dashboard</a>
                        <a href="{{ route('store.index') }}" class="text-blue-600 hover:text-blue-800">Store</a>
                        <a href="{{ route('tribe-base.index') }}" class="text-blue-600 hover:text-blue-800">Tribe Base</a>
                        <a href="{{ route('attack.list') }}" class="text-blue-600 hover:text-blue-800">Attack</a>
                        <a href="{{ route('farm.gold') }}" class="text-blue-600 hover:text-blue-800">Farm Gold</a>
                        <a href="{{ route('dictionary') }}" class="text-blue-600 hover:text-blue-800 font-semibold">Dictionary</a>
                    </div>
                </div>
                <form method="POST" action="{{ route('logout') }}" class="inline">
                    @csrf
                    <button type="submit" class="text-gray-600 hover:text-gray-900">Logout</button>
                </form>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-3xl font-bold mb-8 text-gray-800 text-center">📖 Tribe Dictionary</h1>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                @foreach($tribes as $tribe)
                    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
                        <!-- Tribe Header -->
                        <div class="bg-gradient-to-r from-blue-500 to-blue-600 text-white px-6 py-4">
                            <h2 class="text-2xl font-bold">{{ $tribe->name }}</h2>
                            <p class="text-blue-100 text-sm mt-1">{{ $tribe->description }}</p>
                        </div>
                        
                        <!-- Stats Section -->
                        <div class="p-6">
                            <h3 class="text-lg font-semibold mb-4 text-gray-800 border-b pb-2">⚔️ Attack Stats</h3>
                            <div class="space-y-3 mb-6">
                                @foreach($tribe->tribeStats as $stat)
                                    @if($stat->statType->category === 'attack')
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-700">
                                                @if($stat->statType->attack_type === 'magic')
                                                    🔮 Magic Attack
                                                @elseif($stat->statType->attack_type === 'range')
                                                    🏹 Range Attack
                                                @elseif($stat->statType->attack_type === 'melee')
                                                    ⚔️ Melee Attack
                                                @endif
                                            </span>
                                            <div class="flex items-center gap-3">
                                                <div class="w-32 bg-gray-200 rounded-full h-3">
                                                    <div class="bg-red-500 h-3 rounded-full" style="width: {{ $stat->value }}%"></div>
                                                </div>
                                                <span class="font-bold text-gray-800 w-12 text-right">{{ $stat->value }}</span>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            
                            <h3 class="text-lg font-semibold mb-4 text-gray-800 border-b pb-2">🛡️ Defense Stats</h3>
                            <div class="space-y-3">
                                @foreach($tribe->tribeStats as $stat)
                                    @if($stat->statType->category === 'defense')
                                        <div class="flex justify-between items-center">
                                            <span class="text-gray-700">
                                                @if($stat->statType->attack_type === 'magic')
                                                    🔮 Magic Defense
                                                @elseif($stat->statType->attack_type === 'range')
                                                    🏹 Range Defense
                                                @elseif($stat->statType->attack_type === 'melee')
                                                    ⚔️ Melee Defense
                                                @endif
                                            </span>
                                            <div class="flex items-center gap-3">
                                                <div class="w-32 bg-gray-200 rounded-full h-3">
                                                    <div class="bg-blue-500 h-3 rounded-full" style="width: {{ $stat->value }}%"></div>
                                                </div>
                                                <span class="font-bold text-gray-800 w-12 text-right">{{ $stat->value }}</span>
                                            </div>
                                        </div>
                                    @endif
                                @endforeach
                            </div>
                            
                            <!-- Troop Production -->
                            <div class="mt-6 pt-4 border-t">
                                <div class="flex justify-between items-center">
                                    <span class="text-gray-700">👥 Troops per Minute</span>
                                    <span class="font-bold text-green-600">{{ $tribe->troops_per_minute }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>
    </div>
</body>

</html>
