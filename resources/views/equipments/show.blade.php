<x-app-layout>
    {{-- Session messages for success and error --}}
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
        <div class="flex flex-col lg:flex-row gap-8 items-start">
            {{-- Image Gallery Section --}}
            <div class="w-full lg:w-1/2">
                @php
                    $photos = json_decode($equipment->photo_path ?? '[]', true);
                    // Ensure $photos is an array, even if the DB stores a single string path
                    if (is_string($photos)) {
                        $photos = [$photos];
                    } elseif (empty($photos) && !is_array($photos) && $equipment->photo_path) {
                        $photos = [$equipment->photo_path];
                    }
                @endphp

                @if (!empty($photos) && is_array($photos))
                    <div x-data="{
                        photos: {{ json_encode($photos) }},
                        currentIndex: 0,
                        isDragging: false,
                        startX: 0,
                        currentTranslate: 0,
                        prevTranslate: 0,
                        setActive(index) {
                            this.currentIndex = index;
                            if (this.$refs[`thumb-desktop-${index}`]) {
                                this.$nextTick(() => this.$refs[`thumb-desktop-${index}`].scrollIntoView({ behavior: 'smooth', block: 'nearest' }));
                            }
                        },
                        next() { this.setActive((this.currentIndex + 1) % this.photos.length); },
                        prev() { this.setActive((this.currentIndex - 1 + this.photos.length) % this.photos.length); },
                        scrollUp() { this.$refs.thumbnails.scrollBy({ top: -120, behavior: 'smooth' }); },
                        scrollDown() { this.$refs.thumbnails.scrollBy({ top: 120, behavior: 'smooth' }); },
                        startDrag(event) {
                            this.isDragging = true;
                            this.startX = event.type.includes('mouse') ? event.pageX : event.touches[0].clientX;
                            this.prevTranslate = this.currentIndex * -this.$refs.slider.offsetWidth;
                            this.$refs.slider.style.transition = 'none';
                        },
                        drag(event) {
                            if (!this.isDragging) return;
                            const currentX = event.type.includes('mouse') ? event.pageX : event.touches[0].clientX;
                            const move = currentX - this.startX;
                            this.currentTranslate = this.prevTranslate + move;
                            this.$refs.slider.style.transform = `translateX(${this.currentTranslate}px)`;
                        },
                        endDrag() {
                            if (!this.isDragging) return;
                            this.isDragging = false;
                            this.$refs.slider.style.transition = 'transform 0.3s ease-in-out';
                            const movedBy = this.currentTranslate - this.prevTranslate;
                            if (movedBy < -50 && this.currentIndex < this.photos.length - 1) {
                                this.currentIndex++;
                            }
                            if (movedBy > 50 && this.currentIndex > 0) {
                                this.currentIndex--;
                            }
                            this.$refs.slider.style.transform = `translateX(-${this.currentIndex * 100}%)`;
                            this.setActive(this.currentIndex);
                        }
                    }">
                        {{-- DESKTOP GALLERY --}}
                        <div class="hidden md:flex gap-4">
                            <div class="relative flex-shrink-0 w-24">
                                <button x-show="photos.length > 4" @click="scrollUp()"
                                    class="absolute top-0 left-1/2 -translate-x-1/2 z-10 w-10 h-10 flex items-center justify-center bg-white rounded-full shadow-lg hover:scale-110 transition-transform"
                                    aria-label="Scroll up"><svg class="w-6 h-6 text-gray-800" fill="none"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M4.5 15.75l7.5-7.5 7.5 7.5" />
                                    </svg></button>
                                <div x-ref="thumbnails"
                                    class="absolute inset-0 overflow-auto scrollbar-hide pt-12 pb-12">
                                    <div class="flex flex-col items-center gap-3">
                                        <template x-for="(photo, index) in photos" :key="index">
                                            <div class="flex-shrink-0"><img :src="photo"
                                                    :x-ref="`thumb-desktop-${index}`" alt="Thumbnail"
                                                    class="w-20 h-20 object-cover rounded-lg border-2 cursor-pointer transition-all"
                                                    :class="currentIndex === index ? 'border-blue-500 shadow-md scale-110' :
                                                        'border-transparent hover:border-gray-300'"
                                                    @click="setActive(index)" /></div>
                                        </template>
                                    </div>
                                </div>
                                <button x-show="photos.length > 4" @click="scrollDown()"
                                    class="absolute bottom-0 left-1/2 -translate-x-1/2 z-10 w-10 h-10 flex items-center justify-center bg-white rounded-full shadow-lg hover:scale-110 transition-transform"
                                    aria-label="Scroll down"><svg class="w-6 h-6 text-gray-800" fill="none"
                                        viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round"
                                            d="M19.5 8.25l-7.5 7.5-7.5-7.5" />
                                    </svg></button>
                            </div>
                            <div class="w-full flex-grow relative overflow-hidden bg-gray-100 rounded-lg shadow-inner">
                                <div class="flex transition-transform duration-500 ease-in-out"
                                    :style="`transform: translateX(-${currentIndex * 100}%)`">
                                    <template x-for="(photo, index) in photos" :key="index">
                                        <div class="w-full flex-shrink-0">
                                            <div class="aspect-w-4 aspect-h-3"><img :src="photo"
                                                    alt="Equipment Image" class="w-full h-full object-contain" /></div>
                                        </div>
                                    </template>
                                </div>
                                <template x-if="photos.length > 1">
                                    <div>
                                        <button @click="prev()"
                                            class="absolute top-1/2 left-3 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center bg-white/80 rounded-full shadow-lg hover:bg-white hover:scale-110 transition"
                                            aria-label="Previous"><svg class="w-6 h-6 text-gray-800" fill="none"
                                                viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M15.75 19.5L8.25 12l7.5-7.5" />
                                            </svg></button>
                                        <button @click="next()"
                                            class="absolute top-1/2 right-3 -translate-y-1/2 z-10 w-10 h-10 flex items-center justify-center bg-white/80 rounded-full shadow-lg hover:bg-white hover:scale-110 transition"
                                            aria-label="Next"><svg class="w-6 h-6 text-gray-800" fill="none"
                                                viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor">
                                                <path stroke-linecap="round" stroke-linejoin="round"
                                                    d="M8.25 4.5l7.5 7.5-7.5 7.5" />
                                            </svg></button>
                                    </div>
                                </template>
                            </div>
                        </div>

                        {{-- MOBILE GALLERY --}}
                        <div class="md:hidden">
                            <div class="relative overflow-hidden bg-gray-100 rounded-lg shadow-lg cursor-grab max-h-[300px]"
                                @mousedown.prevent="startDrag" @touchstart.prevent="startDrag" @mousemove.prevent="drag"
                                @touchmove.prevent="drag" @mouseup="endDrag" @touchend="endDrag" @mouseleave="endDrag">
                                <div x-ref="slider" class="flex"
                                    :style="{ transform: `translateX(-${currentIndex * 100}%)` }">
                                    <template x-for="(photo, index) in photos" :key="index">
                                        <div class="w-full flex-shrink-0">
                                            <div class="aspect-w-4 aspect-h-3">
                                                <img :src="photo" alt="Equipment Image"
                                                    class="w-full h-full object-cover pointer-events-none">
                                            </div>
                                        </div>
                                    </template>
                                </div>
                            </div>

                            <template x-if="photos.length > 1">
                                <div class="mt-2 overflow-x-auto scrollbar-hide">
                                    <div class="flex space-x-2 pb-2">
                                        {{-- FIX: Removed .slice(0,6) to show all thumbnails on mobile --}}
                                        <template x-for="(photo, index) in photos" :key="index">
                                            <div class="flex-shrink-0">
                                                <img :src="photo" alt="Thumbnail"
                                                    class="w-12 h-12 object-cover rounded-md border-2"
                                                    :class="currentIndex === index ? 'border-blue-500' : 'border-gray-200'"
                                                    @click="setActive(index)">
                                            </div>
                                        </template>
                                    </div>
                                </div>
                            </template>
                        </div>
                    </div>
                @else
                    {{-- Placeholder when no image is available --}}
                    <div class="w-full aspect-w-4 aspect-h-3 bg-gray-100 rounded-lg flex items-center justify-center">
                        <div class="text-center text-gray-500"><svg class="w-16 h-16 mx-auto mb-2" fill="none"
                                stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                    d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                            </svg>
                            <p>ไม่มีรูปภาพ</p>
                        </div>
                    </div>
                @endif
                {{-- Classic Spec block (responsive) --}}
                @php
                    $sensor = optional($specs['sensor'] ?? null)->spec_value_text;
                    $megapixels = optional($specs['megapixels'] ?? null)->spec_value_number ?? optional($specs['megapixels'] ?? null)->spec_value_text;
                    $wifi = (optional($specs['wifi'] ?? null)->spec_value_bool ?? 0) ? 'Yes' : 'No';
                @endphp
                <div class="mt-4 w-full">
                    <div class="border rounded-lg bg-white">
                        <div class="px-4 py-3 border-b font-semibold text-gray-800">สเปค</div>
                        <div class="divide-y">
                            <div class="flex items-center justify-between px-4 py-3 text-sm">
                                <span class="text-gray-600">Sensor</span>
                                <span class="font-medium text-gray-900">{{ $sensor ?? '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between px-4 py-3 text-sm">
                                <span class="text-gray-600">Megapixels</span>
                                <span class="font-medium text-gray-900">{{ $megapixels ?? '-' }}</span>
                            </div>
                            <div class="flex items-center justify-between px-4 py-3 text-sm">
                                <span class="text-gray-600">Wi‑Fi</span>
                                <span class="font-medium text-gray-900">{{ $wifi }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Details and Borrow Form Section --}}
            <div
                class="w-full lg:w-1/2 flex flex-col justify-start gap-4 p-4 sm:p-5 border border-gray-200 rounded-lg shadow-sm">
                <h1 class="text-2xl md:text-3xl font-bold text-gray-900 break-words">{{ $equipment->name }}</h1>
                <p class="text-gray-500 text-lg break-words">{{ $equipment->category->name }}</p>
                
                {{-- Description with 'Read More' toggle for mobile --}}
                <div class="text-gray-600 text-base leading-relaxed">
                    <div x-data="{ expanded: false }" class="block md:hidden">
                        <span x-show="!expanded" x-transition
                            class="break-words">{{ \Illuminate\Support\Str::limit($equipment->description, 100, '...') }}</span>
                        <span x-show="expanded" x-collapse class="break-words">{{ $equipment->description }}</span>
                        @if (strlen($equipment->description) > 100)
                            <button @click="expanded = !expanded"
                                class="mt-1 text-blue-600 font-semibold hover:underline focus:outline-none"><span
                                    x-show="!expanded">ดูเพิ่มเติม</span><span
                                    x-show="expanded">แสดงน้อยลง</span></button>
                        @endif
                    </div>
                    <div class="hidden md:block">
                        <p class="break-words">{{ $equipment->description }}</p>
                    </div>
                </div>

                {{-- Availability Status Section --}}
                @if($earliestAvailableDate && $earliestAvailableDate['date'])
                    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <i class="fas fa-info-circle text-blue-500"></i>
                            </div>
                            <div class="ml-3">
                                <p class="text-sm text-blue-800">
                                    <span class="font-medium">สถานะอุปกรณ์:</span> {{ $earliestAvailableDate['message'] }}
                                </p>
                            </div>
                        </div>
                    </div>
                @endif

                <div class="mt-4 p-4 sm:p-6 bg-gray-50 border border-gray-200 rounded-lg">
                    <div class="flex items-center justify-between mb-2">
                        <p class="font-semibold text-lg">เลือกวันที่รับ-ส่ง :</p>
                        <div class="text-right">
                            <div class="text-sm text-gray-600">
                                <p>พร้อมให้ยืม: <span class="font-semibold text-green-600">{{ $equipment->available_items_count ?? 0 }}</span> ชิ้น</p>
                                @if($borrowedCount > 0)
                                    <p class="text-xs text-gray-500">ถูกยืมไปแล้ว: <span class="font-medium text-orange-600">{{ $borrowedCount }}</span> ชิ้น</p>
                                @endif
                            </div>
                        </div>
                    </div>
                    <form action="{{ route('borrower.borrow_request', $equipment) }}" method="POST" id="borrowForm">
                        @csrf
                        <input type="hidden" name="equipments_id" value="{{ $equipment->id }}">
                        
                        <div class="mb-4">
                            <label for="request_reason"
                                class="block text-sm font-medium text-gray-700 mb-1">เหตุผลการยืม</label>
                            <select id="request_reason" name="request_reason"
                                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm">
                                <option value="assignment">เพื่อการเรียน/งาน (Assignment)</option>
                                <option value="personal">ส่วนตัว (Personal)</option>
                                <option value="other">อื่น ๆ (ระบุเอง)</option>
                            </select>
                            <input type="text" id="request_reason_other" name="request_reason_other"
                                class="mt-2 hidden w-full border-gray-300 rounded-md shadow-sm"
                                placeholder="ระบุเหตุผลการยืม" maxlength="255">
                            <p class="text-xs text-gray-500 mt-1">หากเลือก "อื่น ๆ" กรุณาระบุเหตุผล</p>
                        </div>
                        <div id="message" class="text-sm text-red-500 mb-4 h-4"></div>
                        <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-4">
                            <div><label for="start_at"
                                    class="block text-sm font-medium text-gray-700">วันที่รับ</label><input
                                    type="text" id="start_at" name="start_at"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm cursor-pointer"
                                    @if ($hasBorrowed) disabled @endif readonly
                                    onclick="toggleCalendar('start')"></div>
                            <div><label for="end_at"
                                    class="block text-sm font-medium text-gray-700">วันที่ส่ง</label><input
                                    type="text" id="end_at" name="end_at"
                                    class="mt-1 block w-full border-gray-300 rounded-md shadow-sm cursor-pointer"
                                    @if ($hasBorrowed) disabled @endif readonly
                                    onclick="toggleCalendar('end')"></div>
                        </div>


                        <div class="mb-4">
                            <label for="quantity"
                                class="block text-sm font-medium text-gray-700">จำนวนที่ต้องการ</label>
                            <input type="number" id="quantity" name="quantity" min="1"
                                max="{{ max($equipment->available_items_count ?? 0, 0) }}" value="1"
                                class="mt-1 block w-32 border-gray-300 rounded-md shadow-sm"
                                @if (($equipment->available_items_count ?? 0) == 0) disabled @endif>
                            <p class="text-xs text-gray-500 mt-1">สูงสุด
                                {{ $equipment->available_items_count ?? 0 }}
                                ชิ้น</p>
                        </div>
                                                <div class="mb-4" x-data="{ open: false }">
                            <div class="flex items-center justify-between">
                                <label class="block text-sm font-medium text-gray-700">อุปกรณ์ประจำตัวเครื่อง (ต่อชิ้น)</label>
                                <button type="button" @click="open=!open" class="text-sm text-blue-600 hover:underline">
                                    <span x-show="!open">แสดง</span>
                                    <span x-show="open">ซ่อน</span>
                                </button>
                            </div>
                            <div x-show="open" class="mt-2 border rounded p-3 bg-white">
                                <p class="text-xs text-gray-500 mb-2">แสดงตามจำนวนที่เลือก</p>
                                <div id="perItemAccContainer" class="space-y-3"></div>
                            </div>
                        </div>
                        <div class="mb-4" x-data="{ open: false }">
                            <label class="block text-sm font-medium text-gray-700 mb-1">ขออุปกรณ์เสริมเพิ่มเติม
                                (ถ้ามี)</label>
                            <button type="button" @click="open=!open"
                                class="px-3 py-2 bg-gray-100 hover:bg-gray-200 rounded text-sm">
                                เลือกอุปกรณ์เสริมเพิ่มเติม
                            </button>
                            <div x-show="open" class="mt-2 border rounded p-3 bg-white">
                                @forelse ($accessories as $acc)
                                    <label class="flex items-center gap-2 py-1">
                                        <input type="checkbox" name="extra_accessories[]"
                                            value="{{ $acc->id }}">
                                        <span>{{ $acc->name }}&nbsp; {{ $acc->description }}</span>
                                    </label>
                                @empty
                                    <div class="text-gray-500 text-sm">ไม่มีอุปกรณ์เสริมเพิ่มเติม</div>
                                @endforelse
                            </div>
                        </div>
                        @if ($hasBorrowed)
                            <a href="{{ route('borrower.equipments.myreq') }}"
                                class="block w-full text-center bg-yellow-500 text-white font-bold py-3 rounded-lg hover:bg-yellow-600 transition">ไปยังหน้าคำขอของฉัน</a>
                        @elseif (($equipment->available_items_count ?? 0) == 0)
                            <button type="button"
                                class="w-full bg-gray-400 text-white font-bold py-3 rounded-lg cursor-not-allowed"
                                disabled>ไม่พร้อมให้ยืม</button>
                        @else
                            <button type="submit" id="borrowButton"
                                class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold py-3 rounded-lg transition duration-200 disabled:opacity-50"
                                disabled>ยืม</button>
                        @endif
                    </form>
                </div>
            </div>
        </div>
    </div>

    {{-- Calendar Modal --}}
    <div id="calendarModal"
        class="hidden fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 p-4">
        <div class="bg-white rounded-lg shadow-xl w-full max-w-sm p-4">
            <div id="calendar"></div>
            <div class="flex justify-between mt-2 pt-2 border-t"><button onclick="setToday()"
                    class="text-sm text-blue-600 hover:underline">วันนี้</button><button onclick="clearDate()"
                    class="text-sm text-red-600 hover:underline">ล้าง</button><button onclick="closeCalendar()"
                    class="text-sm text-gray-600 hover:underline">ปิด</button></div>
        </div>
    </div>
</x-app-layout>

<style>
    .scrollbar-hide::-webkit-scrollbar {
        display: none;
    }

    .scrollbar-hide {
        -ms-overflow-style: none;
        scrollbar-width: none;
    }
</style>

<script>
    let currentInput = null;
    let calendar = null;

    class Calendar {
        constructor(container, dayCounts = {}, totalUnits = 0, rangeEnd = null) {
            this.container = container;
            this.dayCounts = dayCounts; // { 'YYYY-MM-DD': countBorrowed }
            this.totalUnits = totalUnits;
            // Re-enable 3-month limit
            this.rangeEnd = rangeEnd ? new Date(rangeEnd + 'T00:00:00') : new Date(new Date().setMonth(new Date().getMonth() + 3));
            this.currentDate = new Date();
            this.currentDate.setDate(1);
            this.render();
        }
        render() {
            const y = this.currentDate.getFullYear();
            const m = this.currentDate.getMonth();
            const monthNames = ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม',
                'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'
            ];
            const firstDay = new Date(y, m, 1);
            const lastDay = new Date(y, m + 1, 0);
            const daysInMonth = lastDay.getDate();
            const startWeek = firstDay.getDay();
            let html =
                `<div class="bg-white border-gray-200 rounded-lg"><div class="flex justify-between items-center p-2 bg-gray-50 border-b"><button onclick="calendar.prev()" class="px-2 py-1 rounded-full hover:bg-gray-200 transition">‹</button><div class="text-sm font-medium">${monthNames[m]} ${y}</div><button onclick="calendar.next()" class="px-2 py-1 rounded-full hover:bg-gray-200 transition">›</button></div><div class="grid grid-cols-7 bg-gray-50 border-b text-xs font-semibold text-gray-500">${['อา','จ','อ','พ','พฤ','ศ','ส'].map(d => `<div class="p-2 text-center">${d}</div>`).join('')}</div><div class="grid grid-cols-7 text-center">`;
            for (let i = 0; i < startWeek; i++) html += `<div></div>`;
            const today = new Date();
            today.setHours(0, 0, 0, 0);
            for (let d = 1; d <= daysInMonth; d++) {
                const date = new Date(y, m, d);
                const dateStr = `${y}-${String(m + 1).padStart(2, '0')}-${String(d).padStart(2, '0')}`;
                const isPast = date < today;
                const borrowedCount = this.dayCounts[dateStr] || 0;
                const redMark = borrowedCount >= this.totalUnits && this.totalUnits > 0; // fully booked
                const outOfRange = this.rangeEnd ? date > this.rangeEnd : false;
                let cls = "h-10 w-10 flex items-center justify-center text-sm mx-auto rounded-full ";
                let clickHandler = `onclick="selectDate('${dateStr}')"`;
                let title = '';
                if (outOfRange) {
                    cls += "text-gray-300 cursor-not-allowed opacity-50";
                    clickHandler = '';
                    title = 'เกินช่วง 3 เดือนข้างหน้า';
                } else if (isPast) {
                    cls += "text-gray-400 cursor-not-allowed opacity-60";
                    clickHandler = '';
                    title = 'ไม่สามารถเลือกวันที่ในอดีตได้';
                } else {
                    cls += "cursor-pointer hover:bg-blue-100 transition";
                }
                if (redMark) {
                    cls += " ring-2 ring-red-400 bg-red-50 ";
                    title = (title ? title + ' | ' : '') + `จองแล้ว ${borrowedCount} ชิ้น เหลือ ${Math.max(this.totalUnits - borrowedCount, 0)} ชิ้น`;
                    // prevent selecting fully-booked days
                    clickHandler = '';
                } else {
                    title = (title ? title + ' | ' : '') + `เหลือ ${Math.max(this.totalUnits - borrowedCount, 0)} ชิ้น`;
                }
                if (date.getTime() === today.getTime()) {
                    cls += " bg-blue-600 text-white font-bold"
                }
                html +=
                    `<div class="py-1"><div class="${cls}" data-date="${dateStr}" ${clickHandler} title="${title}">${d}</div></div>`;
            }
            html += "</div></div>";
            this.container.innerHTML = html;
        }
        prev() {
            this.currentDate.setMonth(this.currentDate.getMonth() - 1);
            this.render();
        }
        next() {
            // prevent navigating beyond 3 months window visually
            const nextMonth = new Date(this.currentDate);
            nextMonth.setMonth(nextMonth.getMonth() + 1);
            if (this.rangeEnd && nextMonth > this.rangeEnd) return;
            this.currentDate.setMonth(this.currentDate.getMonth() + 1);
            this.render();
        }
    }

    function toggleCalendar(input) {
        currentInput = input;
        const modal = document.getElementById('calendarModal');
        modal.classList.remove('hidden');
        if (!calendar) {
            const cal = @json($calendarData);
            calendar = new Calendar(
                document.getElementById('calendar'),
                cal.dayCounts || {},
                cal.totalUnits || 0,
                cal.rangeEnd || null
            );
        }
    }

    function closeCalendar() {
        document.getElementById('calendarModal').classList.add('hidden');
    }

    function selectDate(dateStr) {
        if (currentInput) {
            const d = new Date(dateStr + 'T00:00:00');
            const day = String(d.getDate()).padStart(2, '0');
            const month = String(d.getMonth() + 1).padStart(2, '0');
            const year = d.getFullYear();

            document.getElementById(currentInput + '_at').value = `${day}/${month}/${year}`;
            closeCalendar();
            validateFormState();
        }
    }

    function clearDate() {
        if (currentInput) {
            document.getElementById(currentInput + '_at').value = '';
            validateFormState();
        }
    }

    function setToday() {
        if (currentInput) {
            const t = new Date();
            selectDate(
                `${t.getFullYear()}-${String(t.getMonth() + 1).padStart(2, '0')}-${String(t.getDate()).padStart(2, '0')}`
            );
        }
    }

    // This function will be defined after caching DOM elements
    let validateFormState = () => {};

    document.addEventListener('DOMContentLoaded', () => {
        // Cache DOM elements for performance
        const s_input = document.getElementById('start_at');
        const e_input = document.getElementById('end_at');
        const button = document.getElementById('borrowButton');
        const msg = document.getElementById('message');
        const reasonSel = document.getElementById('request_reason');
        const reasonOther = document.getElementById('request_reason_other');
        const qtyInput = document.getElementById('quantity');
        const borrowForm = document.getElementById('borrowForm');
        const calendarModal = document.getElementById('calendarModal');
        const perItemAccContainer = document.getElementById('perItemAccContainer');
        const itemAccessories = @json($itemAccessories->map(fn($a) => [
            'equipment_item_id' => $a->equipment_item_id,
            'name' => $a->name,
            'description' => $a->description
        ]));
        const itemSerials = @json($itemSerials);

        // Define validation function in this scope to access cached elements
        validateFormState = () => {
            if (!button) return;

            let isValid = true;
            let errorMsg = '';

            if (!s_input.value || !e_input.value) {
                isValid = false;
            } else {
                const s_parts = s_input.value.split('/');
                const e_parts = e_input.value.split('/');
                const startDate = new Date(`${s_parts[2]}-${s_parts[1]}-${s_parts[0]}`);
                const endDate = new Date(`${e_parts[2]}-${e_parts[1]}-${e_parts[0]}`);

                if (endDate <= startDate) {
                    errorMsg = 'วันส่งควรอยู่หลังวันรับ';
                    isValid = false;
                }
            }

            if (isValid && reasonSel && reasonSel.value === 'other' && reasonOther.value.trim().length === 0) {
                errorMsg = 'กรุณาระบุเหตุผลการยืม';
                isValid = false;
            }

            if (isValid && qtyInput && (parseInt(qtyInput.value || '0', 10) < 1)) {
                errorMsg = 'จำนวนที่ต้องการต้องมากกว่า 0';
                isValid = false;
            }
            
            msg.textContent = errorMsg;
            button.disabled = !isValid;
        };

        if (borrowForm) {
            borrowForm.addEventListener('submit', function(e) {
                @if (!Auth::check())
                    e.preventDefault();
                    Swal.fire({
                        title: 'คุณต้องเข้าสู่ระบบ',
                        text: 'กด ตกลง เพื่อไปยังหน้าล็อกอิน',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'ตกลง',
                        cancelButtonText: 'ยกเลิก'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = "{{ route('login') }}";
                        }
                    });
                    return;
                @endif

                const userVerified = {{ json_encode($userVerified ?? null) }}; 

                if (userVerified === 0) {
                    e.preventDefault();
                    Swal.fire({
                        title: 'กรุณายืนยันตัวตน',
                        text: 'โปรดยืนยันตัวตนเพื่อดำเนินการต่อ',
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonText: 'ไปที่โปรไฟล์',
                        cancelButtonText: 'ยกเลิก'
                    }).then((r) => {
                        if (r.isConfirmed) {
                            window.location.href = "{{ route(name: 'profile.show') }}";
                        }
                    });
                }
            });
        }

        if (reasonSel && reasonOther) {
            reasonSel.addEventListener('change', function() {
                reasonOther.classList.toggle('hidden', this.value !== 'other');
                validateFormState();
            });
            reasonOther.addEventListener('input', validateFormState);
        }

        if (qtyInput) {
            qtyInput.addEventListener('input', () => { 
                validateFormState(); 
                updatePerItemAccessories(); 
            });
        }

        if (calendarModal) {
            calendarModal.addEventListener('click', function(e) {
                if (e.target === calendarModal) {
                    closeCalendar();
                }
            });
        }
        
        function updatePerItemAccessories() {
            if (!perItemAccContainer || !qtyInput) return;
            const count = Math.max(parseInt(qtyInput.value || '0', 10), 0);
            const grouped = {};
            (itemAccessories || []).forEach((a) => {
                if (!grouped[a.equipment_item_id]) grouped[a.equipment_item_id] = [];
                grouped[a.equipment_item_id].push(a);
            });
            const itemIds = Object.keys(grouped).slice(0, count);
            let html = '';
            if (itemIds.length === 0) {
                html = '<div class="text-gray-500 text-sm">ไม่มีข้อมูลอุปกรณ์ประจำชิ้น</div>';
            } else {
                itemIds.forEach((id, idx) => {
                    const serial = itemSerials[id] || id;
                    html += `<div class="border rounded p-2">\n                        <div class="font-medium">ชิ้นที่ ${idx + 1} (${serial})</div>\n                        <ul class="list-disc list-inside text-sm text-gray-800 mt-1">`;
                    grouped[id].forEach((it) => {
                        const desc = it.description ? ` - ${it.description}` : '';
                        html += `<li>${it.name}${desc}</li>`;
                    });
                    html += `</ul></div>`;
                });
            }
            perItemAccContainer.innerHTML = html;
        }

        // Initial validation check and UI update on page load
        validateFormState();
        updatePerItemAccessories();
    });
</script>
