<x-app-layout>
    <div class="max-w-screen-2xl mx-auto py-8 px-3 sm:px-6 lg:px-8">
        <h1 class="text-2xl font-bold text-gray-800 mb-6">คำขอยืมอุปกรณ์ (รออนุมัติ)</h1>

        <div class="space-y-6">
            @foreach ($reQuests as $req)
                <div class="bg-white rounded-2xl shadow p-6">
                    <!-- Header -->
                    <div class="flex justify-between items-center border-b pb-4 mb-4">
                        <h2 class="text-lg font-semibold text-gray-800">
                            คำขอ #{{ $req->req_id }}
                        </h2>
                        <span class="px-3 py-1 text-sm rounded-full
                            @if($req->status === 'pending') bg-yellow-100 text-yellow-700
                            @elseif($req->status === 'approved') bg-green-100 text-green-700
                            @elseif($req->status === 'rejected') bg-red-100 text-red-700
                            @else bg-gray-100 text-gray-700 @endif">
                            {{ 
                                $req->status === 'pending' ? 'รอดำเนินการ' : 
                                ($req->status === 'approved' ? 'อนุมัติ' : 
                                ($req->status === 'rejected' ? 'ปฏิเสธ' : ucfirst($req->status))) 
                            }}
                        </span>
                    </div>

                    <div class="flex gap-4 mb-4">
                        <img src="{{ $req->equipment->photo_path }}" 
                             alt="รูปอุปกรณ์" 
                             class="w-28 h-28 object-cover rounded-lg shadow">
                        <div>
                            <h3 class="font-semibold text-gray-800">{{ $req->equipment->name }}</h3>
                            <p class="text-sm text-gray-500">รหัส: {{ $req->equipment->code }}</p>
                            <p class="text-sm text-gray-500">หมวดหมู่: {{ $req->equipment->category->name }}</p>
                            <span>
                            {{ $req->equipment->description }}
                        </span>
                            <div x-data="{ expanded: false }" class="block md:hidden">
                        <span x-show="!expanded">
                            {{ \Illuminate\Support\Str::limit($req->equipment->description, 80, '...') }}
                        </span>
                        <span x-show="expanded">
                            {{ $req->equipment->description }}
                        </span>
                        <button @click="expanded = !expanded" class="ml-2 text-blue-600 underline focus:outline-none">
                            <span x-show="!expanded">ดูเพี่มเตีม</span>
                            <span x-show="expanded">แสดงน้อยลง</span>
                        </button>
                    </div>
                        </div>
                    </div>
                    <div class="mb-4">
                        <h3 class="font-semibold text-gray-700">ผู้ยืม</h3>
                        <p class="text-sm text-gray-600">{{ $req->user->username }} (UID: {{ $req->user->uid }})</p>
                        <p class="text-sm text-gray-600">อีเมล: {{ $req->user->email }}</p>
                        <p class="text-sm text-gray-600">โทร: {{ $req->user->phonenumber }}</p>
                        <p class="text-sm text-gray-600">อายุ: {{ $req->user->age }}</p>
                    </div>
                    <div class="mb-4">
                        <h3 class="font-semibold text-gray-700">ระยะเวลาการยืม</h3>
                        <p class="text-sm text-gray-600">เริ่ม: {{ $req->start_at }}</p>
                        <p class="text-sm text-gray-600">สิ้นสุด: {{ $req->end_at }}</p>
                    </div>
                    @if ($req->status === "rejected")
                        <div class="mb-4">
                            <h3 class="font-semibold text-red-700">เหตุผลที่ถูกปฏิเสธ</h3>
                            <p class="text-sm text-red-600 italic">{{ $req->reject_reason }}</p>
                        </div>
                    @endif
                    @if ($req->status == 'pending')
                        <div class="flex gap-3">
                        <button class="flex-1 bg-red-600 text-white py-2 rounded-lg hover:bg-red-700 transition">
                            ยกเลิกคำขอ
                        </button>
                    </div>
                    @endif
                </div>
            @endforeach
        </div>
    </div>
</x-app-layout>
