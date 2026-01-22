<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Attack - Tumbal Perang</title>
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
                        <a href="{{ route('store.index') }}" class="text-blue-600 hover:text-blue-800">🏪 Store</a>
                        <a href="{{ route('tribe-base.index') }}" class="text-blue-600 hover:text-blue-800">🏰 Tribe Base</a>
                        <a href="{{ route('attack.list') }}" class="text-blue-600 hover:text-blue-800 font-semibold">⚔️ Attack</a>
                        <a href="{{ route('farm.gold') }}" class="text-blue-600 hover:text-blue-800">💰 Farm Gold</a>
                        <a href="{{ route('dictionary') }}" class="text-blue-600 hover:text-blue-800">📖 Dictionary</a>
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
            <h1 class="text-3xl font-bold mb-6 text-gray-800">Attack Other Players</h1>
            
            @if(session('attack_result'))
                @php $result = session('attack_result'); @endphp
                <div class="mb-6 p-4 rounded {{ $result['status'] === 'win' ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                    @if($result['status'] === 'win')
                        <strong>Attack Success!</strong> You stole {{ $result['stolen_gold'] }} gold.
                    @else
                        <strong>Attack Failed!</strong> All your troops died. Defender has {{ $result['defender_survivors'] }} troops left.
                    @endif
                </div>
            @endif
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                @forelse($targets as $target)
                    <div class="bg-white rounded-lg shadow-md p-6 flex flex-col" id="target-card-{{ $target->id }}">
                        <div class="mb-4">
                            <div class="text-xl font-bold mb-2 text-gray-800">{{ $target->username }}</div>
                            <div class="text-gray-600 text-sm">Tribe: {{ optional($target->tribe)->name ?? '-' }}</div>
                            <div class="text-gray-600 text-sm mt-1">💰 Gold: {{ $target->gold }}</div>
                            <div class="text-gray-600 text-sm">⚔️ Troops: {{ $target->troops }}</div>
                        </div>
                        <button 
                            onclick="attackTarget({{ $target->id }}, '{{ $target->username }}')" 
                            id="attack-btn-{{ $target->id }}"
                            class="w-full bg-red-600 hover:bg-red-700 text-white font-bold px-4 py-3 rounded-lg transition duration-200">
                            ⚔️ Attack
                        </button>
                        <div id="result-{{ $target->id }}" class="mt-3 hidden"></div>
                    </div>
                @empty
                    <div class="col-span-full text-center py-12">
                        <div class="text-gray-500 text-lg">No available targets to attack.</div>
                        <p class="text-gray-400 text-sm mt-2">Players must have at least one barrack and one gold mine to be attackable.</p>
                    </div>
                @endforelse
            </div>
        </div>
    </div>

    <script>
        async function attackTarget(targetId, targetName) {
            const btn = document.getElementById('attack-btn-' + targetId);
            const resultDiv = document.getElementById('result-' + targetId);
            
            // Disable button
            btn.disabled = true;
            btn.classList.add('bg-gray-400', 'cursor-not-allowed');
            btn.classList.remove('bg-red-600', 'hover:bg-red-700');
            
            // Get random terrain first (simulating backend selection)
            const terrains = ['Plains', 'Forest', 'Mountains'];
            const randomTerrain = terrains[Math.floor(Math.random() * terrains.length)];
            
            // Countdown
            for (let i = 3; i > 0; i--) {
                btn.textContent = `Attacking in ${randomTerrain} in ${i}...`;
                await new Promise(resolve => setTimeout(resolve, 1000));
            }
            
            btn.textContent = 'Attacking...';
            
            try {
                const response = await fetch('{{ url("/attack") }}/' + targetId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                });
                
                const data = await response.json();
                
                // Show result
                resultDiv.classList.remove('hidden');
                if (data.status === 'win') {
                    resultDiv.className = 'mt-3 p-3 rounded bg-green-100 text-green-800 text-sm';
                    resultDiv.innerHTML = `<strong>Victory!</strong><br>Terrain: ${data.terrain}<br>Stole ${data.stolen_gold} gold!`;
                    btn.textContent = '✓ Victory!';
                    btn.classList.add('bg-green-600');
                } else {
                    resultDiv.className = 'mt-3 p-3 rounded bg-red-100 text-red-800 text-sm';
                    resultDiv.innerHTML = `<strong>Defeat!</strong><br>Terrain: ${data.terrain}<br>All troops lost!`;
                    btn.textContent = '✗ Defeated';
                    btn.classList.add('bg-red-800');
                }
                
                // Reload page after 3 seconds
                setTimeout(() => location.reload(), 3000);
                
            } catch (error) {
                console.error('Error:', error);
                resultDiv.classList.remove('hidden');
                resultDiv.className = 'mt-3 p-3 rounded bg-red-100 text-red-800 text-sm';
                resultDiv.textContent = 'Attack failed!';
                btn.disabled = false;
                btn.classList.remove('bg-gray-400', 'cursor-not-allowed');
                btn.classList.add('bg-red-600', 'hover:bg-red-700');
                btn.textContent = '⚔️ Attack';
            }
        }
    </script>
</body>

</html>
