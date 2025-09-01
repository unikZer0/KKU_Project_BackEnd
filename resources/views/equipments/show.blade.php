<x-app-layout>
    @if (session('success'))
        <script>
            Swal.fire({
                icon: 'success',
                title: 'สำเร็จ!',
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

    <div class="max-w-screen-2xl mx-auto py-6 px-3 sm:px-6 lg:px-8">
        <div class="flex flex-col md:flex-row gap-6 items-start">

            <div class="md:w-1/2">
                <img src="{{ $equipment->photo_path }}" alt="{{ $equipment->name }}"
                    class="w-full h-auto object-cover rounded-lg shadow-lg">
            </div>

            <div class="md:w-1/2 flex flex-col justify-start gap-4 p-5 border border-gray-300 rounded-lg lg:p-10">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900">{{ $equipment->name }}</h1>
                <p class="text-gray-500 text-lg">{{ $equipment->category->name }}</p>
                <div class="text-gray-500 text-lg">
                    <div x-data="{ expanded: false }" class="block md:hidden">
                        <span x-show="!expanded">
                            {{ \Illuminate\Support\Str::limit($equipment->description, 80, '...') }}
                        </span>
                        <span x-show="expanded">
                            {{ $equipment->description }}
                        </span>
                        <button @click="expanded = !expanded" class="ml-2 text-gray-600 underline focus:outline-none">
                            <span x-show="!expanded">ดูเพี่มเตีม</span>
                            <span x-show="expanded">แสดงน้อยลง</span>
                        </button>
                    </div>

                    <div class="hidden md:block">
                        {{ $equipment->description }}
                    </div>
                </div>
                <p class="mt-4 font-semibold text-lg">เลือกวันที่รับ-ส่ง :</p>
                <div class="ทะข/ p-6 bg-gray-50 border border-gray-200 rounded-lg">
                    <form action="{{ route('borrower.borrow_request', $equipment) }}" method="POST" id="borrowForm">
                        @csrf
                        <div class="mb-4">
                            <input type="hidden" name="equipments_id" value="{{ $equipment->id }}">
                            <label for="start_at"
                                class="block text-sm font-semibold text-gray-700 mb-1">วันที่รับ:</label>
                            <input type="date" id="start_at" name="start_at" required
                                class="w-full p-2 border border-gray-300 rounded-md">
                        </div>
                        <div class="mb-6">

                            <label for="end_at"
                                class="block text-sm font-semibold text-gray-700 mb-1">วันที่คืน:</label>
                            <input type="date" id="end_at" name="end_at" required
                                class="w-full p-2 border border-gray-300 rounded-md">
                        </div>
                        @if ($hasBorrowed)
                            <a href="{{ route('borrower.equipments.myreq') }}">
                                <button type="button"
                                    class="w-full bg-yellow-500 text-white font-bold py-2 rounded-md">
                                    go to my req
                                </button>
                            </a>
                        @elseif ($equipment->status === 'maintenance')
                            <button type="button"
                                class="w-full bg-red-500 text-white font-bold py-2 rounded-md cursor-not-allowed"
                                disabled>
                                อุปกรณ์อยู่ระหว่างซ่อมบำรุง
                            </button>
                        @elseif ($equipment->status !== 'available')
                            <button type="button"
                                class="w-full bg-gray-400 text-white font-bold py-2 rounded-md cursor-not-allowed"
                                disabled>
                                ไม่สามารถยืมได้ ({{ $equipment->status }})
                            </button>
                        @else
                            <button type="submit" id="borrowButton"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 rounded-md transition duration-200">
                                ยืม
                            </button>
                        @endif



                    </form>
                    <p id="message" class="text-center mt-4 font-semibold">
                        @if ($bookings->count())
                            <div class="mt-6 bg-gray-50 p-4 rounded-lg border">
                                <h3 class="font-semibold text-lg mb-2">ช่วงเวลาที่ถูกจองแล้ว:</h3>
                                <ul class="list-disc ml-5 text-gray-600">
                                    @foreach ($bookings as $b)
                                    @if ($currentDate < $b->end_at)
                                        <li>
                                            {{ \Carbon\Carbon::parse($b->start_at)->format('d/m/Y') }}
                                            -
                                            {{ \Carbon\Carbon::parse($b->end_at)->format('d/m/Y') }}
                                        </li>
                                    @endif   
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                    </p>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>

<script>
    document.getElementById('borrowForm').addEventListener('submit', function(e) {
        @if (!Auth::check())
            e.preventDefault();
            Swal.fire({
                title: 'คุณต้องเข้าสู่ระบบ',
                text: 'กด ตกลง เพื่อไปยังหน้าล็อกอิน หรือ ยกเลิก เพื่อทำการยืม',
                icon: 'warning',
                showCancelButton: true,
                confirmButtonText: 'ตกลง',
                cancelButtonText: 'ยกเลิก'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = "{{ route('login') }}";
                }
            });
        @endif
    });
    document.addEventListener('DOMContentLoaded', function() {
        const startDateInput = document.getElementById('start_at');
        const endDateInput = document.getElementById('end_at');
        const borrowButton = document.getElementById('borrowButton');
        const messageElement = document.getElementById('message');

        const today = new Date().toISOString().split('T')[0];
        startDateInput.setAttribute('min', today);

        startDateInput.addEventListener('input', validateDates);
        endDateInput.addEventListener('input', validateDates);

        function validateDates() {
            const startDate = new Date(startDateInput.value);
            const endDate = new Date(endDateInput.value);
            messageElement.textContent = '';

            if (startDateInput.value && endDateInput.value) {
                if (endDate <= startDate) {
                    messageElement.textContent = 'วันส่งควรอยู่หลังวันรับ.';
                    messageElement.classList.remove('text-green-600');
                    messageElement.classList.add('text-red-500');
                    borrowButton.disabled = true;
                } else {
                    messageElement.textContent = '';
                    borrowButton.disabled = false;
                }
            } else {
                borrowButton.disabled = true;
            }
        }
    });
</script>
