<template>
    <div class="p-6 bg-white rounded-lg shadow">
        <h2 class="text-lg font-semibold mb-4">รายงานกิจกรรมระบบ</h2>

        <!-- Log Filter Form -->
        <form @submit.prevent="applyFilters" class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
            <input type="text" v-model="filters.admin" placeholder="ชื่อผู้ใช้"
                class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />

            <select v-model="filters.action"
                class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">การดำเนินการทั้งหมด</option>
                <option value="create">สร้าง</option>
                <option value="update">แก้ไข</option>
                <option value="delete">ลบ</option>
                <option value="login">เข้าสู่ระบบ</option>
                <option value="logout">ออกจากระบบ</option>
                <option value="approve">อนุมัติ</option>
                <option value="reject">ปฏิเสธ</option>
            </select>

            <select v-model="filters.module"
                class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">โมดูลทั้งหมด</option>
                <option value="equipment">อุปกรณ์</option>
                <option value="user_management">จัดการผู้ใช้</option>
                <option value="borrow_request">คำขอยืม</option>
                <option value="authentication">การยืนยันตัวตน</option>
                <option value="system">ระบบ</option>
            </select>

            <select v-model="filters.severity"
                class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">ระดับความสำคัญทั้งหมด</option>
                <option value="info">ข้อมูล</option>
                <option value="warning">คำเตือน</option>
                <option value="error">ข้อผิดพลาด</option>
                <option value="critical">วิกฤต</option>
            </select>

            <input type="date" v-model="filters.date_from" placeholder="วันที่เริ่มต้น"
                class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />

            <input type="date" v-model="filters.date_to" placeholder="วันที่สิ้นสุด"
                class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />

            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                กรอง
            </button>

            <button type="button" @click="clearFilters" class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                ล้าง
            </button>
        </form>

        <!-- Logs Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full text-sm border">
                <thead class="bg-gray-50 border-b">
                    <tr>
                        <th class="px-4 py-2 text-left">รหัส</th>
                        <th class="px-4 py-2 text-left">ผู้ใช้</th>
                        <th class="px-4 py-2 text-left">การดำเนินการ</th>
                        <th class="px-4 py-2 text-left">โมดูล</th>
                        <th class="px-4 py-2 text-left">เป้าหมาย</th>
                        <th class="px-4 py-2 text-left">ระดับความสำคัญ</th>
                        <th class="px-4 py-2 text-left">รายละเอียด</th>
                        <th class="px-4 py-2 text-left">IP Address</th>
                        <th class="px-4 py-2 text-left">วันที่</th>
                        <th class="px-4 py-2 text-left">การดำเนินการ</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="log in logs" :key="log.id" class="border-b hover:bg-gray-50">
                        <td class="px-4 py-2">{{ log.id }}</td>
                        <td class="px-4 py-2">{{ log.user?.name || 'N/A' }}</td>
                        <td class="px-4 py-2">
                            <span :class="getActionBadgeClass(log.action)" class="px-2 py-1 rounded-full text-xs">
                                {{ log.action || '-' }}
                            </span>
                        </td>
                        <td class="px-4 py-2">{{ log.module || '-' }}</td>
                        <td class="px-4 py-2">{{ log.target_name || '-' }}</td>
                        <td class="px-4 py-2">
                            <span :class="getSeverityBadgeClass(log.severity)" class="px-2 py-1 rounded-full text-xs">
                                {{ log.severity || 'info' }}
                            </span>
                        </td>
                        <td class="px-4 py-2 max-w-xs truncate" :title="log.description">
                            {{ log.description || log.formatted_description || '-' }}
                        </td>
                        <td class="px-4 py-2">{{ log.ip_address || '-' }}</td>
                        <td class="px-4 py-2">{{ formatDate(log.created_at) }}</td>
                        <td class="px-4 py-2">
                            <button @click="showLogDetails(log)" 
                                class="px-2 py-1 bg-blue-500 text-white text-xs rounded hover:bg-blue-600">
                                รายละเอียด
                            </button>
                        </td>
                    </tr>
                    <tr v-if="logs.length === 0">
                        <td colspan="10" class="text-center py-4 text-gray-500">
                            ไม่พบข้อมูล
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

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

    <!-- Log Details Modal -->
    <div v-if="selectedLog" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white rounded-lg p-6 max-w-4xl w-full mx-4 max-h-[90vh] overflow-y-auto">
            <div class="flex justify-between items-center mb-4">
                <h3 class="text-lg font-semibold">รายละเอียดบันทึก</h3>
                <button @click="selectedLog = null" class="text-gray-500 hover:text-gray-700">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700">รหัส</label>
                    <p class="mt-1 text-sm text-gray-900">{{ selectedLog.id }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">ผู้ใช้</label>
                    <p class="mt-1 text-sm text-gray-900">{{ selectedLog.user?.name || 'N/A' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">การดำเนินการ</label>
                    <span :class="getActionBadgeClass(selectedLog.action)" class="inline-block px-2 py-1 rounded-full text-xs">
                        {{ selectedLog.action || '-' }}
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">โมดูล</label>
                    <p class="mt-1 text-sm text-gray-900">{{ selectedLog.module || '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">ประเภทเป้าหมาย</label>
                    <p class="mt-1 text-sm text-gray-900">{{ selectedLog.target_type || '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">ชื่อเป้าหมาย</label>
                    <p class="mt-1 text-sm text-gray-900">{{ selectedLog.target_name || '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">ระดับความสำคัญ</label>
                    <span :class="getSeverityBadgeClass(selectedLog.severity)" class="inline-block px-2 py-1 rounded-full text-xs">
                        {{ selectedLog.severity || 'info' }}
                    </span>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">IP Address</label>
                    <p class="mt-1 text-sm text-gray-900">{{ selectedLog.ip_address || '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">User Agent</label>
                    <p class="mt-1 text-sm text-gray-900 break-all">{{ selectedLog.user_agent || '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Session ID</label>
                    <p class="mt-1 text-sm text-gray-900">{{ selectedLog.session_id || '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Request Method</label>
                    <p class="mt-1 text-sm text-gray-900">{{ selectedLog.request_method || '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">Request URL</label>
                    <p class="mt-1 text-sm text-gray-900 break-all">{{ selectedLog.request_url || '-' }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-gray-700">วันที่สร้าง</label>
                    <p class="mt-1 text-sm text-gray-900">{{ formatDate(selectedLog.created_at) }}</p>
                </div>
            </div>

            <div class="mt-4">
                <label class="block text-sm font-medium text-gray-700">รายละเอียด</label>
                <p class="mt-1 text-sm text-gray-900">{{ selectedLog.description || selectedLog.formatted_description || '-' }}</p>
            </div>

            <div v-if="selectedLog.old_values" class="mt-4">
                <label class="block text-sm font-medium text-gray-700">ค่าก่อนหน้า</label>
                <pre class="mt-1 text-sm text-gray-900 bg-gray-100 p-2 rounded">{{ JSON.stringify(selectedLog.old_values, null, 2) }}</pre>
            </div>

            <div v-if="selectedLog.new_values" class="mt-4">
                <label class="block text-sm font-medium text-gray-700">ค่าใหม่</label>
                <pre class="mt-1 text-sm text-gray-900 bg-gray-100 p-2 rounded">{{ JSON.stringify(selectedLog.new_values, null, 2) }}</pre>
            </div>

            <div v-if="selectedLog.request_data" class="mt-4">
                <label class="block text-sm font-medium text-gray-700">ข้อมูลคำขอ</label>
                <pre class="mt-1 text-sm text-gray-900 bg-gray-100 p-2 rounded">{{ JSON.stringify(selectedLog.request_data, null, 2) }}</pre>
            </div>

            <div v-if="selectedLog.notes" class="mt-4">
                <label class="block text-sm font-medium text-gray-700">หมายเหตุ</label>
                <p class="mt-1 text-sm text-gray-900">{{ selectedLog.notes }}</p>
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
            selectedLog: null,
            filters: {
                admin: "",
                action: "",
                module: "",
                severity: "",
                target_type: "",
                date_from: "",
                date_to: "",
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
        clearFilters() {
            this.filters = {
                admin: "",
                action: "",
                module: "",
                severity: "",
                target_type: "",
                date_from: "",
                date_to: "",
            };
            this.fetchLogs(1);
        },
        getActionBadgeClass(action) {
            const classes = {
                'create': 'bg-green-100 text-green-800',
                'update': 'bg-blue-100 text-blue-800',
                'delete': 'bg-red-100 text-red-800',
                'login': 'bg-green-100 text-green-800',
                'logout': 'bg-gray-100 text-gray-800',
                'approve': 'bg-green-100 text-green-800',
                'reject': 'bg-red-100 text-red-800',
                'cancel': 'bg-yellow-100 text-yellow-800',
            };
            return classes[action] || 'bg-gray-100 text-gray-800';
        },
        getSeverityBadgeClass(severity) {
            const classes = {
                'critical': 'bg-red-100 text-red-800',
                'error': 'bg-red-100 text-red-800',
                'warning': 'bg-yellow-100 text-yellow-800',
                'info': 'bg-blue-100 text-blue-800',
                'success': 'bg-green-100 text-green-800',
            };
            return classes[severity] || 'bg-gray-100 text-gray-800';
        },
        showLogDetails(log) {
            this.selectedLog = log;
        },
        changePage(page) {
            if (page >= 1 && page <= this.totalPages) {
                this.fetchLogs(page);
            }
        },
        exportLogs() {
            const query = new URLSearchParams({
                admin: this.filters.admin,
                action: this.filters.action
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
