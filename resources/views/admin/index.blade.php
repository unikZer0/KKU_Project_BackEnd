<x-admin-layout>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- KPI Cards -->
        <div class="lg:col-span-12 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg border p-4">
                <div class="text-2xl font-semibold mt-1">{{ $totalRequests }}</div>
                <div class="text-xs text-gray-500">Total Requests</div>
            </div>
            <!--<div class="bg-white rounded-lg border p-4">
                <div class="text-2xl font-semibold mt-1">NaN</div>
                <div class="text-xs text-gray-500">Best Rated Items</div>
            </div>-->
            <div class="bg-white rounded-lg border p-4">
                <div class="text-2xl font-semibold mt-1">{{ $pendingRequests }}</div>
                <div class="text-xs text-gray-500">Pending Requests</div>
            </div>
            <div class="bg-white rounded-lg border p-4">
                <div class="text-2xl font-semibold mt-1">{{ $penaltyNotices }}</div>
                <div class="text-xs text-gray-500">Penalty Notices</div>
            </div>
            <div class="bg-white rounded-lg border p-4">
<form method="GET" action="{{ route('admin.index') }}" class="mb-4">
    <label for="year" class="mr-2 font-semibold">Select Year:</label>
    <select name="year" id="year" onchange="this.form.submit()" class="border rounded px-2 py-1">
        @foreach($availableYears as $y)
            <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>
                {{ $y }}
            </option>
        @endforeach
    </select>
</form>
            </div>
        </div>
         <!--dropdown year-->
<div id="dashboard-chart" class="lg:col-span-7 bg-white rounded-lg border p-4"
     data-total-requests='@json($borrowStatusMonthly["TotalRequests"] ?? [])'
     data-approved='@json($borrowStatusMonthly["Approved"] ?? [])'
     data-rejected='@json($borrowStatusMonthly["Rejected"] ?? [])'
     data-months='@json($borrowStatusMonthly["months"] ?? [])'
     data-selected-year='@json($selectedYear)'
     data-available-years='@json($availableYears)'>
    <canvas></canvas>
</div>

<div class="lg:col-span-5 bg-white rounded-lg border p-4">
    <div class="text-sm font-medium">Category Distribution</div>
    <canvas id="categoryBar" class="mt-4"></canvas>
</div>

<div id="recent-activities" 
     data-requests='@json($recentRequests)' 
     class="lg:col-span-12 bg-white rounded-lg border">
</div>
</div>
            </div>
        </div>

    </div>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    @vite('resources/js/app.js')

<script>
document.addEventListener('DOMContentLoaded', function() {
    // ---------- Category Bar Chart ----------
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
            options: {
                responsive: true,
                plugins: { legend: { display: false } }
            }
        });
    }
});
</script>
</x-admin-layout>
