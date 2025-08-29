<aside id="admin-sidebar" class="w-64 bg-white border-r min-h-screen md:sticky md:top-0">
    <nav class="p-4 space-y-1">
        <a href="{{ route('admin.index') }}" class="flex items-center px-3 py-2 rounded-md text-sm font-medium bg-gray-100 text-gray-900">Dashboard</a>
        <a href="{{ route('admin.equipment.index')}}" class="flex items-center px-3 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-50">Equipment</a>
        <a href="{{ route('admin.category.index')}}" class="flex items-center px-3 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-50">Categories</a>
        <a href="{{ route('admin.requests.index')}}" class="flex items-center px-3 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-50">Requests</a>
        <a href="#" class="flex items-center px-3 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-50">Users</a>
        <a href="{{ route('admin.requests.report')}}" class="flex items-center px-3 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-50">Reports</a>
        <a href="#" class="flex items-center px-3 py-2 rounded-md text-sm text-gray-700 hover:bg-gray-50">Settings</a>
    </nav>
</aside>



