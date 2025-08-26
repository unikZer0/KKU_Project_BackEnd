<x-admin-layout>
    <div class="bg-white p-6 rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-4">หมวดหมู่ทั้งหมด</h2>

        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-2 text-left">ID</th>
                    <th class="px-4 py-2 text-left">ชื่อหมวดหมู่</th>
                </tr>
            </thead>
            <tbody>
                @foreach($categories as $category)
                <tr class="border-b">
                    <td class="px-4 py-2">{{ $category->id }}</td>
                    <td class="px-4 py-2">{{ $category->name }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('admin.category.edit', $category->id) }}" class="text-blue-600">แก้ไขข้อมูล</a>
                        |
                        <a href="{{ route('admin.category.delete', $category->id) }}" class="text-blue-600">ลบรายการ</a>
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-admin-layout>
