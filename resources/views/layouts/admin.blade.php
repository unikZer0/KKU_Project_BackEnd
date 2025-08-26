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

        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans antialiased bg-gray-50">
        <div class="min-h-screen">
            <x-admin-header />
            <div class="flex">
                <div class="hidden md:block">
                    <x-admin-sidebar />
                </div>
                <main id="admin-main" class="flex-1 px-4 sm:px-6 lg:px-8 py-6">
                    {{ $slot }}
                </main>
            </div>
        </div>
        <script>
            document.addEventListener('DOMContentLoaded', function(){
                const toggle = document.getElementById('sidebar-toggle');
                const sidebarWrapper = document.querySelector('body .flex > .hidden.md\\:block');
                if (toggle && sidebarWrapper) {
                    toggle.addEventListener('click', function(){
                        sidebarWrapper.classList.toggle('hidden');
                    });
                }
            });
        </script>
    </body>
    </html>


