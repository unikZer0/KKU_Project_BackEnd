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
        <span class="font-semibold text-gray-900">Equipments report</span>
    </nav>
    <div class="p-4 bg-white rounded-lg border">

        <h2 class="text-xl font-bold mb-4">รีพอร์ต อุปกรณ์</h2>
        <table class="min-w-full border">
            <thead>
                <tr>
                    <th class="border px-2 py-1">ไอดี</th>
                    <th class="border px-2 py-1">หมายเลขครุภัณฑ์</th>
                    <th class="border px-2 py-1">ชื่ออุปกรณ์</th>
                    <th class="border px-2 py-1">หมวดหมู่</th>
                    <th class="border px-2 py-1">วันที่เพิ่ม</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($equipments as $eq)
                    <tr>
                        <td class="border px-2 py-1">{{ $eq['id'] }}</td>
                        <td class="border px-2 py-1">{{ $eq['code'] }}</td>
                        <td class="border px-2 py-1">{{ $eq['name'] }}</td>
                        <td class="border px-2 py-1">{{ $eq['category_name'] }}</td>
                        <td class="border px-2 py-1">{{ $eq['created_at'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <a href="{{ route('admin.report.export.equipments') }}" class="btn btn-primary">Export Equipment</a>
    </div>
</x-admin-layout>
