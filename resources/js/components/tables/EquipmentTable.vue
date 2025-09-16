<template>
  <div class="bg-white p-6 rounded-lg shadow">
    <!-- Search -->
    <div class="relative mb-4">
      <input
        type="text"
        v-model="searchQuery"
        placeholder="Search"
        class="pl-10 pr-3 py-2 text-sm border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
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
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-lg font-semibold">
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
          <th class="px-4 py-2 text-left">รูป</th>
          <th class="px-4 py-2 text-left">ID</th>
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
          <td class="px-4 py-2 flex items-center space-x-2">
            <img
              v-if="getFirstPhoto(equipment)"
              :src="getFirstPhoto(equipment)"
              alt="Equipment Photo"
              class="w-8 h-8 object-cover rounded cursor-pointer"
              @click="openPhotoModal(getFirstPhoto(equipment))"
            />
          </td>
          <td class="px-4 py-2">{{ equipment.code }}</td>
          <td class="px-4 py-2">{{ equipment.name }}</td>
          <td class="px-4 py-2 max-w-[200px] truncate">
            {{ equipment.description }}
          </td>
          <td class="px-4 py-2">{{ equipment.category?.name || "N/A" }}</td>
          <td class="px-4 py-2">{{ capitalize(equipment.status) }}</td>
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

    <!-- Pagination -->
    <div class="mt-4 flex items-center justify-between">
      <div class="text-sm text-gray-600">
        แสดง {{ pageStart + 1 }} - {{ pageEnd }} จากทั้งหมด
        {{ filteredEquipments.length }} รายการ
      </div>
      <div class="flex items-center space-x-1 flex-wrap">
        <button
          class="px-3 py-1 border rounded disabled:opacity-50"
          :disabled="currentPage === 1"
          @click="prevPage"
        >
          ก่อนหน้า
        </button>
        <button
          v-for="p in visiblePageNumbers"
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

    <!-- Modals -->
    <EquipmentEditModal
      :isOpen="isOpen"
      :equipment="selectedEquipment"
      :categories="categories"
      :statuses="statuses"
      @cancel="isOpen = false"
      @save="updateEquipment"
      @image-change="selectedImageFile = $event"
    />
    <EquipmentCreateModal
      :isOpen="createModal.isOpen"
      :categories="categories"
      :statuses="statuses"
      @cancel="closeCreateModal"
      @create="createEquipment"
    />
    <PhotoModal
      :isOpen="photoModal.isOpen"
      :url="photoModal.url"
      @close="closePhotoModal"
    />
  </div>
</template>

<script>
import EquipmentEditModal from "../modals/EquipmentEditModal.vue";
import EquipmentCreateModal from "../modals/EquipmentCreateModal.vue";
import PhotoModal from "../modals/PhotoModal.vue";

export default {
  name: "EquipmentTable",
  components: {
    EquipmentEditModal,
    EquipmentCreateModal,
    PhotoModal,
  },
  data() {
    const el = document.getElementById("equipment-table");
    return {
      equipments: JSON.parse(el.dataset.equipments || "[]"),
      categories: JSON.parse(el.dataset.categories || "[]"),
      statuses: ["available", "retired", "maintenance"],
      searchQuery: "",
      currentPage: 1,
      pageSize: 15,
      isOpen: false,
      selectedEquipment: {},
      selectedCategoryId: null,
      selectedImageFile: null,
      photoModal: { isOpen: false, url: "" },
      createModal: { isOpen: false },
    };
  },
  computed: {
    filteredEquipments() {
      if (!this.searchQuery) return this.equipments;
      const q = this.searchQuery.toLowerCase();
      return this.equipments.filter(
        (e) =>
          e.name.toLowerCase().includes(q) ||
          (e.category?.name || "").toLowerCase().includes(q) ||
          e.status.toLowerCase().includes(q) ||
          String(e.code).includes(q)
      );
    },
    pageCount() {
      return Math.ceil(this.filteredEquipments.length / this.pageSize) || 0;
    },
    pageStart() {
      return (this.currentPage - 1) * this.pageSize;
    },
    pageEnd() {
      return Math.min(this.pageStart + this.pageSize, this.filteredEquipments.length);
    },
    paginatedEquipments() {
      return this.filteredEquipments.slice(this.pageStart, this.pageEnd);
    },
    visiblePageNumbers() {
      const pages = [];
      const total = this.pageCount;
      const maxVisible = 7; // show max 7 pages
      let start = Math.max(1, this.currentPage - 3);
      let end = Math.min(total, start + maxVisible - 1);
      start = Math.max(1, end - maxVisible + 1);
      for (let i = start; i <= end; i++) pages.push(i);
      return pages;
    },
  },
  methods: {
    capitalize(str) {
      return str ? str.charAt(0).toUpperCase() + str.slice(1) : "";
    },
    getFirstPhoto(equipment) {
      if (!equipment.photo_path) return null;
      try {
        const photos = JSON.parse(equipment.photo_path);
        return Array.isArray(photos) && photos.length ? photos[0] : null;
      } catch (e) {
        return equipment.photo_path;
      }
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
    openCreateModal() {
      this.createModal.isOpen = true;
    },
    closeCreateModal() {
      this.createModal.isOpen = false;
    },
    openModal(equipment) {
      this.selectedEquipment = { ...equipment };
      this.selectedCategoryId = equipment.category?.id || null;
      this.isOpen = true;
    },
    openPhotoModal(url) {
      this.photoModal.url = url;
      this.photoModal.isOpen = true;
    },
    closePhotoModal() {
      this.photoModal.url = "";
      this.photoModal.isOpen = false;
    },
    ensureSwal() {
      return new Promise((resolve) => {
        if (window.Swal) return resolve();
        const script = document.createElement("script");
        script.src = "https://cdn.jsdelivr.net/npm/sweetalert2@11";
        script.onload = () => resolve();
        document.head.appendChild(script);
      });
    },
    notifyError(message) {
      let msg = typeof message === "object" ? message.message || JSON.stringify(message) : message;
      if (window.Swal) {
        window.Swal.fire({ title: "เกิดข้อผิดพลาด", text: msg, icon: "error" });
      } else alert(msg);
    },
  },
  watch: {
    searchQuery() {
      this.currentPage = 1;
    },
    filteredEquipments() {
      if (this.currentPage > this.pageCount) this.currentPage = 1;
    },
  },
};
</script>
