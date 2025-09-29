<template>
    <div class="p-4 bg-white rounded-lg border">

        <!-- Breadcrumb -->
        <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
            <a href="/admin" class="hover:text-gray-700">แดชบอร์ด</a>
            <span>/</span>
            <a href="/admin/report" class="hover:text-gray-700">รายงาน</a>
            <span>></span>
            <span class="font-semibold text-gray-900">รายงานอุปกรณ์</span>
        </nav>

        <!-- title -->
        <h2 class="text-xl font-bold mb-4">รีพอร์ตอุปกรณ์</h2>

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
        <div class="flex flex-wrap gap-2 items-center relative" ref="filtersWrap">
            <button @click="filtersOpen = !filtersOpen" class="px-3 py-1 border rounded">
                ตัวกรอง
                <span class="text-xs text-gray-500 ml-1">
                    {{ filterStatus || 'all' }} ·
                    {{filterCategoryId
                        ? (categories.find(c => String(c.id) === String(filterCategoryId))?.name || 'category')
                        : 'all categories'}}
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

                <!-- Category Filter -->
                <div class="mb-3">
                    <div class="text-sm font-semibold mb-1">หมวดหมู่</div>
                    <select v-model="filterCategoryId" class="w-full px-2 py-1 border rounded">
                        <option value="">ทุกหมวดหมู่ ({{ categoryCounts.all }})</option>
                        <option v-if="categories.length === 0" disabled>ไม่มีหมวดหมู่</option>
                        <option v-for="c in categories" :key="c.id" :value="String(c.id)">
                            {{ c.name }} ({{ categoryCounts[String(c.id)] || 0 }})
                        </option>
                    </select>
                </div>

                <!-- Filter Actions -->
                <div class="flex justify-between">
                    <button class="px-3 py-1 border rounded" @click="clearFilters">ล้างตัวกรอง</button>
                    <button class="px-3 py-1 bg-blue-500 text-white rounded"
                        @click="filtersOpen = false">เสร็จสิ้น</button>
                </div>
            </div>
        </div>

        <!-- Table -->
        <table class="min-w-full bg-gray-50 border mt-6">
            <thead class="bg-gray-200 border-b">
                <tr>
                    <th class="border px-4 py-2">ไอดี</th>
                    <th class="border px-4 py-2">หมายเลขครุภัณฑ์</th>
                    <th class="border px-4 py-2">ชื่ออุปกรณ์</th>
                    <th class="border px-4 py-2">หมวดหมู่</th>
                    <th class="border px-4 py-2">สถานะ</th>
                    <th class="border px-4 py-2">วันที่เพิ่ม</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="eq in filteredEquipments" :key="eq.id">
                    <td class="border px-4 py-2">{{ eq.id }}</td>
                    <td class="border px-4 py-2">{{ eq.code }}</td>
                    <td class="border px-4 py-2">{{ eq.name }}</td>
                    <td class="border px-4 py-2">{{ eq.category?.name || 'NA' }}</td>
                    <td class="border px-4 py-2">{{ capitalize(eq.status) }}</td>
                    <td class="border px-4 py-2">{{ eq.created_at || '—' }}</td>
                </tr>
                <tr v-if="filteredEquipments.length === 0">
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        ไม่พบข้อมูลอุปกรณ์
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Export Button -->
        <button @click="exportEquipments" class="btn btn-primary mt-4 bg-green-500 border">ดาวน์โหลดไฟล์ CSV</button>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'EquipmentReport',
    data() {
        return {
            searchQuery: "",
            filterStatus: "",
            filterCategoryId: "",
            sortKey: "name",
            sortDir: "asc",
            filtersOpen: false,
            statuses: ["available", "retired", "maintenance"],
            categories: [],
            equipments: [],
        };
    },
    computed: {
        statusCounts() {
            const counts = { all: this.equipments.length };
            for (const e of this.equipments) {
                const k = e.status || "unknown";
                counts[k] = (counts[k] || 0) + 1;
            }
            return counts;
        },
        categoryCounts() {
            const counts = { all: this.equipments.length };
            for (const e of this.equipments) {
                const id = String(e.categories_id || e.category?.id || "");
                if (!id) continue;
                counts[id] = (counts[id] || 0) + 1;
            }
            return counts;
        },
        filteredEquipments() {
            const q = (this.searchQuery || "").toLowerCase();
            const catId = this.filterCategoryId ? String(this.filterCategoryId) : "";
            const status = this.filterStatus;

            let list = this.equipments.filter((e) => {
                const matchesSearch =
                    !q ||
                    e.name.toLowerCase().includes(q) ||
                    (e.category?.name || "").toLowerCase().includes(q) ||
                    e.status.toLowerCase().includes(q) ||
                    String(e.code).includes(q);

                const matchesStatus = !status || e.status === status;
                const matchesCategory = !catId || String(e.categories_id || e.category?.id) === catId;

                return matchesSearch && matchesStatus && matchesCategory;
            });

            const dir = this.sortDir === "asc" ? 1 : -1;
            const key = this.sortKey;

            list = list.slice().sort((a, b) => {
                const av = key === "category" ? a.category?.name || "" : a[key] ?? "";
                const bv = key === "category" ? b.category?.name || "" : b[key] ?? "";

                if (typeof av === "number" && typeof bv === "number") return (av - bv) * dir;

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
        async fetchEquipments() {
            try {
                const response = await axios.get('/api/equipments');
                this.equipments = response.data;
            } catch (error) {
                console.error('Failed to fetch equipments:', error);
            }
        },
        async fetchCategories() {
            try {
                const response = await axios.get('/api/categories');
                this.categories = response.data;
            } catch (error) {
                console.error('Failed to fetch categories:', error);
            }
        },
        exportEquipments() {
            const params = new URLSearchParams({
                search: this.searchQuery,
                category_id: this.filterCategoryId,
                status: this.filterStatus,
                sort: this.sortKey,
                direction: this.sortDir
            });

            window.location.href = `/admin/report/export/equipments?${params.toString()}`;
        },
        toggleSortDir() {
            this.sortDir = this.sortDir === "asc" ? "desc" : "asc";
        },
        clearFilters() {
            this.filterStatus = "";
            this.filterCategoryId = "";
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
        filteredEquipments() {
            if (this.currentPage > this.pageCount) this.currentPage = 1;
        }
    },
    mounted() {
        this.fetchEquipments();
        this.fetchCategories();
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
}
</script>

<style scoped>
.btn {
    padding: 0.5rem 1rem;
    border-radius: 0.375rem;
}
</style>