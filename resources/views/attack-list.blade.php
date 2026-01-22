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
                    <div class="bg-white rounded-lg shadow-md p-6 flex flex-col">
                        <div class="mb-4">
                            <div class="text-xl font-bold mb-2 text-gray-800">{{ $target->username }}</div>
                            <div class="text-gray-600 text-sm">Tribe: {{ optional($target->tribe)->name ?? '-' }}</div>
                            <div class="text-gray-600 text-sm mt-1">💰 Gold: {{ $target->gold }}</div>
                            <div class="text-gray-600 text-sm">⚔️ Troops: {{ $target->troops }}</div>
                        </div>
                        <form method="POST" action="{{ route('attack.user', $target->id) }}" class="mt-auto">
                            @csrf
                            <button type="submit" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold px-4 py-3 rounded-lg transition duration-200">
                                ⚔️ Attack
                            </button>
                        </form>
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
</body>

</html>
