<template>
    <!-- Search + Filter -->
    <div class="relative mb-4 flex justify-between items-center">
        <!-- Search -->
        <div class="relative w-1/3">
            <input
                type="text"
                v-model="searchQuery"
                placeholder="Search"
                class="pl-10 pr-3 py-2 text-sm border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-full"
            />
            <svg
                class="w-4 h-4 absolute left-3 top-2.5 text-gray-400"
                fill="none"
                stroke="currentColor"
                viewBox="0 0 24 24"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"
                />
            </svg>
        </div>

        <!-- Filter Button -->
        <button
            @click="toggleFilter"
            class="ml-2 px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M7 8h10M10 12h4"/>
            </svg>
        </button>
    </div>

    <!-- Filter Panel (Slide-in) -->
    <transition name="slide">
        <div
            v-if="isFilterOpen"
            class="fixed inset-0 bg-black bg-opacity-50 flex justify-end z-50"
        >
            <div class="w-80 bg-white h-full shadow-lg p-4 flex flex-col">
                <div class="flex justify-between items-center mb-4">
                    <h2 class="text-lg font-bold">ตัวกรอง</h2>
                    <button
                        @click="toggleFilter"
                        class="text-gray-500 hover:text-gray-800"
                    >
                        ✖
                    </button>
                </div>

                <!-- Category Filter -->
                <label class="block mb-2 font-semibold">หมวดหมู่</label>
                <select
                    v-model="selectedCategoryId"
                    class="border rounded w-full px-2 py-1 mb-4"
                >
                    <option value="">ทั้งหมด</option>
                    <option v-for="c in categories" :key="c.id" :value="c.id">
                        {{ c.name }}
                    </option>
                </select>

                <!-- Status Filter -->
                <label class="block mb-2 font-semibold">สถานะ</label>
                <select
                    v-model="selectedStatus"
                    class="border rounded w-full px-2 py-1 mb-4"
                >
                    <option value="">ทั้งหมด</option>
                    <option v-for="s in statuses" :key="s" :value="s">
                        {{ capitalize(s) }}
                    </option>
                </select>

                <!-- Buttons -->
                <div class="mt-auto flex justify-between">
                    <button
                        @click="resetFilters"
                        class="px-4 py-2 bg-gray-300 rounded hover:bg-gray-400"
                    >
                        ล้าง
                    </button>
                    <button
                        @click="applyFilters"
                        class="px-4 py-2 bg-blue-500 text-white rounded hover:bg-blue-600"
                    >
                        ใช้
                    </button>
                </div>
            </div>
        </div>
    </transition>

    <!-- Header -->
    <div class="flex justify-between items-center mb-4">
        <h2 class="text-lg font-semibold mb-4">
            อุปกรณ์รวมกันทั้งหมด: {{ filteredEquipments.length }} ชิ้น
        </h2>
        <button
            @click="openCreateModal"
            class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700"
        >
            เพิ่มอุปกรณ์ใหม่
        </button>
    </div>

    <!-- Table -->
    <table class="min-w-full text-sm">
        <thead class="bg-gray-50 border-b">
            <tr>
                <th class="px-4 py-2 text-left">รูปหน้าปก</th>
                <th class="px-4 py-2 text-left">หมายเลขครุภัณฑ์</th>
                <th class="px-4 py-2 text-left">ชื่ออุปกรณ์</th>
                <th class="px-4 py-2 text-left">รายละเอียด</th>
                <th class="px-4 py-2 text-left">หมวดหมู่</th>
                <th class="px-4 py-2 text-left">สถานะ</th>
                <th class="px-4 py-2 text-left">แอคชั่น</th>
            </tr>
        </thead>
        <tbody>
            <tr
                v-for="equipment in paginatedEquipments"
                :key="equipment.id"
                class="border-b"
            >
                <!-- Photo -->
                <td class="px-4 py-2 flex items-center space-x-2">
                    <img
                        v-if="equipment.photo_path"
                        :src="equipment.photo_path"
                        alt="Equipment Photo"
                        class="w-8 h-8 object-cover rounded cursor-pointer"
                        @click="openPhotoModal(equipment.photo_path)"
                    />
                </td>
                <td class="px-4 py-2">{{ equipment.code }}</td>
                <td class="px-4 py-2">{{ equipment.name }}</td>
                <td class="px-4 py-2 max-w-[200px] truncate">
                    {{ equipment.description }}
                </td>
                <td class="px-4 py-2">
                    {{ equipment.category?.name || "N/A" }}
                </td>
                <td class="px-4 py-2">
                    {{ capitalize(equipment.status) }}
                </td>
                <td class="px-4 py-2 space-x-2">
                    <button
                        @click="openModal(equipment)"
                        class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded"
                    >
                        แก้ไขข้อมูล
                    </button>
                    <button
                        @click="deleteEquipment(equipment)"
                        class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded"
                    >
                        ลบรายการ
                    </button>
                </td>
            </tr>
        </tbody>
    </table>

    <!-- Pagination Controls -->
    <div class="mt-4 flex items-center justify-between">
        <div class="text-sm text-gray-600">
            แสดง {{ pageStart + 1 }} - {{ pageEnd }} จากทั้งหมด
            {{ filteredEquipments.length }} รายการ
        </div>
        <div class="flex items-center space-x-1">
            <button
                class="px-3 py-1 border rounded disabled:opacity-50"
                :disabled="currentPage === 1"
                @click="prevPage"
            >
                ก่อนหน้า
            </button>
            <button
                v-for="p in pageCount"
                :key="p"
                class="px-3 py-1 border rounded"
                :class="{ 'bg-blue-600 text-white': currentPage === p }"
                @click="goToPage(p)"
            >
                {{ p }}
            </button>
            <button
                class="px-3 py-1 border rounded disabled:opacity-50"
                :disabled="currentPage === pageCount || pageCount === 0"
                @click="nextPage"
            >
                ถัดไป
            </button>
        </div>
    </div>

    <!-- Edit Modal -->
    <EquipmentEditModal
        :isOpen="isOpen"
        :equipment="selectedEquipment"
        :categories="categories"
        :statuses="statuses"
        @cancel="isOpen = false"
        @save="updateEquipment"
        @image-change="selectedImageFile = $event"
    />

    <!-- Create Modal -->
    <EquipmentCreateModal
        :isOpen="createModal.isOpen"
        :categories="categories"
        :statuses="statuses"
        @cancel="closeCreateModal"
        @create="createEquipment"
        @image-change="createModal.imageFile = $event"
    />

    <!-- Photo Modal -->
    <PhotoModal
        :isOpen="photoModal.isOpen"
        :url="photoModal.url"
        @close="closePhotoModal"
    />
</template>

<script>
import EquipmentEditModal from "../modals/EquipmentEditModal.vue";
import EquipmentCreateModal from "../modals/EquipmentCreateModal.vue";
import PhotoModal from "../modals/PhotoModal.vue";

export default {
    name: "EquipmentTable",
    components: { EquipmentEditModal, EquipmentCreateModal, PhotoModal },
    data() {
        const el = document.getElementById("equipment-table");
        return {
            equipments: JSON.parse(el.dataset.equipments || "[]"),
            categories: JSON.parse(el.dataset.categories || "[]"),
            statuses: ["available", "retired", "maintenance"],

            searchQuery: "",
            currentPage: 1,
            pageSize: 15,

            // Filter panel
            isFilterOpen: false,
            selectedCategoryId: "",
            selectedStatus: "",

            isOpen: false,
            selectedEquipment: {},
            selectedImageFile: null,

            photoModal: { isOpen: false, url: "" },
            createModal: {
                isOpen: false,
                form: {
                    name: "",
                    description: "",
                    categories_id: "",
                    status: "available",
                },
                imageFile: null,
            },
        };
    },
    computed: {
        filteredEquipments() {
            let list = this.equipments;

            // Search
            if (this.searchQuery) {
                const q = this.searchQuery.toLowerCase();
                list = list.filter(
                    (e) =>
                        e.name.toLowerCase().includes(q) ||
                        (e.category?.name || "").toLowerCase().includes(q) ||
                        e.status.toLowerCase().includes(q) ||
                        String(e.id).includes(q)
                );
            }

            // Category filter
            if (this.selectedCategoryId) {
                list = list.filter(
                    (e) => e.categories_id == this.selectedCategoryId
                );
            }

            // Status filter
            if (this.selectedStatus) {
                list = list.filter((e) => e.status === this.selectedStatus);
            }

            return list;
        },
        pageCount() {
            return Math.ceil(this.filteredEquipments.length / this.pageSize) || 0;
        },
        pageStart() {
            return (this.currentPage - 1) * this.pageSize;
        },
        pageEnd() {
            return Math.min(
                this.pageStart + this.pageSize,
                this.filteredEquipments.length
            );
        },
        paginatedEquipments() {
            return this.filteredEquipments.slice(this.pageStart, this.pageEnd);
        },
    },
    methods: {
        capitalize(str) {
            return str ? str.charAt(0).toUpperCase() + str.slice(1) : "";
        },
        toggleFilter() {
            this.isFilterOpen = !this.isFilterOpen;
        },
        resetFilters() {
            this.selectedCategoryId = "";
            this.selectedStatus = "";
            this.currentPage = 1;
        },
        applyFilters() {
            this.currentPage = 1;
            this.toggleFilter();
        },

        goToPage(p) {
            this.currentPage = p;
        },
        nextPage() {
            if (this.currentPage < this.pageCount) this.currentPage++;
        },
        prevPage() {
            if (this.currentPage > 1) this.currentPage--;
        },

        // ... keep your CRUD methods from before (updateEquipment, deleteEquipment, etc.)
        // I didn’t remove them, just shortened here for clarity.
    },
};
</script>

