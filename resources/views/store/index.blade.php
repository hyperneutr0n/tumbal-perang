<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Store - Tumbal Perang</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-50">
    <div class="min-h-screen">
        <!-- Navigation -->
        <nav class="bg-white shadow-sm border-b">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4 flex justify-between items-center">
                <div>
                    <h2 class="text-2xl font-bold text-gray-800">Tumbal Perang - Store</h2>
                    <div class="flex gap-4 mt-2 text-sm text-gray-600">
                        <span>👤 {{ auth()->user()->username }}</span>
                        <span>🏹 Tribe: <span id="tribe">{{ optional(auth()->user()->tribe)->name ?? 'None' }}</span></span>
                        <span>💰 Gold: <span id="gold">{{ auth()->user()->gold }}</span></span>
                        <span>⚔️ Troops: <span id="troops">{{ auth()->user()->troops }}</span></span>
                    </div>
                </div>
                <div class="flex gap-4 items-center">
                    <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800">Dashboard</a>
                    <a href="{{ route('tribe-base.index') }}" class="text-blue-600 hover:text-blue-800">Tribe Base</a>
                    <form method="POST" action="{{ route('logout') }}" class="inline">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-gray-900">Logout</button>
                    </form>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
            <h1 class="text-4xl font-bold text-gray-800 mb-8">Building Store</h1>

            <div id="message" class="mb-4 hidden"></div>

            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @foreach($buildings as $building)
                <div class="bg-white rounded-lg shadow-md p-6 hover:shadow-lg transition">
                    <div class="flex justify-between items-start mb-4">
                        <h3 class="text-xl font-bold text-gray-800">{{ $building->name }}</h3>
                        @if($building->is_unique)
                            <span class="bg-purple-100 text-purple-800 text-xs px-2 py-1 rounded">Unique</span>
                        @endif
                    </div>
                    
                    <p class="text-gray-600 text-sm mb-4">{{ $building->description }}</p>
                    
                    <!-- Building Effects -->
                    @if($building->buildingEffects->count() > 0)
                    <div class="bg-blue-50 rounded p-3 mb-4">
                        <h4 class="font-semibold text-sm text-blue-900 mb-2">Effects:</h4>
                        @foreach($building->buildingEffects as $effect)
                        <div class="text-sm text-blue-800">
                            ✨ {{ $effect->description }}: +{{ $effect->value }}
                        </div>
                        @endforeach
                    </div>
                    @endif
                    
                    <!-- Price and Purchase Button -->
                    <div class="flex justify-between items-center mt-4 pt-4 border-t">
                        <div class="text-lg font-bold text-yellow-600">
                            💰 {{ $building->price }} Gold
                        </div>
                        
                        @if($building->price == 0)
                            <button disabled class="bg-gray-400 text-white px-4 py-2 rounded cursor-not-allowed">
                                Free Building
                            </button>
                        @elseif($building->is_unique && in_array($building->id, $userBuildings))
                            <button disabled class="bg-gray-400 text-white px-4 py-2 rounded cursor-not-allowed">
                                Owned
                            </button>
                        @else
                            <button 
                                onclick="purchaseBuilding({{ $building->id }}, '{{ $building->name }}', {{ $building->price }})"
                                class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded transition">
                                Buy
                            </button>
                        @endif
                    </div>
                </div>
                @endforeach
            </div>
        </div>
    </div>

    <script>
        async function purchaseBuilding(buildingId, buildingName, price) {
            const currentGold = parseInt(document.getElementById('gold').textContent);
            
            if (currentGold < price) {
                showMessage('Not enough gold!', 'error');
                return;
            }
            
            if (!confirm(`Purchase ${buildingName} for ${price} gold?`)) {
                return;
            }
            
            try {
                const response = await fetch(`/store/purchase/${buildingId}`, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });
                
                const data = await response.json();
                
                if (data.success) {
                    document.getElementById('gold').textContent = data.gold;
                    showMessage(data.message, 'success');
                    
                    // Reload page after 1 second to update owned buildings
                    setTimeout(() => location.reload(), 1000);
                } else {
                    showMessage(data.message, 'error');
                }
            } catch (error) {
                console.error('Error purchasing building:', error);
                showMessage('Purchase failed!', 'error');
            }
        }
        
        function showMessage(message, type) {
            const messageEl = document.getElementById('message');
            messageEl.textContent = message;
            messageEl.className = `mb-4 p-4 rounded ${type === 'success' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800'}`;
            messageEl.classList.remove('hidden');
            
            setTimeout(() => {
                messageEl.classList.add('hidden');
            }, 3000);
        }

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
