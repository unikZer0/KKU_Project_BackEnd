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

    <!-- Header + Status & Category Counts + Filters -->
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-4">
      <h2 class="text-lg font-semibold">
        อุปกรณ์รวมทั้งหมด: {{ filteredEquipments.length }} ชิ้น
      </h2>

      <!-- Status & Category badges -->
      <div class="flex gap-2 flex-wrap items-center">
        <!-- Status badges -->
        <span
          v-for="s in statuses"
          :key="s"
          class="px-2 py-1 rounded text-sm font-medium"
          :class="statusClass(s)"
        >
          {{ capitalize(s) }}: {{ statusCounts[s] || 0 }}
        </span>

        <!-- Category badges -->
        <span
          v-for="c in categories"
          :key="c.id"
          class="px-2 py-1 rounded text-sm font-medium bg-gray-100 text-gray-800"
        >
          {{ c.name }}: {{ categoryCounts[String(c.id)] || 0 }}
        </span>
      </div>

      <!-- Filters & Create Button -->
      <div class="flex flex-wrap gap-2 items-center relative" ref="filtersWrap">
        <button @click="filtersOpen = !filtersOpen" class="px-3 py-1 border rounded">
          ตัวกรอง
          <span class="text-xs text-gray-500 ml-1">
            {{ filterStatus ? filterStatus : 'all' }}
            · {{ filterCategoryId ? (categories.find(c => String(c.id)===String(filterCategoryId))?.name || 'category') : 'all categories' }}
          </span>
        </button>

        <button class="px-3 py-1 border rounded" @click="toggleSortDir">
          {{ sortDir === 'asc' ? 'ASC' : 'DESC' }}
        </button>

        <button @click="openCreateModal" class="ml-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
          เพิ่มอุปกรณ์ใหม่
        </button>

        <!-- Dropdown panel -->
        <div v-if="filtersOpen" class="absolute right-0 top-10 z-10 bg-white border rounded shadow p-3 w-72">
          <div class="mb-2">
            <div class="text-sm font-semibold mb-1">สถานะ</div>
            <select v-model="filterStatus" class="w-full px-2 py-1 border rounded">
              <option value="">ทั้งหมด ({{ statusCounts.all }})</option>
              <option v-for="s in statuses" :key="s" :value="s">{{ capitalize(s) }} ({{ statusCounts[s] || 0 }})</option>
            </select>
          </div>
          <div class="mb-3">
            <div class="text-sm font-semibold mb-1">หมวดหมู่</div>
            <select v-model="filterCategoryId" class="w-full px-2 py-1 border rounded">
              <option value="">ทุกหมวดหมู่ ({{ categoryCounts.all }})</option>
              <option v-for="c in categories" :key="c.id" :value="String(c.id)">
                {{ c.name }} ({{ categoryCounts[String(c.id)] || 0 }})
              </option>
            </select>
          </div>
          <div class="flex justify-between">
            <button class="px-3 py-1 border rounded" @click="clearFilters">ล้างตัวกรอง</button>
            <button class="px-3 py-1 bg-gray-900 text-white rounded" @click="filtersOpen=false">เสร็จสิ้น</button>
          </div>
        </div>
      </div>
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
        <tr v-for="equipment in paginatedEquipments" :key="equipment.id" class="border-b">
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
          <td class="px-4 py-2 max-w-[200px] truncate">{{ equipment.description }}</td>
          <td class="px-4 py-2">{{ equipment.category?.name || 'N/A' }}</td>
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
        แสดง {{ pageStart + 1 }} - {{ pageEnd }} จากทั้งหมด {{ filteredEquipments.length }} รายการ
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
  components: { EquipmentEditModal, EquipmentCreateModal, PhotoModal },
  data() {
    const el = document.getElementById("equipment-table");
    return {
      equipments: JSON.parse(el?.dataset?.equipments || "[]"),
      categories: JSON.parse(el?.dataset?.categories || "[]"),
      statuses: ["available", "retired", "maintenance"],
      searchQuery: "",
      currentPage: 1,
      pageSize: 15,
      filterStatus: "",
      filterCategoryId: "",
      sortKey: "name",
      sortDir: "asc",
      filtersOpen: false,
      isOpen: false,
      selectedEquipment: {},
      selectedImageFile: null,
      photoModal: { isOpen: false, url: "" },
      createModal: { isOpen: false },
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
    },
    pageCount() { return Math.ceil(this.filteredEquipments.length / this.pageSize) || 0; },
    pageStart() { return (this.currentPage - 1) * this.pageSize; },
    pageEnd() { return Math.min(this.pageStart + this.pageSize, this.filteredEquipments.length); },
    paginatedEquipments() { return this.filteredEquipments.slice(this.pageStart, this.pageEnd); },
    visiblePageNumbers() {
      const pages = [];
      const total = this.pageCount;
      const maxVisible = 7;
      let start = Math.max(1, this.currentPage - 3);
      let end = Math.min(total, start + maxVisible - 1);
      start = Math.max(1, end - maxVisible + 1);
      for (let i = start; i <= end; i++) pages.push(i);
      return pages;
    },
  },
  methods: {
    capitalize(str) { return str ? str.charAt(0).toUpperCase() + str.slice(1) : ""; },
    statusClass(status) {
      switch (status) {
        case "available": return "bg-green-100 text-green-800";
        case "retired": return "bg-red-100 text-red-800";
        case "maintenance": return "bg-yellow-100 text-yellow-800";
        default: return "bg-gray-100 text-gray-800";
      }
    },
    getFirstPhoto(equipment) {
      if (!equipment.photo_path) return null;
      try {
        const photos = JSON.parse(equipment.photo_path);
        return Array.isArray(photos) && photos.length ? photos[0] : null;
      } catch (e) { return equipment.photo_path; }
    },
    goToPage(p) { this.currentPage = p; },
    nextPage() { if(this.currentPage < this.pageCount) this.currentPage++; },
    prevPage() { if(this.currentPage > 1) this.currentPage--; },
    toggleSortDir() { this.sortDir = this.sortDir === "asc" ? "desc" : "asc"; },
    clearFilters() { this.filterStatus = ''; this.filterCategoryId = ''; },
    openCreateModal() { this.createModal.isOpen = true; },
    closeCreateModal() { this.createModal.isOpen = false; },
    openModal(equipment) { this.selectedEquipment = { ...equipment }; this.isOpen = true; },
    openPhotoModal(url) { this.photoModal.url = url; this.photoModal.isOpen = true; },
    closePhotoModal() { this.photoModal.url = ""; this.photoModal.isOpen = false; },

    async createEquipment(payload) {
      try {
        const res = await axios.post("/api/equipments", payload);
        this.equipments.push(res.data);
        this.closeCreateModal();
        Swal.fire("Success", "อุปกรณ์ถูกสร้างเรียบร้อย", "success");
      } catch (err) { this.notifyError(err.response?.data?.message || err.message); }
    },

    async updateEquipment(payload) {
      try {
        const res = await axios.put(`/api/equipments/${payload.id}`, payload);
        const idx = this.equipments.findIndex((e) => e.id === payload.id);
        if (idx !== -1) this.equipments.splice(idx, 1, res.data);
        this.isOpen = false;
        Swal.fire("Success", "อุปกรณ์ถูกอัปเดตเรียบร้อย", "success");
      } catch (err) { this.notifyError(err.response?.data?.message || err.message); }
    },

    async deleteEquipment(equipment) {
      try {
        const confirmed = await Swal.fire({
          title: "ยืนยันการลบ?",
          text: `คุณต้องการลบ ${equipment.name} หรือไม่?`,
          icon: "warning",
          showCancelButton: true,
          confirmButtonText: "ตกลง",
          cancelButtonText: "ยกเลิก"
        });
        if (confirmed.isConfirmed) {
          await axios.delete(`/api/equipments/${equipment.id}`);
          this.equipments = this.equipments.filter((e) => e.id !== equipment.id);
          Swal.fire("Deleted!", "อุปกรณ์ถูกลบเรียบร้อย", "success");
        }
      } catch (err) { this.notifyError(err.response?.data?.message || err.message); }
    },

    notifyError(message) {
      Swal.fire("Error", message || "เกิดข้อผิดพลาด", "error");
    },
  },
  watch: {
    searchQuery() { this.currentPage = 1; },
    filteredEquipments() { if(this.currentPage > this.pageCount) this.currentPage = 1; }
  },
  mounted() {
    this._onClickOutside = (e) => {
      const wrap = this.$refs.filtersWrap;
      if (!wrap) return;
      if (this.filtersOpen && !wrap.contains(e.target)) this.filtersOpen = false;
    };
    document.addEventListener("click", this._onClickOutside);
  },
  beforeUnmount() { document.removeEventListener("click", this._onClickOutside); }
};
</script>
