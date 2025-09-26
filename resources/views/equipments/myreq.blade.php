<x-app-layout>
    <div class="max-w-screen-2xl mx-auto py-6 px-3 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">คำขอยืมอุปกรณ์</h1>

        <!-- Latest 3 Requests Section -->
        <div class="mb-8">
            <h2 class="text-xl font-semibold text-gray-700 mb-4">คำขอล่าสุด (3 รายการ)</h2>
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
            @php
                    $latestActiveRequests = ($latestRequests ?? collect())->where('status', '!=', 'cancelled');
            @endphp

                @if ($latestActiveRequests->isEmpty())
                    <!-- Empty State for Latest -->
                <div class="text-center py-10 text-gray-500 col-span-full">
                    <svg class="mx-auto mb-4 w-12 h-12 text-gray-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6M9 8l6 6" />
                    </svg>
                    <p class="font-medium">คุณยังไม่มีคำขอ</p>
                </div>
            @else
                    @foreach ($latestActiveRequests as $req)
                    <div class="bg-white rounded-2xl shadow p-4 flex flex-col justify-between h-full" x-data="{ openModal: false }">
                        <!-- Card Content -->
                        <div>
                            <!-- Header -->
                            <div class="flex justify-between items-center border-b pb-3 mb-3">
                                <div class="lg:flex">
                                    <h2 class="text-lg font-semibold text-gray-800">คำขอเลขที่:&nbsp;</h2>
                                    <h2 class="text-lg font-semibold text-gray-800">#{{ $req->req_id }}</h2>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if ($req->status === 'pending')
                                        <div class="flex items-center gap-1 px-3 py-1 text-yellow-800 rounded-full text-sm font-medium status-pending">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                            </svg>
                                            รอดำเนินการ
                                        </div>
                                    @elseif($req->status === 'approved')
                                        <div class="flex items-center gap-1 px-3 py-1 text-green-800 rounded-full text-sm font-medium status-approved">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            อนุมัติ
                                        </div>
                                    @elseif($req->status === 'rejected')
                                        <div class="flex items-center gap-1 px-3 py-1 text-red-800 rounded-full text-sm font-medium status-rejected">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                            ปฏิเสธ
                                        </div>
                                    @elseif($req->status === 'cancelled')
                                        <div class="flex items-center gap-1 px-3 py-1 text-gray-700 rounded-full text-sm font-medium status-cancelled">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                            ยกเลิกแล้ว
                                        </div>
                                    @else
                                        <div class="flex items-center gap-1 px-3 py-1 bg-gray-100 border border-gray-300 text-gray-700 rounded-full text-sm font-medium shadow-sm">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                            </svg>
                                            ไม่ทราบสถานะ
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Equipment -->
                            <div class="mb-4 grid grid-cols-1 sm:grid-cols-4 gap-4">
                                @php
                                    $photos = json_decode($req->equipment->photo_path ?? '[]', true);
                                    $firstPhoto = is_array($photos) && count($photos) > 0 ? $photos[0] : $req->equipment->photo_path;
                                @endphp
                                <img src="{{ $firstPhoto }}" alt="equipment photo"
                                    class="w-full h-28 object-cover rounded-lg shadow col-span-1 sm:col-span-1">

                                <div class="col-span-1 sm:col-span-3 space-y-3 relative">
                                    <h3 class="font-semibold text-gray-800 break-words">{{ $req->equipment->name }}</h3>
                                    <p class="text-sm text-gray-500 break-words">รหัส: {{ $req->equipment->code }}</p>
                                    <p class="text-sm text-gray-500 break-words">หมวดหมู่: {{ $req->equipment->category->name }}</p>

                                    <!-- Truncated Description with Tooltip -->
                                    <p class="text-gray-500 text-sm break-words relative" x-data="{ tooltip: false }"
                                       @mouseenter="tooltip = true" @mouseleave="tooltip = false">
                                        {{ \Illuminate\Support\Str::limit($req->equipment->description, 50, '...') }}

                                        <span x-show="tooltip" x-transition
                                              class="absolute left-0 top-full mt-1 z-50 bg-gray-800 text-white text-xs p-2 rounded shadow-lg w-64 break-words">
                                            {{ $req->equipment->description }}
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <!-- Duration -->
                            <div class="mb-4">
                                <h3 class="font-semibold text-gray-700">ระยะเวลายืม</h3>
                                <p class="text-sm text-gray-600">เริ่มต้น: {{ $req->start_at->format('d/m/Y') }}</p>
                                <p class="text-sm text-gray-600">สิ้นสุด: {{ $req->end_at->format('d/m/Y') }}</p>
                            </div>

                            <!-- Reject Reason -->
                            @if ($req->status === 'rejected')
                                <h1 class="text-lg text-red-600 font-semibold">เหตุผลปฏิเสธ</h1>
                                <p class="text-sm text-red-600">{{ $req->reject_reason }}</p>
                            @endif
                        </div>

                        <!-- Card Footer -->
                        <div class="mt-6 flex justify-between items-center">
                            <a href="{{ route('borrower.equipments.reqdetail', $req->req_id) }}"
                                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition text-center">
                                ดูรายละเอียด
                            </a>

                            @if ($req->status === 'pending')
                                <button @click="openModal = true"
                                    class="bg-red-600 text-black px-6 py-2 rounded-lg hover:bg-red-700 transition">
                                    ยกเลิก
                                </button>
                            @endif
                        </div>

                        <!-- Cancel Modal -->
                        <div x-show="openModal"
                            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                            x-cloak>
                            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
                                <h3 class="text-lg font-semibold mb-4">เลือกเหตุผลการยกเลิก</h3>
                                <form action="{{ route('borrower.requests.cancel', $req->id) }}" method="POST"
                                    x-data="{ otherChecked: false, hasSelection: false }" @submit="if (!hasSelection) { $event.preventDefault(); alert('กรุณาเลือกเหตุผลการยกเลิก'); }">
                                    @csrf
                                    @method('PATCH')

                                    <div class="space-y-2 mb-4">
                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" name="cancel_reason[]" value="เปลี่ยนใจ"
                                                class="text-red-600 rounded" @click="otherChecked = false; hasSelection = true">
                                            <span>เปลี่ยนใจ</span>
                                        </label>
                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" name="cancel_reason[]" value="เลือกอุปกรณ์ผิด"
                                                class="text-red-600 rounded" @click="otherChecked = false; hasSelection = true">
                                            <span>เลือกอุปกรณ์ผิด</span>
                                        </label>
                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" name="cancel_reason[]" value="อื่น ๆ"
                                                class="text-red-600 rounded" x-model="otherChecked" @click="hasSelection = true">
                                            <span>อื่น ๆ</span>
                                        </label>
                                        <input type="text" name="cancel_reason[]" placeholder="ระบุเหตุผลอื่น..."
                                            class="mt-2 w-full border rounded px-3 py-2" x-show="otherChecked"
                                            x-transition @input="hasSelection = true">
                                    </div>

                                    <div class="flex justify-end gap-3">
                                        <button type="button" @click="openModal = false"
                                            class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300">
                                            ปิด
                                        </button>
                                        <button type="submit"
                                            class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">
                                            ยืนยันการยกเลิก
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>

        <!-- History Section -->
        <div class="mt-12">
            <h2 class="text-xl font-semibold text-gray-700 mb-6">ประวัติการยืม</h2>
            
            <!-- Search and Filter Controls -->
            <div class="bg-white rounded-lg shadow p-6 mb-6" x-data="{ showFilters: false }">
                <div class="flex flex-col lg:flex-row gap-4 mb-4">
                    <!-- Search -->
                    <div class="flex-1">
                        <form method="GET" class="flex gap-2">
                            <input type="text" name="search" value="{{ request('search') }}" 
                                   placeholder="ค้นหาตามชื่อ, รหัส, เลขที่คำขอ, หมวดหมู่..." 
                                   class="flex-1 border border-gray-300 rounded-lg px-4 py-2 focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                            <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                                ค้นหา
                            </button>
                        </form>
                    </div>
                    
                    <!-- Filter Toggle -->
                    <button @click="showFilters = !showFilters" 
                            class="bg-gray-100 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-200 transition">
                        <span x-show="!showFilters">แสดงตัวกรอง</span>
                        <span x-show="showFilters">ซ่อนตัวกรอง</span>
                    </button>
                </div>

                <!-- Advanced Filters -->
                <div x-show="showFilters" x-transition class="border-t pt-4">
                    <form method="GET" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">
                        <!-- Preserve search -->
                        <input type="hidden" name="search" value="{{ request('search') }}">
                        
                        <!-- Status Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">สถานะ</label>
                            <select name="status" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                <option value="">ทั้งหมด</option>
                                <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>รอดำเนินการ</option>
                                <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>อนุมัติ</option>
                                <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>ปฏิเสธ</option>
                                <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
                            </select>
                        </div>

                        <!-- Category Filter -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">หมวดหมู่</label>
                            <select name="category" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                <option value="">ทั้งหมด</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Date From -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">วันที่เริ่มต้น</label>
                            <input type="text" name="date_from" value="{{ request('date_from') }}" 
                                   placeholder="dd/mm/yyyy" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        </div>

                        <!-- Date To -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">วันที่สิ้นสุด</label>
                            <input type="text" name="date_to" value="{{ request('date_to') }}" 
                                   placeholder="dd/mm/yyyy" 
                                   class="w-full border border-gray-300 rounded-lg px-3 py-2">
                        </div>

                        <!-- Sort Controls -->
                        <div class="lg:col-span-4 flex flex-col sm:flex-row gap-4">
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">เรียงตาม</label>
                                <select name="sort_by" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                    <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>วันที่สร้าง</option>
                                    <option value="start_at" {{ request('sort_by') == 'start_at' ? 'selected' : '' }}>วันที่เริ่มต้น</option>
                                    <option value="end_at" {{ request('sort_by') == 'end_at' ? 'selected' : '' }}>วันที่สิ้นสุด</option>
                                    <option value="status" {{ request('sort_by') == 'status' ? 'selected' : '' }}>สถานะ</option>
                                </select>
                            </div>
                            <div class="flex-1">
                                <label class="block text-sm font-medium text-gray-700 mb-1">ลำดับ</label>
                                <select name="sort_order" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                                    <option value="desc" {{ request('sort_order') == 'desc' ? 'selected' : '' }}>ใหม่ → เก่า</option>
                                    <option value="asc" {{ request('sort_order') == 'asc' ? 'selected' : '' }}>เก่า → ใหม่</option>
                                </select>
                            </div>
                            <div class="flex items-end gap-2">
                                <button type="submit" class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                                    กรอง
                                </button>
                                <a href="{{ route('borrower.equipments.myreq') }}" 
                                   class="bg-gray-500 text-white px-6 py-2 rounded-lg hover:bg-gray-600 transition">
                                    ล้าง
                                </a>
                            </div>
                        </div>
                    </form>
                </div>
            </div>

            <!-- History Results -->
            <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                @if ($historyRequests->isEmpty())
                    <!-- Empty State for History -->
                    <div class="text-center py-10 text-gray-500 col-span-full">
                        <svg class="mx-auto mb-4 w-12 h-12 text-gray-300" fill="none" stroke="currentColor"
                            viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6M9 8l6 6" />
                        </svg>
                        <p class="font-medium">ไม่พบประวัติการยืม</p>
                    </div>
                @else
                    @foreach ($historyRequests as $req)
                    <div class="bg-white rounded-2xl shadow p-4 flex flex-col justify-between h-full" x-data="{ openModal: false }">
                        <!-- Card Content -->
                        <div>
                            <!-- Header -->
                            <div class="flex justify-between items-center border-b pb-3 mb-3">
                                <div class="lg:flex">
                                    <h2 class="text-lg font-semibold text-gray-800">คำขอเลขที่:&nbsp;</h2>
                                    <h2 class="text-lg font-semibold text-gray-800">#{{ $req->req_id }}</h2>
                                </div>
                                <div class="flex items-center gap-2">
                                    @if ($req->status === 'pending')
                                        <div class="flex items-center gap-1 px-3 py-1 text-yellow-800 rounded-full text-sm font-medium status-pending">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                            </svg>
                                            รอดำเนินการ
                                        </div>
                                    @elseif($req->status === 'approved')
                                        <div class="flex items-center gap-1 px-3 py-1 text-green-800 rounded-full text-sm font-medium status-approved">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                            </svg>
                                            อนุมัติ
                                        </div>
                                    @elseif($req->status === 'rejected')
                                        <div class="flex items-center gap-1 px-3 py-1 text-red-800 rounded-full text-sm font-medium status-rejected">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                            ปฏิเสธ
                                        </div>
                                    @elseif($req->status === 'cancelled')
                                        <div class="flex items-center gap-1 px-3 py-1 text-gray-700 rounded-full text-sm font-medium status-cancelled">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                            </svg>
                                            ยกเลิกแล้ว
                                        </div>
                                    @else
                                        <div class="flex items-center gap-1 px-3 py-1 bg-gray-100 border border-gray-300 text-gray-700 rounded-full text-sm font-medium shadow-sm">
                                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                            </svg>
                                            ไม่ทราบสถานะ
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Equipment -->
                            <div class="mb-4 grid grid-cols-1 sm:grid-cols-4 gap-4">
                                @php
                                    $photos = json_decode($req->equipment->photo_path ?? '[]', true);
                                    $firstPhoto = is_array($photos) && count($photos) > 0 ? $photos[0] : $req->equipment->photo_path;
                                @endphp
                                <img src="{{ $firstPhoto }}" alt="equipment photo"
                                    class="w-full h-28 object-cover rounded-lg shadow col-span-1 sm:col-span-1">

                                <div class="col-span-1 sm:col-span-3 space-y-3 relative">
                                    <h3 class="font-semibold text-gray-800 break-words">{{ $req->equipment->name }}</h3>
                                    <p class="text-sm text-gray-500 break-words">รหัส: {{ $req->equipment->code }}</p>
                                    <p class="text-sm text-gray-500 break-words">หมวดหมู่: {{ $req->equipment->category->name }}</p>

                                    <!-- Truncated Description with Tooltip -->
                                    <p class="text-gray-500 text-sm break-words relative" x-data="{ tooltip: false }"
                                       @mouseenter="tooltip = true" @mouseleave="tooltip = false">
                                        {{ \Illuminate\Support\Str::limit($req->equipment->description, 50, '...') }}

                                        <span x-show="tooltip" x-transition
                                              class="absolute left-0 top-full mt-1 z-50 bg-gray-800 text-white text-xs p-2 rounded shadow-lg w-64 break-words">
                                            {{ $req->equipment->description }}
                                        </span>
                                    </p>
                                </div>
                            </div>

                            <!-- Duration -->
                            <div class="mb-4">
                                <h3 class="font-semibold text-gray-700">ระยะเวลายืม</h3>
                                <p class="text-sm text-gray-600">เริ่มต้น: {{ $req->start_at->format('d/m/Y') }}</p>
                                <p class="text-sm text-gray-600">สิ้นสุด: {{ $req->end_at->format('d/m/Y') }}</p>
                            </div>

                            <!-- Reject Reason -->
                            @if ($req->status === 'rejected')
                                <h1 class="text-lg text-red-600 font-semibold">เหตุผลปฏิเสธ</h1>
                                <p class="text-sm text-red-600">{{ $req->reject_reason }}</p>
                            @endif
                        </div>

                        <!-- Card Footer -->
                        <div class="mt-6 flex justify-between items-center">
                            <a href="{{ route('borrower.equipments.reqdetail', $req->req_id) }}"
                                class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition text-center">
                                ดูรายละเอียด
                            </a>

                            @if ($req->status === 'pending')
                                <button @click="openModal = true"
                                    class="bg-red-600 text-black px-6 py-2 rounded-lg hover:bg-red-700 transition">
                                    ยกเลิก
                                </button>
                            @endif
                        </div>

                        <!-- Cancel Modal -->
                        <div x-show="openModal"
                            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
                            x-cloak>
                            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md p-6">
                                <h3 class="text-lg font-semibold mb-4">เลือกเหตุผลการยกเลิก</h3>
                                <form action="{{ route('borrower.requests.cancel', $req->id) }}" method="POST"
                                    x-data="{ otherChecked: false, hasSelection: false }" @submit="if (!hasSelection) { $event.preventDefault(); alert('กรุณาเลือกเหตุผลการยกเลิก'); }">
                                    @csrf
                                    @method('PATCH')

                                    <div class="space-y-2 mb-4">
                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" name="cancel_reason[]" value="เปลี่ยนใจ"
                                                class="text-red-600 rounded" @click="otherChecked = false; hasSelection = true">
                                            <span>เปลี่ยนใจ</span>
                                        </label>
                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" name="cancel_reason[]" value="เลือกอุปกรณ์ผิด"
                                                class="text-red-600 rounded" @click="otherChecked = false; hasSelection = true">
                                            <span>เลือกอุปกรณ์ผิด</span>
                                        </label>
                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" name="cancel_reason[]" value="อื่น ๆ"
                                                class="text-red-600 rounded" x-model="otherChecked" @click="hasSelection = true">
                                            <span>อื่น ๆ</span>
                                        </label>
                                        <input type="text" name="cancel_reason[]" placeholder="ระบุเหตุผลอื่น..."
                                            class="mt-2 w-full border rounded px-3 py-2" x-show="otherChecked"
                                            x-transition @input="hasSelection = true">
                                    </div>

                                    <div class="flex justify-end gap-3">
                                        <button type="button" @click="openModal = false"
                                            class="px-4 py-2 rounded-lg bg-gray-200 hover:bg-gray-300">
                                            ปิด
                                        </button>
                                        <button type="submit"
                                            class="px-4 py-2 rounded-lg bg-red-600 text-white hover:bg-red-700">
                                            ยืนยันการยกเลิก
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </div>
                    @endforeach
                @endif
            </div>
        </div>
    </div>

    @if (session('cancelsuccess'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'ยกเลิกสำเร็จ!',
                text: '{{ session('cancelsuccess') }}',
                timer: 2500,
                showConfirmButton: false
            });
        </script>
    @endif
</x-app-layout>

<style>
/* Enhanced Status Indicators */
.status-pending {
    background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);
    border: 2px solid #f59e0b;
    box-shadow: 0 2px 4px rgba(245, 158, 11, 0.2);
}

.status-approved {
    background: linear-gradient(135deg, #d1fae5 0%, #a7f3d0 100%);
    border: 2px solid #10b981;
    box-shadow: 0 2px 4px rgba(16, 185, 129, 0.2);
}

.status-rejected {
    background: linear-gradient(135deg, #fee2e2 0%, #fecaca 100%);
    border: 2px solid #ef4444;
    box-shadow: 0 2px 4px rgba(239, 68, 68, 0.2);
}

.status-cancelled {
    background: linear-gradient(135deg, #f3f4f6 0%, #e5e7eb 100%);
    border: 2px solid #6b7280;
    box-shadow: 0 2px 4px rgba(107, 114, 128, 0.2);
}

/* Pulse animation for pending status */
@keyframes pulse-yellow {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.8; }
}

.status-pending {
    animation: pulse-yellow 2s infinite;
}

/* Hover effects */
.status-pending:hover {
    transform: scale(1.05);
    transition: transform 0.2s ease;
}

.status-approved:hover {
    transform: scale(1.05);
    transition: transform 0.2s ease;
}

.status-rejected:hover {
    transform: scale(1.05);
    transition: transform 0.2s ease;
}

.status-cancelled:hover {
    transform: scale(1.05);
    transition: transform 0.2s ease;
}
</style>
