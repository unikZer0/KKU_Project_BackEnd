<template>
    <!-- Breadcrumb -->
    <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
        <a href="/admin" class="hover:text-gray-700">แดชบอร์ด</a>
        <span>/</span>
        <span class="font-semibold text-gray-900">หน้าจัดการรายการอุปกรณ์</span>
    </nav>

    <div class="bg-white p-6 rounded-lg shadow">
        <div class="relative mb-4">
            <input type="text" v-model="searchQuery" placeholder="ค้นหา..."
                class="pl-10 pr-3 py-2 text-sm border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-full" />
            <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
            </svg>
        </div>

        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-4">
            <h2 class="text-lg font-semibold">
                รายการอุปกรณ์ทั้งหมด: {{ filteredEquipmentItems.length }} รายการ
            </h2>
            <div class="flex flex-wrap gap-2 items-center relative" ref="filtersWrap">
                <button @click="filtersOpen = !filtersOpen" class="px-3 py-1 border rounded">
                    ตัวกรอง
                    <span class="text-xs text-gray-500 ml-1">
                        {{ filterStatus ? filterStatus : "all" }} ·
                        {{ filterCondition ? filterCondition : "all conditions" }} ·
                        {{ filterEquipmentId ? getEquipmentName(filterEquipmentId) : "all equipments" }}
                    </span>
                </button>

                <button v-if="userRole === 'admin'" @click="openCreateModal"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    เพิ่มรายการใหม่
                </button>

                <button v-if="selectedItems.length > 0" @click="openBulkUpdateModal"
                    class="bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
                    อัปเดตหลายรายการ ({{ selectedItems.length }})
                </button>

                <!-- Dropdown panel -->
                <div v-if="filtersOpen" class="absolute right-0 top-10 z-10 bg-white border rounded shadow p-3 w-72">
                    <div class="mb-2">
                        <div class="text-sm font-semibold mb-1">สถานะ</div>
                        <select v-model="filterStatus" class="w-full px-2 py-1 border rounded">
                            <option value="">ทั้งหมด ({{ statusCounts.all }})</option>
                            <option v-for="s in statuses" :key="s" :value="s">
                                {{ capitalize(s) }} ({{ statusCounts[s] || 0 }})
                            </option>
                        </select>
                    </div>
                    <div class="mb-2">
                        <div class="text-sm font-semibold mb-1">สภาพ</div>
                        <select v-model="filterCondition" class="w-full px-2 py-1 border rounded">
                            <option value="">ทั้งหมด ({{ conditionCounts.all }})</option>
                            <option v-for="c in conditions" :key="c" :value="c">
                                {{ c }} ({{ conditionCounts[c] || 0 }})
                            </option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="text-sm font-semibold mb-1">อุปกรณ์</div>
                        <select v-model="filterEquipmentId" class="w-full px-2 py-1 border rounded">
                            <option value="">ทุกอุปกรณ์ ({{ equipmentCounts.all }})</option>
                            <option v-for="e in equipments" :key="e.id" :value="String(e.id)">
                                {{ e.name }} ({{ equipmentCounts[String(e.id)] || 0 }})
                            </option>
                        </select>
                    </div>
                    <div class="flex justify-between">
                        <button class="px-3 py-1 border rounded" @click="clearFilters">
                            ล้างตัวกรอง
                        </button>
                        <button class="px-3 py-1 bg-gray-900 text-white rounded" @click="filtersOpen = false">
                            เสร็จสิ้น
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-2 py-2">
                        <input type="checkbox" 
                               @change="toggleAllSelection" 
                               :checked="allItemsSelected"
                               class="rounded">
                    </th>
                    <th class="px-4 py-2 text-left cursor-pointer" @click="setSort('id')">
                        ID
                        <span v-if="sortKey === 'id'">{{ sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    </th>
                    <th class="px-4 py-2 text-left cursor-pointer" @click="setSort('equipment.name')">
                        อุปกรณ์
                        <span v-if="sortKey === 'equipment.name'">{{ sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    </th>
                    <th class="px-4 py-2 text-left cursor-pointer" @click="setSort('serial_number')">
                        หมายเลขซีเรียล
                        <span v-if="sortKey === 'serial_number'">{{ sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    </th>
                    <th class="px-4 py-2 text-left cursor-pointer" @click="setSort('condition')">
                        สภาพ
                        <span v-if="sortKey === 'condition'">{{ sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    </th>
                    <th class="px-4 py-2 text-left cursor-pointer" @click="setSort('status')">
                        สถานะ
                        <span v-if="sortKey === 'status'">{{ sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    </th>
                    <th class="px-4 py-2 text-left">หมวดหมู่</th>
                    <th class="px-4 py-2 text-left">จำนวนอุปกรณ์เสริม</th>
                    <th class="px-4 py-2 text-left">วันที่สร้าง</th>
                    <th class="px-4 py-2 text-left" v-if="userRole === 'admin'">แอคชั่น</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="item in paginatedEquipmentItems" :key="item.id" class="border-b hover:bg-gray-50"
                    :class="{ 'bg-blue-50': selectedItems.includes(item.id) }">
                    <td class="px-2 py-2">
                        <input type="checkbox" 
                               :value="item.id"
                               v-model="selectedItems"
                               class="rounded">
                    </td>
                    <td class="px-4 py-2 font-mono text-xs">{{ item.id }}</td>
                    <td class="px-4 py-2">
                        <div class="font-medium">{{ item.equipment?.name || 'N/A' }}</div>
                        <div class="text-xs text-gray-500">{{ item.equipment?.code || '' }}</div>
                    </td>
                    <td class="px-4 py-2 font-mono text-xs">
                        {{ item.serial_number || 'N/A' }}
                    </td>
                    <td class="px-4 py-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                              :class="getConditionClass(item.condition)">
                            {{ item.condition }}
                        </span>
                    </td>
                    <td class="px-4 py-2">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                              :class="getStatusClass(item.status)">
                            {{ capitalize(item.status) }}
                        </span>
                    </td>
                    <td class="px-4 py-2">
                        {{ item.equipment?.category?.name || 'N/A' }}
                    </td>
                    <td class="px-4 py-2 text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                            {{ item.accessories?.length || 0 }} ชิ้น
                        </span>
                    </td>
                    <td class="px-4 py-2 text-xs text-gray-500">
                        {{ formatDate(item.created_at) }}
                    </td>
                    <td class="px-4 py-2 space-x-2">
                        <button v-if="userRole === 'admin'" @click="openEditModal(item)"
                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-xs">
                            แก้ไข
                        </button>
                        <button v-if="userRole === 'admin'" @click="deleteEquipmentItem(item)"
                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs">
                            ลบ
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="mt-4 flex items-center justify-between">
            <div class="text-sm text-gray-600">
                แสดง {{ pageStart + 1 }} - {{ pageEnd }} จากทั้งหมด {{ filteredEquipmentItems.length }} รายการ
            </div>
            <div class="flex items-center space-x-1">
                <button class="px-3 py-1 border rounded disabled:opacity-50" :disabled="currentPage === 1"
                    @click="prevPage">
                    ก่อนหน้า
                </button>
                <button v-for="p in pageCount" :key="p" class="px-3 py-1 border rounded"
                    :class="{ 'bg-blue-600 text-white': currentPage === p }" @click="goToPage(p)">
                    {{ p }}
                </button>
                <button class="px-3 py-1 border rounded disabled:opacity-50"
                    :disabled="currentPage === pageCount || pageCount === 0" @click="nextPage">
                    ถัดไป
                </button>
            </div>
        </div>

        <!-- Modals -->
        <EquipmentItemEditModal 
            :isOpen="editModal.isOpen" 
            :item="editModal.selectedItem" 
            :equipments="equipments"
            @cancel="closeEditModal" 
            @save="updateEquipmentItem" />

        <EquipmentItemCreateModal 
            :isOpen="createModal.isOpen" 
            :equipments="equipments"
            @cancel="closeCreateModal" 
            @create="createEquipmentItem" />

        <BulkUpdateModal 
            :isOpen="bulkUpdateModal.isOpen" 
            :selectedCount="selectedItems.length"
            @cancel="closeBulkUpdateModal" 
            @update="bulkUpdateItems" />
    </div>
</template>

<script>
import EquipmentItemEditModal from "../modals/EquipmentItemEditModal.vue";
import EquipmentItemCreateModal from "../modals/EquipmentItemCreateModal.vue";
import BulkUpdateModal from "../modals/BulkUpdateModal.vue";

export default {
    name: "EquipmentItemsTable",
    components: {
        EquipmentItemEditModal,
        EquipmentItemCreateModal,
        BulkUpdateModal,
    },
    data() {
        const el = document.getElementById("equipment-items-table");
        return {
            userRole: el?.dataset?.role || "",
            equipmentItems: JSON.parse(el?.dataset?.equipmentItems || "[]"),
            equipments: JSON.parse(el?.dataset?.equipments || "[]"),
            categories: JSON.parse(el?.dataset?.categories || "[]"),
            sortKey: "created_at",
            sortDirection: "desc",
            statuses: ["available", "unavailable", "maintenance", "retired"],
            conditions: ["Good", "Fair", "Poor"],
            searchQuery: "",
            currentPage: 1,
            pageSize: 15,
            filtersOpen: false,
            filterStatus: "",
            filterCondition: "",
            filterEquipmentId: "",
            selectedItems: [],
            
            editModal: {
                isOpen: false,
                selectedItem: {},
            },
            createModal: {
                isOpen: false,
            },
            bulkUpdateModal: {
                isOpen: false,
            },
        };
    },
    computed: {
        statusCounts() {
            const counts = { all: (this.equipmentItems || []).length };
            for (const item of this.equipmentItems || []) {
                const k = item?.status || "unknown";
                counts[k] = (counts[k] || 0) + 1;
            }
            return counts;
        },
        conditionCounts() {
            const counts = { all: (this.equipmentItems || []).length };
            for (const item of this.equipmentItems || []) {
                const k = item?.condition || "unknown";
                counts[k] = (counts[k] || 0) + 1;
            }
            return counts;
        },
        equipmentCounts() {
            const counts = { all: (this.equipmentItems || []).length };
            for (const item of this.equipmentItems || []) {
                const id = String(item?.equipment_id || "");
                if (!id) continue;
                counts[id] = (counts[id] || 0) + 1;
            }
            return counts;
        },
        filteredEquipmentItems() {
            const q = (this.searchQuery || "").toLowerCase();
            const status = this.filterStatus;
            const condition = this.filterCondition;
            const equipmentId = this.filterEquipmentId ? String(this.filterEquipmentId) : "";
            
            let list = (this.equipmentItems || []).filter((item) => {
                const matchesSearch = !q ||
                    String(item?.serial_number || "").toLowerCase().includes(q) ||
                    String(item?.equipment?.name || "").toLowerCase().includes(q) ||
                    String(item?.equipment?.code || "").toLowerCase().includes(q) ||
                    String(item?.equipment?.brand || "").toLowerCase().includes(q) ||
                    String(item?.equipment?.model || "").toLowerCase().includes(q) ||
                    String(item?.equipment?.category?.name || "").toLowerCase().includes(q);
                
                const matchesStatus = !status || String(item?.status || "") === status;
                const matchesCondition = !condition || String(item?.condition || "") === condition;
                const matchesEquipment = !equipmentId || String(item?.equipment_id || "") === equipmentId;
                
                return matchesSearch && matchesStatus && matchesCondition && matchesEquipment;
            });
            
            list.sort((a, b) => {
                let x, y;
                
                if (this.sortKey.includes('.')) {
                    const keys = this.sortKey.split('.');
                    x = keys.reduce((obj, key) => obj?.[key], a) ?? "";
                    y = keys.reduce((obj, key) => obj?.[key], b) ?? "";
                } else {
                    x = a[this.sortKey] ?? "";
                    y = b[this.sortKey] ?? "";
                }

                if (this.sortKey === "created_at" || this.sortKey === "updated_at") {
                    x = new Date(x);
                    y = new Date(y);
                } else {
                    x = String(x).toLowerCase();
                    y = String(y).toLowerCase();
                }
                
                if (x < y) return this.sortDirection === "asc" ? -1 : 1;
                if (x > y) return this.sortDirection === "asc" ? 1 : -1;
                return 0;
            });
            
            return list;
        },
        pageCount() {
            return Math.ceil(this.filteredEquipmentItems.length / this.pageSize) || 0;
        },
        pageStart() {
            return (this.currentPage - 1) * this.pageSize;
        },
        pageEnd() {
            const end = this.pageStart + this.pageSize;
            return Math.min(end, this.filteredEquipmentItems.length);
        },
        paginatedEquipmentItems() {
            return this.filteredEquipmentItems.slice(this.pageStart, this.pageEnd);
        },
        allItemsSelected() {
            return this.paginatedEquipmentItems.length > 0 && 
                   this.paginatedEquipmentItems.every(item => this.selectedItems.includes(item.id));
        }
    },
    methods: {
        capitalize(str) {
            if (!str) return "";
            return str.charAt(0).toUpperCase() + str.slice(1);
        },
        getStatusClass(status) {
            switch (status) {
                case "available":
                    return "bg-green-100 text-green-800";
                case "unavailable":
                    return "bg-red-100 text-red-800";
                case "maintenance":
                    return "bg-yellow-100 text-yellow-800";
                case "retired":
                    return "bg-gray-100 text-gray-800";
                default:
                    return "bg-gray-100 text-gray-800";
            }
        },
        getConditionClass(condition) {
            switch (condition) {
                case "Good":
                    return "bg-green-100 text-green-800";
                case "Fair":
                    return "bg-yellow-100 text-yellow-800";
                case "Poor":
                    return "bg-red-100 text-red-800";
                default:
                    return "bg-gray-100 text-gray-800";
            }
        },
        getEquipmentName(equipmentId) {
            const equipment = this.equipments.find(e => String(e.id) === String(equipmentId));
            return equipment ? equipment.name : "Unknown";
        },
        formatDate(dateString) {
            if (!dateString) return 'N/A';
            return new Date(dateString).toLocaleDateString('th-TH');
        },
        setSort(key) {
            if (this.sortKey === key) {
                this.sortDirection = this.sortDirection === "asc" ? "desc" : "asc";
            } else {
                this.sortKey = key;
                this.sortDirection = "asc";
            }
        },
        goToPage(p) {
            this.currentPage = p;
        },
        nextPage() {
            if (this.currentPage < this.pageCount) this.currentPage += 1;
        },
        prevPage() {
            if (this.currentPage > 1) this.currentPage -= 1;
        },
        clearFilters() {
            this.filterStatus = "";
            this.filterCondition = "";
            this.filterEquipmentId = "";
        },
        toggleAllSelection() {
            if (this.allItemsSelected) {
                this.selectedItems = [];
            } else {
                this.selectedItems = this.paginatedEquipmentItems.map(item => item.id);
            }
        },
        openCreateModal() {
            this.createModal.isOpen = true;
        },
        closeCreateModal() {
            this.createModal.isOpen = false;
        },
        openEditModal(item) {
            this.editModal.selectedItem = { ...item };
            this.editModal.isOpen = true;
        },
        closeEditModal() {
            this.editModal.isOpen = false;
        },
        openBulkUpdateModal() {
            if (this.selectedItems.length > 0) {
                this.bulkUpdateModal.isOpen = true;
            }
        },
        closeBulkUpdateModal() {
            this.bulkUpdateModal.isOpen = false;
        },
        createEquipmentItem(payload) {
            fetch('/admin/equipment-items/store', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload),
            })
            .then(async (res) => {
                if (!res.ok) {
                    const errorData = await res.json();
                    throw new Error(errorData.message || 'Create failed');
                }
                return res.json();
            })
            .then((data) => {
                this.equipmentItems.unshift(data.data);
                this.closeCreateModal();
                this.showSuccess(`เพิ่มรายการใหม่สำเร็จ: ${payload.serial_number || 'N/A'}`);
            })
            .catch((err) => {
                this.showError(err.message || 'ไม่สามารถเพิ่มรายการได้');
            });
        },
        updateEquipmentItem(payload) {
            fetch(`/admin/equipment-items/update/${payload.id}`, {
                method: 'PUT',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(payload),
            })
            .then(async (res) => {
                if (!res.ok) {
                    const errorData = await res.json();
                    throw new Error(errorData.message || 'Update failed');
                }
                return res.json();
            })
            .then((data) => {
                const index = this.equipmentItems.findIndex(item => item.id === data.data.id);
                if (index !== -1) {
                    this.equipmentItems.splice(index, 1, data.data);
                }
                this.closeEditModal();
                this.showSuccess(`อัปเดตรายการสำเร็จ: ${payload.serial_number || 'N/A'}`);
            })
            .catch((err) => {
                this.showError(err.message || 'ไม่สามารถอัปเดตรายการได้');
            });
        },
        deleteEquipmentItem(item) {
            this.ensureSwal().then(() => {
                window.Swal.fire({
                    title: 'ลบรายการ?',
                    text: `คุณกำลังจะลบรายการ ID: ${item.id} (${item.equipment?.name || 'N/A'})`,
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonText: 'ลบ',
                    cancelButtonText: 'ยกเลิก',
                    confirmButtonColor: '#ef4444',
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/admin/equipment-items/destroy/${item.id}`, {
                            method: 'DELETE',
                            headers: {
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                        })
                        .then(async (res) => {
                            if (!res.ok) {
                                const errorData = await res.json();
                                throw new Error(errorData.message || 'Delete failed');
                            }
                            return res.json();
                        })
                        .then(() => {
                            this.equipmentItems = this.equipmentItems.filter(i => i.id !== item.id);
                            this.showSuccess(`ลบรายการสำเร็จ: ${item.serial_number || 'N/A'}`);
                        })
                        .catch((err) => {
                            this.showError(err.message || 'ไม่สามารถลบรายการได้');
                        });
                    }
                });
            });
        },
        bulkUpdateItems(payload) {
            const data = {
                item_ids: this.selectedItems,
                status: payload.status,
                condition: payload.condition,
            };

            fetch('/admin/equipment-items/bulk-update', {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                    'Accept': 'application/json',
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data),
            })
            .then(async (res) => {
                if (!res.ok) {
                    const errorData = await res.json();
                    throw new Error(errorData.message || 'Bulk update failed');
                }
                return res.json();
            })
            .then(() => {
                // Refresh the data
                window.location.reload();
                this.closeBulkUpdateModal();
                this.selectedItems = [];
                this.showSuccess(`อัปเดต ${this.selectedItems.length} รายการสำเร็จ`);
            })
            .catch((err) => {
                this.showError(err.message || 'ไม่สามารถอัปเดตหลายรายการได้');
            });
        },
        ensureSwal() {
            return new Promise((resolve) => {
                if (window.Swal) return resolve();
                const script = document.createElement('script');
                script.src = 'https://cdn.jsdelivr.net/npm/sweetalert2@11';
                script.onload = () => resolve();
                document.head.appendChild(script);
            });
        },
        showSuccess(message) {
            this.ensureSwal().then(() => {
                window.Swal.fire({
                    title: 'สำเร็จ',
                    text: message,
                    icon: 'success',
                    timer: 1500,
                    showConfirmButton: false,
                });
            });
        },
        showError(message) {
            this.ensureSwal().then(() => {
                window.Swal.fire({
                    title: 'เกิดข้อผิดพลาด',
                    text: message,
                    icon: 'error',
                });
            });
        },
    },
    mounted() {
        this._onClickOutside = (e) => {
            const wrap = this.$refs.filtersWrap;
            if (!wrap) return;
            if (this.filtersOpen && !wrap.contains(e.target))
                this.filtersOpen = false;
        };
        document.addEventListener('click', this._onClickOutside);
    },
    beforeUnmount() {
        document.removeEventListener('click', this._onClickOutside);
    },
};
</script>
