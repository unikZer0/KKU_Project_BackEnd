<x-app-layout>
    <div class="max-w-screen-2xl mx-auto py-6 px-3 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">คำขอยืมอุปกรณ์</h1>

        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6 my-5">
            @php
                $activeRequests = ($requests ?? collect())->where('status', '!=', 'cancelled');
            @endphp

            @if ($activeRequests->isEmpty())
                <!-- Empty State -->
                <div class="text-center py-10 text-gray-500 col-span-full">
                    <svg class="mx-auto mb-4 w-12 h-12 text-gray-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6M9 8l6 6" />
                    </svg>
                    <p class="font-medium">คุณยังไม่มีคำขอ</p>
                </div>
            @else
                @foreach ($activeRequests as $req)
                    <div class="bg-white rounded-2xl shadow p-4 flex flex-col justify-between h-full" x-data="{ openModal: false }">
                        <!-- Card Content -->
                        <div>
                            <!-- Header -->
                            <div class="flex justify-between items-center border-b pb-3 mb-3">
                                <div class="lg:flex">
                                    <h2 class="text-lg font-semibold text-gray-800">คำขอเลขที่:&nbsp;</h2>
                                    <h2 class="text-lg font-semibold text-gray-800">#{{ $req->req_id }}</h2>
                                </div>
                                <span class="px-3 py-1 text-sm rounded-full
                                    @if ($req->status === 'pending') bg-yellow-100 text-yellow-700
                                    @elseif($req->status === 'approved') bg-green-100 text-green-700
                                    @elseif($req->status === 'rejected') bg-red-100 text-red-700
                                    @elseif($req->status === 'cancelled') bg-gray-200 text-gray-600
                                    @else bg-gray-100 text-gray-700 @endif">
                                    {{ $req->status === 'pending' ? 'รอดำเนินการ' : '' }}
                                    {{ $req->status === 'approved' ? 'อนุมัติ' : '' }}
                                    {{ $req->status === 'rejected' ? 'ปฏิเสธ' : '' }}
                                    {{ $req->status === 'cancelled' ? 'ยกเลิกแล้ว' : '' }}
                                </span>
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
                                    x-data="{ otherChecked: false }">
                                    @csrf
                                    @method('PATCH')

                                    <div class="space-y-2 mb-4">
                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" name="cancel_reason[]" value="เปลี่ยนใจ"
                                                class="text-red-600 rounded" @click="otherChecked = false">
                                            <span>เปลี่ยนใจ</span>
                                        </label>
                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" name="cancel_reason[]" value="เลือกอุปกรณ์ผิด"
                                                class="text-red-600 rounded" @click="otherChecked = false">
                                            <span>เลือกอุปกรณ์ผิด</span>
                                        </label>
                                        <label class="flex items-center gap-2">
                                            <input type="checkbox" name="cancel_reason[]" value="อื่น ๆ"
                                                class="text-red-600 rounded" x-model="otherChecked">
                                            <span>อื่น ๆ</span>
                                        </label>
                                        <input type="text" name="cancel_reason[]" placeholder="ระบุเหตุผลอื่น..."
                                            class="mt-2 w-full border rounded px-3 py-2" x-show="otherChecked"
                                            x-transition>
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

    <!-- SweetAlert -->
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'ยกเลิกสำเร็จ!',
                text: '{{ session('success') }}',
                timer: 2500,
                showConfirmButton: false
            });
        </script>
    @endif

    @if (session('error'))
        <script>
            Swal.fire({
                icon: 'error',
                title: 'เกิดข้อผิดพลาด!',
                text: '{{ session('error') }}',
                timer: 2500,
                showConfirmButton: false
            });
        </script>
    @endif
</x-app-layout>
