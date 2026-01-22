<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Farm Gold - Tumbal Perang</title>
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
                        <a href="{{ route('attack.list') }}" class="text-blue-600 hover:text-blue-800">⚔️ Attack</a>
                        <a href="{{ route('farm.gold') }}" class="text-blue-600 hover:text-blue-800 font-semibold">💰 Farm Gold</a>
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
            <div class="max-w-2xl mx-auto">
                <h1 class="text-3xl font-bold mb-8 text-gray-800 text-center">💰 Farm Gold</h1>
                
                <!-- Gold Display -->
                <div class="bg-white rounded-lg shadow-lg p-8 mb-8 text-center">
                    <p class="text-gray-600 text-lg mb-2">Your Gold</p>
                    <p class="text-6xl font-bold text-yellow-600" id="gold-display">{{ $user->gold }}</p>
                </div>

                <!-- Farm Button -->
                <div class="text-center">
                    <button 
                        onclick="farmGold()"
                        class="bg-green-600 hover:bg-green-700 text-white font-bold py-6 px-12 rounded-lg text-2xl transition duration-200 shadow-lg hover:shadow-xl transform hover:scale-105">
                        🌾 FARM
                    </button>
                </div>

                <!-- Message Display -->
                <div id="message" class="mt-6 text-center text-green-600 font-semibold hidden"></div>
            </div>
        </div>
    </div>

    <script>
        async function farmGold() {
            try {
                const response = await fetch('{{ route('farm.action') }}', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    }
                });

                const data = await response.json();
                if (data.success) {
                    // Update gold display
                    document.getElementById('gold-display').textContent = data.gold;
                    
                    // Show success message
                    const messageEl = document.getElementById('message');
                    messageEl.textContent = '+1 Gold earned!';
                    messageEl.classList.remove('hidden');
                    
                    // Hide message after 1 second
                    setTimeout(() => {
                        messageEl.classList.add('hidden');
                    }, 1000);
                }
            } catch (error) {
                console.error('Error farming gold:', error);
            }
        }
    </script>
</body>

</html>
