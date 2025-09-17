<x-admin-layout>
    <div class="p-6 bg-white rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-4">Activity Logs</h2>
        <div class="p-6 bg-white rounded-lg shadow">
            <h2 class="text-lg font-semibold mb-4">Admin Logs</h2>

            <!-- Log Filter Form -->
            <form method="GET" action="{{ route('admin.report.logs') }}" class="flex space-x-4 mb-4">
                <input type="text" name="admin" placeholder="Admin Name" value="{{ request('admin') }}"
                    class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />

                <select name="action"
                    class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Actions</option>
                    <option value="create" {{ request('action') == 'create' ? 'selected' : '' }}>Create</option>
                    <option value="update" {{ request('action') == 'update' ? 'selected' : '' }}>Update</option>
                    <option value="delete" {{ request('action') == 'delete' ? 'selected' : '' }}>Delete</option>
                </select>

                <select name="target_type"
                    class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Types</option>
                    <option value="equipment" {{ request('target_type') == 'equipment' ? 'selected' : '' }}>Equipment
                    </option>
                    <option value="category" {{ request('target_type') == 'category' ? 'selected' : '' }}>Category
                    </option>
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
                    @foreach ($logs as $log)
                        <tr class="border-b">
                            <td class="px-4 py-2">{{ $log->id }}</td>
                            <td class="px-4 py-2">{{ $log->admin->name ?? 'N/A' }}</td>
                            <td class="px-4 py-2">{{ $log->action }}</td>
                            <td class="px-4 py-2">{{ $log->target_type }}</td>
                            <td class="px-4 py-2">{{ $log->target_id }}</td>
                            <td class="px-4 py-2">{{ $log->description }}</td>
                            <td class="px-4 py-2">{{ $log->created_at->format('Y-m-d H:i') }}</td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <a href="{{ route('admin.report.export.logs') }}" class="btn btn-primary">Export admin logs</a>

            <!-- Pagination -->
            <div class="mt-4">
                {{ $logs->withQueryString()->links() }}
            </div>
        </div>
</x-admin-layout>
