<!DOCTYPE html>
<html lang="en">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Edit Profile - Tumbal Perang</title>
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

    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
      <div class="mb-4">
        <a href="{{ route('dashboard') }}" class="text-blue-600 hover:text-blue-800">
          ← Back to Dashboard
        </a>
      </div>

      <div class="bg-white rounded-lg shadow-md p-8">
        <h2 class="text-3xl font-bold text-gray-800 mb-6">Edit Profile</h2>

    
        @if(session('success'))
        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
          {{ session('success') }}
        </div>
        @endif

        @if($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
          <ul>
            @foreach($errors->all() as $error)
            <li>{{ $error }}</li>
            @endforeach
          </ul>
        </div>
        @endif

        <form method="POST" action="{{ route('profile.update') }}" class="space-y-8">
          @csrf
          @method('PUT')

          <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">
            <!-- Left Side - Preview -->
            <div class="flex flex-col items-center">
              <h3 class="text-xl font-semibold mb-4 text-gray-700">Character Preview</h3>
              <div
                class="w-80 h-96 flex items-center justify-center bg-gradient-to-b from-blue-100 to-blue-50 rounded-lg p-8">
                <!-- Character Assembly -->
                <div class="relative flex flex-col items-center justify-center" style="width: 200px; height: 320px;">
        
                  <div class="flex justify-center mb-2">
                    <img id="preview-head" src="{{ $user->head ? asset($user->head->image_path) : '' }}" 
                         alt="Head" class="w-20 h-20 object-contain">
                  </div>
                  
                  <!-- Body with Arms -->
                  <div class="flex items-center justify-center gap-1 mb-2">
                    <!-- Left Arm -->
                    <img id="preview-arm-left" src="{{ $user->arm ? asset($user->arm->image_path) : '' }}" 
                         alt="Arm" class="w-16 h-20 object-contain">
                    
                    <!-- Body -->
                    <img id="preview-body" src="{{ $user->body ? asset($user->body->image_path) : '' }}" 
                         alt="Body" class="w-20 h-24 object-contain">
                    
                    <!-- Right Arm (same as left) -->
                    <img id="preview-arm-right" src="{{ $user->arm ? asset($user->arm->image_path) : '' }}" 
                         alt="Arm" class="w-16 h-20 object-contain transform scale-x-[-1]">
                  </div>
                  
                  <!-- Legs -->
                  <div class="flex justify-center">
                    <img id="preview-leg" src="{{ $user->leg ? asset($user->leg->image_path) : '' }}" 
                         alt="Legs" class="w-20 h-24 object-contain">
                  </div>
                </div>
              </div>
              <p class="mt-4 text-sm text-gray-600">Preview of your character appearance</p>
            </div>

            <!-- Right Side - Character Part Selection -->
            <div class="space-y-6">
              <!-- Head Selection -->
              <div>
                <label class="block text-lg font-semibold text-gray-700 mb-3">Head</label>
                <div class="grid grid-cols-2 gap-3">
                  @foreach($characterParts['heads'] as $head)
                  <label
                    class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition hover:bg-blue-50
                    {{ $user->head_id == $head->id ? 'border-blue-600 bg-blue-50' : 'border-gray-300' }}">
                    <input type="radio" name="head_id" value="{{ $head->id }}" class="hidden peer"
                      {{ $user->head_id == $head->id ? 'checked' : '' }}>
                    <div class="flex items-center gap-3 w-full">
                      <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center overflow-hidden">
                        @if($head->image_path)
                        <img src="{{ asset($head->image_path) }}" alt="{{ $head->name }}" class="w-full h-full object-cover">
                        @else
                        <span class="text-2xl">👤</span>
                        @endif
                      </div>
                      <div class="flex-1">
                        <p class="font-medium text-gray-800">{{ $head->name }}</p>
                        <p class="text-xs text-gray-500">{{ $head->tribe->name }}</p>
                      </div>
                      <div
                        class="absolute top-2 right-2 w-5 h-5 rounded-full border-2 peer-checked:bg-blue-600 peer-checked:border-blue-600">
                      </div>
                    </div>
                  </label>
                  @endforeach
                </div>
              </div>

              <!-- Body Selection -->
              <div>
                <label class="block text-lg font-semibold text-gray-700 mb-3">Body</label>
                <div class="grid grid-cols-2 gap-3">
                  @foreach($characterParts['bodies'] as $body)
                  <label
                    class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition hover:bg-blue-50
                    {{ $user->body_id == $body->id ? 'border-blue-600 bg-blue-50' : 'border-gray-300' }}">
                    <input type="radio" name="body_id" value="{{ $body->id }}" class="hidden peer"
                      {{ $user->body_id == $body->id ? 'checked' : '' }}>
                    <div class="flex items-center gap-3 w-full">
                      <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center overflow-hidden">
                        @if($body->image_path)
                        <img src="{{ asset($body->image_path) }}" alt="{{ $body->name }}" class="w-full h-full object-cover">
                        @else
                        <span class="text-2xl">👕</span>
                        @endif
                      </div>
                      <div class="flex-1">
                        <p class="font-medium text-gray-800">{{ $body->name }}</p>
                        <p class="text-xs text-gray-500">{{ $body->tribe->name }}</p>
                      </div>
                      <div
                        class="absolute top-2 right-2 w-5 h-5 rounded-full border-2 peer-checked:bg-blue-600 peer-checked:border-blue-600">
                      </div>
                    </div>
                  </label>
                  @endforeach
                </div>
              </div>

              <!-- Arm Selection -->
              <div>
                <label class="block text-lg font-semibold text-gray-700 mb-3">Arms</label>
                <div class="grid grid-cols-2 gap-3">
                  @foreach($characterParts['arms'] as $arm)
                  <label
                    class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition hover:bg-blue-50
                    {{ $user->arm_id == $arm->id ? 'border-blue-600 bg-blue-50' : 'border-gray-300' }}">
                    <input type="radio" name="arm_id" value="{{ $arm->id }}" class="hidden peer"
                      {{ $user->arm_id == $arm->id ? 'checked' : '' }}>
                    <div class="flex items-center gap-3 w-full">
                      <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center overflow-hidden">
                        @if($arm->image_path)
                        <img src="{{ asset($arm->image_path) }}" alt="{{ $arm->name }}" class="w-full h-full object-cover">
                        @else
                        <span class="text-2xl">💪</span>
                        @endif
                      </div>
                      <div class="flex-1">
                        <p class="font-medium text-gray-800">{{ $arm->name }}</p>
                        <p class="text-xs text-gray-500">{{ $arm->tribe->name }}</p>
                      </div>
                      <div
                        class="absolute top-2 right-2 w-5 h-5 rounded-full border-2 peer-checked:bg-blue-600 peer-checked:border-blue-600">
                      </div>
                    </div>
                  </label>
                  @endforeach
                </div>
              </div>

              <!-- Leg Selection -->
              <div>
                <label class="block text-lg font-semibold text-gray-700 mb-3">Legs</label>
                <div class="grid grid-cols-2 gap-3">
                  @foreach($characterParts['legs'] as $leg)
                  <label
                    class="relative flex items-center p-4 border-2 rounded-lg cursor-pointer transition hover:bg-blue-50
                    {{ $user->leg_id == $leg->id ? 'border-blue-600 bg-blue-50' : 'border-gray-300' }}">
                    <input type="radio" name="leg_id" value="{{ $leg->id }}" class="hidden peer"
                      {{ $user->leg_id == $leg->id ? 'checked' : '' }}>
                    <div class="flex items-center gap-3 w-full">
                      <div class="w-12 h-12 bg-gray-200 rounded flex items-center justify-center overflow-hidden">
                        @if($leg->image_path)
                        <img src="{{ asset($leg->image_path) }}" alt="{{ $leg->name }}" class="w-full h-full object-cover">
                        @else
                        <span class="text-2xl">👖</span>
                        @endif
                      </div>
                      <div class="flex-1">
                        <p class="font-medium text-gray-800">{{ $leg->name }}</p>
                        <p class="text-xs text-gray-500">{{ $leg->tribe->name }}</p>
                      </div>
                      <div
                        class="absolute top-2 right-2 w-5 h-5 rounded-full border-2 peer-checked:bg-blue-600 peer-checked:border-blue-600">
                      </div>
                    </div>
                  </label>
                  @endforeach
                </div>
              </div>

              <!-- Submit Button -->
              <div class="pt-4">
                <button type="submit"
                  class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-4 px-6 rounded-lg text-lg transition duration-200 shadow-lg hover:shadow-xl">
                  Save Changes
                </button>
              </div>
            </div>
          </div>
        </form>
      </div>
    </div>
  </div>

  <style>
    /* Custom styling for checked radio buttons */
    input[type="radio"]:checked+div .absolute {
      background-color: #2563eb;
      border-color: #2563eb;
    }

    input[type="radio"]:checked+div .absolute::after {
      content: '✓';
      color: white;
      font-size: 12px;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100%;
    }
  </style>

  <script>
    // Update character preview when selections change
    document.addEventListener('DOMContentLoaded', function() {
      const characterParts = @json($characterParts);
      
      // Function to update preview
      function updatePreview(partType, partId) {
        const allParts = [...characterParts.heads, ...characterParts.bodies, ...characterParts.arms, ...characterParts.legs];
        const selectedPart = allParts.find(p => p.id == partId);
        
        if (selectedPart && selectedPart.image_path) {
          const imagePath = '{{ asset('') }}' + selectedPart.image_path;
          
          if (partType === 'head') {
            document.getElementById('preview-head').src = imagePath;
          } else if (partType === 'body') {
            document.getElementById('preview-body').src = imagePath;
          } else if (partType === 'arm') {
            document.getElementById('preview-arm-left').src = imagePath;
            document.getElementById('preview-arm-right').src = imagePath;
          } else if (partType === 'leg') {
            document.getElementById('preview-leg').src = imagePath;
          }
        }
      }
      
      // Add event listeners to all radio buttons
      document.querySelectorAll('input[name="head_id"]').forEach(radio => {
        radio.addEventListener('change', (e) => updatePreview('head', e.target.value));
      });
      
      document.querySelectorAll('input[name="body_id"]').forEach(radio => {
        radio.addEventListener('change', (e) => updatePreview('body', e.target.value));
      });
      
      document.querySelectorAll('input[name="arm_id"]').forEach(radio => {
        radio.addEventListener('change', (e) => updatePreview('arm', e.target.value));
      });
      
      document.querySelectorAll('input[name="leg_id"]').forEach(radio => {
        radio.addEventListener('change', (e) => updatePreview('leg', e.target.value));
      });
    });
  </script>
</body>

</html>
