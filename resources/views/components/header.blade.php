<header class="bg-white shadow-sm border-b border-gray-200">
    <!-- Top Navigation Bar -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
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
            <div class="hidden md:flex items-center space-x-4">
                <div class="w-px h-6 bg-gray-300"></div>
                @auth
                    <div class="flex items-center space-x-3">
                        <div class="flex items-center  font-medium">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>{{ Auth::user()->name }}</span>
                        </div>
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="inline-flex items-center px-3 py-1.5 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md transition">
                                ออกจากระบบ
                            </button>
                        </form>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="flex items-center text-gray-700 hover:text-gray-900 font-medium">
                        <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                        </svg>
                        เข้าสู่ระบบ
                    </a>
                    <span class="text-gray-400">/</span>
                    <a href="{{ route('register') }}" class="text-gray-700 hover:text-gray-900 font-medium">ลงทะเบียน</a>
                @endauth
            </div>

            <!-- Mobile menu button -->
            <div class="md:hidden">
                <button id="mobile-menu-button" class="text-gray-700 hover:text-gray-900 p-2 rounded-md hover:bg-gray-100 transition-colors duration-200">
                    <svg id="menu-icon" class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                    <svg id="close-icon" class="w-6 h-6 hidden" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Mobile Menu -->
    <div id="mobile-menu" class="md:hidden hidden bg-gray-50 border-t border-gray-200">
        <div class="px-4 py-6 space-y-4">
            <!-- Mobile Navigation Links -->
            <div class="space-y-3">
                <a href="#" class="block text-blue-600 hover:text-blue-700 font-medium py-2">หน้าหลัก</a>
                <div class="space-y-2">
                    <div class="text-gray-700 font-medium py-2">หมวดหมู่</div>
                    <div class="pl-4 space-y-2">
                        <a href="#" class="block text-gray-600 hover:text-gray-800 py-1">อุปกรณ์ห้องแล็บ</a>
                        <a href="#" class="block text-gray-600 hover:text-gray-800 py-1">อุปกรณ์มัลติมีเดีย</a>
                        <a href="#" class="block text-gray-600 hover:text-gray-800 py-1">อุปกรณ์กีฬา</a>
                    </div>
                </div>
                <a href="#" class="block text-gray-700 hover:text-gray-900 font-medium py-2">ติดต่อ</a>
                @can('admin')
                    <a href="#" class="block text-gray-700 hover:text-gray-900 font-medium py-2">แอดมิน</a>
                @endcan
            </div>
            
            <!-- Mobile User Authentication -->
            <div class="pt-4 border-t border-gray-200">
                @auth
                    <div class="flex items-center space-x-3 mb-3">
                        <div class="flex items-center font-medium text-gray-700">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                            </svg>
                            <span>{{ Auth::user()->name }}</span>
                        </div>
                    </div>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="w-full text-left inline-flex items-center px-3 py-2 text-sm bg-gray-100 hover:bg-gray-200 text-gray-700 rounded-md transition">
                            ออกจากระบบ
                        </button>
                    </form>
                @else
                    <div class="space-y-2">
                        <a href="{{ route('login') }}" class="block text-gray-700 hover:text-gray-900 font-medium py-2">
                            เข้าสู่ระบบ
                        </a>
                        <a href="{{ route('register') }}" class="block text-gray-700 hover:text-gray-900 font-medium py-2">
                            ลงทะเบียน
                        </a>
                    </div>
                @endauth
            </div>
        </div>
    </div>

    <!-- Search Bar Section (Vue mount) -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-6">
        <div id="header-search" >
        </div>
        <!-- Navigation Links - Hidden on mobile -->
        <div class="hidden md:flex justify-end space-x-8">
            <a href="#" class="text-blue-600 hover:text-blue-700 font-medium">หน้าหลัก</a>
            <!-- Dropdown -->
            <div class="relative group">
                <button class="flex items-center text-gray-700 hover:text-gray-900 font-medium">
                    หมวดหมู่
                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" 
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 
                            111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 
                            010-1.414z" 
                            clip-rule="evenodd" />
                    </svg>
                </button>
                <!-- Dropdown menu -->
                <div class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-10 opacity-0 invisible group-hover:opacity-100 group-hover:visible transition-all duration-200">
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">อุปกรณ์ห้องแล็บ</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">อุปกรณ์มัลติมีเดีย</a>
                    <a href="#" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">อุปกรณ์กีฬา</a>
                </div>
            </div>
            <a href="#" class="text-gray-700 hover:text-gray-900 font-medium">ติดต่อ</a>
            @can('admin')
                <a href="#" class="text-gray-700 hover:text-gray-900 font-medium">แอดมิน</a>
            @endcan
        </div>
    </div>
</header>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const mobileMenuButton = document.getElementById('mobile-menu-button');
    const mobileMenu = document.getElementById('mobile-menu');
    const menuIcon = document.getElementById('menu-icon');
    const closeIcon = document.getElementById('close-icon');

    mobileMenuButton.addEventListener('click', function() {
        const isHidden = mobileMenu.classList.contains('hidden');
        
        if (isHidden) {
            mobileMenu.classList.remove('hidden');
            menuIcon.classList.add('hidden');
            closeIcon.classList.remove('hidden');
        } else {
            mobileMenu.classList.add('hidden');
            menuIcon.classList.remove('hidden');
            closeIcon.classList.add('hidden');
        }
    });

    // Close mobile menu when clicking outside
    document.addEventListener('click', function(event) {
        if (!mobileMenuButton.contains(event.target) && !mobileMenu.contains(event.target)) {
            mobileMenu.classList.add('hidden');
            menuIcon.classList.remove('hidden');
            closeIcon.classList.add('hidden');
        }
    });
});
</script>
