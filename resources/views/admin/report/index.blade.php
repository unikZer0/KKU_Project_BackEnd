<x-admin-layout>
    <!--Breadcrumb-->
    <nav class="flex items-center space-x-2 text-sm text-gray-500" aria-label="Breadcrumb">
        <!-- Home -->
        <a href={{ route('admin.index') }} class="hover:text-gray-700">Dashboard</a>

        <!-- Separator -->
        <span>/</span>

        <!-- Sales (inactive or intermediate) -->
        <a href="{{ route('admin.report.index') }}" class="hover:text-gray-700">Reports</a>

        <!-- Separator -->
        <span>></span>

        <!-- Sales (inactive or intermediate) -->
        <span class="font-semibold text-gray-900">Overview</span>
    </nav>
    <div class="p-4 bg-white rounded-lg border">
        nothing burger
    </div>
</x-admin-layout>
