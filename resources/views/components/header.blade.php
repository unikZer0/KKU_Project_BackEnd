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

            <!-- User Authentication -->
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
                <button class="text-gray-700 hover:text-gray-900">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Search Bar Section (Vue mount) -->
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-6">
            <div id="header-search">
            </div>
            <!-- Navigation Links -->
        <div class="flex justify-end space-x-8">
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
            </div>
            <a href="#" class="text-gray-700 hover:text-gray-900 font-medium">ติดต่อ</a>
            @can('admin')
                <button class="flex items-center text-gray-700 hover:text-gray-900 font-medium">
                    แอดมิน
                    <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" 
                            d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 
                            111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 
                            010-1.414z" 
                            clip-rule="evenodd" />
                    </svg>
                </button>
            @endcan
            
        </div>

    </div>
</header>
