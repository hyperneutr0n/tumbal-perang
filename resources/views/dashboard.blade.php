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
                <div class="flex items-center gap-8">
                    <h1 class="text-2xl font-bold text-gray-800">Tumbal Perang</h1>
                    <div class="flex gap-6">
                        <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800 font-semibold">Dashboard</a>
                        <a href="{{ route('store.index') }}" class="text-blue-600 hover:text-blue-800">🏪 Store</a>
                        <a href="{{ route('tribe-base.index') }}" class="text-blue-600 hover:text-blue-800">🏰 Tribe Base</a>
                    </div>
                    <div class="flex flex-col text-sm text-gray-700">
                        <span>👤 Username: <span id="username">{{ auth()->user()->username }}</span></span>
                        <span>🏹 Tribe: <span id="tribe">{{ optional(auth()->user()->tribe)->name ?? 'None' }}</span></span>
                        <span>💰 Gold: <span id="gold">{{ auth()->user()->gold }}</span></span>
                        <span>⚔️ Troops: <span id="troops">{{ auth()->user()->troops }}</span></span>
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
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Character Appearance Section -->
                <div class="lg:col-span-1">
                    <div class="bg-white rounded-lg shadow-md p-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">Character</h2>
                        
                        <!-- Character Background Placeholder -->
                        <div class="relative bg-gradient-to-b from-blue-100 to-blue-50 rounded-lg p-4 min-h-[400px] flex items-center justify-center">
                            <!-- Background placeholder - will contain tribe-specific background -->
                            <div class="absolute inset-0 bg-center bg-cover bg-no-repeat rounded-lg opacity-20" 
                                 style="background-image: url('/storage/assets/backgrounds/{{ optional(auth()->user()->tribe)->name ?? 'default' }}.png');">
                            </div>
                            
                            <!-- Character Display Area -->
                            <div class="relative z-10 flex flex-col items-center">
                                <!-- Character parts will be layered here -->
                                <div class="relative w-64 h-64 flex items-center justify-center">
                                    <!-- Placeholder for character -->
                                    <div class="text-center">
                                        <div class="text-6xl mb-4">👤</div>
                                        <p class="text-gray-600 text-sm">Character Preview</p>
                                        <p class="text-gray-500 text-xs mt-2">
                                            Tribe: {{ optional(auth()->user()->tribe)->name ?? 'None' }}
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Character Info -->
                        <div class="mt-4 space-y-2 text-sm text-gray-600">
                            <p>✨ Customize your character appearance</p>
                            <p>🎨 Parts reflect your tribe heritage</p>
                        </div>
                    </div>
                </div>

                <!-- Dashboard Stats Section -->
                <div class="lg:col-span-2">
                    <h1 class="text-4xl font-bold text-gray-800 mb-6">Dashboard</h1>
                    <!-- Additional dashboard content can go here -->
                </div>
            </div>
            
            <script>
                // Check for gold update every 30 seconds (backend handles 5-minute logic)
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
                        console.error('Error adding gold:', error);
                    }
                }, 30000);
            </script>
        </div>
    </div>
</body>
</html>
