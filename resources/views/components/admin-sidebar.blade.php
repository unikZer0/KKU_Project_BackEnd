<aside id="admin-sidebar" class="w-64 bg-white border-r min-h-screen md:sticky md:top-0">
    <nav class="p-4 space-y-1">
        <a href="{{ route('admin.index') }}" class=" {{request()->routeIs('admin.index') ? 'flex items-center px-3 py-2 rounded-md text-sm font-medium bg-gray-100 text-gray-900' : 'flex items-center px-3 py-2 rounded-md text-sm text-gray-900' }}">Dashboard</a>
        <a href="{{ route('admin.equipment.index')}}" class=" {{request()->routeIs('admin.equipment.index') ? 'flex items-center px-3 py-2 rounded-md text-sm bg-gray-100 text-gray-900'  : 'flex items-center px-3 py-2 rounded-md text-sm text-gray-900'}}">Equipment</a>
        <a href="{{ route('admin.category.index')}}" class="{{request()->routeIs('admin.category.index') ? 'flex items-center px-3 py-2 rounded-md text-sm font-medium bg-gray-100 text-gray-900' : 'flex items-center px-3 py-2 rounded-md text-sm text-gray-900' }}">Categories</a>
        <a href="{{ route('admin.requests.index')}}" class="{{request()->routeIs('admin.requests.index') ? 'flex items-center px-3 py-2 rounded-md text-sm font-medium bg-gray-100 text-gray-900' : 'flex items-center px-3 py-2 rounded-md text-sm text-gray-900' }}">Requests</a>
        {{-- <a href="#" class="{{request()->routeIs('admin.index') ? 'flex items-center px-3 py-2 rounded-md text-sm font-medium bg-gray-100 text-gray-900' : 'flex items-center px-3 py-2 rounded-md text-sm text-gray-900' }}">Users</a> --}}
        <a href="{{ route('admin.report.index')}}" class="{{request()->routeIs('admin.report.index') ? 'flex items-center px-3 py-2 rounded-md text-sm font-medium bg-gray-100 text-gray-900' : 'flex items-center px-3 py-2 rounded-md text-sm text-gray-900' }}">Reports</a>
        {{-- <a href="#" class="{{request()->routeIs('admin.index') ? 'flex items-center px-3 py-2 rounded-md text-sm font-medium bg-gray-100 text-gray-900' : 'flex items-center px-3 py-2 rounded-md text-sm text-gray-900' }}">Settings</a> --}}
    </nav>
</aside>



