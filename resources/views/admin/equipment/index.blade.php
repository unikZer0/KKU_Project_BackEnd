<x-admin-layout>
    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-4">All Equipment</h2>

        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-2 text-left">ID</th>
                    <th class="px-4 py-2 text-left">Name</th>
                    <th class="px-4 py-2 text-left">Category</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-left">Action</th>
                </tr>
            </thead>
            <tbody>
                @foreach($equipments as $equipment)
                <tr class="border-b">
                    <td class="px-4 py-2">{{ $equipment->id }}</td>
                    <td class="px-4 py-2">{{ $equipment->name }}</td>
                    <td class="px-4 py-2">{{ $equipment->category->name ?? 'N/A' }}</td>
                    <td class="px-4 py-2">{{ ucfirst($equipment->status) }}</td>
                    <td class="px-4 py-2">
                        <a href="#" class="text-blue-600">Edit</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-admin-layout>
