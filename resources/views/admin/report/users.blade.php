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
        <span class="font-semibold text-gray-900">Users report</span>
    </nav>
    <div class="p-4 bg-white rounded-lg border">
        <h2 class="text-xl font-bold mb-4">User Report</h2>
        <table class="min-w-full border">
            <thead>
                <tr>
                    <th class="border px-2 py-1">ID</th>
                    <th class="border px-2 py-1">ชื่อผู้ใช้</th>
                    <th class="border px-2 py-1">อายุ</th>
                    <th class="border px-2 py-1">เบอร์โทร</th>
                    <th class="border px-2 py-1">วันที่สร้าง</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($users as $user)
                    <tr>
                        <td class="border px-2 py-1">{{ $user['uid'] }}</td>
                        <td class="border px-2 py-1">{{ $user['name'] }}</td>
                        <td class="border px-2 py-1">{{ $user['email'] }}</td>
                        <td class="border px-2 py-1">{{ $user['phonenumber'] }}</td>
                        <td class="border px-2 py-1">{{ $user['created_at'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-admin-layout>
