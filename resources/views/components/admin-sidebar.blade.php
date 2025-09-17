<aside id="admin-sidebar" class="w-64 bg-white border-r min-h-screen md:sticky md:top-0">
    <nav class="p-4 space-y-1">
        <a href="{{ route('admin.index') }}"
            class=" {{ request()->routeIs('admin.index') ? 'flex items-center px-3 py-2 rounded-md text-sm font-medium bg-gray-100 text-gray-900' : 'flex items-center px-3 py-2 rounded-md text-sm text-gray-900' }}">Dashboard</a>
        <!-- Reports Dropdown -->
        <div x-data="{ open: false }" class="space-y-1">
            <button @click="open = !open"
                class="w-full flex items-center justify-between px-3 py-2 rounded-md text-sm font-medium text-gray-900 hover:bg-gray-100 focus:outline-none">
                <span class="flex items-center gap-2">
                    <!-- Icon -->
                    <svg class="w-5 h-5 text-gray-500" fill="currentColor" viewBox="0 0 20 20">
                        <path
                            d="M2.5 5a.5.5 0 01.5-.5h.5a.5.5 0 01.5.5v.5a.5.5 0 01-.5.5H3a.5.5 0 01-.5-.5V5zM2 9a8 8 0 018 8h-2a6 6 0 00-6-6V9zm0-4a12 12 0 0112 12h-2A10 10 0 002 5V5z" />
                    </svg>
                    Reports
                </span>
                <!-- Arrow -->
                <svg :class="{ 'rotate-180': open }"
                    class="w-4 h-4 text-gray-500 transform transition-transform duration-200" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Dropdown Links -->
            <div x-show="open" x-transition class="pl-6 space-y-1">
                <a href="{{ route('admin.report.index') }}"
                    class="{{ request()->routeIs('admin.report.index') ? 'block px-3 py-2 rounded-md text-sm font-medium bg-gray-100 text-gray-900' : 'block px-3 py-2 rounded-md text-sm text-gray-900' }}">
                    Overview
                </a>
                <a href="{{ route('admin.report.users') }}"
                    class="{{ request()->routeIs('admin.report.users') ? 'block px-3 py-2 rounded-md text-sm font-medium bg-gray-100 text-gray-900' : 'block px-3 py-2 rounded-md text-sm text-gray-900' }}">
                    Users report
                </a>
                <a href="{{ route('admin.report.equipments') }}"
                    class="{{ request()->routeIs('admin.report.equipments') ? 'block px-3 py-2 rounded-md text-sm font-medium bg-gray-100 text-gray-900' : 'block px-3 py-2 rounded-md text-sm text-gray-900' }}">
                    Equipments report
                </a>
                <a href="{{ route('admin.report.categories') }}"
                    class="{{ request()->routeIs('admin.report.categories') ? 'block px-3 py-2 rounded-md text-sm font-medium bg-gray-100 text-gray-900' : 'block px-3 py-2 rounded-md text-sm text-gray-900' }}">
                    Categories report
                </a>
                <a href="{{ route('admin.report.requests') }}"
                    class="{{ request()->routeIs('admin.report.requests') ? 'block px-3 py-2 rounded-md text-sm font-medium bg-gray-100 text-gray-900' : 'block px-3 py-2 rounded-md text-sm text-gray-900' }}">
                    Requests report
                </a>
                @can('admin')
                <a href="{{ route('admin.report.logs') }}"
                    class="{{ request()->routeIs('admin.report.logs') ? 'block px-3 py-2 rounded-md text-sm font-medium bg-gray-100 text-gray-900' : 'block px-3 py-2 rounded-md text-sm text-gray-900' }}">
                    Logs report
                </a>
                @endcan
            </div>
        </div>
        <a href="{{ route('admin.equipment.index') }}"
            class=" {{ request()->routeIs('admin.equipment.index') ? 'flex items-center px-3 py-2 rounded-md text-sm bg-gray-100 text-gray-900' : 'flex items-center px-3 py-2 rounded-md text-sm text-gray-900' }}">Equipment</a>
        <a href="{{ route('admin.category.index') }}"
            class="{{ request()->routeIs('admin.category.index') ? 'flex items-center px-3 py-2 rounded-md text-sm font-medium bg-gray-100 text-gray-900' : 'flex items-center px-3 py-2 rounded-md text-sm text-gray-900' }}">Categories</a>
        <a href="{{ route('admin.requests.index') }}"
            class="{{ request()->routeIs('admin.requests.*') ? 'flex items-center px-3 py-2 rounded-md text-sm font-medium bg-gray-100 text-gray-900' : 'flex items-center px-3 py-2 rounded-md text-sm text-gray-900' }}">Requests</a>
    </nav>
</aside>
