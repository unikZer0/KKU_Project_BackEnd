<template>
    <div class="p-4 bg-white rounded-lg border">

        <!-- Breadcrumb -->
        <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
            <a href="/admin" class="hover:text-gray-700">แดชบอร์ด</a>
            <span>/</span>
            <a href="/admin/report" class="hover:text-gray-700">รายงาน</a>
            <span>></span>
            <span class="font-semibold text-gray-900">รายงานผู้ใช้</span>
        </nav>

        <!-- title -->
        <h2 class="text-xl font-bold mb-4">รายงานผู้ใช้</h2>

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
                    {{ filterRole || 'ทั้งหมด' }} ·
                </span>
            </button>

            <button class="px-3 py-1 border rounded" @click="toggleSortDir">
                {{ sortDir === 'asc' ? 'ASC' : 'DESC' }}
            </button>

            <!-- Dropdown Panel -->
            <div v-if="filtersOpen" class="absolute left-0 top-10 z-10 bg-white border rounded shadow p-3 w-72">
                <!-- Status Filter -->
                <div class="mb-2">
                    <div class="text-sm font-semibold mb-1">ตำแหน่ง</div>
                    <select v-model="filterRoles" class="w-full px-2 py-1 border rounded">
                        <option value="">ทั้งหมด ({{ roleCounts.all }})</option>
                        <option v-for="r in roles" :key="r" :value="r">
                            {{ capitalize(r) }} ({{ roleCounts[r] || 0 }})
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
                    <th class="border px-4 py-2">รหัสผู้ใช้</th>
                    <th class="border px-4 py-2">ชื่อ</th>
                    <th class="border px-4 py-2">อีเมล</th>
                    <th class="border px-4 py-2">เบอร์โทรศัพท์</th>
                    <th class="border px-4 py-2">ตำแหน่ง</th>
                    <th class="border px-4 py-2">วันที่เพิ่ม</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="u in filteredUsers" :key="u.id">
                    <td class="border px-4 py-2">{{ u.id }}</td>
                    <td class="border px-4 py-2">{{ u.uid }}</td>
                    <td class="border px-4 py-2">{{ u.name }}</td>
                    <td class="border px-4 py-2">{{ u.email }}</td>
                    <td class="border px-4 py-2">{{ u.phonenumber }}</td>
                    <td class="border px-4 py-2">{{ u.role }}</td>
                    <td class="border px-4 py-2">{{ u.created_at || '—' }}</td>
                </tr>
                <tr v-if="filteredUsers.length === 0">
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        ไม่พบข้อมูลผู้ใช้
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Export Button -->
        <button @click="exportUser" class="btn btn-primary mt-4 bg-green-500 border">ดาวน์โหลดไฟล์ CSV</button>
    </div>
</template>

<script>
import axios from 'axios';

export default {
    name: 'UserReport',
    data() {
        return {
            searchQuery: "",
            filterRoles: "",
            sortKey: "name",
            sortDir: "asc",
            filtersOpen: false,
            roles: ["borrower", "staff", "admin"],
            user: [],
        };
    },
    computed: {
        roleCounts() {
            const counts = { all: this.user.length };
            for (const u of this.user) {
                const role = u.role || "unknown";
                counts[role] = (counts[role] || 0) + 1;
            }
            return counts;
        },
        filteredUsers() {
            const q = this.searchQuery.toLowerCase();
            const role = this.filterRoles;

            let list = this.user.filter((u) => {
                const matchesSearch =
                    !q ||
                    u.name.toLowerCase().includes(q) ||
                    u.email.toLowerCase().includes(q) ||
                    u.phonenumber?.toLowerCase().includes(q) ||
                    u.role?.toLowerCase().includes(q) ||
                    String(u.uid).includes(q);

                const matchesRole = !role || u.role === role;

                return matchesSearch && matchesRole;
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
        async fetchUsers() {
            try {
                const response = await axios.get('/api/users');
                this.user = response.data;
            } catch (error) {
                console.error('Failed to fetch users:', error);
            }
        },
        exportUser() {
            const params = new URLSearchParams({
                search: this.searchQuery,
                role: this.filterRoles,
                sort: this.sortKey,
                direction: this.sortDir
            });

            window.location.href = `/admin/report/export/users?${params.toString()}`;
        },
        toggleSortDir() {
            this.sortDir = this.sortDir === "asc" ? "desc" : "asc";
        },
        clearFilters() {
            this.filterRoles = "";
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
        filteredUsers() {
            if (this.currentPage > this.pageCount) this.currentPage = 1;
        }
    },
    mounted() {
        this.fetchUsers();
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