<template>
    <div class="p-4 bg-white rounded-lg border">

        <!-- Breadcrumb -->
        <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
            <a href="/admin" class="hover:text-gray-700">แดชบอร์ด</a>
            <span>/</span>
            <a href="/admin/report" class="hover:text-gray-700">รายงาน</a>
            <span>></span>
            <span class="font-semibold text-gray-900">รายงานหมวดหมู่</span>
        </nav>

        <!-- title -->
        <h2 class="text-xl font-bold mb-4">รีพอร์ตหมวดหมู่อุปกรณ์</h2>

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
            <button class="px-3 py-1 border rounded" @click="toggleSortDir">
                {{ sortDir === 'asc' ? 'ASC' : 'DESC' }}
            </button>
        </div>

        <!-- Table -->
        <table class="min-w-full bg-gray-50 border mt-6">
            <thead class="bg-gray-200 border-b">
                <tr>
                    <th class="border px-4 py-2">ไอดี</th>
                    <th class="border px-4 py-2">รหัสหมวดหมู่</th>
                    <th class="border px-4 py-2">ชื่อหมวดหมู่</th>
                    <th class="border px-4 py-2">จำนวนอุปกรณ์</th>
                    <th class="border px-4 py-2">วันที่เพิ่ม</th>
                    <th class="border px-4 py-2">วันที่อัพเดทล่าสุด</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="cate in filteredCategories" :key="cate.id">
                    <td class="border px-4 py-2">{{ cate.id }}</td>
                    <td class="border px-4 py-2">{{ cate.cate_id }}</td>
                    <td class="border px-4 py-2">{{ cate.name }}</td>
                    <td class="border px-4 py-2">{{ cate.equipments_count ?? 0 }}</td>
                    <td class="border px-4 py-2">{{ cate.created_at || '—' }}</td>
                    <td class="border px-4 py-2">{{ cate.updated_at || '—' }}</td>
                </tr>
            </tbody>
        </table>

        <!-- Export Button -->
        <button @click="exportCategories" class="btn btn-primary mt-4 bg-green-500 border">ดาวน์โหลดไฟล์ CSV</button>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'CategoryReport',
    data() {
        return {
            searchQuery: "",
            sortKey: "name",
            sortDir: "asc",
            filtersOpen: false,
            categories: [],
        };
    },
    computed: {
        filteredCategories() {
            const q = (this.searchQuery || "").toLowerCase();
            let list = this.categories.filter((c) => {
                return (
                    !q ||
                    c.name.toLowerCase().includes(q) ||
                    c.code.toLowerCase().includes(q)
                );
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
        async fetchCategories() {
            try {
                const response = await axios.get('/api/categories');
                this.categories = response.data;
            } catch (error) {
                console.error('Failed to fetch categories:', error);
            }
        },
        exportCategories() {
            const sortMap = {
                cate_id: "code", // map frontend key to backend column
                name: "name"
            };

            const params = new URLSearchParams({
                search: this.searchQuery,
                sort: sortMap[this.sortKey] || "name",
                direction: this.sortDir
            });

            window.location.href = `/admin/report/export/categories?${params.toString()}`;
        },
        toggleSortDir() {
            this.sortDir = this.sortDir === "asc" ? "desc" : "asc";
        },
        clearFilters() {
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