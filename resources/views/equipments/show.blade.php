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
        <div class="flex flex-col lg:flex-row gap-6 items-start">
            <div class="w-full lg:w-1/2">
                <img src="{{ $equipment->photo_path }}" alt="{{ $equipment->name }}"
                    class="w-full h-auto max-h-[40vh] sm:max-h-[50vh] lg:max-h-[70vh] object-contain rounded-lg shadow-lg">
            </div>

            <div class="w-full lg:w-1/2 flex flex-col justify-start gap-4 p-4 sm:p-5 border border-gray-300 rounded-lg lg:p-10">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 break-words">{{ $equipment->name }}</h1>
                <p class="text-gray-500 text-lg break-words">{{ $equipment->category->name }}</p>
                <div class="text-gray-500 text-lg break-words">
                    <div x-data="{ expanded: false }" class="block md:hidden">
                        <span x-show="!expanded">
                            {{ \Illuminate\Support\Str::limit($equipment->description, 80, '...') }}
                        </span>
                        <span x-show="expanded" class="break-words">
                            {{ $equipment->description }}
                        </span>
                        <button @click="expanded = !expanded" class="ml-2 text-gray-600 underline focus:outline-none">
                            <span x-show="!expanded">ดูเพี่มเตีม</span>
                            <span x-show="expanded">แสดงน้อยลง</span>
                        </button>
                    </div>
                    <div class="hidden md:block break-words">
                        {{ $equipment->description }}
                    </div>
                </div>

                <p class="mt-4 font-semibold text-lg">เลือกวันที่รับ-ส่ง :</p>
                <div class="p-4 sm:p-6 bg-gray-50 border border-gray-200 rounded-lg">
                    <form action="{{ route('borrower.borrow_request', $equipment) }}" method="POST" id="borrowForm">
                        @csrf
                        <input type="hidden" name="equipments_id" value="{{ $equipment->id }}">
                        <div class="mb-4">
                            <label for="start_at" class="block text-sm font-semibold text-gray-700 mb-1">วันที่รับ:</label>
                            <div class="relative">
                                <input type="text" id="start_at" name="start_at" required readonly
                                    placeholder="mm/dd/yyyy"
                                    class="w-full p-2 pr-10 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <button type="button" onclick="toggleCalendar('start')" 
                                    class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div class="mb-6">
                            <label for="end_at" class="block text-sm font-semibold text-gray-700 mb-1">วันที่คืน:</label>
                            <div class="relative">
                                <input type="text" id="end_at" name="end_at" required readonly
                                    placeholder="mm/dd/yyyy"
                                    class="w-full p-2 pr-10 border border-gray-300 rounded-md focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                <button type="button" onclick="toggleCalendar('end')" 
                                    class="absolute right-2 top-1/2 transform -translate-y-1/2 text-gray-400 hover:text-gray-600">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                        @if ($hasBorrowed)
                            <a href="{{ route('borrower.equipments.myreq') }}">
                                <button type="button"
                                    class="w-full bg-yellow-500 text-white font-bold py-2 rounded-md">
                                    ไปยังหน้าคำขอของฉัน
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
                        <p id="message" class="text-sm mt-2"></p>
                    </form>
                    <div id="calendarModal" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 flex items-center justify-center">
                        <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-4">
                            <div class="p-4 border-b border-gray-200">
                                <div class="flex justify-between items-center">
                                    <h3 class="text-lg font-semibold text-gray-900">เลือกวันที่</h3>
                                    <button onclick="closeCalendar()" class="text-gray-400 hover:text-gray-600">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                            <div class="p-4">
                                <div id="calendar" class="max-w-full"></div>
                                <div class="flex justify-between mt-4">
                                    <button onclick="clearDate()" class="text-blue-600 hover:text-blue-800 text-sm">Clear</button>
                                    <button onclick="setToday()" class="text-blue-600 hover:text-blue-800 text-sm">Today</button>
                                </div>
                            </div>
                        </div>
                    </div>
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

    let currentInput = null;
    let calendar = null;

    class Calendar {
        constructor(container, borrowedDates = []) {
            this.container = container;
            this.borrowedDates = borrowedDates;
            this.currentDate = new Date();
            this.render();
        }
        render() {
            const y = this.currentDate.getFullYear();
            const m = this.currentDate.getMonth();
            const monthNames = ['มกราคม','กุมภาพันธ์','มีนาคม','เมษายน','พฤษภาคม','มิถุนายน','กรกฎาคม','สิงหาคม','กันยายน','ตุลาคม','พฤศจิกายน','ธันวาคม'];
            const firstDay = new Date(y, m, 1);
            const lastDay = new Date(y, m+1, 0);
            const daysInMonth = lastDay.getDate();
            const startWeek = firstDay.getDay();

            let html = `
              <div class="bg-white border border-gray-200 rounded-lg">
                <div class="flex justify-between items-center p-2 bg-gray-50 border-b">
                  <button onclick="calendar.prev()" class="px-2 py-1 hover:bg-gray-200">‹</button>
                  <div class="text-sm font-medium">${monthNames[m]} ${y}</div>
                  <button onclick="calendar.next()" class="px-2 py-1 hover:bg-gray-200">›</button>
                </div>
                <div class="grid grid-cols-7 bg-gray-50 border-b text-xs font-semibold text-gray-500">
                  ${['อา','จ','อ','พ','พฤ','ศ','ส'].map(d=>`<div class="p-2 text-center">${d}</div>`).join('')}
                </div>
                <div class="grid grid-cols-7">`;

            const prevDays = new Date(y, m, 0).getDate();
            for (let i=startWeek-1;i>=0;i--) html += `<div class="p-2 text-center text-gray-300 border">${prevDays-i}</div>`;

            const today = new Date();
             for (let d=1; d<=daysInMonth; d++) {
                 const dateStr = `${y}-${String(m+1).padStart(2,'0')}-${String(d).padStart(2,'0')}`;
                 const isToday = today.toDateString() === new Date(y,m,d).toDateString();
                 const isBorrowed = this.borrowedDates.some(b=>{
                     const s=new Date(b.start_at), e=new Date(b.end_at), chk=new Date(dateStr);
                     return chk>=s && chk<=e;
                 });
                 let cls="p-2 text-center border flex items-center justify-center text-sm ";
                 if (isBorrowed) {
                     cls+="bg-red-100 text-red-600 font-bold cursor-not-allowed opacity-60 ";
                 } else if (isToday) {
                     cls+="bg-blue-100 text-blue-600 font-bold cursor-pointer hover:bg-blue-200 ";
                     html+=`<div class="${cls}" data-date="${dateStr}" onclick="selectDate('${dateStr}')">${d}</div>`;
                     continue;
                 } else {
                     cls+="cursor-pointer hover:bg-gray-50 ";
                 }
                 
                 if (isBorrowed) {
                     html+=`<div class="${cls}" title="วันที่นี้ถูกจองแล้ว">${d}</div>`;
                 } else {
                     html+=`<div class="${cls}" data-date="${dateStr}" onclick="selectDate('${dateStr}')">${d}</div>`;
                 }
             }
            const filled=startWeek+daysInMonth, total=42;
            for(let d=1;d<=total-filled;d++) html+=`<div class="p-2 text-center text-gray-300 border">${d}</div>`;
            html+="</div></div>";
            this.container.innerHTML=html;
        }
        prev(){this.currentDate.setMonth(this.currentDate.getMonth()-1);this.render();}
        next(){this.currentDate.setMonth(this.currentDate.getMonth()+1);this.render();}
    }

     function toggleCalendar(input) {
         currentInput=input;
         const modal = document.getElementById('calendarModal');
         modal.classList.remove('hidden');
         if (!calendar) {
             const borrowed=@json($bookings->map(fn($b)=>['start_at'=>$b->start_at,'end_at'=>$b->end_at]));
             calendar=new Calendar(document.getElementById('calendar'),borrowed);
         }
         
         modal.addEventListener('click', function(e) {
             if (e.target === modal) {
                 closeCalendar();
             }
         });
     }
    function closeCalendar(){document.getElementById('calendarModal').classList.add('hidden');}
    function selectDate(dateStr){
        if(currentInput){
            const d=new Date(dateStr);
            document.getElementById(currentInput+'_at').value=`${String(d.getMonth()+1).padStart(2,'0')}/${String(d.getDate()).padStart(2,'0')}/${d.getFullYear()}`;
            closeCalendar();
        }
    }
    function clearDate(){if(currentInput){document.getElementById(currentInput+'_at').value='';closeCalendar();}}
    function setToday(){if(currentInput){const t=new Date();document.getElementById(currentInput+'_at').value=`${String(t.getMonth()+1).padStart(2,'0')}/${String(t.getDate()).padStart(2,'0')}/${t.getFullYear()}`;closeCalendar();}}
    document.addEventListener('DOMContentLoaded',()=>{
        const s=document.getElementById('start_at'), e=document.getElementById('end_at'), b=document.getElementById('borrowButton'), msg=document.getElementById('message');
        function validate(){
            if(!s.value||!e.value){b.disabled=true;return;}
            const sd=new Date(s.value), ed=new Date(e.value);
            if(ed<=sd){msg.textContent='วันส่งควรอยู่หลังวันรับ.';msg.classList.add('text-red-500');b.disabled=true;}
            else{msg.textContent='';b.disabled=false;}
        }
        s.addEventListener('input',validate);e.addEventListener('input',validate);
    });
</script>
