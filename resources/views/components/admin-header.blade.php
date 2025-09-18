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
</style>

<header class="bg-white border-b">
    <div class="max-w-screen-2xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex items-center justify-between h-16">
            <div class="flex items-center space-x-3">
                <button id="sidebar-toggle" class="p-2 rounded hover:bg-gray-100 md:hidden">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                            d="M4 6h16M4 12h16M4 18h16" />
                    </svg>
                </button>
                <a href="{{ route('admin.index') }}" class="font-semibold text-gray-800">Admin Dashboard</a>
            </div>
            <div class="hidden md:flex items-center space-x-4">
                <button id="notiBtn" class="p-2 rounded-full hover:bg-gray-200 relative">
                    <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 64 64">
                        <path
                            d="M 32 10 C 29.662 10 28.306672 11.604938 27.638672 13.085938 C 24.030672 13.809937 17.737984 16.956187 16.958984 24.742188 C 16.665984 29.334188 16.1185 37.883781 13.0625 39.300781 C 12.8505 39.398781 12.655234 39.533219 12.490234 39.699219 C 12.235234 39.954219 10 42.294 10 46 C 10 47.104 10.896 48 12 48 L 25.257812 48 C 25.652433 51.372928 28.522752 54 32 54 C 35.477248 54 38.347567 51.372928 38.742188 48 L 52 48 C 53.104 48 54 47.104 54 46 C 54 42.294 51.764766 39.954219 51.509766 39.699219 C 51.344766 39.534219 51.1495 39.397828 50.9375 39.298828 C 47.8825 37.881828 47.333203 29.333922 47.033203 24.669922 C 46.258203 16.945922 39.966375 13.806984 36.359375 13.083984 C 35.692375 11.603984 34.338 10 32 10 z M 32 14 C 32.603 14 32.766719 14.619859 32.886719 15.255859 C 33.063719 16.190859 33.884422 16.914062 34.857422 16.914062 C 34.931422 16.914063 42.311828 17.650047 43.048828 24.998047 C 43.557828 32.932047 44.389891 40.250797 48.837891 42.716797 C 49.024891 42.956797 49.333937 43.401 49.585938 44 L 14.414062 44 C 14.667063 43.397 14.976203 42.95375 15.158203 42.71875 C 19.609203 40.25475 20.442312 32.935313 20.945312 25.070312 C 21.688313 17.650312 29.068578 16.914062 29.142578 16.914062 C 30.099578 16.914062 30.934375 16.156391 31.109375 15.275391 C 31.232375 14.660391 31.396 14 32 14 z M 29.335938 48 L 34.664062 48 C 34.319789 49.152328 33.262739 50 32 50 C 30.737261 50 29.680211 49.152328 29.335938 48 z">
                        </path>
                    </svg>
                    @auth
                        <span id="notiCount"
                            class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                            {{ auth()->user()->unreadNotifications->count() }}
                        </span>
                    @else
                        <span id="notiCount"
                            class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
                            0
                        </span>
                    @endauth

                </button>

                <!-- Notification Dropdown -->
                <div id="notiMenu"
                    class="hidden absolute right-80 top-20 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
                    <div class="p-4 border-b flex justify-between items-center">
                        <span class="font-semibold text-gray-700">Notifications</span>
                        <button id="markAllReadBtn" class="text-sm text-blue-600 hover:underline">Mark all
                            read</button>
                    </div>

                    <div id="notiList" class="max-h-72 overflow-y-auto">
                        @auth
                            @forelse(auth()->user()->unreadNotifications as $notification)
                                <div class="px-4 py-3 hover:bg-gray-100 border-b flex justify-between items-start"
                                    data-id="{{ $notification->id }}" data-url="{{ $notification->data['url'] ?? '#' }}">
                                    <div class="cursor-pointer noti-link flex-1">
                                        <div class="font-semibold text-gray-800">
                                            {{ $notification->data['user'] ?? 'admin' }}
                                        </div>
                                        @isset($notification->data['status'])
                                            @if ($notification->data['status'] === 'rejected')
                                                <div class="text-sm text-red-600">{{ $notification->data['message'] }}</div>
                                            @elseif ($notification->data['status'] === 'approved')
                                                <div class="text-sm text-green-600">{{ $notification->data['message'] }}
                                                </div>
                                            @else
                                                <div class="text-sm text-yellow-600">{{ $notification->data['message'] }}
                                                </div>
                                            @endif
                                        @endisset

                                        <div class="text-xs text-gray-400 mt-1">
                                            อุปกรณ์: {{ $notification->data['equipment'] }} |
                                            {{ $notification->created_at->diffForHumans() }}
                                        </div>
                                    </div>
                                    <button class="mark-read-btn text-xs text-blue-600 hover:underline ml-2">Mark
                                        read</button>
                                </div>
                            @empty
                                <div class="p-4 text-gray-500">ยังไม่มีการแจ้งเตือน</div>
                            @endforelse
                        @else
                            <div class="p-4 text-gray-500">กรุณาเข้าสู่ระบบเพื่อดูการแจ้งเตือน</div>
                        @endauth
                    </div>
                </div>
                <span class="text-sm text-gray-500">{{ now()->format('d/m/Y H:i:s') }}</span>
                <div class="flex items-center space-x-2">
                    <span class="text-sm">{{ Auth::user()->name ?? 'Admin' }}</span>
                    <form method="POST" action="{{ route('logout') }}" class="logout-form">
                        @csrf
                        <button type="submit"
                            class="px-3 py-1.5 text-sm bg-gray-100 hover:bg-gray-200 rounded-md">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</header>
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const btn = document.getElementById("notiBtn");
        const menu = document.getElementById("notiMenu");
        const notiList = document.getElementById("notiList");

        // Toggle dropdown
        btn.addEventListener("click", (e) => {
            e.stopPropagation(); // prevent window click from closing immediately
            menu.classList.toggle("hidden");
        });

        // Close dropdown when clicking outside
        window.addEventListener("click", (e) => {
            if (!btn.contains(e.target) && !menu.contains(e.target)) {
                menu.classList.add("hidden");
            }
        });

        // Event delegation for notification clicks and marking as read
        notiList.addEventListener("click", (e) => {
            if (e.target.classList.contains("mark-read-btn")) {
                e.stopPropagation(); // Prevent triggering the noti-link click
                const item = e.target.closest("[data-id]");
                const id = item.getAttribute("data-id");

                fetch(`/notifications/mark-read/${id}`, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            // Mark notification as read visually instead of removing
                            item.style.opacity = '0.6';
                            item.classList.add('read-notification');
                            const notiCount = document.getElementById("notiCount");
                            if (notiCount) notiCount.innerText = data.unread_count;
                        }
                    });
            } else if (e.target.closest(".noti-link")) {
                // Handle notification click for redirection
                e.preventDefault();
                const item = e.target.closest("[data-id]");
                const url = item.getAttribute("data-url");
                const id = item.getAttribute("data-id");

                // Mark as read first
                fetch(`/notifications/mark-read/${id}`, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            // Update notification count
                            const notiCount = document.getElementById("notiCount");
                            if (notiCount) notiCount.innerText = data.unread_count;
                            // Mark notification as read visually
                            item.style.opacity = '0.6';
                            item.classList.add('read-notification');
                            // Close notification dropdown
                            menu.classList.add("hidden");
                            // Redirect to the URL
                            if (url && url !== '#') {
                                window.location.href = url;
                            }
                        }
                    });
            }
        });

        // Mark all as read
        const markAllBtn = document.getElementById('markAllReadBtn');
        if (markAllBtn) {
            markAllBtn.addEventListener('click', () => {
                fetch(`/notifications/mark-all-read`, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": "{{ csrf_token() }}",
                        "Accept": "application/json"
                    }
                })
                    .then(res => res.json())
                    .then(data => {
                        if (data.success) {
                            // Mark all notifications as read visually instead of removing them
                            const allNotifications = document.querySelectorAll('[data-id]');
                            allNotifications.forEach(item => {
                                item.style.opacity = '0.6';
                                item.classList.add('read-notification');
                            });
                            const notiCount = document.getElementById("notiCount");
                            if (notiCount) notiCount.innerText = 0;
                        }
                    });
            });
        }
    });
</script>