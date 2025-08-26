<x-admin-layout>
    <div class="grid grid-cols-1 lg:grid-cols-12 gap-6">
        <!-- KPI Cards -->
        <div class="lg:col-span-12 grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg border p-4">
                <div class="text-2xl font-semibold mt-1">{{ $totalRequests }}</div>
                <div class="text-2xl font-semibold mt-1">{{ $totalRequests }}</div>
            </div>
            <div class="bg-white rounded-lg border p-4">
                <div class="text-xs text-gray-500">Best Rated Items</div>
                <div class="text-2xl font-semibold mt-1">87</div>
            </div>
            <div class="bg-white rounded-lg border p-4">
                <div class="text-2xl font-semibold mt-1">{{ $pendingRequests }}</div>
            </div>
            <div class="bg-white rounded-lg border p-4">
                <div class="text-2xl font-semibold mt-1">{{ $penaltyNotices }}</div>
            </div>
        </div>

        <!-- Charts -->
        <div class="lg:col-span-7 bg-white rounded-lg border p-4">
            <div class="text-sm font-medium">Equipment Status</div>
            <canvas id="equipmentPie" class="mt-4"></canvas>
        </div>
        <div class="lg:col-span-5 bg-white rounded-lg border p-4">
            <div class="text-sm font-medium">Category Distribution</div>
            <canvas id="categoryBar" class="mt-4"></canvas>
        </div>

        <!-- Recent Activities Table -->
        <div class="lg:col-span-12 bg-white rounded-lg border">
            <div class="flex items-center justify-between p-4">
                <div class="text-sm font-medium">Recent Activities</div>
                <a href="#" class="text-xs text-blue-600">View all</a>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full text-sm">
                    <thead class="bg-gray-50 border-t border-b">
                        <tr>
                            <th class="text-left px-4 py-2">Request ID</th>
                            <th class="text-left px-4 py-2">User</th>
                            <th class="text-left px-4 py-2">Equipment</th>
                            <th class="text-left px-4 py-2">Date</th>
                            <th class="text-left px-4 py-2">Status</th>
                            <th class="text-left px-4 py-2">Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr class="border-b">
                            <td class="px-4 py-2">RQ5001</td>
                            <td class="px-4 py-2">John Doe</td>
                            <td class="px-4 py-2">Multimix Pro</td>
                            <td class="px-4 py-2">2025-09-02</td>
                            <td class="px-4 py-2"><span class="px-2 py-1 text-xs rounded bg-yellow-100 text-yellow-700">Pending</span></td>
                            <td class="px-4 py-2 space-x-2"><a href="#" class="text-blue-600">View</a></td>
                        </tr>
                        <tr class="border-b">
                            <td class="px-4 py-2">RQ5021</td>
                            <td class="px-4 py-2">Jane Smith</td>
                            <td class="px-4 py-2">DSLR Camera</td>
                            <td class="px-4 py-2">2025-09-01</td>
                            <td class="px-4 py-2"><span class="px-2 py-1 text-xs rounded bg-green-100 text-green-700">Returned</span></td>
                            <td class="px-4 py-2 space-x-2"><a href="#" class="text-blue-600">View</a></td>
                        </tr>
                        <tr>
                            <td class="px-4 py-2">RQ5031</td>
                            <td class="px-4 py-2">Mike Johnson</td>
                            <td class="px-4 py-2">Projector</td>
                            <td class="px-4 py-2">2025-08-30</td>
                            <td class="px-4 py-2"><span class="px-2 py-1 text-xs rounded bg-red-100 text-red-700">Overdue</span></td>
                            <td class="px-4 py-2 space-x-2"><a href="#" class="text-blue-600">View</a></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Charts.js CDN for demo -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function(){
            const pieEl = document.getElementById('equipmentPie');
            if (pieEl) {
                new Chart(pieEl, {
                    type: 'doughnut',
                    data: {
                        labels: ['Available', 'Unavailable', 'Maintenance'],
                        datasets: [{
                            data: [60, 30, 10],
                            backgroundColor: ['#22c55e', '#3b82f6', '#f59e0b']
                        }]
                    },
                    options: {responsive: true, plugins: {legend: {position: 'bottom'}}}
                });
            }

            const barEl = document.getElementById('categoryBar');
            if (barEl) {
                new Chart(barEl, {
                    type: 'bar',
                    data: {
                        labels: ['Lab', 'Multimedia', 'Sports', 'Other'],
                        datasets: [{
                            label: 'Items',
                            data: [30, 45, 20, 12],
                            backgroundColor: '#3b82f6'
                        }]
                    },
                    options: {responsive: true, plugins: {legend: {display: false}}}
                });
            }
        });
    </script>
</x-admin-layout>
