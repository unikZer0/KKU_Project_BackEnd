<x-app-layout>
    <div class=" ">
        <div id="header-search"></div>
        <x-filter />
        <hr>
    </div>
    <div class="max-w-screen-2xl mx-auto py-6 px-3 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-5 gap-2 sm:gap-6 lg:gap-5 my-5">
            @foreach ($equipments as $equipment)
                <a href="{{ route('equipments.show', $equipment->code) }}" class="block">
                    <div
                        class="bg-white rounded-lg sm:rounded-2xl shadow-md hover:shadow-lg transition overflow-hidden group border border-gray-150">
                        <div class="relative">
                            <img src="{{ $equipment->photo_path }}" alt="{{ $equipment->name }}"
                                class="w-full h-32 sm:h-48 lg:h-60 object-cover group-hover:scale-105 transition-transform" />
                            @if ($equipment->status === 'maintenance')
                                <span
                                    class="absolute top-2 right-2 bg-red-600 text-white text-xs px-1.5 py-0.5 rounded">
                                    {{ ucfirst($equipment->status) }}
                                </span>
                            @elseif ($equipment->status === 'retired')
                                <span
                                    class="absolute top-2 right-2 bg-gray-600 text-white text-xs px-1.5 py-0.5 rounded">
                                    {{ ucfirst($equipment->status) }}
                                </span>
                            @else
                                <span
                                    class="absolute top-2 right-2 bg-green-600 text-white text-xs px-1.5 py-0.5 rounded">
                                    {{ ucfirst($equipment->status) }}
                                </span>
                            @endif
                        </div>
                        <div class="p-2 sm:p-4 p-5 pb-0">
                            <h3 class="text-sm sm:text-base lg:text-lg font-bold text-gray-900 mb-1 truncate">
                                {{ $equipment->name }}</h3>
                            <p class="text-xs sm:text-sm text-gray-500 mb-1 sm:mb-2">{{ $equipment->category->name }}
                            </p>
                            <p class="text-xs text-gray-400 mb-2 line-clamp-2">
                                {{ $equipment->description }}
                            </p>

                            {{-- <span class="text-sm sm:text-lg font-semibold text-green-600">Free</span> --}}
                        </div>
                    </div>
                </a>
            @endforeach
        </div>

        <!-- Pagination -->
        @if ($equipments->hasPages())
            <div id="pagination" data-current-page="{{ $equipments->currentPage() }}"
                data-total-pages="{{ $equipments->lastPage() }}" data-per-page="{{ $equipments->perPage() }}"
                data-total="{{ $equipments->total() }}">
            </div>
        @endif
    </div>
    <div class="">
        <x-pagination />
    </div>
</x-app-layout>
