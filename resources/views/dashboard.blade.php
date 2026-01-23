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
            <a href="{{ route('store.index') }}" class="text-blue-600 hover:text-blue-800">Store</a>
            <a href="{{ route('tribe-base.index') }}" class="text-blue-600 hover:text-blue-800">Tribe Base</a>
            <a href="{{ route('attack.list') }}" class="text-blue-600 hover:text-blue-800">Attack</a>
            <a href="{{ route('farm.gold') }}" class="text-blue-600 hover:text-blue-800">Farm Gold</a>
            <a href="{{ route('dictionary') }}" class="text-blue-600 hover:text-blue-800">Dictionary</a>
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
            <!-- Character Sprite - Assembled Character -->
            <div
              class="w-64 h-80 flex items-center justify-center bg-gradient-to-b from-blue-100 to-blue-50 rounded-lg mb-4 p-6">
              <div class="relative flex flex-col items-center justify-center" style="width: 160px; height: 256px;">
                <!-- Head -->
                <div class="flex justify-center mb-1">
                  @if(auth()->user()->head && auth()->user()->head->image_path)
                  <img src="{{ asset(auth()->user()->head->image_path) }}" alt="Head" class="w-16 h-16 object-contain">
                  @else
                  <div class="w-16 h-16 flex items-center justify-center text-4xl">👤</div>
                  @endif
                </div>
                
                <!-- Body with Arms -->
                <div class="flex items-center justify-center gap-1 mb-1">
                  <!-- Left Arm -->
                  @if(auth()->user()->arm && auth()->user()->arm->image_path)
                  <img src="{{ asset(auth()->user()->arm->image_path) }}" alt="Arm" class="w-12 h-16 object-contain">
                  @else
                  <div class="w-12 h-16"></div>
                  @endif
                  
                  <!-- Body -->
                  @if(auth()->user()->body && auth()->user()->body->image_path)
                  <img src="{{ asset(auth()->user()->body->image_path) }}" alt="Body" class="w-16 h-20 object-contain">
                  @else
                  <div class="w-16 h-20 flex items-center justify-center text-4xl">👕</div>
                  @endif
                  
                  <!-- Right Arm (mirrored) -->
                  @if(auth()->user()->arm && auth()->user()->arm->image_path)
                  <img src="{{ asset(auth()->user()->arm->image_path) }}" alt="Arm" class="w-12 h-16 object-contain transform scale-x-[-1]">
                  @else
                  <div class="w-12 h-16"></div>
                  @endif
                </div>
                
                <!-- Legs -->
                <div class="flex justify-center">
                  @if(auth()->user()->leg && auth()->user()->leg->image_path)
                  <img src="{{ asset(auth()->user()->leg->image_path) }}" alt="Legs" class="w-16 h-20 object-contain">
                  @else
                  <div class="w-16 h-20 flex items-center justify-center text-4xl">👖</div>
                  @endif
                </div>
              </div>
            </div>

            <!-- Edit Appearance Link -->
            <a href="{{ route('profile.edit') }}" class="text-blue-600 hover:text-blue-800 underline text-sm">
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
        }, 60 * 1000); // Changed from 5 seconds to 60 seconds
      </script>
    </div>
  </div>
</body>

</html>
