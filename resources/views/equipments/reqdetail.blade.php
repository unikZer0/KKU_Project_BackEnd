<x-app-layout>
    <div class="max-w-screen-2xl mx-auto py-6 px-3 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">คำขอยืมอุปกรณ์</h1>
            @php
                $activeRequests = $reQuests->where('status', '!=', 'cancelled');
            @endphp
            @if ($activeRequests->isEmpty())
                <div class="text-center py-10 text-gray-500 ">
                    <svg class="mx-auto mb-4 w-12 h-12 text-gray-300" fill="none" stroke="currentColor"
                        viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 14l6-6M9 8l6 6">
                        </path>
                    </svg>
                    <p class="font-medium">คุณยังไม่มีคำขอ</p>
                </div>
            @else
                @foreach ($activeRequests as $req)
                    <div class="bg-white rounded-2xl shadow p-3 mb-10" x-data="{ openModal: false }">
                        <!-- Header -->
                        <div class="flex justify-between items-center border-b pb-4 mb-4">
                            <div class="lg:flex">
                                <h2 class="text-lg font-semibold text-gray-800">คำขอเลขที่: &nbsp</h2>
                                <h2 class="text-lg font-semibold text-gray-800">
                                    #{{ $req->req_id }}
                                </h2>
                            </div>
                            <span
                                class="px-3 py-1 text-sm rounded-full
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
                        <div class=" mb-4 ">
                            <img src="{{ $req->equipment->photo_path }}" alt="equipment photo"
                                class="w-28 h-28 object-cover rounded-lg shadow">
                            <div class="space-y-3">
                                <h3 class="font-semibold text-gray-800">{{ $req->equipment->name }}</h3>
                                <p class="text-sm text-gray-500">รหัส: {{ $req->equipment->code }}</p>
                                <p class="text-sm text-gray-500">หมวดหมู่: {{ $req->equipment->category->name }}</p>
                                <div class="text-gray-500 text-sm">
                                    <div x-data="{ expanded: false }" class="block md:hidden">
                                        <span x-show="!expanded">
                                            {{ \Illuminate\Support\Str::limit($req->equipment->description, 80, '...') }}
                                        </span>
                                        <span x-show="expanded">
                                            {{ $req->equipment->description }}
                                        </span>
                                        <button @click="expanded = !expanded"
                                            class="ml-2 text-blue-600 underline focus:outline-none">
                                            <span x-show="!expanded">ดูเพี่มเตีม</span>
                                            <span x-show="expanded">แสดงน้อยลง</span>
                                        </button>
                                    </div>

                                    <div class="hidden md:block">
                                        {{ $req->equipment->description }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h3 class="font-semibold text-gray-700">ระยะเวลายืม</h3>
                            <p class="text-sm text-gray-600">เริ่มต้น: {{ $req->start_at->format('d/m/Y') }}</p>
                            <p class="text-sm text-gray-600">สิ้นสุด: {{ $req->end_at->format('d/m/Y') }}</p>
                        </div>

                        @if ($req->status === 'rejected')
                            <h1 class="text-lg text-red-600 font-semibold ">
                                เหตุผลปฏิเสธ
                            </h1>
                            <p class="text-sm text-red-600">{{ $req->reject_reason }}</p>
                        @endif
                        <div class="container w-185">
                            
                                <div class="flex flex mt-6 justify-between ">
                                    <button
                                        class="bg-blue-600 text-white px-8 py-1 rounded-lg hover:bg-blue-700 transition">
                                        <a href="{{route('borrower.equipments.myreq')}}">
                                            กลับ
                                        </a>
                                    </button>
                                    @if ($req->status === 'pending')
                                    <button @click="openModal = true"
                                        class="bg-red-600 text-white px-8 py-1 rounded-lg hover:bg-red-700 transition">
                                        ยกเลิกคำขอ
                                    </button>
                                    @endif
                                </div>
                            
                            <!-- Modal -->
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
                    </div>
                @endforeach
            @endif
    </div>

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
