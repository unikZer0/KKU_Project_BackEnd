<x-admin-layout>
    <div class="p-6 bg-white rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-4">Request Report</h2>
        <table class="min-w-full text-sm border">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-2 text-left">Request ID</th>
                    <th class="px-4 py-2 text-left">User</th>
                    <th class="px-4 py-2 text-left">Equipment</th>
                    <th class="px-4 py-2 text-left">Date</th>
                    <th class="px-4 py-2 text-left">Status</th>
                    <th class="px-4 py-2 text-left">Reason</th>
                </tr>
            </thead>
            <tbody>
                @foreach($requests as $r)
                    <tr class="border-b">
                        <td class="px-4 py-2">{{ $r['id'] }}</td>
                        <td class="px-4 py-2">{{ $r['user_name'] }}</td>
                        <td class="px-4 py-2">{{ $r['equipment_name'] }}</td>
                        <td class="px-4 py-2">{{ $r['date'] }}</td>
                        <td class="px-4 py-2">{{ $r['status'] }}</td>
                        <td class="px-4 py-2">{{ $r['reason'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-admin-layout>
