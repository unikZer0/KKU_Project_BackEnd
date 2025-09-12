<x-admin-layout>
    <div class="p-6 bg-white rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-4">Activity Logs</h2>

        <!-- Navigation Buttons -->
        <div class="flex space-x-4 mb-4">
            <a href="{{ route('admin.report.index') }}" 
               class="p-2 bg-yellow-500 text-white rounded hover:bg-yellow-600 flex items-center justify-center"
               title="Request Report">
               <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
                    viewBox="0 0 24 24" stroke="currentColor">
                   <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                         d="M9 12h6m-6 4h6M4 6h16v12H4V6z"/>
                </svg>
            </a>

            <a href="{{ route('admin.report.users') }}" 
               class="p-2 bg-blue-500 text-white rounded hover:bg-blue-600 flex items-center justify-center"
               title="Users">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" 
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M5.121 17.804A8.966 8.966 0 0112 15c2.21 0 4.21.895 5.879 2.344M15 11a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
            </a>

            <a href="{{ route('admin.report.equipments') }}" 
               class="p-2 bg-green-500 text-white rounded hover:bg-green-600 flex items-center justify-center"
               title="Equipments">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" 
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M9 17v-6a2 2 0 012-2h2a2 2 0 012 2v6M5 17h14M5 17a2 2 0 002 2h10a2 2 0 002-2M5 17V7a2 2 0 012-2h10a2 2 0 012 2v10" />
                </svg>
            </a>

            <a href="{{ route('admin.report.categories') }}" 
               class="p-2 bg-purple-500 text-white rounded hover:bg-purple-600 flex items-center justify-center"
               title="Categories">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none" 
                     viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
                          d="M4 6h16M4 10h16M4 14h16M4 18h16" />
                </svg>
            </a>
            <a href="{{ route('admin.report.logs') }}" 
   class="p-2 bg-red-500 text-white rounded hover:bg-red-600 flex items-center justify-center"
   title="Logs">
   <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" fill="none"
        viewBox="0 0 24 24" stroke="currentColor">
       <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
             d="M3 7h18M3 12h18M3 17h18" />
   </svg>
</a>
        </div>
    <div class="p-6 bg-white rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-4">Admin Logs</h2>

        <!-- Log Filter Form -->
        <form method="GET" action="{{ route('admin.report.logs') }}" class="flex space-x-4 mb-4">
            <input type="text" name="admin" placeholder="Admin Name" 
                   value="{{ request('admin') }}" 
                   class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />

            <select name="action" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Actions</option>
                <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Create</option>
                <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Update</option>
                <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Delete</option>
            </select>

            <select name="target_type" class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Types</option>
                <option value="equipment" {{ request('target_type') == 'equipment' ? 'selected' : '' }}>Equipment</option>
                <option value="category" {{ request('target_type') == 'category' ? 'selected' : '' }}>Category</option>
                <option value="user" {{ request('target_type') == 'user' ? 'selected' : '' }}>User</option>
            </select>

            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                Filter
            </button>
        </form>

        <!-- Logs Table -->
        <table class="min-w-full text-sm border">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-2 text-left">ID</th>
                    <th class="px-4 py-2 text-left">Admin</th>
                    <th class="px-4 py-2 text-left">Action</th>
                    <th class="px-4 py-2 text-left">Target Type</th>
                    <th class="px-4 py-2 text-left">Target ID</th>
                    <th class="px-4 py-2 text-left">Description</th>
                    <th class="px-4 py-2 text-left">Date</th>
                </tr>
            </thead>
            <tbody>
                @foreach($logs as $log)
                    <tr class="border-b">
                        <td class="px-4 py-2">{{ $log->id }}</td>
                        <td class="px-4 py-2">{{ $log->admin->username ?? 'N/A' }}</td>
                        <td class="px-4 py-2">{{ $log->action }}</td>
                        <td class="px-4 py-2">{{ $log->target_type }}</td>
                        <td class="px-4 py-2">{{ $log->target_id }}</td>
                        <td class="px-4 py-2">{{ $log->description }}</td>
                        <td class="px-4 py-2">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Pagination -->
        <div class="mt-4">
            {{ $logs->withQueryString()->links() }}
        </div>
    </div>
</x-admin-layout>
