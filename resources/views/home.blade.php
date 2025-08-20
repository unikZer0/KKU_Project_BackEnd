<x-app-layout>
    <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold mb-4">tesss</h1>
        @auth
            <div>Your role: <span class="font-semibold">{{ Auth::user()->role }}</span></div>
        @endauth
    </div>
</x-app-layout>
