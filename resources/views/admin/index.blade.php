<x-admin-layout>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- KPI Cards -->
        <div class="lg:col-span-12 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg border p-4">
                <div class="text-2xl font-semibold mt-1">{{ $borrowStatus['TotalRequests'] }}</div>
                <div class="text-xs text-gray-500">คำขอทั้งหมด</div>
            </div>
            <div class="bg-white rounded-lg border p-4">
                <div class="text-2xl font-semibold mt-1">{{ $borrowStatus['checkinReq'] }}</div>
                <div class="text-xs text-gray-500">การยืมที่สำเร็จ</div>
            </div>
            <div class="bg-white rounded-lg border p-4">
                <div class="text-2xl font-semibold mt-1">{{ $borrowStatus['Pending'] }}</div>
                <div class="text-xs text-gray-500">คำขอที่รอดำเนินการ</div>
            </div>
            <div class="bg-white rounded-lg border p-4">
                <div class="text-2xl font-semibold mt-1">{{ $borrowStatus['Rejected'] }}</div>
                <div class="text-xs text-gray-500">คำขอที่ถูกปฏิเสธ</div>
            </div>
        </div>

        <!-- Year + Month Filter -->
        <div class="lg:col-span-12 bg-white rounded-lg border p-4">
            <form method="GET" action="{{ route('admin.index') }}" class="flex flex-wrap gap-3 items-end mb-4">
                {{-- Year --}}
                <div>
                    <label for="year" class="block text-sm font-medium text-gray-700">Year</label>
                    <select name="year" id="year" onchange="this.form.submit()" class="mt-1 block w-32 border-gray-300 rounded-md shadow-sm">
                        @foreach($availableYears as $yearOption)
                            <option value="{{ $yearOption }}" {{ $selectedYear == $yearOption ? 'selected' : '' }}>
                                {{ $yearOption }}
                            </option>
                        @endforeach
                    </select>
                </div>

                {{-- Month --}}
                <div>
                    <label for="month" class="block text-sm font-medium text-gray-700">Month</label>
                    <select name="month" id="month" onchange="this.form.submit()" class="mt-1 block w-32 border-gray-300 rounded-md shadow-sm">
                        <option value="">All</option>
                        @foreach(['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'] as $index => $monthName)
                            @php $monthNumber = $index + 1; @endphp
                            <option value="{{ $monthNumber }}" {{ (isset($selectedMonth) && $selectedMonth == $monthNumber) ? 'selected' : '' }}>
                                {{ $monthName }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </form>
        </div>

        <!-- Chart Toggle -->
        <div class="lg:col-span-12 bg-white rounded-lg border p-4 mb-4">
            <div class="flex gap-2">
                <button 
                    id="monthly-chart-btn" 
                    class="px-4 py-2 text-sm rounded-md bg-blue-500 text-white transition-colors"
                    onclick="showMonthlyChart()"
                >
                    กราฟรายเดือน
                </button>
                <button 
                    id="equipment-chart-btn" 
                    class="px-4 py-2 text-sm rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300 transition-colors"
                    onclick="showEquipmentChart()"
                >
                    กราฟรายการอุปกรณ์
                </button>
            </div>
        </div>

        <!-- Monthly Chart -->
        <div id="monthly-chart-container" class="lg:col-span-7 bg-white rounded-lg border p-4" data-chart='@json($chartData)'>
            <canvas></canvas>
        </div>

        <!-- Equipment Chart -->
        <div id="equipment-chart-container" class="lg:col-span-7 bg-white rounded-lg border p-4" style="display: none;" data-equipment-time='@json($equipmentTimeData)'>
        </div>

        <!-- Category Chart -->
        <div class="lg:col-span-5 bg-white rounded-lg border p-4">
            <div class="text-sm font-medium">การกระจายตามหมวดหมู่</div>
            <canvas id="categoryBar" class="mt-4"></canvas>
        </div>

        <!-- Recent Requests -->
        <div id="recent-activities" data-requests='@json($recentRequests)' class="lg:col-span-12 bg-white rounded-lg border"></div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite('resources/js/app.js')

    <script>
        let monthlyChart = null;
        let equipmentChartApp = null;
        let isTransitioning = false;
        let transitionTimeout = null;

        document.addEventListener('DOMContentLoaded', function () {
            // ---------- Category Chart ----------
            const barEl = document.getElementById('categoryBar');
            if (barEl) {
                new Chart(barEl, {
                    type: 'bar',
                    data: {
                        labels: @json($categoryCounts->pluck('name')),
                        datasets: [{
                            label: 'Items',
                            data: @json($categoryCounts->pluck('equipments_count')),
                            backgroundColor: '#3b82f6'
                        }]
                    },
                    options: { responsive: true, plugins: { legend: { display: false } } }
                });
            }

            // Initialize monthly chart
            initializeMonthlyChart();
        });

        function initializeMonthlyChart() {
            const chartEl = document.getElementById('monthly-chart-container');
            if (!chartEl) return;

            const chartData = JSON.parse(chartEl.dataset.chart);
            const ctx = chartEl.querySelector('canvas').getContext('2d');

            monthlyChart = new Chart(ctx, {
                type: 'bar',
                data: {
                    labels: chartData.labels,
                    datasets: [
                        { label: 'คำขอทั้งหมด', data: chartData.TotalRequests, backgroundColor: 'rgba(74,222,128,0.6)', borderColor: '#4ade80', borderWidth: 1 },
                        { label: 'คำขอที่อนุมัติ', data: chartData.Approved, backgroundColor: 'rgba(59,130,246,0.6)', borderColor: '#3b82f6', borderWidth: 1 },
                        { label: 'คำขอที่ถูกปฏิเสธ', data: chartData.Rejected, backgroundColor: 'rgba(239,68,68,0.6)', borderColor: '#ef4444', borderWidth: 1 },
                    ]
                },
                options: { responsive: true, plugins: { legend: { position: 'bottom' } }, scales: { y: { beginAtZero: true } } }
            });
        }

        function showMonthlyChart() {
            // Prevent rapid switching
            if (isTransitioning) {
                return;
            }

            isTransitioning = true;
            
            // Clear any existing timeout
            if (transitionTimeout) {
                clearTimeout(transitionTimeout);
            }

            // Debounce the chart switching
            transitionTimeout = setTimeout(() => {
                document.getElementById('monthly-chart-container').style.display = 'block';
                document.getElementById('equipment-chart-container').style.display = 'none';
                
                // Update button styles
                document.getElementById('monthly-chart-btn').className = 'px-4 py-2 text-sm rounded-md bg-blue-500 text-white transition-colors';
                document.getElementById('equipment-chart-btn').className = 'px-4 py-2 text-sm rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300 transition-colors';
                
                isTransitioning = false;
            }, 200);
        }

        function showEquipmentChart() {
            // Prevent rapid switching
            if (isTransitioning) {
                return;
            }

            isTransitioning = true;
            
            // Clear any existing timeout
            if (transitionTimeout) {
                clearTimeout(transitionTimeout);
            }

            // Debounce the chart switching
            transitionTimeout = setTimeout(() => {
                document.getElementById('monthly-chart-container').style.display = 'none';
                document.getElementById('equipment-chart-container').style.display = 'block';
                
                // Update button styles
                document.getElementById('equipment-chart-btn').className = 'px-4 py-2 text-sm rounded-md bg-blue-500 text-white transition-colors';
                document.getElementById('monthly-chart-btn').className = 'px-4 py-2 text-sm rounded-md bg-gray-200 text-gray-700 hover:bg-gray-300 transition-colors';

                // Initialize equipment chart if not already done
                if (!equipmentChartApp) {
                    // Wait a bit for Vue components to be available
                    setTimeout(() => {
                        const equipmentEl = document.getElementById('equipment-chart-container');
                        if (equipmentEl && window.createApp && window.EquipmentChart) {
                            const equipmentTimeData = JSON.parse(equipmentEl.dataset.equipmentTime);
                            equipmentChartApp = window.createApp(window.EquipmentChart, {
                                equipmentTimeData: equipmentTimeData
                            }).mount(equipmentEl);
                        } else {
                            console.error('Vue components not loaded yet');
                        }
                    }, 100);
                }
                
                isTransitioning = false;
            }, 200);
        }
    </script>
</x-admin-layout>
