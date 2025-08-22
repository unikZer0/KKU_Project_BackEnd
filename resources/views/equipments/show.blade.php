<x-app-layout>
    <div class="max-w-4xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <div class="bg-white rounded-lg shadow-md overflow-hidden">
            <div class="relative">
                @if($equipment->photo_path)
                    <img src="{{ asset('storage/' . $equipment->photo_path) }}"
                         alt="{{ $equipment->name }}"
                         class="w-full h-64 object-cover">
                @endif
                <span class="absolute top-2 right-2 
                    @if($equipment->status == 'available') bg-green-600
                    @elseif($equipment->status == 'maintenance') bg-yellow-600
                    @else bg-red-600 @endif
                    text-white text-sm px-2 py-1 rounded">
                    {{ ucfirst($equipment->status) }}
                </span>
            </div>
            <div class="p-6">
                <h2 class="text-2xl font-bold mb-2">{{ $equipment->name }}</h2>
                <p class="text-gray-500 mb-1">{{ $equipment->categories->name ?? '' }}</p>
                <p class="text-gray-400 mb-3">{{ $equipment->code }}</p>
                <p class="text-gray-700">Here you can add more description about this equipment, usage instructions, or borrowing rules.</p>
            </div>
        </div>
    </div>
</x-app-layout>
