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
            <a href="{{ route('attack.list') }}" class="text-blue-600 hover:text-blue-800">⚔️ Attack</a>
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
      <div class="bg-white rounded-lg shadow-md p-8">
        <div class="grid grid-cols-1 md:grid-cols-2 gap-8 items-start">
          <!-- Left Side - Character Sprite -->
          <div class="flex flex-col items-center">
            <!-- Character Sprite - Large Icon Placeholder -->
            <div
              class="w-64 h-64 flex items-center justify-center bg-gradient-to-b from-blue-100 to-blue-50 rounded-lg mb-4">
              <div class="text-9xl">👤</div>
            </div>

            <!-- Edit Appearance Link -->
            <a href="#" class="text-blue-600 hover:text-blue-800 underline text-sm">
              Edit Profile
            </a>
          </div>

          <!-- Right Side - User Stats and Attack Button -->
          <div class="flex flex-col justify-between h-full space-y-6">
            <!-- User Stats -->
            <div class="space-y-4">
              <!-- Username -->
              <div class="border-b pb-3">
                <p class="text-gray-600 text-sm">Username</p>
                <p class="text-xl font-semibold text-gray-800">{{ auth()->user()->username }}</p>
              </div>

              <!-- Tribe Name -->
              <div class="border-b pb-3">
                <p class="text-gray-600 text-sm">Tribe</p>
                <p class="text-xl font-semibold text-gray-800">{{ optional(auth()->user()->tribe)->name ?? 'No Tribe' }}
                </p>
              </div>

              <!-- Gold -->
              <div class="border-b pb-3">
                <p class="text-gray-600 text-sm">Gold</p>
                <p class="text-xl font-semibold text-yellow-600" id="gold">{{ auth()->user()->gold ?? 0 }}</p>
              </div>

              <!-- Troops -->
              <div class="border-b pb-3">
                <p class="text-gray-600 text-sm">Troops</p>
                <p class="text-xl font-semibold text-gray-800" id="troops">{{ auth()->user()->troops ?? 0 }}</p>
              </div>
            </div>

            <!-- Attack Button -->
            <div class="mt-8">
              <a href="{{ route('attack.list') }}"
                class="w-full block text-center bg-red-600 hover:bg-red-700 text-white font-bold py-4 px-6 rounded-lg text-xl transition duration-200 shadow-lg hover:shadow-xl">
                ATTACK
              </a>
            </div>
          </div>
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
        },5 * 1000);

        // Check for troops update every second (for testing)
        setInterval(async () => {
          try {
            const response = await fetch('{{ route('add.troops') }}', {
              method: 'POST',
              headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
              }
            });

            const data = await response.json();
            if (data.success) {
              document.querySelectorAll('#troops').forEach(el => el.textContent = data.troops);
            }
          } catch (error) {
            console.error('Error adding troops:', error);
          }
        }, 5 * 1000);
      </script>
    </div>
  </div>
</body>

</html>
