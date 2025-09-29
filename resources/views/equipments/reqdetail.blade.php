<x-app-layout>
    <div class="max-w-screen-2xl mx-auto py-6 px-3 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">คำขอยืมอุปกรณ์</h1>
            @php
                $activeRequests = ($reQuests ?? collect())->where('status', '!=', 'cancelled');
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
                                    <div class="flex items-center gap-1 px-3 py-1 text-gray-700 rounded-full text-sm font-medium status-cancelled">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7-4a1 1 0 11-2 0 1 1 0 012 0zM9 9a1 1 0 000 2v3a1 1 0 001 1h1a1 1 0 100-2v-3a1 1 0 00-1-1H9z" clip-rule="evenodd"></path>
                                        </svg>
                                        ไม่ทราบสถานะ
                                    </div>
                                @endif
                            </div>
                        </div>
                        <!-- Equipment -->
                        <div class="mb-4 flex flex-col sm:flex-row gap-4">
                            @php
                                $photos = json_decode($req->equipment->photo_path ?? '[]', true);
                                $firstPhoto = is_array($photos) && count($photos) > 0 ? $photos[0] : $req->equipment->photo_path;
                            @endphp
                            <img src="{{ $firstPhoto }}" alt="equipment photo"
                                class="w-full sm:w-28 h-28 object-cover rounded-lg shadow">
                            <div class="space-y-3 flex-1">
                                <h3 class="font-semibold text-gray-800 break-words">{{ $req->equipment->name }}</h3>
                                <p class="text-sm text-gray-500 break-words">รหัส: {{ $req->equipment->code }}</p>
                                <p class="text-sm text-gray-500 break-words">หมวดหมู่: {{ $req->equipment->category->name }}</p>
                                <div class="text-gray-500 text-sm">
                                    <div x-data="{ expanded: false }" class="block md:hidden">
                                        <span x-show="!expanded" class="break-words">
                                            {{ \Illuminate\Support\Str::limit($req->equipment->description, 80, '...') }}
                                        </span>
                                        <span x-show="expanded" class="break-words">
                                            {{ $req->equipment->description }}
                                        </span>
                                        <button @click="expanded = !expanded"
                                            class="ml-2 text-blue-600 underline focus:outline-none">
                                            <span x-show="!expanded">ดูเพี่มเตีม</span>
                                            <span x-show="expanded">แสดงน้อยลง</span>
                                        </button>
                                    </div>

                                    <div class="hidden md:block">
                                        <span class="break-words">{{ $req->equipment->description }}</span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="mb-4">
                            <h3 class="font-semibold text-gray-700">ระยะเวลายืม</h3>
                            <p class="text-sm text-gray-600">เริ่มต้น: {{ $req->start_at->format('d/m/Y') }}</p>
                            <p class="text-sm text-gray-600">สิ้นสุด: {{ $req->end_at->format('d/m/Y') }}</p>
                        </div>

                        <!-- Equipment Details Section -->
                        <div class="mb-6 bg-gray-50 rounded-lg p-4">
                            <h3 class="font-semibold text-gray-700 mb-4">รายละเอียดอุปกรณ์</h3>
                            
                            <!-- Equipment Summary -->
                            <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                                <div class="bg-white rounded-lg p-3 text-center">
                                    <div class="text-2xl font-bold text-blue-600">{{ $req->items ? $req->items->count() : 0 }}</div>
                                    <div class="text-sm text-gray-600">จำนวนอุปกรณ์ที่ยืม</div>
                                </div>
                                <div class="bg-white rounded-lg p-3 text-center">
                                    <div class="text-2xl font-bold text-green-600">{{ $req->items ? $req->items->sum(function($item) { return $item->accessories ? $item->accessories->count() : 0; }) : 0 }}</div>
                                    <div class="text-sm text-gray-600">ของที่ติดมากับเครื่อง</div>
                                </div>
                                @php
                                    $additionalAccessories = collect();

                                    // General accessories for this equipment (not assigned to specific item)
                                    $generalAccessories = \App\Models\EquipmentAccessory::where('equipment_id', $req->equipment_id)
                                        ->whereNull('equipment_item_id')
                                        ->pluck('id')
                                        ->toArray();

                                    // Loop through borrowed items
                                    foreach ($req->items as $item) {
                                        foreach ($item->accessories as $borrowedAccessory) {
                                            // Only add if it's in the general accessories and available
                                            if ($borrowedAccessory->accessory && in_array($borrowedAccessory->accessory->id, $generalAccessories)) {
                                                $additionalAccessories->push($borrowedAccessory);
                                            }
                                        }
                                    }

                                    // Assigned accessories already linked to this equipment item (available only)
                                    $assignedAccessories = collect();
                                    foreach ($req->items as $item) {
                                        foreach ($item->accessories as $borrowedAccessory) {
                                            if ($borrowedAccessory->accessory && $borrowedAccessory->accessory->equipment_item_id == $item->equipment_item_id && $borrowedAccessory->accessory->status == 'available') {
                                                $assignedAccessories->push($borrowedAccessory);
                                            }
                                        }
                                    }

                                    // Merge both sets
                                    $allAccessories = $assignedAccessories->merge($additionalAccessories);
                                    @endphp

                                <div class="bg-white rounded-lg p-3 text-center">
                                    <div class="text-2xl font-bold text-green-600">{{ $additionalAccessories->count() }}</div>
                                    <div class="text-sm text-gray-600">อุปกรณ์เสริมทั้งหมด</div>
                                </div>
                            </div>

                            <!-- Individual Equipment Items -->
                            @if($req->items && $req->items->count() > 0)
                                <div class="space-y-4">
                                    <h4 class="font-medium text-gray-700">รายการอุปกรณ์ที่ยืม</h4>
                                    @foreach($req->items as $index => $item)
                                        <div class="bg-white rounded-lg p-4 border">
                                            <div class="flex justify-between items-start mb-3">
                                                <h5 class="font-medium text-gray-800">อุปกรณ์ชิ้นที่ {{ $index + 1 }}</h5>
                                                <span class="text-sm text-gray-500">Serial: {{ $item->equipmentItem->serial_number ?? 'N/A' }}</span>
                                            </div>
                                            
                                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-3">
                                                <div>
                                                    <span class="text-sm text-gray-600">สภาพเมื่อยืม:</span>
                                                    <span class="ml-2 px-2 py-1 bg-blue-100 text-blue-800 rounded text-sm">
                                                        {{ $item->condition_out ?? 'ไม่ระบุ' }}
                                                    </span>
                                                </div>
                                            </div>

                                            <!-- Accessories for this item -->
                                            @php
                                                // Filter accessories that are specifically attached to this equipment item
                                                $itemSpecificAccessories = $item->accessories->filter(function($accessory) use ($item) {
                                                    return $accessory->accessory && 
                                                           $accessory->accessory->equipment_item_id == $item->equipment_item_id;
                                                });
                                            @endphp
                                            @if($itemSpecificAccessories->count() > 0)
                                                <div class="mt-3">
                                                    <h6 class="text-sm font-medium text-gray-700 mb-2">ของที่ติดมากับเครื่อง ({{ $itemSpecificAccessories->count() }} รายการ)</h6>
                                                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                                        @foreach($itemSpecificAccessories as $accessory)
                                                            <div class="bg-gray-50 rounded p-2 text-sm">
                                                                <div class="font-medium text-gray-800">{{ $accessory->accessory->name ?? 'N/A' }}</div>
                                                                @if($accessory->accessory && $accessory->accessory->description)
                                                                    <div class="text-gray-600 text-xs">{{ $accessory->accessory->description }}</div>
                                                                @endif
                                                                <div class="flex justify-between items-center mt-1">
                                                                    <span class="text-xs text-gray-500">สภาพ: {{ $accessory->condition_out ?? 'ไม่ระบุ' }}</span>
                                                                    @if($accessory->condition_in)
                                                                        <span class="text-xs text-gray-500">คืน: {{ $accessory->condition_in }}</span>
                                                                    @endif
                                                                </div>
                                                            </div>
                                                        @endforeach
                                                    </div>
                                                </div>
                                            @else
                                                <div class="text-sm text-gray-500 italic">ไม่มีของที่ติดมากับเครื่อง</div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            @else
                                <div class="text-center py-4 text-gray-500">
                                    <p>ไม่พบรายการอุปกรณ์</p>
                                </div>
                            @endif
                            
                            @if($additionalAccessories->count() > 0)
                            <div class="mt-4">
                                <h4 class="font-medium text-gray-700 mb-2">อุปกรณ์เสริมที่ยืมเพิ่ม</h4>
                                <div class="grid grid-cols-1 sm:grid-cols-2 gap-2">
                                    @foreach($additionalAccessories as $accessory)
                                        <div class="bg-gray-50 rounded p-2 text-sm">
                                            <div class="font-medium text-gray-800">{{ $accessory->accessory->name ?? 'N/A' }}</div>
                                            @if($accessory->accessory && $accessory->accessory->description)
                                                <div class="text-gray-600 text-xs">{{ $accessory->accessory->description }}</div>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                        </div>

                        @if ($req->status === 'rejected')
                            <h1 class="text-lg text-red-600 font-semibold ">
                                เหตุผลปฏิเสธ
                            </h1>
                            <p class="text-sm text-red-600">{{ $req->reject_reason }}</p>
                        @endif
                        <div class="mt-6">
                            <div class="flex flex-col sm:flex-row gap-3 justify-between">
                                <a href="{{route('borrower.equipments.myreq')}}"
                                    class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition text-center">
                                    กลับ
                                </a>
                                @if ($req->status === 'pending')
                                <button @click="openModal = true"
                                    class="bg-red-600 text-white px-6 py-2 rounded-lg hover:bg-red-700 transition">
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
                    </div>
                @endforeach
            @endif
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
    @if (session('reqsuccess'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'ส่งคำขอสำเร็จ!',
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
