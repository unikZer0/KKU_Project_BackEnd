<x-admin-layout>
    <!--Breadcrumb-->
    <nav class="flex items-center space-x-2 text-sm text-gray-500" aria-label="Breadcrumb">
        <!-- Home -->
        <a href="{{ route('admin.index') }}" class="hover:text-gray-700">Dashboard</a>

        <!-- Separator -->
        <span>/</span>

        <!-- Sales (inactive or intermediate) -->
        <a href="{{ route('admin.report.index') }}" class="hover:text-gray-700">Reports</a>

        <!-- Separator -->
        <span>></span>

        <!-- Current Page -->
        <span class="font-semibold text-gray-900">Requests</span>
    </nav>

    <div class="p-6 bg-white rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-4">Request Report</h2>
        <!-- Table -->
        <table class="min-w-full text-sm border">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-2 text-left">ID</th>
                    <th class="px-4 py-2 text-left">ชื่อผู้ใช้</th>
                    <th class="px-4 py-2 text-left">ชื่ออุปกรณ์</th>
                    <th class="px-4 py-2 text-left">วันที่ส่งคำขอ</th>
                    <th class="px-4 py-2 text-left">สถานะ</th>
                    <th class="px-4 py-2 text-left">เหตุผล</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($requests as $req)
                    <tr class="border-b">
                        <td class="px-4 py-2">{{ $req['req_id'] }}</td>
                        <td class="px-4 py-2">{{ $req['user_name'] }}</td>
                        <td class="px-4 py-2">{{ $req['equipment_name'] }}</td>
                        <td class="px-4 py-2">{{ $req['date'] }}</td>
                        <td class="px-4 py-2">{{ $req['status'] }}</td>
                        <td class="px-4 py-2">{{ $req['reason'] }}</td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <a href="{{ route('admin.report.export.requests') }}" class="btn btn-primary">Export request</a>
    </div>
</x-admin-layout>
