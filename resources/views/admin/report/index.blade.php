<x-admin-layout>
    <div class="p-6 bg-white rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-4">Request Report</h2>

<!-- Request Report Button -->
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

<!-- Icon Navigation Buttons, simple layout -->    
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

        <!-- Table -->
        <table class="min-w-full text-sm border">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-2 text-left">Request ID</th>
                    <th class="px-4 py-2 text-left">ชื่อผู้ใช้</th>
                    <th class="px-4 py-2 text-left">ชื่ออุปกรณ์</th>
                    <th class="px-4 py-2 text-left">วันที่ส่งคำขอ</th>
                    <th class="px-4 py-2 text-left">สถานะ</th>
                    <th class="px-4 py-2 text-left">เหตุผล</th>
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
