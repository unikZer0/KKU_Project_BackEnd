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
        <span class="font-semibold text-gray-900">Categories report</span>
    </nav>
    <div class="p-4 bg-white rounded-lg border">
        <h2 class="text-xl font-bold mb-4">Category Report</h2>
        <table class="min-w-full border">
            <thead>
                <tr>
                    <th class="border px-2 py-1">ID</th>
                    <th class="border px-2 py-1">ชื่อหมวดหมู่</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($categories as $cat)
                    <tr>
                        <td class="border px-2 py-1">{{ $cat['id'] }}</td>
                        <td class="border px-2 py-1">{{ $cat['name'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <a href="{{ route('admin.report.export.categories') }}" class="btn btn-primary">Export Categories</a>
    </div>
</x-admin-layout>
