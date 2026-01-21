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
            <h1 class="text-4xl font-bold text-gray-800">Dashboard</h1>
            <script>
                // Add 5 gold per second to database
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
                }, 1000);
            </script>
        </div>
    </div>
</body>
</html>
