<x-app-layout>
    <div class="max-w-7xl mx-auto py-3 sm:py-6 px-3 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-6 lg:gap-5">

            <div class="row">

                @foreach ($equipments as $equipment)

            <div class="bg-white rounded-lg sm:rounded-2xl shadow-md hover:shadow-lg transition overflow-hidden group">
                <div class="relative">
                    <img src="{{ asset('storage/' . $equipment->image) }}"
                        alt="Product Image"
                        class="w-full h-32 sm:h-48 lg:h-60 object-cover group-hover:scale-105 transition-transform" />
                    <span
                        class="absolute top-2 right-2 bg-green-600 text-white text-[10px] sm:text-xs px-1.5 py-0.5 rounded">{{$equipment->status}}</span>
                </div>
                <div class="p-2 sm:p-4 p-5 pb-0">
                    <h3 class="text-sm sm:text-base lg:text-lg font-bold text-gray-900 mb-1 truncate">{{ $equipment->name }}</h3>
                    <p class="text-xs sm:text-sm text-gray-500 mb-1 sm:mb-2 block">{{ $equipment->category }}</p>
                    <p class="text-xs text-gray-400 mb-2 block ">{{ $equipment->code }}</p><span
                        class="text-sm  sm:text-lg font-semibold text-green-600 ">Free</span>
                </div>
            </div>

                @endforeach

            </div>
        </div>
    </div>
</x-app-layout>
