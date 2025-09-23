<template>
    <div class="p-6 bg-white rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-4">Activity Logs</h2>

        <div class="p-6 bg-white rounded-lg shadow">
            <h2 class="text-lg font-semibold mb-4">Admin Logs</h2>

            <!-- Log Filter Form -->
            <form @submit.prevent="applyFilters" class="flex space-x-4 mb-4">
                <input type="text" v-model="filters.admin" placeholder="Admin Name"
                    class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />

                <select v-model="filters.action"
                    class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Actions</option>
                    <option value="create">Create</option>
                    <option value="update">Update</option>
                    <option value="delete">Delete</option>
                </select>

                <select v-model="filters.target_type"
                    class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">All Types</option>
                    <option value="equipment">Equipment</option>
                    <option value="category">Category</option>
                    <option value="user">User</option>
                </select>

                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                    Filter
                </button>
            </form>

            <!-- Logs Table -->
            <table class="min-w-full text-sm border">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-2 text-left">ไอดี</th>
                        <th class="px-4 py-2 text-left">ชื่อผู้จัดการ</th>
                        <th class="px-4 py-2 text-left">แอคชั่น</th>
                        <th class="px-4 py-2 text-left">ประเภทของแอคชั่น</th>
                        <th class="px-4 py-2 text-left">เป้าหมาย</th>
                        <th class="px-4 py-2 text-left">รายละเอียด</th>
                        <th class="px-4 py-2 text-left">สร้างวันที่</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="log in logs" :key="log.id" class="border-b">
                        <td class="px-4 py-2">{{ log.id }}</td>
                        <td class="px-4 py-2">{{ log.admin?.name || 'N/A' }}</td>
                        <td class="px-4 py-2">{{ log.action }}</td>
                        <td class="px-4 py-2">{{ log.target_type }}</td>
                        <td class="px-4 py-2">{{ log.target_id }}</td>
                        <td class="px-4 py-2">{{ log.description }}</td>
                        <td class="px-4 py-2">{{ formatDate(log.created_at) }}</td>
                    </tr>
                    <tr v-if="logs.length === 0">
                        <td colspan="7" class="text-center py-4 text-gray-500">
                            ไม่พบข้อมูล
                        </td>
                    </tr>
                </tbody>
            </table>

            <button @click="exportLogs" class="mt-4 px-4 py-2 bg-green-500 text-white rounded hover:bg-green-600">
                ดาวน์โหลดไฟล์ CSV
            </button>

            <!-- Pagination -->
            <div class="mt-4 flex space-x-2">
                <button class="px-3 py-1 border rounded" :disabled="currentPage === 1"
                    @click="changePage(currentPage - 1)">
                    ก่อนหน้า
                </button>
                <button v-for="p in totalPages" :key="p" @click="changePage(p)" class="px-3 py-1 border rounded"
                    :class="{ 'bg-blue-600 text-white': currentPage === p }">
                    {{ p }}
                </button>
                <button class="px-3 py-1 border rounded" :disabled="currentPage === totalPages"
                    @click="changePage(currentPage + 1)">
                    ถัดไป
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "ActivityLogs",
    data() {
        return {
            logs: [],
            currentPage: 1,
            totalPages: 1,
            filters: {
                admin: "",
                action: "",
                target_type: "",
            },
        };
    },
    methods: {
        async fetchLogs(page = 1) {
            const params = new URLSearchParams({
                ...this.filters,
                page,
            }).toString();

            const res = await fetch(`/api/logs?${params}`);
            const data = await res.json();

            this.logs = data.data || [];
            this.currentPage = data.current_page || 1;
            this.totalPages = data.last_page || 1;
        },
        applyFilters() {
            this.fetchLogs(1);
        },
        changePage(page) {
            if (page >= 1 && page <= this.totalPages) {
                this.fetchLogs(page);
            }
        },
        exportLogs() {
            const query = new URLSearchParams({
                admin: this.filters.admin,
                action: this.filters.action,
                target_type: this.filters.target_type
            }).toString();

            window.location.href = `/admin/report/export/log?${query}`;
        },
        formatDate(date) {
            return new Date(date).toLocaleString("th-TH", {
                year: "numeric",
                month: "2-digit",
                day: "2-digit",
                hour: "2-digit",
                minute: "2-digit",
            });
        },
    },
    mounted() {
        this.fetchLogs();
    },
};
</script>
