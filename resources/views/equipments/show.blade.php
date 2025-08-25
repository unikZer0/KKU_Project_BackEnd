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

                <!-- Borrow Form -->
                <div class="mt-6">
                    <form action="{{ route('borrows.store') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="equipment_id" value="{{ $equipment->id }}">

                        <div>
                            <label class="block text-sm font-medium text-gray-700">Start Borrowing At</label>
                            <input type="date" name="start_date" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>

                        <div>
                            <label class="block text-sm font-medium text-gray-700">End Borrowing At</label>
                            <input type="date" name="end_date" required
                                   class="mt-1 block w-full rounded-md border-gray-300 shadow-sm">
                        </div>

                        <button type="submit" 
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded-lg shadow">
                            Borrow
                        </button>
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
