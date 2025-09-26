<style>
.read-notification {
    opacity: 0.6 !important;
    background-color: #f9fafb;
}
.read-notification .font-semibold {
    color: #6b7280 !important;
}
.read-notification .text-sm {
    color: #9ca3af !important;
}

/* Mobile submenu transitions */
.mobile-submenu {
    max-height: 0;
    overflow: hidden;
    transition: max-height 0.3s ease-in-out;
}

.mobile-submenu.show {
    max-height: 300px; /* Adjust based on your content */
}

/* Rotate animation for arrow */
.rotate-180 {
    transform: rotate(180deg);
}
</style>

<header class="bg-white shadow-sm border-b  border-gray-200 ">
    <!-- Top Navigation Bar -->
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between items-center h-16">
            <!-- Logo -->
            <div class="flex items-center">
                <a href="{{ route('home') }}" class="flex items-center space-x-2">
                    <div class="w-8 h-8 bg-blue-600 rounded flex items-center justify-center">
                        KKU
                    </div>
                    <span class="text-xl font-semibold text-gray-800">Borrow</span>
                </a>
            </div>
            <!-- User Authentication - Hidden on mobile -->
            <div class="flex">
                <!-- Vue Notification System -->
                <div id="notification-system"></div>


                <div class="hidden md:flex items-center space-x-4">
                    <div class="w-px h-6 bg-gray-300"></div>
                    @auth
                        <div class="flex items-center space-x-3">
                        <a href="{{ route('profile.show') }}" class="flex items-center  font-medium hover:text-blue-600">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                        d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <span>{{ Auth::user()->name }}</span>
                            </a>
                            <form method="POST" action="{{ route('logout') }}" class="logout-form">
                                @csrf
                                <button type="submit"
                                    class="inline-flex items-center px-3 py-1.5 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md transition">
                                    ออกจากระบบ
                                </button>
                            </form>
                        </div>
                    @else
                        <a href="{{ route('login') }}"
                            class="flex items-center text-gray-700 hover:text-gray-900 font-medium">
                            <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            เข้าสู่ระบบ
                        </a>
                        <span class="text-gray-400">/</span>
                        <a href="{{ route('register') }}"
                            class="text-gray-700 hover:text-gray-900 font-medium">ลงทะเบียน</a>
                    @endauth
                </div>

                <!-- Mobile menu button -->
                <div class="md:hidden">
                    <button id="mobile-menu-button"
                        class="text-gray-700 hover:text-gray-900 p-2 rounded-md hover:bg-gray-100 transition-colors duration-200">
                        <svg id="menu-icon" class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                        <svg id="close-icon" class="w-7 h-7 hidden" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                d="M6 18L18 6M6 6l12 12" />
                        </svg>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="md:hidden hidden bg-gray-50 border-t border-gray-200">
        <div class="px-4 py-6 space-y-4">
            <!-- Mobile Navigation Links -->
            <div class="space-y-3">
                <a href="{{ route('home') }}" class="block text-blue-600 hover:text-blue-700 font-medium py-2">หน้าหลัก</a>
                
                <!-- Categories Dropdown -->
                <div class="space-y-2">
                    <button class="mobile-submenu-toggle w-full text-left flex items-center justify-between text-gray-700 font-medium py-2">
                        <span>หมวดหมู่</span>
                        <svg class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                        </svg>
                    </button>
                    <div class="pl-4 space-y-2 mobile-submenu">
                        @foreach ($categories ?? [] as $cat)
                            @if (is_object($cat))
                                <a href="/?category={{ $cat->cate_id }}"
                                    class="block text-gray-600 hover:text-gray-800 py-1 transition-colors duration-200">{{ $cat->name }}</a>
                            @endif
                        @endforeach
                    </div>
                </div>
                
                                @php
                    $user = Auth::user();
                @endphp

                @auth
                    @if ($user && in_array($user->role, ['admin', 'staff']))
                        <a href="{{ route('admin.index') }}"
                    class="{{ request()->routeIs('admin.*') ? 'text-blue-600 font-medium' : 'text-gray-700 hover:text-gray-900' }}">
                    แอดมิน
                </a>
                    @endif
                @endauth

                    <a href="{{ route('borrower.equipments.myreq') }}"
                        class="block text-gray-700 hover:text-gray-900 font-medium py-2">คำขอของฉัน</a>
            </div>

            <!-- Mobile User Authentication -->
            <div class="pt-4 border-t border-gray-200">
                @auth
                    <div class="flex items-center space-x-3 mb-3">
                        <a href="{{ route('profile.show') }}" class="flex items-center font-medium text-gray-700 hover:text-blue-600">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>{{ Auth::user()->name }}</span>
                        </a>
                    </div>
                    <form method="POST" action="{{ route('logout') }}" class="logout-form">
                        @csrf
                        <button type="submit"
                            class="w-full text-left inline-flex items-center px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md transition">
                            ออกจากระบบ
                        </button>
                    </form>
                @else
                    <div class="space-y-2">
                        <a href="{{ route('login') }}" class="block text-gray-700 hover:text-gray-900 font-medium py-2">
                            เข้าสู่ระบบ
                        </a>
                        <a href="{{ route('register') }}"
                            class="block text-gray-700 hover:text-gray-900 font-medium py-2">
                            ลงทะเบียน
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    <!-- Search Bar Section (Vue mount) -->
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8 pb-6">
        <div id="header-search">
        </div>
        <!-- Navigation Links - Hidden on mobile -->
        <!-- Desktop Menu -->
        <div class="hidden md:flex justify-end space-x-8">
            <a href="{{ route('home') }}"
                class="{{ request()->routeIs('home') ? 'text-blue-600 font-medium' : 'text-gray-700 hover:text-blue-700' }}">
                หน้าหลัก
            </a>

            <!-- Dropdown -->
            <div class="relative group">
                <button
                    class="flex items-center {{ request()->is('category*') ? 'text-blue-600 font-medium' : 'text-gray-700 hover:text-blue-700' }}">
                    หมวดหมู่
                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0
                        111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0
                        010-1.414z" clip-rule="evenodd" />
                    </svg>
                </button>
                <div
                    class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                    @foreach ($categories ?? [] as $cat)
                        @if (is_object($cat))
                            <a href="/?category={{ $cat->cate_id }}"
                                class="block px-4 py-2 text-sm {{ request('category') == $cat->cate_id ? 'text-blue-600 font-medium bg-gray-50' : 'text-gray-700 hover:bg-gray-100' }}">
                                {{ $cat->name }}
                            </a>
                        @endif
                    @endforeach
                </div>
            </div>
                @php
                    $user = Auth::user();
                @endphp

                @auth
                    @if ($user && in_array($user->role, ['admin', 'staff']))
                        <a href="{{ route('admin.index') }}"
                    class="{{ request()->routeIs('admin.*') ? 'text-blue-600 font-medium' : 'text-gray-700 hover:text-gray-900' }}">
                    แอดมิน
                </a>
                    @endif
                @endauth
                <a href="{{ route('borrower.equipments.myreq') }}"
                    class="{{ request()->routeIs('borrower.equipments.myreq') ? 'text-blue-600 font-medium' : 'text-gray-700 hover:text-blue-700' }}">
                    คำขอของฉัน
                </a>
        </div>

        {{-- Breadcrumb --}}
        <nav class="flex items-center text-sm text-gray-500 space-x-1 mb-4" aria-label="Breadcrumb">
            {{-- Home --}}
            <a href="{{ route('home') }}"
                class="flex items-center hover:text-blue-600 {{ request()->routeIs('home') ? 'text-blue-600 font-medium' : '' }}">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 2L2 8h2v8h4V12h4v4h4V8h2L10 2z"></path>
                </svg>
                หน้าแรก
            </a>
            @if (!request()->routeIs('home'))
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            @endif
            @if (request()->routeIs('equipments.show'))
                <span class="text-gray-700 font-medium">
                    {{ $equipment->name ?? 'รายละเอียด' }}
                </span>
            @endif
            @if (request()->routeIs('borrower.equipments.myreq') || request()->routeIs('borrower.equipments.reqdetail'))
                <a href="{{ route('borrower.equipments.myreq') }}"
                    class="hover:text-blue-600 {{ request()->routeIs('borrower.equipments.myreq') ? 'text-blue-600 font-medium' : '' }}">
                    คำขอของฉัน
                </a>
            @endif
            @if (request()->routeIs('borrower.equipments.reqdetail'))
                <svg class="w-4 h-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7" />
                </svg>
            @endif
            @if (request()->routeIs('borrower.equipments.reqdetail'))
                <span class="text-gray-700 font-medium">รายละเอียดคำขอ</span>
            @endif
        </nav>

    </div>
</header>


<script>
    document.addEventListener('DOMContentLoaded', function() {
        const menuButton = document.getElementById('mobile-menu-button');
        const mobileMenu = document.getElementById('mobile-menu');
        const menuIcon = document.getElementById('menu-icon');
        const closeIcon = document.getElementById('close-icon');

        menuButton.addEventListener('click', () => {
            mobileMenu.classList.toggle('hidden');
            menuIcon.classList.toggle('hidden');
            closeIcon.classList.toggle('hidden');
        });

        document.querySelectorAll('.mobile-submenu-toggle').forEach(btn => {
            btn.addEventListener('click', () => {
                const submenu = btn.nextElementSibling;
                const arrow = btn.querySelector('svg');
                
                // Toggle submenu visibility
                submenu.classList.toggle('show');
                
                // Rotate arrow icon
                arrow.classList.toggle('rotate-180');
            });
        });
    });
    document.addEventListener('DOMContentLoaded', function() {
        const logoutForms = document.querySelectorAll('.logout-form');

        logoutForms.forEach(function(form) {
            form.addEventListener('submit', function(e) {
                e.preventDefault();

                Swal.fire({
                    title: 'ยืนยันการออกจากระบบ',
                    text: 'คุณต้องการออกจากระบบใช่หรือไม่?',
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'ตกลง',
                    cancelButtonText: 'ยกเลิก',
                    reverseButtons: true
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
    // Old notification JavaScript removed - now handled by Vue component
</script>
