<x-admin-layout>
    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-4">อุปกรณ์ทั้งหมด</h2>
        <h2 class="text-lg font-semibold mb-4">Total Equipments: {{ $equipments->count() }}</h2>
        <a href="{{ route('admin.equipment.create') }}" 
               class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
               Assign New Equipment
            </a>

        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-2 text-left">ID</th>
                    <th class="px-4 py-2 text-left">ชื่ออุปกรณ์</th>
                    <th class="px-4 py-2 text-left">หมวดหมู่</th>
                    <th class="px-4 py-2 text-left">สถานะ</th>
                    <th class="px-4 py-2 text-left">แอคชั่น</th>
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
                        <a href="{{ route('admin.equipment.edit', $equipment->id) }}" class="text-blue-600">แก้ไขข้อมูล</a>
                        |
                        <a href="{{ route('admin.equipment.destroy', $equipment->id) }}" class="text-blue-600">ลบรายการ</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-admin-layout>
