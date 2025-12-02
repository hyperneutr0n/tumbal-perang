<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Character - Tumbal Perang</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-purple-900 to-indigo-900 min-h-screen">
    <div class="flex items-center justify-center min-h-screen px-4 py-8">
        <div class="bg-white rounded-lg shadow-2xl w-full max-w-2xl p-8">
            <h1 class="text-3xl font-bold text-center mb-2 text-gray-800">Welcome to Tumbal Perang</h1>
            <p class="text-center text-gray-600 mb-8">Create your character to get started</p>

            <form method="POST" action="{{ route('character.store') }}" class="space-y-6">
                @csrf

                <!-- Username/Nickname -->
                <div>
                    <label for="username" class="block text-sm font-medium text-gray-700 mb-2">
                        Nickname
                    </label>
                    <input
                        type="text"
                        id="username"
                        name="username"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-purple-500 @error('username') border-red-500 @enderror"
                        placeholder="Enter your nickname"
                        value="{{ old('username') }}"
                        required
                    >
                    @error('username')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tribe Selection -->
                <div>
                    <label for="tribe_id" class="block text-sm font-medium text-gray-700 mb-2">
                        Select Tribe
                    </label>
                    <select
                        id="tribe_id"
                        name="tribe_id"
                        class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:border-purple-500 @error('tribe_id') border-red-500 @enderror"
                        required
                        onchange="updateTribeDescription()"
                    >
                        <option value="">Choose a tribe...</option>
                        @foreach($tribes as $tribe)
                            <option value="{{ $tribe->id }}" data-description="{{ $tribe->description }}">
                                {{ $tribe->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('tribe_id')
                        <p class="text-red-500 text-sm mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <!-- Tribe Description -->
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p id="tribe-description" class="text-sm text-gray-600">
                        Select a tribe to see its description
                    </p>
                </div>

                <!-- Starting Resources Info -->
                <div class="bg-blue-50 p-4 rounded-lg border border-blue-200">
                    <h3 class="font-semibold text-blue-900 mb-2">Starting Resources</h3>
                    <ul class="text-sm text-blue-800 space-y-1">
                        <li>💰 Gold: <span class="font-bold">100</span></li>
                        <li>⚔️ Troops: <span class="font-bold">100</span></li>
                    </ul>
                </div>

                <!-- Submit Button -->
                <button
                    type="submit"
                    class="w-full bg-purple-600 hover:bg-purple-700 text-white font-semibold py-3 px-4 rounded-lg transition duration-200"
                >
                    Create Character
                </button>
            </form>
        </div>
    </div>

    <script>
        function updateTribeDescription() {
            const select = document.getElementById('tribe_id');
            const selectedOption = select.options[select.selectedIndex];
            const description = selectedOption.dataset.description || 'Select a tribe to see its description';
            document.getElementById('tribe-description').textContent = description;
        }
    </script>
</body>
</html>
