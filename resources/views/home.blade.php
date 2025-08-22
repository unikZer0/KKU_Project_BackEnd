<x-app-layout>
    <div class="max-w-7xl mx-auto py-3 sm:py-6 px-3 sm:px-6 lg:px-8">
        <div class="grid grid-cols-2 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3 sm:gap-6 lg:gap-5">
            <!-- Card 1 -->
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

            <!-- Card 2 -->
            <div class="bg-white rounded-lg sm:rounded-2xl shadow-md hover:shadow-lg transition overflow-hidden group">
                <div class="relative">
                    <img src="https://images.unsplash.com/photo-1593305841991-05c297ba4575?q=80&w=2057&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                        alt="Product Image"
                        class="w-full h-32 sm:h-48 lg:h-60 object-cover group-hover:scale-105 transition-transform" />
                    <span
                        class="absolute top-2 right-2 bg-green-600 text-white text-[10px] sm:text-xs px-1.5 py-0.5 rounded">available</span>
                </div>
                <div class="p-2 sm:p-4 lg:p-5">
                    <h3 class="text-sm sm:text-base lg:text-lg font-bold text-gray-900 mb-1 truncate">Projector</h3>
                    <p class="text-xs sm:text-sm text-gray-500 mb-1 sm:mb-2 block">Multimedia Equipment</p>
                    <p class="text-xs text-gray-400 mb-2 block ">EQ-002 • AV • 4K Resolution</p>
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                        <span class="text-sm sm:text-lg font-semibold text-green-600">Free</span>
                    </div>
                </div>
            </div>

            <!-- Card 3 -->
            <div class="bg-white rounded-lg sm:rounded-2xl shadow-md hover:shadow-lg transition overflow-hidden group">
                <div class="relative">
                    <img src="https://images.unsplash.com/photo-1502920917128-1aa500764cbd?q=80&w=2070&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                        alt="Product Image"
                        class="w-full h-32 sm:h-48 lg:h-60 object-cover group-hover:scale-105 transition-transform" />
                    <span
                        class="absolute top-2 right-2 bg-yellow-600 text-white text-[10px] sm:text-xs px-1.5 py-0.5 rounded">maintenance</span>
                </div>
                <div class="p-2 sm:p-4 lg:p-5">
                    <h3 class="text-sm sm:text-base lg:text-lg font-bold text-gray-900 mb-1 truncate">Camera Pro</h3>
                    <p class="text-xs sm:text-sm text-gray-500 mb-1 sm:mb-2 block">Photography Equipment</p>
                    <p class="text-xs text-gray-400 mb-2 block ">EQ-003 • Photo • 24MP Sensor</p>
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                        <span class="text-sm sm:text-lg font-semibold text-gray-400">Unavailable</span>
                    </div>
                </div>
            </div>

            <!-- Card 4 -->
            <div class="bg-white rounded-lg sm:rounded-2xl shadow-md hover:shadow-lg transition overflow-hidden group">
                <div class="relative">
                    <img src="https://images.unsplash.com/photo-1581833971358-2c8b550f87b3?q=80&w=2071&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                        alt="Product Image"
                        class="w-full h-32 sm:h-48 lg:h-60 object-cover group-hover:scale-105 transition-transform" />
                    <span
                        class="absolute top-2 right-2 bg-green-600 text-white text-[10px] sm:text-xs px-1.5 py-0.5 rounded">available</span>
                </div>
                <div class="p-2 sm:p-4 lg:p-5">
                    <h3 class="text-sm sm:text-base lg:text-lg font-bold text-gray-900 mb-1 truncate">Laptop Computer
                    </h3>
                    <p class="text-xs sm:text-sm text-gray-500 mb-1 sm:mb-2 block">Computing Equipment</p>
                    <p class="text-xs text-gray-400 mb-2 block ">EQ-004 • Tech • i7 Processor</p>
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                        <span class="text-sm sm:text-lg font-semibold text-green-600">Free</span>
                    </div>
                </div>
            </div>

            <!-- Card 5 -->
            <div class="bg-white rounded-lg sm:rounded-2xl shadow-md hover:shadow-lg transition overflow-hidden group">
                <div class="relative">
                    <img src="https://images.unsplash.com/photo-1581833971358-2c8b550f87b3?q=80&w=2071&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                        alt="Product Image"
                        class="w-full h-32 sm:h-48 lg:h-60 object-cover group-hover:scale-105 transition-transform" />
                    <span
                        class="absolute top-2 right-2 bg-green-600 text-white text-[10px] sm:text-xs px-1.5 py-0.5 rounded">available</span>
                </div>
                <div class="p-2 sm:p-4 lg:p-5">
                    <h3 class="text-sm sm:text-base lg:text-lg font-bold text-gray-900 mb-1 truncate">Sports Equipment
                    </h3>
                    <p class="text-xs sm:text-sm text-gray-500 mb-1 sm:mb-2 block">Sports & Recreation</p>
                    <p class="text-xs text-gray-400 mb-2 block ">EQ-005 • Sports • Basketball</p>
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                        <span class="text-sm sm:text-lg font-semibold text-green-600">Free</span>
                    </div>
                </div>
            </div>

            <!-- Card 6 -->
            <div class="bg-white rounded-lg sm:rounded-2xl shadow-md hover:shadow-lg transition overflow-hidden group">
                <div class="relative">
                    <img src="https://images.unsplash.com/photo-1581833971358-2c8b550f87b3?q=80&w=2071&auto=format&fit=crop&ixlib=rb-4.0.3&ixid=M3wxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8fA%3D%3D"
                        alt="Product Image"
                        class="w-full h-32 sm:h-48 lg:h-60 object-cover group-hover:scale-105 transition-transform" />
                    <span
                        class="absolute top-2 right-2 bg-red-600 text-white text-[10px] sm:text-xs px-1.5 py-0.5 rounded">borrowed</span>
                </div>
                <div class="p-2 sm:p-4 lg:p-5">
                    <h3 class="text-sm sm:text-base lg:text-lg font-bold text-gray-900 mb-1 truncate">Tablet Device</h3>
                    <p class="text-xs sm:text-sm text-gray-500 mb-1 sm:mb-2 block">Computing Equipment</p>
                    <p class="text-xs text-gray-400 mb-2 block ">EQ-006 • Tech • iPad Pro</p>
                    <div class="flex flex-col sm:flex-row sm:justify-between sm:items-center gap-2">
                        <span class="text-sm sm:text-lg font-semibold text-red-600">Borrowed</span>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
