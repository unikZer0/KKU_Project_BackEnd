<x-admin-layout>
    <div class="min-h-screen bg-gradient-to-br from-gray-50 to-blue-50">
        <div class="max-w-7xl mx-auto py-8 px-4">
            <!-- Header -->
            <div class="text-center mb-8">
                <div class="inline-flex items-center justify-center w-16 h-16 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full mb-4">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h1 class="text-4xl font-bold text-gray-800 mb-2">จัดการคำขอยืนยันตัวตน</h1>
                <p class="text-gray-600">ตรวจสอบและอนุมัติคำขอยืนยันตัวตนของผู้ใช้</p>
            </div>

            @if (session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded-lg mb-6 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                    {{ session('error') }}
                </div>
            @endif

            <!-- Verification Requests List -->
            <div class="space-y-6">
                @if($verificationRequests->count() > 0)
                    @foreach($verificationRequests as $request)
                        <div class="bg-white rounded-2xl shadow-lg border border-gray-100 overflow-hidden hover:shadow-xl transition-shadow duration-300">
                            <div class="p-6">
                                <div class="flex items-start justify-between mb-6">
                                    <div class="flex items-center space-x-4">
                                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-bold text-lg">
                                            {{ strtoupper(substr($request->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <h3 class="text-xl font-bold text-gray-800">{{ $request->user->name ?? 'N/A' }}</h3>
                                            <p class="text-gray-600">{{ $request->user->email }}</p>
                                            <p class="text-sm text-gray-500">รหัสผู้ใช้: {{ $request->user->uid }}</p>
                                        </div>
                                    </div>
                                    
                                    <div class="text-right">
                                        <span class="inline-flex items-center px-4 py-2 rounded-full text-sm font-medium
                                            {{ $request->status === 'pending' ? 'bg-yellow-100 text-yellow-800 border border-yellow-200' : 
                                               ($request->status === 'approved' ? 'bg-green-100 text-green-800 border border-green-200' : 'bg-red-100 text-red-800 border border-red-200') }}">
                                            <div class="w-2 h-2 rounded-full mr-2 {{ $request->status === 'pending' ? 'bg-yellow-500' : ($request->status === 'approved' ? 'bg-green-500' : 'bg-red-500') }}"></div>
                                            {{ $request->status === 'pending' ? 'รอดำเนินการ' : ($request->status === 'approved' ? 'อนุมัติแล้ว' : 'ปฏิเสธแล้ว') }}
                                        </span>
                                        <p class="text-sm text-gray-500 mt-1">{{ $request->created_at->format('d/m/Y H:i') }}</p>
                                    </div>
                                </div>

                                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                                    <!-- Request Details -->
                                    <div class="space-y-4">
                                        @if($request->reason)
                                            <div class="bg-gray-50 rounded-xl p-4">
                                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                                                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                                                    </svg>
                                                    เหตุผล
                                                </h4>
                                                <p class="text-gray-700">{{ $request->reason }}</p>
                                            </div>
                                        @endif

                                        @if($request->processed_at)
                                            <div class="bg-blue-50 rounded-xl p-4">
                                                <h4 class="font-semibold text-gray-800 mb-2 flex items-center">
                                                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    ข้อมูลการดำเนินการ
                                                </h4>
                                                <p class="text-sm text-gray-600">วันที่: {{ $request->processed_at->format('d/m/Y H:i') }}</p>
                                                <p class="text-sm text-gray-600">โดย: {{ $request->processedBy->name ?? 'ไม่ระบุ' }}</p>
                                            </div>
                                        @endif
                                    </div>

                                    <!-- Citizen ID Image -->
                                    <div class="space-y-4">
                                        @if($request->citizen_id_image_path)
                                            <div class="bg-gray-50 rounded-xl p-4">
                                                <h4 class="font-semibold text-gray-800 mb-3 flex items-center">
                                                    <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                    </svg>
                                                    รูปภาพบัตรประชาชน
                                                </h4>
                                                <div class="relative">
                                                    <img src="{{ asset('storage/' . $request->citizen_id_image_path) }}" 
                                                         alt="Citizen ID" 
                                                         class="w-full max-w-sm rounded-lg border-2 border-gray-200 shadow-sm cursor-pointer hover:shadow-md transition-shadow"
                                                         onclick="openImageModal('{{ asset('storage/' . $request->citizen_id_image_path) }}')">
                                                    <div class="absolute inset-0 bg-black bg-opacity-0 hover:bg-opacity-10 transition-all duration-200 rounded-lg flex items-center justify-center">
                                                        <svg class="w-8 h-8 text-white opacity-0 hover:opacity-100 transition-opacity" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                                        </svg>
                                                    </div>
                                                </div>
                                                <p class="text-xs text-gray-500 mt-2 text-center">คลิกเพื่อดูรูปภาพขนาดเต็ม</p>
                                            </div>
                                        @endif
                                    </div>
                                </div>

                                <!-- Action Buttons -->
                                @if($request->status === 'pending')
                                    <div class="mt-6 pt-6 border-t border-gray-200 flex justify-end space-x-3">
                                        <form action="{{ route('admin.verification.approve', $request->id) }}" method="POST" class="inline">
                                            @csrf
                                            <button type="submit" class="bg-gradient-to-r from-green-500 to-green-600 hover:from-green-600 hover:to-green-700 text-white px-6 py-2 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                                <span class="flex items-center">
                                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                    </svg>
                                                    อนุมัติ
                                                </span>
                                            </button>
                                        </form>
                                        <button onclick="openRejectModal({{ $request->id }})" class="bg-gradient-to-r from-red-500 to-red-600 hover:from-red-600 hover:to-red-700 text-white px-6 py-2 rounded-xl font-semibold transition-all duration-200 transform hover:scale-105 shadow-lg hover:shadow-xl">
                                            <span class="flex items-center">
                                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                </svg>
                                                ปฏิเสธ
                                            </span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endforeach
                @else
                    <div class="bg-white rounded-2xl shadow-lg p-12 text-center">
                        <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-semibold text-gray-800 mb-2">ไม่มีคำขอยืนยันตัวตน</h3>
                        <p class="text-gray-600">ไม่มีคำขอยืนยันตัวตนในขณะนี้</p>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Reject Modal -->
    <div id="rejectModal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6 mx-4">
            <div class="flex items-center mb-6">
                <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mr-4">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.5 0L4.268 19.5c-.77.833.192 2.5 1.732 2.5z"></path>
                    </svg>
                </div>
                <div>
                    <h3 class="text-xl font-bold text-gray-800">ปฏิเสธคำขอยืนยันตัวตน</h3>
                    <p class="text-gray-600">กรุณาระบุเหตุผลในการปฏิเสธ</p>
                </div>
            </div>
            
            <form id="rejectForm" method="POST">
                @csrf
                <div class="mb-6">
                    <label for="admin_notes" class="block text-sm font-medium text-gray-700 mb-2">เหตุผลการปฏิเสธ <span class="text-red-500">*</span></label>
                    <textarea name="admin_notes" id="admin_notes" rows="4" required
                        class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors resize-none"
                        placeholder="ระบุเหตุผลในการปฏิเสธคำขอนี้..."></textarea>
                </div>
                
                <div class="flex justify-end space-x-3">
                    <button type="button" onclick="closeRejectModal()" 
                        class="px-6 py-2 border-2 border-gray-300 text-gray-700 rounded-xl hover:bg-gray-50 transition-colors">
                        ยกเลิก
                    </button>
                    <button type="submit" 
                        class="px-6 py-2 bg-gradient-to-r from-red-500 to-red-600 text-white rounded-xl hover:from-red-600 hover:to-red-700 transition-all duration-200 transform hover:scale-105">
                        ยืนยันการปฏิเสธ
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 flex items-center justify-center z-50 hidden">
        <div class="bg-white rounded-2xl p-6 max-w-4xl max-h-full overflow-auto mx-4">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-xl font-bold text-gray-800">รูปภาพบัตรประชาชน</h3>
                <button onclick="closeImageModal()" class="text-gray-500 hover:text-gray-700 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <img id="modalImage" src="" alt="Citizen ID" class="max-w-full h-auto rounded-lg shadow-lg">
        </div>
    </div>

    <script>
        function openRejectModal(requestId) {
            document.getElementById('rejectForm').action = `/admin/verification/${requestId}/reject`;
            document.getElementById('rejectModal').classList.remove('hidden');
        }

        function closeRejectModal() {
            document.getElementById('rejectModal').classList.add('hidden');
            document.getElementById('admin_notes').value = '';
        }

        function openImageModal(imageSrc) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }
    </script>
</x-admin-layout>
