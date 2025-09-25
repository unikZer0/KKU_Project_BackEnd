<aside id="admin-sidebar" class="w-64 bg-white border-r min-h-screen md:sticky md:top-0">
    <nav class="p-4 space-y-1">
        <a href="{{ route('admin.index') }}"
            class="{{ request()->routeIs('admin.index') ? 'flex items-center px-3 py-2 rounded-md text-sm font-medium bg-blue-100 text-gray-900' : 'flex items-center px-3 py-2 rounded-md text-sm text-gray-900' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round" d="M10.5 6a7.5 7.5 0 1 0 7.5 7.5h-7.5V6Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M13.5 10.5H21A7.5 7.5 0 0 0 13.5 3v7.5Z" />
            </svg>

            แดชบอร์ด
        </a>
        <div x-data="{ open: false }" class="space-y-1">
            <button @click="open = !open"
                class="w-full flex items-center justify-between px-3 py-2 rounded-md text-sm font-medium text-gray-900 hover:focus:outline-none">
                <span class="flex items-center gap-2">
                    <!-- Icon -->
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M19.5 14.25v-2.625a3.375 3.375 0 0 0-3.375-3.375h-1.5A1.125 1.125 0 0 1 13.5 7.125v-1.5a3.375 3.375 0 0 0-3.375-3.375H8.25M9 16.5v.75m3-3v3M15 12v5.25m-4.5-15H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 0 0-9-9Z" />
                    </svg>

                    หน้าจัดการรายงาน
                </span>
                <!-- Arrow -->
                <svg :class="{ 'rotate-180': open }"
                    class="w-4 h-4 text-gray-500 transform transition-transform duration-200" fill="none"
                    stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            <!-- Dropdown container -->
            <div x-show="open" x-transition class="pl-6 mt-1 space-y-1 border-l border-gray-200">
                <a href="{{ route('admin.report.vue', ['type' => 'user']) }}"
                    class="{{ request()->routeIs('admin.report.vue?type=user') ? 'block px-3 py-2 rounded-md text-sm font-medium text-gray-900 flex items-center bg-blue-100' : 'block px-3 py-2 rounded-md text-sm text-gray-900 flex items-center' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="M15 19.128a9.38 9.38 0 0 0 2.625.372 9.337 9.337 0 0 0 4.121-.952 4.125 4.125 0 0 0-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 0 1 8.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0 1 11.964-3.07M12 6.375a3.375 3.375 0 1 1-6.75 0 3.375 3.375 0 0 1 6.75 0Zm8.25 2.25a2.625 2.625 0 1 1-5.25 0 2.625 2.625 0 0 1 5.25 0Z" />
                    </svg>

                    รายงานผู้ใช้
                </a>

                <a href="{{ route('admin.report.vue', ['type' => 'equipment']) }}"
                    class="{{ request()->routeIs('admin.report.equipments') ? 'block px-3 py-2 rounded-md text-sm font-medium text-gray-900 flex items-center bg-blue-100' : 'block px-3 py-2 rounded-md text-sm text-gray-900 flex items-center' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
                    </svg>

                    รายงานอุปกรณ์
                </a>

                <a href="{{ route('admin.report.vue', ['type' => 'category']) }}"
                    class="{{ request()->routeIs('admin.report.categories') ? 'block px-3 py-2 rounded-md text-sm font-medium text-gray-900 flex items-center bg-blue-100' : 'block px-3 py-2 rounded-md text-sm text-gray-900 flex items-center' }}">
                    <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                    </svg>

                    รายงานหมวดหมู่
                </a>

                <a href="{{ route('admin.report.vue', ['type' => 'request']) }}"
                    class="{{ request()->routeIs('admin.report.requests') ? 'block px-3 py-2 rounded-md text-sm font-medium text-gray-900 flex items-center bg-blue-100' : 'block px-3 py-2 rounded-md text-sm text-gray-900 flex items-center' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0-3-3m3 3 3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                    </svg>

                    รายงานคำขอ
                </a>
                <a href="{{ route('admin.report.vue', ['type' => 'log']) }}"
                    class="{{ request()->routeIs('admin.report.logs') ? 'block px-3 py-2 rounded-md text-sm font-medium text-gray-900 flex items-center bg-blue-100' : 'block px-3 py-2 rounded-md text-sm text-gray-900 flex items-center' }}">
                    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                        stroke="currentColor" class="size-6">
                        <path stroke-linecap="round" stroke-linejoin="round"
                            d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0-3-3m3 3 3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
                    </svg>

                    รายงานบันทึกการใช้งานในระบบ
                </a>
            </div>
        </div>

        <a href="{{ route('admin.equipment.index') }}"
            class="{{ request()->routeIs('admin.equipment.index') ? 'flex items-center px-3 py-2 rounded-md text-sm bg-blue-100 text-gray-900' : 'flex items-center px-3 py-2 rounded-md text-sm text-gray-900' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="m21 7.5-9-5.25L3 7.5m18 0-9 5.25m9-5.25v9l-9 5.25M3 7.5l9 5.25M3 7.5v9l9 5.25m0-9v9" />
            </svg>

            หน้าจัดการอุปกรณ์
        </a>

        <a href="{{ route('admin.equipment-items.index') }}"
            class="{{ request()->routeIs('admin.equipment-items.*') ? 'flex items-center px-3 py-2 rounded-md text-sm bg-blue-100 text-gray-900' : 'flex items-center px-3 py-2 rounded-md text-sm text-gray-900' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5M10 11.25h4M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z" />
            </svg>

            หน้าจัดการรายการอุปกรณ์
        </a>

        <a href="{{ route('admin.category.index') }}"
            class="{{ request()->routeIs('admin.category.index') ? 'flex items-center px-3 py-2 rounded-md text-sm font-medium bg-blue-100 text-gray-900' : 'flex items-center px-3 py-2 rounded-md text-sm text-gray-900' }}">
            <svg class="w-5 h-5 mr-2 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M4 6h16M4 10h16M4 14h16M4 18h16" />
            </svg>

            หน้าจัดการหมวดหมู่
        </a>

        <a href="{{ route('admin.requests.index') }}"
            class="{{ request()->routeIs('admin.requests.*') ? 'flex items-center px-3 py-2 rounded-md text-sm font-medium bg-blue-100 text-gray-900' : 'flex items-center px-3 py-2 rounded-md text-sm text-gray-900' }}">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5"
                stroke="currentColor" class="size-6">
                <path stroke-linecap="round" stroke-linejoin="round"
                    d="m20.25 7.5-.625 10.632a2.25 2.25 0 0 1-2.247 2.118H6.622a2.25 2.25 0 0 1-2.247-2.118L3.75 7.5m8.25 3v6.75m0 0-3-3m3 3 3-3M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125Z" />
            </svg>

            หน้าจัดการคำขอ
        </a>
    </nav>
</aside>