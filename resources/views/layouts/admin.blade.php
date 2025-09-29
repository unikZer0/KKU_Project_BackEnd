<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Admin Dashboard - {{ config('app.name', 'Laravel') }}</title>

    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="//unpkg.com/alpinejs" defer></script>


    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>

<body class="font-noto antialiased bg-gray-50">
    <div class="min-h-screen">
        <x-admin-header />
        <div class="flex">
            <div id="admin-sidebar" class="hidden md:block">
                <x-admin-sidebar />
            </div>
            <main id="admin-main" class="flex-1">
                {{ $slot }}
            </main>
        </div>
    </div>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const toggle = document.getElementById('sidebar-toggle');
            const sidebarWrapper = document.getElementById('admin-sidebar');
            if (toggle && sidebarWrapper) {
                toggle.addEventListener('click', function () {
                    sidebarWrapper.classList.toggle('hidden');
                });
            }
        });
        document.addEventListener('DOMContentLoaded', function () {
            const logoutForms = document.querySelectorAll('.logout-form');

            logoutForms.forEach(function (form) {
                form.addEventListener('submit', function (e) {
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
    </script>
</body>

</html>
