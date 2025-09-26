<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>รายละเอียดคำขอยืนยันตัวตน</title>
    
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="min-h-screen py-4 sm:py-8">
        <div class="max-w-6xl mx-auto px-4 sm:px-6">
            
            @if (session('success'))
                <script>
                    Swal.fire({ icon: 'success', title: 'สำเร็จ!', text: '{{ session('success') }}', timer: 2200, showConfirmButton: false });
                </script>
            @endif

            @if (session('error'))
                <script>
                    Swal.fire({ icon: 'error', title: 'เกิดข้อผิดพลาด!', text: '{{ session('error') }}', timer: 3000, showConfirmButton: false });
                </script>
            @endif

            <!-- Header -->
            <div class="mb-6 sm:mb-8">
                <div class="flex items-center mb-4 sm:mb-6">
                    <a href="{{ route('admin.verification.index') }}" class="inline-flex items-center px-3 py-2 sm:px-4 bg-white border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors duration-200 shadow-sm hover:shadow-md text-sm sm:text-base">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        กลับ
                    </a>
                </div>
                <div class="text-center">
                    <h1 class="text-2xl sm:text-4xl font-bold text-gray-800 mb-2">รายละเอียดคำขอยืนยันตัวตน</h1>
                    <p class="text-sm sm:text-base text-gray-600">ตรวจสอบและดำเนินการคำขอยืนยันตัวตน</p>
                </div>
            </div>

            <!-- User Information -->
            <div class="bg-white rounded-2xl shadow-xl p-4 sm:p-8 mb-6 border border-gray-100">
                <h3 class="text-xl font-bold text-gray-800 mb-4">ข้อมูลผู้ใช้</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">ชื่อ</p>
                        <p class="font-semibold text-gray-800">{{ $verificationRequest->user->name }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">อีเมล</p>
                        <p class="font-semibold text-gray-800">{{ $verificationRequest->user->email }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">รหัสผู้ใช้</p>
                        <p class="font-semibold text-gray-800">{{ $verificationRequest->user->uid }}</p>
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">เบอร์โทรศัพท์</p>
                        <p class="font-semibold text-gray-800">{{ $verificationRequest->user->phonenumber ?? '-' }}</p>
                    </div>
                </div>
            </div>

            <!-- Request Information -->
            <div class="bg-white rounded-2xl shadow-xl p-4 sm:p-8 mb-6 border border-gray-100">
                <h3 class="text-xl font-bold text-gray-800 mb-4">ข้อมูลคำขอ</h3>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <p class="text-sm text-gray-500">สถานะ</p>
                        @if($verificationRequest->status === 'pending')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                                รอดำเนินการ
                            </span>
                        @elseif($verificationRequest->status === 'approved')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-green-100 text-green-800">
                                อนุมัติแล้ว
                            </span>
                        @elseif($verificationRequest->status === 'rejected')
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-red-100 text-red-800">
                                ถูกปฏิเสธ
                            </span>
                        @endif
                    </div>
                    <div>
                        <p class="text-sm text-gray-500">วันที่ส่งคำขอ</p>
                        <p class="font-semibold text-gray-800">{{ $verificationRequest->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @if($verificationRequest->reason)
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-500">เหตุผลที่ระบุ</p>
                        <p class="font-semibold text-gray-800">{{ $verificationRequest->reason }}</p>
                    </div>
                    @endif
                    @if($verificationRequest->admin_notes)
                    <div class="md:col-span-2">
                        <p class="text-sm text-gray-500">หมายเหตุจากผู้ดูแล</p>
                        <p class="font-semibold text-gray-800">{{ $verificationRequest->admin_notes }}</p>
                    </div>
                    @endif
                    @if($verificationRequest->processedBy)
                    <div>
                        <p class="text-sm text-gray-500">ดำเนินการโดย</p>
                        <p class="font-semibold text-gray-800">{{ $verificationRequest->processedBy->name }}</p>
                    </div>
                    @endif
                    @if($verificationRequest->processed_at)
                    <div>
                        <p class="text-sm text-gray-500">วันที่ดำเนินการ</p>
                        <p class="font-semibold text-gray-800">{{ $verificationRequest->processed_at->format('d/m/Y H:i') }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Image Section -->
            <div class="bg-white rounded-2xl shadow-xl p-4 sm:p-8 mb-6 border border-gray-100">
                <h3 class="text-xl font-bold text-gray-800 mb-4">รูปภาพเอกสาร</h3>
                
                <!-- Main Citizen ID Image -->
                @if($verificationRequest->citizen_id_image_path)
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-700 mb-3">รูปภาพบัตรนักสึกสา</h4>
                    <div class="max-w-2xl mx-auto">
                        <img src="{{ $verificationRequest->citizen_id_image_path }}" 
                             alt="Citizen ID" 
                             class="w-full rounded-lg border shadow-sm cursor-pointer hover:shadow-lg transition-shadow"
                             onclick="openImageModal('{{ $verificationRequest->citizen_id_image_path }}', 'รูปภาพบัตรนักสึกสา')">
                    </div>
                </div>
                @endif

                <!-- Additional Images (if any exist) -->
                @if($verificationRequest->verification_images && count(array_filter($verificationRequest->verification_images)) > 0)
                <div class="mb-6">
                    <h4 class="text-lg font-semibold text-gray-700 mb-3">รูปภาพเพิ่มเติม</h4>
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4">
                        @foreach(array_filter($verificationRequest->verification_images) as $index => $imagePath)
                        <div class="relative">
                            <img src="{{ $imagePath }}" 
                                 alt="Additional Image {{ $index + 1 }}" 
                                 class="w-full h-48 object-cover rounded-lg border shadow-sm cursor-pointer hover:shadow-lg transition-shadow"
                                 onclick="openImageModal('{{ $imagePath }}', 'รูปภาพเพิ่มเติม {{ $index + 1 }}')">
                            <div class="absolute top-2 right-2 bg-blue-500 text-white text-xs px-2 py-1 rounded">
                                {{ $index + 1 }}
                            </div>
                        </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>

            <!-- Action Buttons -->
            @if($verificationRequest->status === 'pending')
            <div class="bg-white rounded-2xl shadow-xl p-4 sm:p-8 border border-gray-100">
                <h3 class="text-xl font-bold text-gray-800 mb-4">ดำเนินการ</h3>
                
                <div class="flex flex-col sm:flex-row gap-4">
                    <!-- Approve Form -->
                    <form action="{{ route('admin.verification.approve', $verificationRequest->id) }}" method="POST" class="flex-1">
                        @csrf
                        <div class="mb-4">
                            <label for="approve_notes" class="block text-sm font-medium text-gray-700 mb-2">หมายเหตุ (ไม่บังคับ)</label>
                            <textarea name="admin_notes" id="approve_notes" rows="3" 
                                class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-green-500 focus:border-green-500 transition-colors resize-none"
                                placeholder="หมายเหตุเพิ่มเติม..."></textarea>
                        </div>
                        <button type="submit" 
                            class="w-full bg-green-500 text-white font-semibold py-3 px-6 rounded-xl hover:bg-green-600 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl"
                            onclick="return confirm('ยืนยันการอนุมัติการยืนยันตัวตน?')">
                            <span class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                </svg>
                                อนุมัติ
                            </span>
                        </button>
                    </form>

                    <!-- Reject Form -->
                    <form action="{{ route('admin.verification.reject', $verificationRequest->id) }}" method="POST" class="flex-1">
                        @csrf
                        <div class="mb-4">
                            <label for="reject_notes" class="block text-sm font-medium text-gray-700 mb-2">เหตุผลในการปฏิเสธ <span class="text-red-500">*</span></label>
                            <textarea name="admin_notes" id="reject_notes" rows="3" required
                                class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-red-500 focus:border-red-500 transition-colors resize-none"
                                placeholder="ระบุเหตุผลในการปฏิเสธ..."></textarea>
                        </div>
                        <button type="submit" 
                            class="w-full bg-red-500 text-white font-semibold py-3 px-6 rounded-xl hover:bg-red-600 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl"
                            onclick="return confirm('ยืนยันการปฏิเสธการยืนยันตัวตน?')">
                            <span class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                </svg>
                                ปฏิเสธ
                            </span>
                        </button>
                    </form>
                </div>
            </div>
            @endif
        </div>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="fixed inset-0 bg-black bg-opacity-75 z-50 hidden flex items-center justify-center p-4">
        <div class="bg-white rounded-xl max-w-4xl max-h-full overflow-auto">
            <div class="p-4 border-b border-gray-200 flex justify-between items-center">
                <h3 id="modalTitle" class="text-lg font-semibold text-gray-800"></h3>
                <button onclick="closeImageModal()" class="text-gray-400 hover:text-gray-600">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
            <div class="p-4">
                <img id="modalImage" src="" alt="" class="max-w-full max-h-96 mx-auto rounded-lg">
            </div>
        </div>
    </div>

    <script>
        function openImageModal(imageSrc, title) {
            document.getElementById('modalImage').src = imageSrc;
            document.getElementById('modalTitle').textContent = title;
            document.getElementById('imageModal').classList.remove('hidden');
        }

        function closeImageModal() {
            document.getElementById('imageModal').classList.add('hidden');
        }

        // Close modal when clicking outside
        document.getElementById('imageModal').addEventListener('click', function(e) {
            if (e.target === this) {
                closeImageModal();
            }
        });

        // Close modal with Escape key
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeImageModal();
            }
        });
    </script>
</body>
</html>
