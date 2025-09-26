<!DOCTYPE html>
<html lang="en"></html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
        
            <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

        <title>{{ config('app.name', 'Laravel') }}</title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body>
    <div class="min-h-screen  py-4 sm:py-8">
        <div class="max-w-4xl mx-auto px-4 sm:px-6">
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
                    <a href="{{ route('home') }}" class="inline-flex items-center px-3 py-2 sm:px-4 bg-white border border-gray-300 rounded-xl text-gray-700 hover:bg-gray-50 transition-colors duration-200 shadow-sm hover:shadow-md text-sm sm:text-base">
                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"></path>
                        </svg>
                        กลับ
                    </a>
                </div>
                <div class="text-center">
                    <h1 class="text-2xl sm:text-4xl font-bold text-gray-800 mb-2">โปรไฟล์ของฉัน</h1>
                    <p class="text-sm sm:text-base text-gray-600">จัดการข้อมูลส่วนตัวและการยืนยันตัวตน</p>
                </div>
            </div>

            <!-- Profile Information Card -->
            <div class="bg-white rounded-2xl shadow-xl p-4 sm:p-8 mb-6 sm:mb-8 border border-gray-100">
                <div class="flex flex-col sm:flex-row items-center sm:items-start mb-4 sm:mb-6">
                    <div class="w-16 h-16 sm:w-20 sm:h-20 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white text-xl sm:text-2xl font-bold mb-4 sm:mb-0">
                        {{ strtoupper(substr($user->name, 0, 1)) }}
                    </div>
                    <div class="text-center sm:text-left sm:ml-4">
                        <h2 class="text-xl sm:text-2xl font-bold text-gray-800">{{ $user->name }}</h2>
                        <p class="text-sm sm:text-base text-gray-600">{{ $user->email }}</p>
                    </div>
                </div>

                <div class="space-y-4">
                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V8a2 2 0 00-2-2h-5m-4 0V4a2 2 0 114 0v2m-4 0a2 2 0 104 0m-5 8a2 2 0 100-4 2 2 0 000 4zm0 0c1.306 0 2.417.835 2.83 2M9 14a3.001 3.001 0 00-2.83 2M15 11h3m-3 4h2"></path>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm text-gray-500">รหัสผู้ใช้</p>
                            <p class="font-semibold text-gray-800 truncate">{{ $user->uid }}</p>
                        </div>
                    </div>

                    <div class="flex items-center">
                        <div class="w-10 h-10 bg-green-100 rounded-lg flex items-center justify-center mr-3 flex-shrink-0">
                            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="min-w-0 flex-1">
                            <p class="text-sm text-gray-500">สถานะการยืนยันตัวตน</p>
                            @if($latestVerificationRequest && $latestVerificationRequest->status === 'pending')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs sm:text-sm font-medium bg-blue-100 text-blue-800">
                                    รอดำเนินการ
                                </span>
                            @elseif($latestVerificationRequest && $latestVerificationRequest->status === 'approved')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs sm:text-sm font-medium bg-green-100 text-green-800">
                                    ยืนยันแล้ว
                                </span>
                            @elseif($latestVerificationRequest && $latestVerificationRequest->status === 'rejected')
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs sm:text-sm font-medium bg-red-100 text-red-800">
                                    ถูกปฏิเสธ
                                </span>
                            @else
                                <span class="inline-flex items-center px-3 py-1 rounded-full text-xs sm:text-sm font-medium bg-gray-100 text-gray-800">
                                    ยังไม่ยืนยัน
                                </span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <!-- Verification Request Form -->
            @if(!$latestVerificationRequest || !in_array($latestVerificationRequest->status, ['pending', 'approved']))
            <div class="bg-white rounded-2xl shadow-xl p-4 sm:p-8 border border-gray-100">
                <div class="text-center mb-6 sm:mb-8">
                    <h3 class="text-xl sm:text-2xl font-bold text-gray-800 mb-2">ขอยืนยันตัวตน</h3>
                    <p class="text-sm sm:text-base text-gray-600">อัปโหลดรูปภาพบัตรนักสึกสาเพื่อยืนยันตัวตน</p>
                </div>

                <form action="{{ route('profile.requestVerification') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
                    @csrf
                    
                    <!-- File Upload Section -->
                    <div class="space-y-4">
                        <label for="citizen_id_image" class="block text-lg font-semibold text-gray-800">
                            รูปภาพบัตรนักสึกสา <span class="text-red-500">*</span>
                        </label>
                        
                        <div class="relative">
                            <input type="file" name="citizen_id_image" id="citizen_id_image" accept="image/*" required
                                class="w-full p-4 border-2 border-dashed border-gray-300 rounded-xl focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors cursor-pointer hover:border-blue-400"
                                onchange="previewImage(this, 'previewImg', 'imagePreview')">
                        </div>
                        
                        <!-- Image Preview -->
                        <div id="imagePreview" class="hidden">
                            <div class="bg-gray-50 rounded-xl p-4 border-2 border-dashed border-gray-200">
                                <p class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                    </svg>
                                    ตัวอย่างรูปภาพ:
                                </p>
                                <img id="previewImg" src="" alt="Preview" class="max-w-md rounded-lg border shadow-sm mx-auto">
                            </div>
                        </div>
                        
                        <p class="text-sm text-gray-500 flex items-center">
                            <svg class="w-4 h-4 mr-2 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            รองรับไฟล์: JPG, PNG, GIF (ขนาดไม่เกิน 2MB)
                        </p>
                    </div>

                    <!-- Reason Section -->
                    <div class="space-y-4">
                        <label for="reason" class="block text-lg font-semibold text-gray-800">
                            เหตุผลในการขอยืนยันตัวตน <span class="text-gray-500 text-sm font-normal">(ไม่บังคับ)</span>
                        </label>
                        <textarea name="reason" id="reason" rows="4" 
                            class="w-full border-2 border-gray-200 rounded-xl px-4 py-3 focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-colors resize-none"
                            placeholder="ระบุเหตุผลที่ต้องการยืนยันตัวตน..."></textarea>
                    </div>

                    <!-- Submit Button -->
                    <div class="pt-4">
                        <button type="submit" class="w-full bg-blue-500 text-white font-semibold py-4 px-8 rounded-xl hover:from-blue-700 hover:to-purple-700 transform hover:scale-105 transition-all duration-200 shadow-lg hover:shadow-xl">
                            <span class="flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                </svg>
                                ขอยืนยันตัวตน
                            </span>
                        </button>
                    </div>
                </form>
            </div>
            @else
            <!-- Show status message for pending or approved requests -->
            <div class="bg-white rounded-2xl shadow-xl p-4 sm:p-8 border border-gray-100">
                <div class="text-center">
                    @if($latestVerificationRequest->status === 'pending')
                        <div class="w-16 h-16 bg-blue-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-bold text-gray-800 mb-2">คำขอยืนยันตัวตนกำลังรอดำเนินการ</h3>
                        <p class="text-sm sm:text-base text-gray-600 mb-4">คำขอยืนยันตัวตนของคุณถูกส่งแล้ว และกำลังรอการตรวจสอบจากผู้ดูแลระบบ</p>
                    @elseif($latestVerificationRequest->status === 'approved')
                        <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl sm:text-2xl font-bold text-gray-800 mb-2">การยืนยันตัวตนได้รับการอนุมัติแล้ว</h3>
                        <p class="text-sm sm:text-base text-gray-600 mb-4">คุณสามารถใช้งานระบบยืมอุปกรณ์ได้แล้ว</p>
                    @endif
                    <p class="text-xs text-gray-500">ส่งเมื่อ: {{ $latestVerificationRequest->created_at->format('d/m/Y H:i') }}</p>
                </div>
            </div>
            @endif
        </div>
    </div>
    </div>

    <script>
        function previewImage(input, imgId, containerId) {
            const preview = document.getElementById(containerId);
            const previewImg = document.getElementById(imgId);
            
            if (input.files && input.files[0]) {
                const reader = new FileReader();
                
                reader.onload = function(e) {
                    previewImg.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.classList.add('hidden');
            }
        }
    </script>



</body>
</html>
                reader.readAsDataURL(input.files[0]);
            } else {
                preview.classList.add('hidden');
            }
        }
    </script>



</body>
</html>
