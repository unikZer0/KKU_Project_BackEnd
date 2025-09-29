<x-admin-layout>
    <div class="max-w-screen-2xl mx-auto py-6 px-3 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">รายละเอียดคำขอยืมอุปกรณ์</h1>
        
        <div class="bg-white rounded-2xl shadow p-6 mb-6">
            <!-- Header -->
        <div class="flex flex-col lg:flex-row items-start gap-6">
            @if ($requests->equipment?->photo_path)
                @php
                    $photos = json_decode($requests->equipment->photo_path ?? '[]', true);
                    $firstPhoto = is_array($photos) && count($photos) > 0 ? $photos[0] : $requests->equipment->photo_path;
                @endphp
                <img src="{{ $firstPhoto }}" alt="{{ $requests->equipment->name }}"
                        class="w-full sm:w-40 h-40 object-cover rounded-lg shadow" />
            @endif
            <div class="flex-1 w-full">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                        <div class="lg:flex">
                            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">คำขอเลขที่: &nbsp</h2>
                            <h2 class="text-xl sm:text-2xl font-bold text-gray-800">#{{ $requests->req_id }}</h2>
                        </div>
                        <div class="flex items-center gap-2">
                            @if ($requests->status === 'pending')
                                <div class="flex items-center gap-1 px-3 py-1 text-yellow-800 rounded-full text-sm font-medium status-pending">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    รอดำเนินการ
                                </div>
                            @elseif($requests->status === 'approved')
                                <div class="flex items-center gap-1 px-3 py-1 text-green-800 rounded-full text-sm font-medium status-approved">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                                    </svg>
                                    อนุมัติ
                                </div>
                            @elseif($requests->status === 'rejected')
                                <div class="flex items-center gap-1 px-3 py-1 text-red-800 rounded-full text-sm font-medium status-rejected">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                    ปฏิเสธ
                                </div>
                            @elseif($requests->status === 'check_out')
                                <div class="flex items-center gap-1 px-3 py-1 text-blue-800 rounded-full text-sm font-medium status-checkout">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-8.293l-3-3a1 1 0 00-1.414 0l-3 3a1 1 0 001.414 1.414L9 9.414V13a1 1 0 102 0V9.414l1.293 1.293a1 1 0 001.414-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                    เช็คเอาท์แล้ว
                                </div>
                            @elseif($requests->status === 'check_in')
                                <div class="flex items-center gap-1 px-3 py-1 text-purple-800 rounded-full text-sm font-medium status-checkin">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm1-12a1 1 0 10-2 0v4a1 1 0 00.293.707l2.828 2.829a1 1 0 101.415-1.415L11 9.586V6z" clip-rule="evenodd"></path>
                                    </svg>
                                    เช็คอินแล้ว
                                </div>
                            @elseif($requests->status === 'cancelled')
                                <div class="flex items-center gap-1 px-3 py-1 text-gray-700 rounded-full text-sm font-medium status-cancelled">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                                    </svg>
                                    ยกเลิกแล้ว
                                </div>
                            @endif
                        </div>
                    </div>
                    <p class="text-gray-500 mt-1">สร้างเมื่อ {{ $requests->created_at->format('d/m/Y') }}</p>

                    <!-- Equipment -->
                    <div class="mb-4 flex flex-col sm:flex-row gap-4">
                        <div class="space-y-3 flex-1">
                            <h3 class="font-semibold text-gray-800 break-words">{{ $requests->equipment->name }}</h3>
                            <p class="text-sm text-gray-500 break-words">รหัส: {{ $requests->equipment->code }}</p>
                            <p class="text-sm text-gray-500 break-words">หมวดหมู่: {{ $requests->equipment->category->name }}</p>
                            <div class="text-gray-500 text-sm">
                                <div x-data="{ expanded: false }" class="block md:hidden">
                                    <span x-show="!expanded" class="break-words">
                                        {{ \Illuminate\Support\Str::limit($requests->equipment->description, 80, '...') }}
                                    </span>
                                    <span x-show="expanded" class="break-words">
                                        {{ $requests->equipment->description }}
                    </span>
                                    <button @click="expanded = !expanded"
                                        class="ml-2 text-blue-600 underline focus:outline-none">
                                        <span x-show="!expanded">ดูเพิ่มเติม</span>
                                        <span x-show="expanded">แสดงน้อยลง</span>
                                    </button>
                                </div>

                                <div class="hidden md:block">
                                    <span class="break-words">{{ $requests->equipment->description }}</span>
                                </div>
                            </div>
                        </div>
                </div>

                    <div class="mb-4">
                        <h3 class="font-semibold text-gray-700">ข้อมูลผู้ใช้</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                        <div class="space-y-1 text-sm">
                            <p><span class="text-gray-500">ชื่อ:</span> {{ $requests->user->name ?? '-' }}</p>
                            <p><span class="text-gray-500">UID:</span> {{ $requests->user->uid ?? '-' }}</p>
                            </div>
                            <div class="space-y-1 text-sm">
                            <p><span class="text-gray-500">อีเมล:</span> {{ $requests->user->email ?? '-' }}</p>
                                <p><span class="text-gray-500">เบอร์โทร:</span> {{ $requests->user->phone ?? '-' }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="mb-4">
                        <h3 class="font-semibold text-gray-700">ระยะเวลายืม</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                            <p class="text-sm text-gray-600">เริ่มต้น: {{ $requests->start_at ? $requests->start_at->setTimezone('Asia/Bangkok')->format('d/m/Y') : '-' }}</p>
                            <p class="text-sm text-gray-600">สิ้นสุด: {{ $requests->end_at ? $requests->end_at->setTimezone('Asia/Bangkok')->format('d/m/Y') : '-' }}</p>
                        </div>
                    </div>

                <!-- Request Reason Section -->
                <div class="mt-6">
                    <h3 class="font-semibold text-gray-700 mb-2">เหตุผลในการขอยืม</h3>
                    <div class="bg-gray-50 rounded-lg p-4">
                        <p class="text-sm text-gray-700">{{ $requests->request_reason ?? '-' }}</p>
                    </div>
                </div>

                    <!-- Equipment Details Section -->
                    <div class="mb-6 bg-gray-50 rounded-lg p-4">
                        <h3 class="font-semibold text-gray-700 mb-4">รายละเอียดอุปกรณ์</h3>
                                                    @php
                                            // Filter accessories that are specifically attached to this equipment item
                                            
                            // Get accessories that were originally general accessories (equipment_item_id = NULL)
                            // and were selected as additional accessories, not the ones that belong to specific equipment items
                            $additionalAccessories = collect();
                            
                            // First, get all general accessories for this equipment (equipment_item_id = NULL)
                            $generalAccessories = \App\Models\EquipmentAccessory::where('equipment_id', operator: $requests->equipment_id)
                                ->whereNull('equipment_item_id')
                                ->pluck('id')
                                ->toArray();
                            
                            // Then, find which of these general accessories were actually borrowed
                            foreach($requests->items as $item) {
                                foreach($item->accessories as $borrowedAccessory) {
                                    if($borrowedAccessory->accessory && 
                                       in_array($borrowedAccessory->accessory->id, $generalAccessories)) {
                                        $additionalAccessories->push($borrowedAccessory);
                                    }
                                }
                            }
                            $itemSpecificAccessories = $item->accessories->filter(function($accessory) use ($item) {
                                                return $accessory->accessory && 
                                                       $accessory->accessory->equipment_item_id == $item->equipment_item_id;
                                            });
                        @endphp
                        <!-- Equipment Summary -->
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-4">
                            <div class="bg-white rounded-lg p-3 text-center">
                                <div class="text-2xl font-bold text-blue-600">{{ $requests->items ? $requests->items->count() : 0 }}</div>
                                <div class="text-sm text-gray-600">จำนวนอุปกรณ์ที่ยืม</div>
                            </div>
                            
                            <div class="bg-white rounded-lg p-3 text-center">
                                <div class="text-2xl font-bold text-green-600">{{ $itemSpecificAccessories->count() }}</div>
                                <div class="text-sm text-gray-600">ของที่ติดมากับเครื่อง</div>
                            </div>
                            <div class="bg-white rounded-lg p-3 text-center">
                                <div class="text-2xl font-bold text-green-600">{{ $additionalAccessories->count() }}</div>
                                <div class="text-sm text-gray-600">อุปกรณ์เสริมทั้งหมด</div>
                            </div>
                        </div>

                        <!-- Individual Equipment Items -->
                        @if($requests->items && $requests->items->count() > 0)
                            <div class="space-y-4">
                                <h4 class="font-medium text-gray-700">รายการอุปกรณ์ที่ยืม</h4>
                                @foreach($requests->items as $index => $item)
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
                                            @if($item->condition_in)
                                            <div>
                                                <span class="text-sm text-gray-600">สภาพเมื่อคืน:</span>
                                                <span class="ml-2 px-2 py-1 {{ $item->condition_in === 'สภาพดี' ? 'bg-green-100 text-green-800' : ($item->condition_in === 'สามาถซ่อมได้' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }} rounded text-sm">
                                                    {{ $item->condition_in }}
                                                </span>
                                            </div>
                                            @elseif($requests->status === 'check_out')
                                            <div>
                                                <span class="text-sm text-gray-600">สภาพเมื่อคืน:</span>
                                                <select name="item_condition_in[{{ $item->id }}]" class="ml-2 border rounded px-2 py-1 text-sm">
                                                    <option value="">เลือกสภาพ</option>
                                                    <option value="สภาพดี" {{ $item->condition_in === 'สภาพดี' ? 'selected' : '' }}>สภาพดี</option>
                                                    <option value="ไม่สามาถซ่อมได้" {{ $item->condition_in === 'ไม่สามาถซ่อมได้' ? 'selected' : '' }}>ไม่สามาถซ่อมได้</option>
                                                    <option value="พัง" {{ $item->condition_in === 'พัง' ? 'selected' : '' }}>พัง</option>
                                                    <option value="อุปกรณ์ไม่พร้อมใช้งาน" {{ $item->condition_in === 'อุปกรณ์ไม่พร้อมใช้งาน' ? 'selected' : '' }}>อุปกรณ์ไม่พร้อมใช้งาน</option>
                                                </select>
                                            </div>
                                            @endif
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
                                                                    <span class="text-xs px-1 py-0.5 rounded {{ $accessory->condition_in === 'สภาพดี' ? 'bg-green-100 text-green-800' : ($accessory->condition_in === 'สามาถซ่อมได้' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                                                        คืน: {{ $accessory->condition_in }}
                                                                    </span>
                                                                @elseif($requests->status === 'check_out')
                                                                    <select name="accessory_condition_in[{{ $accessory->id }}]" class="text-xs border rounded px-1 py-0.5" onchange="updateMainItemCondition({{ $item->id }}, this.value)">
                                                                        <option value="">เลือกสภาพ</option>
                                                                        <option value="สภาพดี" {{ $accessory->condition_in === 'สภาพดี' ? 'selected' : '' }}>สภาพดี</option>
                                                                        <option value="ไม่สามาถซ่อมได้" {{ $accessory->condition_in === 'ไม่สามาถซ่อมได้' ? 'selected' : '' }}>ไม่สามาถซ่อมได้</option>
                                                                        <option value="พัง" {{ $accessory->condition_in === 'พัง' ? 'selected' : '' }}>พัง</option>
                                                                        <option value="หาย" {{ $accessory->condition_in === 'หาย' ? 'selected' : '' }}>หาย</option>
                                                                    </select>
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
                                            <div class="flex justify-between items-center mt-1">
                                                <span class="text-xs text-gray-500">สภาพ: {{ $accessory->condition_out ?? 'ไม่ระบุ' }}</span>
                                                @if($accessory->condition_in)
                                                    <span class="text-xs text-gray-500">คืน: {{ $accessory->condition_in }}</span>
                                                @elseif($requests->status === 'check_out')
                                                    <select name="accessory_condition_in[{{ $accessory->id }}]" class="text-xs border rounded px-1 py-0.5">
                                                        <option value="">เลือกสภาพ</option>
                                                        <option value="สภาพดี" {{ $accessory->condition_in === 'สภาพดี' ? 'selected' : '' }}>สภาพดี</option>
                                                        <option value="ไม่สามาถซ่อมได้" {{ $accessory->condition_in === 'ไม่สามาถซ่อมได้' ? 'selected' : '' }}>ไม่สามาถซ่อมได้</option>
                                                        <option value="พัง" {{ $accessory->condition_in === 'พัง' ? 'selected' : '' }}>พัง</option>
                                                        <option value="หาย" {{ $accessory->condition_in === 'หาย' ? 'selected' : '' }}>หาย</option>
                                                    </select>
                                                @endif
                                            </div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endif
                    </div>

                <!-- Rejection/Cancellation Details -->
                @if($requests->status === 'rejected' && $requests->reject_reason)
                <div class="mt-6">
                    <h3 class="font-semibold text-red-700 mb-2">เหตุผลในการปฏิเสธ</h3>
                    <div class="bg-red-50 border border-red-200 rounded-lg p-4">
                        <p class="text-sm text-red-700">{{ $requests->reject_reason }}</p>
                    </div>
                </div>
                @endif

                @if($requests->status === 'cancelled' && $requests->cancel_reason)
                <div class="mt-6">
                    <h3 class="font-semibold text-orange-700 mb-2">เหตุผลในการยกเลิก</h3>
                    <div class="bg-orange-50 border border-orange-200 rounded-lg p-4">
                        <p class="text-sm text-orange-700">{{ $requests->cancel_reason }}</p>
                    </div>
                </div>
                @endif
                </div>
            </div>
        </div>

        <!-- Transaction Section -->
        <div class="bg-white rounded-2xl shadow p-6">
                <div class="mt-8">
                    <h3 class="font-semibold text-gray-700 mb-4">ข้อมูลธุรกรรม</h3>
                    
                    @if ($requests->status != 'pending')
                    <!-- Transaction Status Info -->
                    <div class="mb-6 p-4 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-4 text-sm">
                            <div>
                                <span class="text-gray-600">สถานะ:</span>
                                <span class="ml-2 px-2 py-1 rounded-full text-xs {{ $requests->status === 'approved' ? 'bg-green-100 text-green-700' : ($requests->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                                    {{ ucfirst($requests->status) }}
                                </span>
                            </div>
                            @if($requests->pickup_deadline)
                            <div>
                                <span class="text-gray-600">กำหนดมารับ:</span>
                                <span class="ml-2 font-medium">{{ $requests->pickup_deadline->setTimezone('Asia/Bangkok')->format('d/m/Y') }}</span>
                            </div>
                            @endif
                            @if($requests->is_checked_out)
                            <div>
                                <span class="text-gray-600">ผู้เช็คเอาท์:</span>
                                <span class="ml-2 font-medium">{{ $requests->checked_out_by ?? '-' }}</span>
                            </div>
                            @endif
                        </div>
                    </div>
                    @endif

                    @if ($requests->status == 'pending')
                    <!-- Show only basic form for pending requests -->
                    <form action="{{ route('admin.requests.approve', $requests->req_id) }}" method="POST"
                        class="space-y-5">
                        @csrf
                        @method('PATCH')
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-gray-50 rounded p-4">
                                <label class="text-gray-500 text-sm block mb-1">วันที่เริ่ม</label>
                                <input type="date" id="start_at" name="start_at"
                                    class="w-full border rounded px-2 py-1"
                                    value="{{ optional($requests->start_at)->setTimezone('Asia/Bangkok')->format('Y-m-d') }}" />
                            </div>

                            <div class="bg-gray-50 rounded p-4">
                                <label class="text-gray-500 text-sm block mb-1">วันที่มาส่งคืนที่อนุญาต</label>
                                <input type="date" id="end_at" name="end_at"
                                    class="w-full border rounded px-2 py-1"
                                    value="{{ optional($requests->end_at)->setTimezone('Asia/Bangkok')->format('Y-m-d') }}" />
                            </div>

                            <div class="bg-gray-50 rounded p-4">
                                <p class="font-semibold text-lg">รวมวันที่จะยืมทั้งสิ้น</p>
                                <p id="total-days" class="font-semibold">0 วัน</p>
                            </div>
                        </div>
                        
                        <!-- Action buttons for pending requests -->
                        <div class="flex flex-col sm:flex-row justify-end gap-3 mt-4">
                            <button type="button"
                                class="bg-red-600 text-white px-4 sm:px-6 py-2 rounded-md hover:bg-red-700 text-sm sm:text-base"
                                onclick="showRejectModal()">
                                ปฏิเสธ
                            </button>
                            <button type="submit"
                                class="bg-blue-600 text-white px-4 sm:px-6 py-2 rounded-md hover:bg-blue-700 text-sm sm:text-base">
                                อนุมัติ
                            </button>
                        </div>
                    </form>
                    @else
                    <!-- Show full transaction details for non-pending requests -->
                    <form action="{{ route('admin.requests.approve', $requests->req_id) }}" method="POST"
                        class="space-y-5">
                        @csrf
                        @method('PATCH')
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-gray-50 rounded p-4">
                                <label class="text-gray-500 text-sm block mb-1">วันที่เริ่ม</label>
                                <input type="date" id="start_at" name="start_at"
                                    class="w-full border rounded px-2 py-1 {{ in_array($requests->status, ['approved', 'rejected', 'cancelled', 'check_in']) ? 'bg-gray-100' : '' }}"
                                    value="{{ optional($requests->start_at)->setTimezone('Asia/Bangkok')->format('Y-m-d') }}" 
                                    @if (in_array($requests->status, ['approved', 'rejected', 'cancelled', 'check_in'])) readonly @endif />
                            </div>


                            <div class="bg-gray-50 rounded p-4">
                                <label class="text-gray-500 text-sm block mb-1">วันที่มาส่งคืนที่อนุญาต</label>
                                <input type="date" id="end_at" name="end_at"
                                    class="w-full border rounded px-2 py-1 {{ in_array($requests->status, ['approved', 'rejected', 'cancelled', 'check_in']) ? 'bg-gray-100' : '' }}"
                                    value="{{ optional($requests->end_at)->setTimezone('Asia/Bangkok')->format('Y-m-d') }}"
                                    @if (in_array($requests->status, ['approved', 'rejected', 'cancelled', 'check_in'])) readonly @endif />
                            </div>

                            <div class="bg-gray-50 rounded p-4">
                                <p class="font-semibold text-lg">รวมวันที่จะยืมทั้งสิ้น</p>
                                <p id="total-days" class="font-semibold">0 วัน</p>
                            </div>
                            <div class="bg-gray-50 rounded p-4">
                                <label class="text-gray-500 text-sm block mb-1">วันที่มาเเอาของ</label>
                                <input type="datetime-local" name="checked_out_at" id="checked_out_at"
                                    class="w-full border rounded px-2 py-1 @error('checked_out_at') border-red-500 @enderror {{ in_array($requests->status, ['rejected', 'cancelled', 'check_in']) ? 'bg-gray-100' : '' }}"
                                    value="{{ old('checked_out_at', optional($requests->transaction?->checked_out_at)->format('Y-m-d\TH:i')) }}" 
                                    @if (in_array($requests->status, ['check_out', 'rejected', 'cancelled', 'check_in'])) readonly @endif required />
                                <p class="text-xs text-gray-500 mt-1" id="checkout-date-hint">ต้องอยู่ในช่วงวันที่เริ่มถึงวันที่สิ้นสุดที่อนุญาต</p>
                                @error('checked_out_at')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                                @if ($requests->status === 'check_out')
                                    <div class="bg-gray-50 rounded p-4">
                                    <label class="text-gray-500 text-sm block mb-1">วันที่มาส่ง</label>
                                    <input type="datetime-local" name="checked_in_at" id="checked_in_at"
                                        class="w-full border rounded px-2 py-1 @error('checked_in_at') border-red-500 @enderror {{ in_array($requests->status, ['rejected', 'cancelled', 'check_in']) ? 'bg-gray-100' : '' }}"
                                        value="{{ old('checked_in_at', optional($requests->transaction?->checked_in_at)->format('Y-m-d\TH:i')) }}" 
                                        @if (in_array($requests->status, ['rejected', 'cancelled', 'check_in'])) readonly @endif required/>
                                    <p class="text-xs text-gray-500 mt-1" id="checkin-date-hint">ต้องเป็นวันถัดไปจากวันที่มาเเอาของ</p>
                                    @error('checked_in_at')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="bg-gray-50 rounded p-4">
                                    <label class="text-gray-500 text-sm block mb-1">ค่าปรับ</label>
                                    <input type="number" step="0.01" min="0" name="penalty_amount"
                                        class="w-full border rounded px-2 py-1 {{ in_array($requests->status, ['rejected', 'cancelled', 'check_in']) ? 'bg-gray-100' : '' }}"
                                        value="{{ $requests->transaction->penalty_amount ?? 0 }}"
                                        @if (in_array($requests->status, ['rejected', 'cancelled', 'check_in'])) readonly @endif />
                                </div>
                                <div class="bg-gray-50 rounded p-4 md:col-span-2">
                                    <label class="text-gray-500 text-sm block mb-1">หมายเหตุ</label>
                                    <textarea name="notes" class="w-full border rounded px-2 py-1 {{ in_array($requests->status, ['rejected', 'cancelled', 'check_in']) ? 'bg-gray-100' : '' }}" rows="2" 
                                        @if (in_array($requests->status, ['rejected', 'cancelled', 'check_in'])) readonly @endif >{{ $requests->transaction->notes ?? '' }}</textarea>
                                </div>
                                @endif
                                
                            @endif

                        </div>

                        @if ($requests->status != 'pending')
                        <!-- Transaction History -->
                        @if($requests->transaction && ($requests->transaction->checked_out_at || $requests->transaction->checked_in_at))
                        <div class="mt-6 p-4 bg-gray-50 rounded-lg">
                            <h4 class="font-semibold text-gray-700 mb-3">ประวัติการทำธุรกรรม</h4>
                            <div class="space-y-2 text-sm">
                                @if($requests->transaction->checked_out_at)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">วันที่มาเเอาของ:</span>
                                    <span class="font-medium">{{ $requests->transaction->checked_out_at->setTimezone('Asia/Bangkok')->format('d/m/Y') }}</span>
                                </div>
                                @endif
                                @if($requests->transaction->checked_in_at)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">วันที่มาคืน:</span>
                                    <span class="font-medium">{{ $requests->transaction->checked_in_at->setTimezone('Asia/Bangkok')->format('d/m/Y') }}</span>
                                </div>
                                @endif
                                @if($requests->transaction->penalty_amount && $requests->transaction->penalty_amount > 0)
                                <div class="flex justify-between">
                                    <span class="text-gray-600">ค่าปรับ:</span>
                                    <span class="font-medium text-red-600">฿{{ number_format($requests->transaction->penalty_amount, 2) }}</span>
                                </div>
                                @endif
                                @if($requests->transaction->notes)
                                <div class="mt-2">
                                    <span class="text-gray-600">หมายเหตุ:</span>
                                    <p class="text-gray-800 mt-1">{{ $requests->transaction->notes }}</p>
                                </div>
                                @endif
                            </div>
                        </div>
                        @endif
                        @endif

                        @if (!in_array($requests->status, ['rejected', 'cancelled', 'check_in']))
                        <div class="flex flex-col sm:flex-row justify-end gap-3 mt-4">
                            @if ($requests->status == 'approved')
                                <button type="submit"
                                    formaction="{{ route('admin.requests.update', $requests->req_id) }}"
                                    formmethod="post"
                                    class="bg-emerald-600 text-white px-4 sm:px-6 py-2 rounded-md hover:bg-emerald-700 text-sm sm:text-base">
                                    มารับของแล้ว
                                </button>
                            @elseif ($requests->status == 'check_out')
                                <button type="submit"
                                    formaction="{{ route('admin.requests.update', $requests->req_id) }}"
                                    formmethod="post"
                                    class="bg-purple-600 text-white px-4 sm:px-6 py-2 rounded-md hover:bg-purple-700 text-sm sm:text-base">
                                    มาคืนของแล้ว
                                </button>
                            @endif
                        </div>
                        @else
                        <div class="mt-4 p-4 bg-gray-100 rounded-lg">
                            <p class="text-sm text-gray-600 text-center">
                                @if($requests->status == 'rejected')
                                    คำขอนี้ถูกปฏิเสธแล้ว - ไม่สามารถดำเนินการเพิ่มเติมได้
                                @elseif($requests->status == 'cancelled')
                                    คำขอนี้ถูกยกเลิกแล้ว - ไม่สามารถดำเนินการเพิ่มเติมได้
                                @elseif($requests->status == 'check_in')
                                    คำขอนี้เสร็จสิ้นแล้ว - อุปกรณ์ถูกคืนเรียบร้อยแล้ว
                                @endif
                            </p>
                        </div>
                        @endif
                    </form>
                    @if ($requests->status == 'pending')
                        <form id="reject-form" action="{{ route('admin.requests.reject', $requests->req_id) }}" method="POST" class="hidden">
                            @csrf
                            <input type="hidden" name="reason" id="reject-reason" />
                        </form>
                    @endif
                </div>
                </div>
            </div>
        </div>
    </div>

        <!-- Recent Requests Table -->
        <div class="mt-6 bg-white rounded-2xl shadow p-6 mx-5">
        <h3 class="text-lg font-semibold mb-4">คำขอล่าสุด</h3>
        <div id="admin-table" data-requests='@json($tableRequests)'></div>
        </div>
    </div>
</x-admin-layout>

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

.status-checkout {
    background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);
    border: 2px solid #3b82f6;
    box-shadow: 0 2px 4px rgba(59, 130, 246, 0.2);
}

.status-checkin {
    background: linear-gradient(135deg, #e9d5ff 0%, #ddd6fe 100%);
    border: 2px solid #8b5cf6;
    box-shadow: 0 2px 4px rgba(139, 92, 246, 0.2);
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
.status-pending:hover,
.status-approved:hover,
.status-rejected:hover,
.status-checkout:hover,
.status-checkin:hover,
.status-cancelled:hover {
    transform: scale(1.05);
    transition: transform 0.2s ease;
}
</style>

<script>
    // Wait for the entire HTML document to be loaded and parsed
    document.addEventListener('DOMContentLoaded', function() {

        // --- Element Caching ---
        // Cache all frequently used DOM elements to improve performance
        const elements = {
            startAtInput: document.getElementById('start_at'),
            endAtInput: document.getElementById('end_at'),
            totalDaysOutput: document.getElementById('total-days'),
            checkoutAtInput: document.getElementById('checked_out_at'),
            checkoutHint: document.getElementById('checkout-date-hint'),
            checkinAtInput: document.getElementById('checked_in_at'),
            checkinHint: document.getElementById('checkin-date-hint'),
            rejectForm: document.getElementById('reject-form'),
            rejectReasonInput: document.getElementById('reject-reason'),
            mainForm: document.querySelector('form') // Assumes there's one main form
        };

        // --- Helper Functions ---

        /**
         * Shows a standardized SweetAlert2 warning message.
         * @param {string} title - The title of the alert.
         * @param {string} text - The main text of the alert.
         */
        function showAlert(title, text) {
                Swal.fire({
                    icon: 'warning',
                title: title,
                text: text,
                    timer: 3000,
                    showConfirmButton: false
                });
        }

        /**
         * Validates that a selected date is not in the past.
         * Clears the input if the date is invalid.
         * @param {HTMLInputElement} input - The date input element.
         * @param {string} label - A descriptive label for the alert message.
         * @returns {boolean} - True if the date is valid, false otherwise.
         */
        function validateNotPastDate(input, label) {
            if (!input.value) return true;

            const selectedDate = new Date(input.value);
            const today = new Date();
            today.setHours(0, 0, 0, 0); // Normalize today's date to midnight
            
            if (selectedDate < today) {
                showAlert('วันที่ไม่ถูกต้อง!', `${label}ต้องไม่เป็นวันที่ในอดีต`);
                input.value = ''; // Clear the invalid date
                return false;
            }
            return true;
        }

        /**
         * Calculates and displays the total number of days between two dates.
         * Validates that the end date is not before the start date.
         */
        function calculateAndValidateDateRange() {
            if (!elements.startAtInput || !elements.endAtInput) return;

            const startDateStr = elements.startAtInput.value;
            const endDateStr = elements.endAtInput.value;

            // Reset display if dates are not set
            if (!startDateStr || !endDateStr) {
                elements.totalDaysOutput.textContent = '0 วัน';
                elements.totalDaysOutput.classList.remove('text-red-700');
                return;
            }

            const startDate = new Date(startDateStr);
            const endDate = new Date(endDateStr);
            const diffTime = endDate.getTime() - startDate.getTime();

            // Check if end date is before start date
            if (diffTime < 0) {
                elements.totalDaysOutput.textContent = '0 วัน';
                elements.totalDaysOutput.classList.add('text-red-700');
                showAlert('ช่วงวันที่ไม่ถูกต้อง!', 'วันที่สิ้นสุดต้องไม่มาก่อนวันที่เริ่มต้น');
                elements.endAtInput.value = ''; // Clear invalid end date
            return;
        }
        
            // Calculate days (inclusive)
            const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24)) + 1;
            elements.totalDaysOutput.textContent = `${diffDays} วัน`;
            elements.totalDaysOutput.classList.remove('text-red-700');
        }


        /**
         * Updates the constraints for the check-out date input based on the selected start/end dates.
         */
        function updateCheckoutConstraints() {
            if (!elements.checkoutAtInput || !elements.startAtInput || !elements.endAtInput) return;

            const startDate = elements.startAtInput.value;
            const endDate = elements.endAtInput.value;

            if (startDate && endDate) {
                elements.checkoutAtInput.min = startDate + 'T00:00';
                elements.checkoutAtInput.max = endDate + 'T23:59';
            } else {
                elements.checkoutAtInput.min = '';
                elements.checkoutAtInput.max = '';
            }
        }

        /**
         * Updates the constraints for the check-in date input based on the check-out date.
         */
        function updateCheckinConstraints() {
            if (!elements.checkinAtInput || !elements.checkoutAtInput) return;

            const checkoutDateStr = elements.checkoutAtInput.value;

            if (checkoutDateStr) {
                const checkoutDate = new Date(checkoutDateStr);
                // Set the minimum check-in time to be exactly at the same time on the next day
                const nextDay = new Date(checkoutDate.getTime() + (24 * 60 * 60 * 1000));
                elements.checkinAtInput.min = nextDay.toISOString().slice(0, 16);
            } else {
                elements.checkinAtInput.min = '';
            }
        }

        /**
         * Validates the selected check-out date against the allowed range.
         */
        function validateCheckoutDate() {
            if (!elements.checkoutAtInput || !elements.startAtInput || !elements.endAtInput) return;

            const checkoutDateStr = elements.checkoutAtInput.value;
            const startDateStr = elements.startAtInput.value;
            const endDateStr = elements.endAtInput.value;

            if (!checkoutDateStr || !startDateStr || !endDateStr) {
                elements.checkoutHint.textContent = 'ต้องอยู่ในช่วงวันที่เริ่มถึงวันที่สิ้นสุดที่อนุญาต';
                elements.checkoutHint.className = 'text-sm text-gray-500 mt-1';
                elements.checkoutAtInput.classList.remove('border-red-500', 'border-green-500');
            return;
        }
        
            const checkoutDate = new Date(checkoutDateStr);
            const startDate = new Date(startDateStr);
            const endDate = new Date(endDateStr);
            endDate.setHours(23, 59, 59, 999); // Set end date to the very end of the day

            if (checkoutDate < startDate || checkoutDate > endDate) {
                showAlert('วันที่ไม่ถูกต้อง!', `วันที่มารับของต้องอยู่ในช่วง ${startDateStr} ถึง ${endDateStr}`);
                elements.checkoutHint.textContent = 'วันที่มารับของต้องอยู่ในช่วงที่ได้รับอนุญาต';
                elements.checkoutHint.className = 'text-sm text-red-500 mt-1';
                elements.checkoutAtInput.classList.add('border-red-500');
                elements.checkoutAtInput.value = ''; // Clear invalid date
        } else {
                elements.checkoutHint.textContent = 'วันที่มารับของถูกต้อง';
                elements.checkoutHint.className = 'text-sm text-green-500 mt-1';
                elements.checkoutAtInput.classList.remove('border-red-500');
                elements.checkoutAtInput.classList.add('border-green-500');
            }
        }

        /**
         * Validates the selected check-in date against the check-out date.
         */
        function validateCheckinDate() {
            if (!elements.checkinAtInput || !elements.checkoutAtInput) return;

            const checkinDateStr = elements.checkinAtInput.value;
            const checkoutDateStr = elements.checkoutAtInput.value;
            
            if (!checkinDateStr || !checkoutDateStr) {
                elements.checkinHint.textContent = 'วันที่มาส่งคืนต้องอยู่หลังวันที่มารับของ';
                elements.checkinHint.className = 'text-sm text-gray-500 mt-1';
                elements.checkinAtInput.classList.remove('border-red-500', 'border-green-500');
                return;
            }
            
            const checkinDate = new Date(checkinDateStr);
            const checkoutDate = new Date(checkoutDateStr);

            if (checkinDate <= checkoutDate) {
                showAlert('วันที่ไม่ถูกต้อง!', 'วันที่มาส่งคืนต้องอยู่หลังวันที่มารับของ');
                elements.checkinHint.textContent = 'วันที่มาส่งคืนต้องอยู่หลังวันที่มารับของ';
                elements.checkinHint.className = 'text-sm text-red-500 mt-1';
                elements.checkinAtInput.classList.add('border-red-500');
                elements.checkinAtInput.value = ''; // Clear invalid date
            } else {
                elements.checkinHint.textContent = 'วันที่มาส่งคืนถูกต้อง';
                elements.checkinHint.className = 'text-sm text-green-500 mt-1';
                elements.checkinAtInput.classList.remove('border-red-500');
                elements.checkinAtInput.classList.add('border-green-500');
            }
        }
        
        /**
         * A final validation check before the form is submitted.
         * @returns {boolean} - True if all dates are valid, false otherwise.
         */
        function validateFormSubmission() {
             // Re-run all validations to catch any errors
            calculateAndValidateDateRange();
            validateCheckoutDate();
            validateCheckinDate();

            // Check for any invalid input states
            if (
                (elements.endAtInput && !elements.endAtInput.value) ||
                (elements.checkoutAtInput && elements.checkoutAtInput.classList.contains('border-red-500')) ||
                (elements.checkinAtInput && elements.checkinAtInput.classList.contains('border-red-500'))
            ) {
                Swal.fire({
                    icon: 'error',
                    title: 'ข้อมูลไม่ถูกต้อง',
                    text: 'กรุณาตรวจสอบวันที่ทั้งหมดให้ถูกต้องก่อนบันทึก',
                    confirmButtonText: 'ตกลง'
                });
                return false;
            }
            
            return true;
        }

        // --- Event Listeners ---

        if (elements.startAtInput) {
            elements.startAtInput.addEventListener('change', () => {
                if (validateNotPastDate(elements.startAtInput, 'วันที่เริ่ม')) {
                    // Set the minimum for the end date
                    if (elements.endAtInput) elements.endAtInput.min = elements.startAtInput.value;
                    calculateAndValidateDateRange();
                    updateCheckoutConstraints();
                    validateCheckoutDate(); // Re-validate checkout
                    updateCheckinConstraints();
                    validateCheckinDate(); // Re-validate check-in
                }
            });
        }

        if (elements.endAtInput) {
            elements.endAtInput.addEventListener('change', () => {
                if (validateNotPastDate(elements.endAtInput, 'วันที่สิ้นสุด')) {
                    calculateAndValidateDateRange();
                    updateCheckoutConstraints();
                    validateCheckoutDate(); // Re-validate checkout
                }
            });
        }
        
        if (elements.checkoutAtInput) {
            elements.checkoutAtInput.addEventListener('change', () => {
                validateCheckoutDate();
                updateCheckinConstraints(); // Update check-in min date
                validateCheckinDate(); // Re-validate check-in
            });
        }

        if (elements.checkinAtInput) {
            elements.checkinAtInput.addEventListener('change', () => {
                validateCheckinDate();
            });
        }
        
        if (elements.mainForm) {
            elements.mainForm.addEventListener('submit', (event) => {
                if (!validateFormSubmission()) {
                    event.preventDefault(); // Stop form submission if validation fails
                }
            });
        }

        // --- Initializations ---
        // Run initial calculations and set constraints on page load
        calculateAndValidateDateRange();
        updateCheckoutConstraints();
        updateCheckinConstraints();
        if (elements.endAtInput && elements.startAtInput) {
           elements.endAtInput.min = elements.startAtInput.value;
        }
    });


    // --- Global Functions ---
    // This function remains in the global scope to be accessible from HTML onclick attributes

    /**
     * Displays a SweetAlert2 modal for entering a rejection reason.
     * On confirmation, it populates and submits the hidden rejection form.
     */
    window.showRejectModal = function() {
        Swal.fire({
            title: 'ปฏิเสธคำขอ',
            html: `
                <div class="text-left space-y-2">
                  <label class="flex items-center gap-2"><input type="radio" name="reason" value="ไม่ตรงตามเงื่อนไขการยืม"> ไม่ตรงตามเงื่อนไขการยืม</label>
                  <label class="flex items-center gap-2"><input type="radio" name="reason" value="อุปกรณ์ไม่พร้อมใช้งาน"> อุปกรณ์ไม่พร้อมใช้งาน</label>
                  <label class="flex items-center gap-2"><input type="radio" name="reason" value="เอกสารไม่ครบถ้วน"> เอกสารไม่ครบถ้วน</label>
                  <label class="flex items-center gap-2"><input type="radio" name="reason" value="อื่นๆ"> อื่นๆ</label>
                  <input id="reason-text" type="text" placeholder="ระบุเหตุผลเพิ่มเติม (ถ้าเลือก อื่นๆ)" class="w-full border rounded px-2 py-1 mt-2" />
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'ยืนยันการปฏิเสธ',
            cancelButtonText: 'ยกเลิก',
            confirmButtonColor: '#d33',
            focusConfirm: false,
            preConfirm: () => {
                const selectedReason = document.querySelector('input[name="reason"]:checked');
                const otherReasonText = document.getElementById('reason-text').value.trim();

                if (!selectedReason) {
                    Swal.showValidationMessage('กรุณาเลือกเหตุผลในการปฏิเสธ');
                    return false;
                }

                if (selectedReason.value === 'อื่นๆ') {
                    if (!otherReasonText) {
                        Swal.showValidationMessage('กรุณาระบุเหตุผลเพิ่มเติมในช่อง "อื่นๆ"');
                        return false;
                    }
                    return otherReasonText; // Return the custom text as the reason
                }

                return selectedReason.value; // Return the pre-defined reason
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('reject-form');
                const reasonInput = document.getElementById('reject-reason');
                
                if (form && reasonInput) {
                    reasonInput.value = result.value; // The confirmed reason
                form.submit();
                } else {
                    console.error('Reject form or reason input not found!');
                }
            }
        });
    }
    function updateMainItemCondition(itemId, accessoryCondition) {
        const mainItemSelect = document.querySelector(`select[name="item_condition_in[${itemId}]"]`);
        
        if (mainItemSelect) {
            if (accessoryCondition === 'หาย') {
                mainItemSelect.value = 'อุปกรณ์ไม่พร้อมใช้งาน';
            } else if (accessoryCondition === 'พัง') {
                mainItemSelect.value = 'อุปกรณ์ไม่พร้อมใช้งาน';
            }
             else if (accessoryCondition === 'ไม่สามาถซ่อมได้') {
                mainItemSelect.value = 'อุปกรณ์ไม่พร้อมใช้งาน';
            }
        }
    }
    function setDefaultConditions() {
        const mainItemSelects = document.querySelectorAll('select[name^="item_condition_in"]');
        mainItemSelects.forEach(select => {
            if (!select.value) {
                select.value = 'สภาพดี';
            }
        });
        const accessorySelects = document.querySelectorAll('select[name^="accessory_condition_in"]');
        accessorySelects.forEach(select => {
            if (!select.value) {
                select.value = 'สภาพดี';
            }
        });
    }

    // Call the function when the page loads
    document.addEventListener('DOMContentLoaded', function() {
        setDefaultConditions();
    });
</script>

