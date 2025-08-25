<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 px-3 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row gap-6 items-start">

            <!-- Left: Image -->
            <div class="md:w-1/2">
                <img src="{{ $equipment->photo_path }}" 
                     alt="{{ $equipment->name }}" 
                     class="w-full h-auto object-cover rounded-lg shadow-lg">
            </div>

            <!-- Right: Detail -->
            <div class="md:w-1/2 flex flex-col justify-start gap-4">
                <h1 class="text-2xl md:text-3xl font-bold">{{ $equipment->name }}</h1>
                <p class="text-gray-500 text-lg">{{ $equipment->category }}</p>
                <p class="text-gray-400">{{ $equipment->code }}</p>
                <p class="text-gray-700">Status: 
                    <span class="font-semibold">{{ ucfirst($equipment->status) }}</span>
                </p>
                <p class="text-green-600 font-semibold text-lg">Free</p>
            </div>

        </div>
    </div>
</x-app-layout>
