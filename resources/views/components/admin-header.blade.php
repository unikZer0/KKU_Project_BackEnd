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

                <!-- Notification Dropdown -->
                <div id="notification-system"></div>
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
        if (btn && menu) {
            btn.addEventListener("click", (e) => {
                e.stopPropagation(); // prevent window click from closing immediately
                menu.classList.toggle("hidden");
            });
        }

        // Close dropdown when clicking outside
        if (btn && menu) {
            window.addEventListener("click", (e) => {
                if (!btn.contains(e.target) && !menu.contains(e.target)) {
                    menu.classList.add("hidden");
                }
            });
        }

        // Event delegation for notification clicks and marking as read
        if (notiList) {
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
        }

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
