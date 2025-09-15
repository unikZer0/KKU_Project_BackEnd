<x-admin-layout>
    <div class="max-w-7xl mx-auto bg-white p-4 sm:p-6 rounded shadow">
        <div class="flex flex-col lg:flex-row items-start gap-6">
            @if ($requests->equipment?->photo_path)
                <img src="{{ $requests->equipment->photo_path }}" alt="{{ $requests->equipment->name }}"
                    class="w-full sm:w-40 h-40 object-cover rounded border" />
            @endif
            <div class="flex-1 w-full">
                <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-3">
                    <h2 class="text-xl sm:text-2xl font-bold">คำขอยืม #{{ $requests->req_id }}</h2>
                    <span
                        class="inline-flex items-center px-3 py-1 rounded-full text-sm {{ $requests->status === 'approved' ? 'bg-green-100 text-green-700' : ($requests->status === 'rejected' ? 'bg-red-100 text-red-700' : 'bg-yellow-100 text-yellow-700') }}">
                        {{ ucfirst($requests->status) }}
                    </span>
                </div>
                <p class="text-gray-500 mt-1">สร้างเมื่อ {{ $requests->created_at->format('Y-m-d H:i') }}</p>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mt-6">
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-2">ข้อมูลผู้ใช้</h3>
                        <div class="space-y-1 text-sm">
                            <p><span class="text-gray-500">ชื่อ:</span> {{ $requests->user->name ?? '-' }}</p>
                            <p><span class="text-gray-500">UID:</span> {{ $requests->user->uid ?? '-' }}</p>
                            <p><span class="text-gray-500">อีเมล:</span> {{ $requests->user->email ?? '-' }}</p>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-700 mb-2">ข้อมูลอุปกรณ์</h3>
                        <div class="space-y-1 text-sm">
                            <p><span class="text-gray-500">ชื่ออุปกรณ์:</span> {{ $requests->equipment->name ?? '-' }}
                            </p>
                            <p><span class="text-gray-500">รหัสอุปกรณ์:</span> {{ $requests->equipment->code ?? '-' }}
                            </p>
                            <p><span class="text-gray-500">หมวดหมู่:</span>
                                {{ $requests->equipment->category->name ?? '-' }}</p>
                        </div>
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mt-6">
                    <div class="bg-gray-50 rounded p-4">
                        <p class="text-gray-500 text-sm">วันที่เริ่ม</p>
                        <p class="font-semibold">{{ $requests->start_at?->format('Y-m-d') ?? '-' }}</p>
                    </div>
                    <div class="bg-gray-50 rounded p-4">
                        <p class="text-gray-500 text-sm font-semibold">วันที่สิ้นสุด</p>
                        <p class="font-semibold">{{ $requests->end_at?->format('Y-m-d') ?? '-' }}</p>
                    </div>
                    <div class="bg-gray-50  rounded p-4">
                        <p class="font-semibold text-lg">รวมวันที่ขอยืมทั้งสิ้น</p>
                        @php
                            $days = 0;
                            if ($requests->start_at && $requests->end_at) {
                                $signed = $requests->start_at->diffInDays($requests->end_at, false) + 1;
                                $days = max($signed, 0);
                            }
                        @endphp
                        <p class="font-semibold {{ $days <= 0 ? 'text-red-700' : '' }}">{{ $days }} วัน</p>
                    </div>
                </div>

                <div class="mt-8">
                    <h3 class="font-semibold text-gray-700 mb-4">ข้อมูลธุรกรรม</h3>
                    <form action="{{ route('admin.requests.approve', $requests->req_id) }}" method="POST"
                        class="space-y-5">
                        @csrf
                        @method('PATCH')
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                            <div class="bg-gray-50 rounded p-4">
                                <label class="text-gray-500 text-sm block mb-1">วันที่เริ่ม</label>
                                <input type="date" id="start_at" name="start_at"
                                    class="w-full border rounded px-2 py-1"
                                    value="{{ optional($requests->start_at)->format('Y-m-d') }}" readonly />
                            </div>

                            <div class="bg-gray-50 rounded p-4">
                                <label class="text-gray-500 text-sm block mb-1">วันที่มาส่งคืนที่อนุญาต</label>
                                <input type="date" id="end_at" name="end_at"
                                    class="w-full border rounded px-2 py-1"
                                    value="{{ optional($requests->end_at)->format('Y-m-d') }}"
                                    @if ($requests->status !== 'pending') readonly @endif />
                            </div>

                            <div class="bg-gray-50 rounded p-4">
                                <p class="font-semibold text-lg">รวมวันที่จะยืมทั้งสิ้น</p>
                                <p id="total-days" class="font-semibold">0 วัน</p>
                            </div>
                            @if ($requests->status != 'pending')
                            <div class="bg-gray-50 rounded p-4">
                                <label class="text-gray-500 text-sm block mb-1">วันที่มาเเอาของ</label>
                                <input type="datetime-local" name="checked_out_at" id="checked_out_at"
                                    class="w-full border rounded px-2 py-1 @error('checked_out_at') border-red-500 @enderror"
                                    value="{{ old('checked_out_at', optional($requests->transaction?->checked_out_at)->format('Y-m-d\TH:i')) }}" 
                                    @if ($requests->status === 'check_out') readonly @endif required/>
                                <p class="text-xs text-gray-500 mt-1" id="checkout-date-hint">ต้องอยู่ในช่วงวันที่เริ่มถึงวันที่สิ้นสุดที่อนุญาต</p>
                                @error('checked_out_at')
                                    <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                @enderror
                            </div>
                                @if ($requests->status === 'check_out')
                                    <div class="bg-gray-50 rounded p-4">
                                    <label class="text-gray-500 text-sm block mb-1">วันที่มาส่ง</label>
                                    <input type="datetime-local" name="checked_in_at" id="checked_in_at"
                                        class="w-full border rounded px-2 py-1 @error('checked_in_at') border-red-500 @enderror"
                                        value="{{ old('checked_in_at', optional($requests->transaction?->checked_in_at)->format('Y-m-d\TH:i')) }}" required/>
                                    <p class="text-xs text-gray-500 mt-1" id="checkin-date-hint">ต้องเป็นวันถัดไปจากวันที่มาเเอาของ</p>
                                    @error('checked_in_at')
                                        <p class="text-xs text-red-500 mt-1">{{ $message }}</p>
                                    @enderror
                                </div>
                                <div class="bg-gray-50 rounded p-4">
                                    <label class="text-gray-500 text-sm block mb-1">ค่าปรับ</label>
                                    <input type="number" step="0.01" min="0" name="penalty_amount"
                                        class="w-full border rounded px-2 py-1"
                                        value="{{ $requests->transaction->penalty_amount ?? 0 }}"/>
                                </div>
                                <div class="bg-gray-50 rounded p-4 md:col-span-2">
                                    <label class="text-gray-500 text-sm block mb-1">หมายเหตุ</label>
                                    <textarea name="notes" class="w-full border rounded px-2 py-1" rows="2" >{{ $requests->transaction->notes ?? '' }}</textarea>
                                </div>
                                @endif
                                
                            @endif

                        </div>
                        <div class="flex flex-col sm:flex-row justify-end gap-3 mt-4">
                            @if ($requests->status == 'pending')
                                <button type="button"
                                    class="bg-red-600 text-white px-4 sm:px-6 py-2 rounded-md hover:bg-red-700 text-sm sm:text-base"
                                    onclick="showRejectModal()">
                                    ปฏิเสธ
                                </button>
                                <button type="submit"
                                    formaction="{{ route('admin.requests.approve', $requests->req_id) }}"
                                    formmethod="post"
                                    class="bg-blue-600 text-white px-4 sm:px-6 py-2 rounded-md hover:bg-blue-700 text-sm sm:text-base"
                                    onclick="event.preventDefault(); const f=this.closest('form'); const m=document.createElement('input'); m.type='hidden'; m.name='_method'; m.value='PATCH'; f.appendChild(m); f.action='{{ route('admin.requests.approve', $requests->req_id) }}'; f.submit();">
                                    อนุมัติ
                                </button>
                            @elseif ($requests->status !== 'pending' && $requests->status !== 'rejected')
                                <button type="submit"
                                    formaction="{{ route('admin.requests.update', $requests->req_id) }}"
                                    formmethod="post"
                                    class="bg-emerald-600 text-white px-4 sm:px-6 py-2 rounded-md hover:bg-emerald-700 text-sm sm:text-base"
                                    onclick="event.preventDefault(); 
                                        const f=this.closest('form'); 
                                        const m=document.createElement('input'); 
                                        m.type='hidden'; 
                                        m.name='_method'; 
                                        m.value='PATCH'; 
                                        f.appendChild(m); 
                                        f.action='{{ route('admin.requests.update', $requests->req_id) }}'; 
                                        f.submit();">
                                    {{ $requests->status === 'approved' ? 'มารับของแล้ว' : 'มาคืนของแล้ว' }}
                                </button>
                            @elseif ($requests->status == 'rejected')
                                <button type="submit"
                                    formaction="{{ route('admin.requests.update', $requests->req_id) }}"
                                    formmethod="post"
                                    class="bg-emerald-600 text-white px-6 py-2 rounded-md hover:bg-emerald-700 hidden"
                                    onclick="event.preventDefault(); 
                                        const f=this.closest('form'); 
                                        const m=document.createElement('input'); 
                                        m.type='hidden'; 
                                        m.name='_method'; 
                                        m.value='PATCH'; 
                                        f.appendChild(m); 
                                        f.action='{{ route('admin.requests.update', $requests->req_id) }}'; 
                                        f.submit();">
                                    {{ $requests->status === 'approved' ? 'มารับของแล้ว' : 'มาคืนของแล้ว' }}
                                </button>
                            @endif
                        </div>
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

    <div class="mt-6 bg-white p-4 sm:p-6 rounded shadow">
        <h3 class="text-lg font-semibold mb-4">คำขอล่าสุด</h3>
        <div id="admin-table" data-requests='@json($tableRequests)'></div>
    </div>
</x-admin-layout>

<script>
    window.showRejectModal = function() {
        Swal.fire({
            title: 'ปฏิเสธคำขอ',
            html: `
                <div class="text-left space-y-2">
                  <label class="flex items-center gap-2"><input type="radio" name="reason" value="ไม่ตรงตามเงื่อนไขการยืม"> ไม่ตรงตามเงื่อนไขการยืม</label>
                  <label class="flex items-center gap-2"><input type="radio" name="reason" value="อุปกรณ์ไม่พร้อมใช้งาน"> อุปกรณ์ไม่พร้อมใช้งาน</label>
                  <label class="flex items-center gap-2"><input type="radio" name="reason" value="เอกสารไม่ครบถ้วน"> เอกสารไม่ครบถ้วน</label>
                  <label class="flex items-center gap-2"><input type="radio" name="reason" value="อื่นๆ"> อื่นๆ</label>
                  <input id="reason-text" type="text" placeholder="ระบุเหตุผลเพิ่มเติม (ถ้าเลือก อื่นๆ)" class="w-full border rounded px-2 py-1" />
                </div>
            `,
            showCancelButton: true,
            confirmButtonText: 'ปฏิเสธ',
            cancelButtonText: 'ยกเลิก',
            focusConfirm: false,
            preConfirm: () => {
                const selected = document.querySelector('input[name="reason"]:checked');
                const text = (document.getElementById('reason-text') || {}).value || '';
                let reason = selected ? selected.value : '';
                if (!reason) {
                    Swal.showValidationMessage('กรุณาเลือกเหตุผล');
                    return false;
                }
                if (reason === 'อื่นๆ') {
                    if (!text.trim()) {
                        Swal.showValidationMessage('กรุณาระบุเหตุผลเพิ่มเติม');
                        return false;
                    }
                    reason = text.trim();
                }
                return reason;
            }
        }).then((result) => {
            if (result.isConfirmed) {
                const form = document.getElementById('reject-form');
                document.getElementById('reject-reason').value = result.value;
                form.submit();
            }
        });
    }
    function calculateDays() {
        const startInput = document.getElementById('start_at').value;
        const endInput = document.getElementById('end_at').value;
        const output = document.getElementById('total-days');

        if (startInput && endInput) {
            const startDate = new Date(startInput);
            const endDate = new Date(endInput);
            const diffTime = endDate - startDate;
            const diffDays = Math.floor(diffTime / (1000 * 60 * 60 * 24)) + 1;

            if (diffDays > 0) {
                output.textContent = diffDays + ' วัน';
                output.classList.remove('text-red-700');
            } else {
                output.textContent = '0 วัน';
                output.classList.add('text-red-700');
                
                // Show alert for invalid date range
                Swal.fire({
                    icon: 'warning',
                    title: 'ช่วงวันที่ไม่ถูกต้อง!',
                    text: 'วันที่สิ้นสุดต้องอยู่หลังวันที่เริ่มต้น',
                    timer: 3000,
                    showConfirmButton: false
                });
            }
        } else {
            output.textContent = '0 วัน';
            output.classList.remove('text-red-700');
        }
    }

    // Add validation for past dates
    function validateDateInput(inputId, label) {
        const input = document.getElementById(inputId);
        const value = input.value;
        
        if (value) {
            const selectedDate = new Date(value);
            const today = new Date();
            today.setHours(0, 0, 0, 0); // Reset time to start of day
            
            if (selectedDate < today) {
                Swal.fire({
                    icon: 'warning',
                    title: 'วันที่ไม่ถูกต้อง!',
                    text: `${label}ไม่สามารถเป็นวันที่ในอดีตได้`,
                    timer: 3000,
                    showConfirmButton: false
                });
                input.value = '';
                return false;
            }
        }
        return true;
    }

    document.getElementById('start_at').addEventListener('change', function() {
        validateDateInput('start_at', 'วันที่เริ่ม');
        calculateDays();
    });
    document.getElementById('end_at').addEventListener('change', function() {
        validateDateInput('end_at', 'วันที่สิ้นสุด');
        calculateDays();
    });
    calculateDays();

    // Add validation for check-out date
    function validateCheckoutDate() {
        const startDate = document.getElementById('start_at').value;
        const endDate = document.getElementById('end_at').value;
        const checkoutDate = document.getElementById('checked_out_at').value;
        const hint = document.getElementById('checkout-date-hint');
        
        if (!startDate || !endDate) {
            return;
        }
        
        if (checkoutDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            const checkout = new Date(checkoutDate);
            
            // Set min and max attributes for the input
            document.getElementById('checked_out_at').min = startDate + 'T00:00';
            document.getElementById('checked_out_at').max = endDate + 'T23:59';
            
            if (checkout < start || checkout > end) {
                hint.textContent = 'วันที่มาเเอาของต้องอยู่ในช่วงวันที่เริ่มถึงวันที่สิ้นสุดที่อนุญาต';
                hint.classList.remove('text-gray-500');
                hint.classList.add('text-red-500');
                document.getElementById('checked_out_at').classList.add('border-red-500');
                
                // Show alert for invalid date selection
                Swal.fire({
                    icon: 'warning',
                    title: 'วันที่ไม่ถูกต้อง!',
                    text: `วันที่มาเเอาของต้องอยู่ในช่วง ${startDate} ถึง ${endDate}`,
                    timer: 3000,
                    showConfirmButton: false
                });
                
                // Clear the invalid date
                document.getElementById('checked_out_at').value = '';
            } else {
                hint.textContent = 'วันที่มาเเอาของอยู่ในช่วงที่อนุญาต';
                hint.classList.remove('text-red-500');
                hint.classList.add('text-green-500');
                document.getElementById('checked_out_at').classList.remove('border-red-500');
            }
        } else {
            hint.textContent = 'ต้องอยู่ในช่วงวันที่เริ่มถึงวันที่สิ้นสุดที่อนุญาต';
            hint.classList.remove('text-red-500', 'text-green-500');
            hint.classList.add('text-gray-500');
            document.getElementById('checked_out_at').classList.remove('border-red-500');
        }
    }

    // Set initial constraints and add event listeners
    function setCheckoutConstraints() {
        const startDate = document.getElementById('start_at').value;
        const endDate = document.getElementById('end_at').value;
        
        if (startDate && endDate) {
            document.getElementById('checked_out_at').min = startDate + 'T00:00';
            document.getElementById('checked_out_at').max = endDate + 'T23:59';
        }
    }

    document.getElementById('start_at').addEventListener('change', setCheckoutConstraints);
    document.getElementById('end_at').addEventListener('change', setCheckoutConstraints);
    document.getElementById('checked_out_at').addEventListener('change', function() {
        validateCheckoutDate();
        setCheckinConstraints();
    });
    
    // Add validation for check-in date
    function validateCheckinDate() {
        const checkoutDate = document.getElementById('checked_out_at').value;
        const checkinDate = document.getElementById('checked_in_at').value;
        const hint = document.getElementById('checkin-date-hint');
        
        if (!checkoutDate || !checkinDate) {
            return;
        }
        
        const checkout = new Date(checkoutDate);
        const checkin = new Date(checkinDate);
        
        if (checkin <= checkout) {
            hint.textContent = 'วันที่มาส่งต้องอยู่หลังวันที่มาเเอาของ';
            hint.classList.remove('text-gray-500');
            hint.classList.add('text-red-500');
            document.getElementById('checked_in_at').classList.add('border-red-500');
            
            // Show alert for invalid date selection
            Swal.fire({
                icon: 'warning',
                title: 'วันที่ไม่ถูกต้อง!',
                text: 'วันที่มาส่งต้องอยู่หลังวันที่มาเเอาของ',
                timer: 3000,
                showConfirmButton: false
            });
            
            // Clear the invalid date
            document.getElementById('checked_in_at').value = '';
        } else {
            hint.textContent = 'วันที่มาส่งถูกต้อง';
            hint.classList.remove('text-red-500');
            hint.classList.add('text-green-500');
            document.getElementById('checked_in_at').classList.remove('border-red-500');
        }
    }

    // Set minimum date for check-in based on check-out date
    function setCheckinConstraints() {
        const checkoutDate = document.getElementById('checked_out_at').value;
        const checkinInput = document.getElementById('checked_in_at');
        
        if (checkoutDate && checkinInput) {
            // Set minimum date to the day after checkout date
            const checkout = new Date(checkoutDate);
            const nextDay = new Date(checkout);
            nextDay.setDate(checkout.getDate() + 1);
            
            // Format for datetime-local input (YYYY-MM-DDTHH:MM)
            const minDate = nextDay.toISOString().slice(0, 16);
            checkinInput.min = minDate;
            
            // Clear current value if it's before the minimum date
            if (checkinInput.value && new Date(checkinInput.value) <= checkout) {
                checkinInput.value = '';
                validateCheckinDate();
            }
        }
    }

    // Add event listener for check-in date
    const checkinInput = document.getElementById('checked_in_at');
    if (checkinInput) {
        checkinInput.addEventListener('change', validateCheckinDate);
    }
    // Initialize constraints
    setCheckoutConstraints();
    validateCheckoutDate();
    setCheckinConstraints();

    // Add form submission validation
    function validateFormSubmission() {
        const startDate = document.getElementById('start_at').value;
        const endDate = document.getElementById('end_at').value;
        const checkoutDate = document.getElementById('checked_out_at').value;
        const checkinDate = document.getElementById('checked_in_at')?.value;
        
        if (!startDate || !endDate) {
            Swal.fire({
                icon: 'error',
                title: 'ข้อมูลไม่ครบถ้วน!',
                text: 'กรุณาระบุวันที่เริ่มและวันที่สิ้นสุดที่อนุญาต',
                confirmButtonText: 'ตกลง'
            });
            return false;
        }
        
        if (checkoutDate) {
            const start = new Date(startDate);
            const end = new Date(endDate);
            const checkout = new Date(checkoutDate);
            
            if (checkout < start || checkout > end) {
                Swal.fire({
                    icon: 'error',
                    title: 'วันที่ไม่ถูกต้อง!',
                    text: `วันที่มาเเอาของต้องอยู่ในช่วง ${startDate} ถึง ${endDate}`,
                    confirmButtonText: 'ตกลง'
                });
                return false;
            }
        }
        
        if (checkinDate && checkoutDate) {
            const checkout = new Date(checkoutDate);
            const checkin = new Date(checkinDate);
            
            // Check if check-in is at least one day after check-out
            const nextDay = new Date(checkout);
            nextDay.setDate(checkout.getDate() + 1);
            nextDay.setHours(0, 0, 0, 0);
            
            if (checkin < nextDay) {
                Swal.fire({
                    icon: 'error',
                    title: 'วันที่ไม่ถูกต้อง!',
                    text: 'วันที่มาส่งต้องเป็นวันถัดไปจากวันที่มาเเอาของ',
                    confirmButtonText: 'ตกลง'
                });
                return false;
            }
        }
        
        return true;
    }

    // Add event listener to form submission
    document.addEventListener('DOMContentLoaded', function() {
        const forms = document.querySelectorAll('form');
        forms.forEach(form => {
            form.addEventListener('submit', function(e) {
                if (!validateFormSubmission()) {
                    e.preventDefault();
                    return false;
                }
            });
        });
    });


</script>
