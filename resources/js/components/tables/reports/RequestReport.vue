<template>
    <div class="p-4 bg-white rounded-lg border">

        <!-- Breadcrumb -->
        <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
            <a href="/admin" class="hover:text-gray-700">แดชบอร์ด</a>
            <span>/</span>
            <a href="/admin/report" class="hover:text-gray-700">รายงาน</a>
            <span>></span>
            <span class="font-semibold text-gray-900">รายงานคำขอยืม</span>
        </nav>

        <!-- title -->
        <h2 class="text-xl font-bold mb-4">รายงานคำร้องขอยืม</h2>

        <!-- Search Input -->
        <div class="relative mb-4">
            <input type="text" v-model="searchQuery" placeholder="Search"
                class="pl-10 pr-3 py-2 text-sm border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
            </svg>
        </div>

        <!-- Filter Controls -->
        <div class="flex flex-wrap gap-2 items-center relative mb-4" ref="filtersWrap">
            <button @click="filtersOpen = !filtersOpen" class="px-3 py-1 border rounded">
                ตัวกรอง
                <span class="text-xs text-gray-500 ml-1">
                    {{ filterStatus || 'ทั้งหมด' }} ·
                </span>
            </button>

            <button class="px-3 py-1 border rounded" @click="toggleSortDir">
                {{ sortDir === 'asc' ? 'ASC' : 'DESC' }}
            </button>

            <!-- Dropdown Panel -->
            <div v-if="filtersOpen" class="absolute left-0 top-10 z-10 bg-white border rounded shadow p-3 w-72">
                <!-- Status Filter -->
                <div class="mb-2">
                    <div class="text-sm font-semibold mb-1">สถานะ</div>
                    <select v-model="filterStatus" class="w-full px-2 py-1 border rounded">
                        <option value="">ทั้งหมด ({{ statusCounts.all }})</option>
                        <option v-for="s in statuses" :key="s" :value="s">
                            {{ capitalize(s) }} ({{ statusCounts[s] || 0 }})
                        </option>
                    </select>
                </div>

                <!-- Filter Actions -->
                <div class="flex justify-between">
                    <button class="px-3 py-1 border rounded" @click="clearFilters">ล้างตัวกรอง</button>
                    <button class="px-3 py-1 bg-gray-900 text-white rounded"
                        @click="filtersOpen = false">เสร็จสิ้น</button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <table class="min-w-full bg-gray-50 border mt-6">
            <thead class="bg-gray-200 border-b">
                <tr>
                    <th class="border px-4 py-2">ไอดี</th>
                    <th class="border px-4 py-2">รหัสคําร้อง</th>
                    <th class="border px-4 py-2">รหัสผู้ใช้</th>
                    <th class="border px-4 py-2">รหัสอุปกรณ์</th>
                    <th class="border px-4 py-2">เริ่มวันที่</th>
                    <th class="border px-4 py-2">ถึงวันที่</th>
                    <th class="border px-4 py-2">สถานะ</th>
                    <th class="border px-4 py-2">สาเหตุการปฏิเสธ</th>
                    <th class="border px-4 py-2">สาเหตุการยกเลิก</th>
                    <th class="border px-4 py-2">วันที่เพิ่ม</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="req in filteredRequests" :key="req.id">
                    <td class="border px-4 py-2">{{ req.id }}</td>
                    <td class="border px-4 py-2">{{ req.req_id }}</td>
                    <td class="border px-4 py-2">{{ req.user_name }}</td>
                    <td class="border px-4 py-2">{{ req.equipment_name }}</td>
                    <td class="border px-4 py-2">{{ req.start_at }}</td>
                    <td class="border px-4 py-2">{{ req.end_at }}</td>
                    <td class="border px-4 py-2">{{ req.status }}</td>
                    <td class="border px-4 py-2">{{ req.reject_reason || 'ไม่มี' }}</td>
                    <td class="border px-4 py-2">{{ req.cancel_reason || 'ไม่มี' }}</td>
                    <td class="border px-4 py-2">{{ req.created_at || '—' }}</td>
                </tr>
                <tr v-if="filteredRequests.length === 0">
                    <td colspan="7" class="px-6 py-4 text-center text-gray-500">
                        ไม่พบข้อมูลคำขอ
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Export Button -->
        <button @click="exportRequests" class="btn btn-primary mt-4 bg-green-500 border">ดาวน์โหลดไฟล์ CSV</button>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'RequestReport',
    data() {
        return {
            searchQuery: "",
            filterStatus: "",
            sortKey: "created_at",
            sortDir: "asc",
            filtersOpen: false,
            isLoading: false,
            statuses: ["pending", "approved", "rejected", "check_out", "check_in", "cancelled"],
            requests: [],
        };
    },
    computed: {
        statusCounts() {
            const counts = { all: this.requests.length };
            for (const req of this.requests) {
                const rs = req.status || "unknown";
                counts[rs] = (counts[rs] || 0) + 1;
            }
            return counts;
        },
        filteredRequests() {
            const q = this.searchQuery.toLowerCase();
            const status = this.filterStatus;

            let list = this.requests.filter((req) => {
                const matchesSearch =
                    !q ||
                    req.req_id?.toLowerCase().includes(q) ||
                    req.equipment_name?.toLowerCase().includes(q) ||
                    req.user_name?.toLowerCase().includes(q) ||
                    req.status?.toLowerCase().includes(q) ||
                    req.reject_reason?.toLowerCase().includes(q) ||
                    req.cancel_reason?.toLowerCase().includes(q) ||
                    String(req.id).includes(q) ||
                    String(req.created_at || "").includes(q);

                const matchesStatus = !status || (req.status && req.status.toLowerCase() === status.toLowerCase());

                return matchesSearch && matchesStatus;
            });

            const dir = this.sortDir === "asc" ? 1 : -1;
            const key = this.sortKey;

            list = list.slice().sort((a, b) => {
                const av = a[key] ?? "";
                const bv = b[key] ?? "";
                const as = String(av).toLowerCase();
                const bs = String(bv).toLowerCase();
                if (as < bs) return -1 * dir;
                if (as > bs) return 1 * dir;
                return 0;
            });

            return list;
        }
    },
    methods: {

        async fetchRequests() {
            try {
                const response = await axios.get('/api/requests');
                this.requests = response.data;
            } catch (error) {
                console.error('Failed to fetch Requests:', error);
            }
        },
        exportRequests() {
            const params = new URLSearchParams({
                search: this.searchQuery,
                status: this.filterStatus,
                sort: this.sortKey,
                direction: this.sortDir
            });

            window.location.href = `/admin/report/export/requests?${params.toString()}`;
        },
        toggleSortDir() {
            this.sortDir = this.sortDir === "asc" ? "desc" : "asc";
        },
        clearFilters() {
            this.filterStatus = "";
            this.searchQuery = "";
        },
        capitalize(str) {
            return str ? str.charAt(0).toUpperCase() + str.slice(1) : "";
        }
    },
    watch: {
        searchQuery() {
            this.currentPage = 1;
        },
        filteredRequests() {
            if (this.currentPage > this.pageCount) this.currentPage = 1;
        }
    },
    mounted() {
        this.fetchRequests();
        this._onClickOutside = (e) => {
            const wrap = this.$refs.filtersWrap;
            if (!wrap) return;
            if (this.filtersOpen && !wrap.contains(e.target)) this.filtersOpen = false;
        };
        document.addEventListener("click", this._onClickOutside);
    },
    beforeUnmount() {
        document.removeEventListener("click", this._onClickOutside);
    }
};
</script>

<style scoped>
.btn {
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
}
</style>