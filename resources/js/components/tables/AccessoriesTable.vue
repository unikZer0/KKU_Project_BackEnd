<template>
    <div class="bg-white p-6 rounded-lg shadow">
        <!-- Breadcrumb -->
        <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
            <a href="/admin" class="hover:text-gray-700">แดชบอร์ด</a>
            <span>/</span>
            <span class="font-semibold text-gray-900">หน้าจัดการอุปกรณ์เสริม</span>
        </nav>
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
                รายการอุปกรณ์เสริมทั้งหมด: {{ filteredAccessories.length }} รายการ
            </h2>
            <div class="flex flex-wrap gap-2 items-center relative" ref="filtersWrap">
                <button @click="filtersOpen = !filtersOpen" class="px-3 py-1 border rounded">
                    ตัวกรอง
                    <span class="text-xs text-gray-500 ml-1">
                        {{ filterStatus !== 'all' ? filterStatus : 'all' }} ·
                        {{ filterCondition !== 'all' ? filterCondition : 'all conditions' }} ·
                        {{ filterType !== 'all' ? (filterType === 'standalone' ? 'ใช้งานได้ทุกรุ่น' : 'ใช้งานได้เฉพาะรุ่น') : 'alltypes' }} ·
                        {{ filterEquipment !== 'all' ? getEquipmentName(filterEquipment) : 'all equipments' }}
                    </span>
                </button>

                <button @click="openCreateModal" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    เพิ่มอุปกรณ์เสริมใหม่
                </button>

                <!-- Dropdown panel -->
                <div v-if="filtersOpen" class="absolute right-0 top-10 z-10 bg-white border rounded shadow p-3 w-72">
                    <div class="mb-2">
                        <div class="text-sm font-semibold mb-1">อุปกรณ์หลัก</div>
                        <select v-model="filterEquipment" class="w-full px-2 py-1 border rounded">
                            <option value="all">ทั้งหมด</option>
                            <option v-for="equipment in equipments" :key="equipment.id" :value="equipment.id">
                                {{ equipment.name }}
                            </option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <div class="text-sm font-semibold mb-1">สถานะ</div>
                        <select v-model="filterStatus" class="w-full px-2 py-1 border rounded">
                            <option value="all">ทั้งหมด</option>
                            <option value="available">พร้อมใช้งาน</option>
                            <option value="unavailable">ไม่พร้อมใช้งาน</option>
                            <option value="maintenance">ซ่อมบำรุง</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <div class="text-sm font-semibold mb-1">สภาพ</div>
                        <select v-model="filterCondition" class="w-full px-2 py-1 border rounded">
                            <option value="all">ทั้งหมด</option>
                            <option value="good">ดี</option>
                            <option value="fair">พอใช้</option>
                            <option value="poor">ชำรุด</option>
                        </select>
                    </div>

                    <div class="mb-2">
                        <div class="text-sm font-semibold mb-1">ประเภท</div>
                        <select v-model="filterType" class="w-full px-2 py-1 border rounded">
                            <option value="all">ทั้งหมด</option>
                            <option value="standalone">ใช้งานได้ทุกรุ่น</option>
                            <option value="tied">ใช้งานได้เฉพาะรุ่น</option>
                        </select>
                    </div>
                    <div class="flex gap-2">
                        <button @click="applyFilters" class="bg-blue-600 text-white px-3 py-1 rounded text-sm">
                            กรอง
                        </button>
                        <button @click="resetFilters" class="bg-gray-500 text-white px-3 py-1 rounded text-sm">
                            รีเซ็ต
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <!-- Accessories Table -->
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">ID
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            อุปกรณ์เสริม</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            หมายเลขซีเรียล</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สภาพ
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">สถานะ
                        </th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            อุปกรณ์หลัก</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            วันที่สร้าง</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                            การดำเนินการ</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    <tr v-for="accessory in paginatedAccessories" :key="accessory.id" class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                            {{ accessory.id }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ accessory.name }}</div>
                            <div v-if="accessory.description" class="text-sm text-gray-500">{{
                                accessory.description.substring(0, 50) }}{{ accessory.description.length > 50 ? '...' :
                                '' }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ accessory.serial_number }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span :class="getConditionClass(accessory.condition)"
                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                                {{ getConditionLabel(accessory.condition) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span :class="getStatusClass(accessory.status)"
                                class="inline-flex px-2 py-1 text-xs font-semibold rounded-full">
                                {{ getStatusLabel(accessory.status) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            <div class="font-medium">{{ accessory.equipment?.name }}</div>
                            <div class="text-xs text-gray-500">{{ accessory.equipment?.category?.name || 'ไม่มีหมวดหมู่'
                                }}</div>
                            <div v-if="accessory.equipment_item" class="text-xs text-blue-600 mt-1">
                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-blue-100 text-blue-800">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1">
                                        </path>
                                    </svg>
                                    ใช้งานร่วมกับ: {{ accessory.equipment_item.serial_number }}
                                </span>
                            </div>
                            <div v-else class="text-xs text-gray-600 mt-1">
                                <span class="inline-flex items-center px-2 py-1 rounded-full bg-gray-100 text-gray-600">
                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                            d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                    </svg>
                                    ใช้งานได้ทุกรุ่น
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                            {{ formatDate(accessory.created_at) }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <div class="flex space-x-2">
                                <ActionButtons :item="accessory" @view="openEditModal" @edit="openEditModal"
                                    @delete="deleteAccessory" />
                            </div>
                        </td>
                    </tr>
                    <tr v-if="filteredAccessories.length === 0">
                        <td colspan="8" class="px-6 py-4 text-center text-gray-500">
                            ไม่พบข้อมูลอุปกรณ์เสริม
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div v-if="totalPages > 1" class="px-6 py-3 border-t border-gray-200">
            <div class="flex items-center justify-between">
                <div class="text-sm text-gray-700">
                    แสดง {{ (currentPage - 1) * pageSize + 1 }} ถึง {{ Math.min(currentPage * pageSize,
                    filteredAccessories.length) }}
                    จาก {{ filteredAccessories.length }} รายการ
                </div>
                <div class="flex space-x-2">
                    <button @click="currentPage = Math.max(1, currentPage - 1)" :disabled="currentPage === 1"
                        class="px-3 py-1 border rounded disabled:opacity-50 disabled:cursor-not-allowed">
                        ก่อนหน้า
                    </button>
                    <span class="px-3 py-1">{{ currentPage }} / {{ totalPages }}</span>
                    <button @click="currentPage = Math.min(totalPages, currentPage + 1)"
                        :disabled="currentPage === totalPages"
                        class="px-3 py-1 border rounded disabled:opacity-50 disabled:cursor-not-allowed">
                        ถัดไป
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Create Modal -->
    <AccessoryCreateModal :isOpen="createModalOpen" :equipments="equipments" @close="createModalOpen = false"
        @create="handleCreateAccessory" />

    <!-- Edit Modal -->
    <AccessoryEditModal :isOpen="editModalOpen" :accessory="selectedAccessory" :equipments="equipments"
        @close="editModalOpen = false" @update="handleUpdateAccessory" />
</template>

<script>
import AccessoryCreateModal from "../modals/AccessoryCreateModal.vue";
import AccessoryEditModal from "../modals/AccessoryEditModal.vue";
import ActionButtons from "../ui/ActionButtons.vue";

export default {
    name: "AccessoriesTable",
    components: {
        AccessoryCreateModal,
        AccessoryEditModal,
        ActionButtons,
    },
    data() {
        const el = document.getElementById("accessories-table");
        const accessories = el?.dataset?.accessories ? JSON.parse(el.dataset.accessories) : [];
        const equipments = el?.dataset?.equipments ? JSON.parse(el.dataset.equipments) : [];

        return {
            accessories: Array.isArray(accessories) ? accessories : [],
            equipments: Array.isArray(equipments) ? equipments : [],
            searchQuery: "",
            filterCondition: "all",
            filterStatus: "all",
            filterEquipment: "all",
            filterType: "all",
            filtersOpen: false,
            currentPage: 1,
            pageSize: 20,
            createModalOpen: false,
            editModalOpen: false,
            selectedAccessory: {},
        };
    },
    computed: {
        filteredAccessories() {
            // Ensure accessories is always an array
            let filtered = Array.isArray(this.accessories) ? this.accessories : [];

            // Search filter
            if (this.searchQuery) {
                const query = this.searchQuery.toLowerCase();
                filtered = filtered.filter(accessory =>
                    accessory.name?.toLowerCase().includes(query) ||
                    accessory.serial_number?.toLowerCase().includes(query) ||
                    accessory.description?.toLowerCase().includes(query) ||
                    accessory.equipment?.name?.toLowerCase().includes(query)
                );
            }

            // Condition filter
            if (this.filterCondition !== "all") {
                filtered = filtered.filter(accessory => accessory.condition === this.filterCondition);
            }

            // Status filter
            if (this.filterStatus !== "all") {
                filtered = filtered.filter(accessory => accessory.status === this.filterStatus);
            }

            // Equipment filter
            if (this.filterEquipment !== "all") {
                filtered = filtered.filter(accessory => accessory.equipment_id == this.filterEquipment);
            }

            // Type filter (standalone vs tied)
            if (this.filterType !== "all") {
                if (this.filterType === "standalone") {
                    filtered = filtered.filter(accessory => !accessory.equipment_item_id);
                } else if (this.filterType === "tied") {
                    filtered = filtered.filter(accessory => accessory.equipment_item_id);
                }
            }

            return filtered;
        },
        paginatedAccessories() {
            const start = (this.currentPage - 1) * this.pageSize;
            const end = start + this.pageSize;
            return this.filteredAccessories.slice(start, end);
        },
        totalPages() {
            return Math.ceil(this.filteredAccessories.length / this.pageSize);
        }
    },
    mounted() {
        console.log('AccessoriesTable mounted');
        console.log('Accessories data:', this.accessories);
        console.log('Equipments data:', this.equipments);
    },
    methods: {
        openCreateModal() {
            this.createModalOpen = true;
        },
        openEditModal(accessory) {
            this.selectedAccessory = { ...accessory };
            this.editModalOpen = true;
        },
        applyFilters() {
            this.currentPage = 1;
        },
        resetFilters() {
            this.searchQuery = "";
            this.filterCondition = "all";
            this.filterStatus = "all";
            this.filterEquipment = "all";
            this.filterType = "all";
            this.currentPage = 1;
        },
        getConditionClass(condition) {
            const classes = {
                'สภาพดี': 'bg-green-100 text-green-800',
                'สามารถซ่อมได้': 'bg-yellow-100 text-yellow-800',
                'ไม่สามารถซ่อมได้': 'bg-indigo-100 text-indigo-800',
                'พัง': 'bg-red-100 text-red-800',
                'หาย': 'bg-blue-100 text-blue-800'
            };
            return classes[condition] || 'bg-gray-100 text-gray-800';
        },
        getConditionLabel(condition) {
            const labels = {
                'สภาพดี': 'สภาพดี',
                'สามารถซ่อมได้': 'สามารถซ่อมได้',
                'ไม่สามารถซ่อมได้': 'ไม่สามารถซ่อมได้',
                'พัง': 'พัง',
                'หาย': 'ทำหาย'
            };
            return labels[condition] || condition;
        },
        getStatusClass(status) {
            const classes = {
                'available': 'bg-green-100 text-green-800',
                'Available': 'bg-green-100 text-green-800',
                'unavailable': 'bg-red-100 text-red-800',
                'Unavailable': 'bg-red-100 text-red-800',
                'maintenance': 'bg-yellow-100 text-yellow-800',
                'Maintenance': 'bg-yellow-100 text-yellow-800'
            };
            return classes[status] || 'bg-gray-100 text-gray-800';
        },
        getStatusLabel(status) {
            const labels = {
                'available': 'พร้อมใช้งาน',
                'Available': 'พร้อมใช้งาน',
                'unavailable': 'ไม่พร้อมใช้งาน',
                'Unavailable': 'ไม่พร้อมใช้งาน',
                'maintenance': 'ซ่อมบำรุง',
                'Maintenance': 'ซ่อมบำรุง'
            };
            return labels[status] || status;
        },
        formatDate(dateString) {
            const date = new Date(dateString);
            return date.toLocaleDateString('th-TH');
        },
        async handleCreateAccessory(accessoryData) {
            try {
                const response = await fetch('/admin/accessories/store', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(accessoryData)
                });

                if (response.ok) {
                    const newAccessory = await response.json();
                    this.accessories.unshift(newAccessory.accessory);
                    this.createModalOpen = false;
                    this.showSuccess('อุปกรณ์เสริมถูกสร้างเรียบร้อยแล้ว');
                } else {
                    const error = await response.json();
                    this.showError(error.message || 'เกิดข้อผิดพลาดในการสร้างอุปกรณ์เสริม');
                }
            } catch (error) {
                this.showError('เกิดข้อผิดพลาดในการเชื่อมต่อ');
            }
        },
        async handleUpdateAccessory(accessoryData) {
            try {
                const response = await fetch(`/admin/accessories/update/${accessoryData.id}`, {
                    method: 'PUT',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                    },
                    body: JSON.stringify(accessoryData)
                });

                if (response.ok) {
                    const updatedAccessory = await response.json();
                    const index = this.accessories.findIndex(a => a.id === updatedAccessory.accessory.id);
                    if (index !== -1) {
                        this.accessories.splice(index, 1, updatedAccessory.accessory);
                    }
                    this.editModalOpen = false;
                    this.showSuccess('อุปกรณ์เสริมถูกอัปเดตเรียบร้อยแล้ว');
                } else {
                    const error = await response.json();
                    this.showError(error.message || 'เกิดข้อผิดพลาดในการอัปเดตอุปกรณ์เสริม');
                }
            } catch (error) {
                this.showError('เกิดข้อผิดพลาดในการเชื่อมต่อ');
            }
        },
        async deleteAccessory(accessory) {
            if (confirm('คุณแน่ใจหรือไม่ที่จะลบอุปกรณ์เสริมนี้?')) {
                try {
                    const response = await fetch(`/admin/accessories/destroy/${accessory.id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                        }
                    });

                    if (response.ok) {
                        const index = this.accessories.findIndex(a => a.id === accessory.id);
                        if (index !== -1) {
                            this.accessories.splice(index, 1);
                        }
                        this.showSuccess('อุปกรณ์เสริมถูกลบเรียบร้อยแล้ว');
                    } else {
                        const error = await response.json();
                        this.showError(error.message || 'เกิดข้อผิดพลาดในการลบอุปกรณ์เสริม');
                    }
                } catch (error) {
                    this.showError('เกิดข้อผิดพลาดในการเชื่อมต่อ');
                }
            }
        },
        showSuccess(message) {
            // You can implement a toast notification here
            alert(message);
        },
        showError(message) {
            // You can implement a toast notification here
            alert(message);
        },
        getEquipmentName(equipmentId) {
            const equipment = this.equipments.find(e => e.id == equipmentId);
            return equipment ? equipment.name : 'Unknown';
        }
    }
};
</script>
