<x-admin-layout>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">

        <!-- KPI Cards -->
        <div class="lg:col-span-12 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg border p-4">
                <div class="text-2xl font-semibold mt-1">{{ $totalRequests }}</div>
                <div class="text-xs text-gray-500">Total Requests</div>
            </div>
            <div class="bg-white rounded-lg border p-4">
                <div class="text-2xl font-semibold mt-1">87</div>
                <div class="text-xs text-gray-500">Best Rated Items</div>
            </div>
            <div class="bg-white rounded-lg border p-4">
                <div class="text-2xl font-semibold mt-1">{{ $pendingRequests }}</div>
                <div class="text-xs text-gray-500">Pending Requests</div>
            </div>
            <div class="bg-white rounded-lg border p-4">
                <div class="text-2xl font-semibold mt-1">{{ $penaltyNotices }}</div>
                <div class="text-xs text-gray-500">Penalty Notices</div>
            </div>
        </div>

        <!-- Spline Area Chart (Vue component) -->
<div id="dashboard-chart" class="lg:col-span-7 bg-white rounded-lg border p-4"
     @foreach ($equipmentStatus as $status => $count)
         data-{{ $status }}="{{ $count }}"
         data-{{ $status }}-monthly='@json($equipmentStatusMonthly[$status] ?? [])'
     @endforeach
     data-months='@json($equipmentStatusMonthly['months'])'>
     
    <!-- Show current counts -->
    @foreach ($equipmentStatus as $status => $count)
        <div>{{ ucfirst($status) }}: {{ $count }}</div>
    @endforeach

    <!-- Canvas for chart -->
    <canvas></canvas>
</div>
        <!-- Category Bar Chart (Chart.js) -->
<div class="lg:col-span-5 bg-white rounded-lg border p-4">
    <div class="text-sm font-medium">Category Distribution</div>
    <canvas id="categoryBar" class="mt-4"></canvas>
</div>

        <!-- Recent Activities Table (Vue component) -->

<div id="recent-activities" 
     data-requests='@json($recentRequests)' 
     class="lg:col-span-12 bg-white rounded-lg border">
</div>
</div>
                
            </div>
        </div>

    </div>

    <!-- Chart.js for Category Bar -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script src="{{ mix('js/app.js') }}"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // ---------- Equipment Status Chart ----------
    const chartDiv = document.getElementById('dashboard-chart');
    if (chartDiv) {
        const months = JSON.parse(chartDiv.dataset.months || '[]');

        const statuses = ['available', 'retired', 'maintenance'];
        const datasets = statuses.map(status => {
            const monthlyData = JSON.parse(chartDiv.dataset[`${status}Monthly`] || '[]');
            return {
                label: status.charAt(0).toUpperCase() + status.slice(1),
                data: monthlyData,
                backgroundColor: status === 'available' ? '#10B981' 
                               : status === 'retired' ? '#EF4444' 
                               : '#F59E0B',
            };
        });

        const canvas = chartDiv.querySelector('canvas');
        new Chart(canvas, {
            type: 'bar',
            data: { labels: months, datasets: datasets },
            options: {
                responsive: true,
                plugins: {
                    legend: { position: 'top' },
                    title: { display: true, text: 'Monthly Equipment Status' }
                },
                scales: { y: { beginAtZero: true } }
            }
        });
    }

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
