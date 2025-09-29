<template>
  <div class="bg-white p-6 rounded-lg shadow">
    <!-- Breadcrumb -->
    <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
      <a href="/admin" class="hover:text-gray-700">แดชบอร์ด</a>
      <span>/</span>
      <span class="font-semibold text-gray-900">หน้าจัดการหมวดหมู่</span>
    </nav>
    <!-- Search Bar -->
    <div class="relative mb-4">
      <input type="text" v-model="searchQuery" placeholder="Search"
        class="pl-10 pr-3 py-2 text-sm border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
      <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
      </svg>
    </div>

    <!-- Header + Counts -->
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-4">
      <h2 class="text-lg font-semibold">
        หมวดหมู่ทั้งหมด: {{ filteredCategories.length }}
      </h2>
      <div class="flex flex-wrap gap-2 items-center">
        <!-- Add button (only admin) -->
        <button v-if="userRole === 'admin'" @click="createModalOpen = true"
          class="ml-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
          เพิ่มหมวดหมู่ใหม่
        </button>
      </div>
    </div>

    <!-- Table -->
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 border-b">
        <tr>
          <th class="px-4 py-2 text-left">
            <button @click="setSort('cate_id')" class="flex items-center space-x-1 hover:text-blue-600">
              <span>รหัสหมวดหมู่</span>
              <span class="flex flex-col">
                <svg class="w-3 h-3"
                  :class="{ 'text-blue-600': sortKey === 'cate_id' && sortDirection === 'asc', 'text-gray-400': !(sortKey === 'cate_id' && sortDirection === 'asc') }"
                  fill="currentColor" viewBox="0 0 20 20">
                  <path
                    d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" />
                </svg>
                <svg class="w-3 h-3 -mt-1"
                  :class="{ 'text-blue-600': sortKey === 'cate_id' && sortDirection === 'desc', 'text-gray-400': !(sortKey === 'cate_id' && sortDirection === 'desc') }"
                  fill="currentColor" viewBox="0 0 20 20">
                  <path
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                </svg>
              </span>
            </button>
          </th>
          <th class="px-4 py-2 text-left">
            <button @click="setSort('name')" class="flex items-center space-x-1 hover:text-blue-600">
              <span>ชื่อหมวดหมู่</span>
              <span class="flex flex-col">
                <svg class="w-3 h-3"
                  :class="{ 'text-blue-600': sortKey === 'name' && sortDirection === 'asc', 'text-gray-400': !(sortKey === 'name' && sortDirection === 'asc') }"
                  fill="currentColor" viewBox="0 0 20 20">
                  <path
                    d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" />
                </svg>
                <svg class="w-3 h-3 -mt-1"
                  :class="{ 'text-blue-600': sortKey === 'name' && sortDirection === 'desc', 'text-gray-400': !(sortKey === 'name' && sortDirection === 'desc') }"
                  fill="currentColor" viewBox="0 0 20 20">
                  <path
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                </svg>
              </span>
            </button>
          </th>
          <th class="px-4 py-2 text-left">
            <button @click="setSort('equipments_count')" class="flex items-center space-x-1 hover:text-blue-600">
              <span>จํานวนอุปกรณ์</span>
              <span class="flex flex-col">
                <svg class="w-3 h-3"
                  :class="{ 'text-blue-600': sortKey === 'equipments_count' && sortDirection === 'asc', 'text-gray-400': !(sortKey === 'equipments_count' && sortDirection === 'asc') }"
                  fill="currentColor" viewBox="0 0 20 20">
                  <path
                    d="M14.707 12.707a1 1 0 01-1.414 0L10 9.414l-3.293 3.293a1 1 0 01-1.414-1.414l4-4a1 1 0 011.414 0l4 4a1 1 0 010 1.414z" />
                </svg>
                <svg class="w-3 h-3 -mt-1"
                  :class="{ 'text-blue-600': sortKey === 'equipments_count' && sortDirection === 'desc', 'text-gray-400': !(sortKey === 'equipments_count' && sortDirection === 'desc') }"
                  fill="currentColor" viewBox="0 0 20 20">
                  <path
                    d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" />
                </svg>
              </span>
            </button>
          </th>
          <th v-if="userRole === 'admin'" class="px-4 py-2 text-left">แอคชั่น</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="category in paginatedCategories" :key="category.id" class="border-b hover:bg-gray-50">
          <td class="px-4 py-2">{{ category.cate_id }}</td>
          <td class="px-4 py-2">{{ category.name }}</td>
          <td class="px-4 py-2">
            <span
              class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
              {{ category.equipments_count }} ชิ้น</span>
          </td>
          <td class="px-4 py-2 space-x-2">
            <ActionButtons :item="category" @view="openModal" @edit="openModal" @delete="deleteCategory" />
          </td>
        </tr>
        <tr v-if="filteredCategories.length === 0">
          <td colspan="4" class="px-6 py-4 text-center text-gray-500">
            ไม่พบข้อมูลหมวดหมู่
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Pagination -->
    <div class="mt-4 flex items-center justify-between">
      <div class="text-sm text-gray-600">
        แสดง {{ pageStart + 1 }} - {{ pageEnd }} จากทั้งหมด {{ filteredCategories.length }} รายการ
      </div>
      <div class="flex items-center space-x-1">
        <button class="px-3 py-1 border rounded disabled:opacity-50" :disabled="currentPage === 1" @click="prevPage">
          ก่อนหน้า
        </button>
        <button v-for="p in visiblePages" :key="p" class="px-3 py-1 border rounded"
          :class="{ 'bg-blue-600 text-white': currentPage === p }" @click="goToPage(p)">
          {{ p }}
        </button>
        <button class="px-3 py-1 border rounded disabled:opacity-50"
          :disabled="currentPage === pageCount || pageCount === 0" @click="nextPage">
          ถัดไป
        </button>
      </div>
    </div>

    <!-- Edit Modal -->
    <CategoryEditModal :isOpen="isOpen" :category="selectedCategory" @close="isOpen = false"
      @save="updateCategoryFromModal" />

    <!-- Create Modal -->
    <CategoryCreateModal :isOpen="createModalOpen" @close="createModalOpen = false" @create="handleCreateCategory" />
  </div>
</template>

<script>
import CategoryEditModal from '../modals/CategoryEditModal.vue';
import CategoryCreateModal from '../modals/CategoryCreateModal.vue';
import ActionButtons from "../ui/ActionButtons.vue";

export default {
  name: "CategoriesTable",
  components: { CategoryEditModal, CategoryCreateModal, ActionButtons },
  data() {
    const el = document.getElementById("category-table");
    return {
      userRole: el?.dataset?.role || "",
      equipments: [],
      categories: [],
      searchQuery: "",
      isOpen: false,
      createModalOpen: false,
      selectedCategory: null,
      categoryTypes: [],
      sortKey: "created_at",
      sortDirection: "asc",
      currentPage: 1,
      pageSize: 10,
    };
  },
  computed: {
    filteredCategories() {
      let list = this.categories;
      if (this.searchQuery) {
        const q = this.searchQuery.toLowerCase();
        list = list.filter(c => c.name.toLowerCase().includes(q));
      }
      // Sort
      list = [...list].sort((a, b) => {
        let aVal = a[this.sortKey];
        let bVal = b[this.sortKey];

        // Handle different data types
        if (this.sortKey === 'created_at') {
          aVal = new Date(aVal);
          bVal = new Date(bVal);
        } else if (this.sortKey === 'equipments_count') {
          aVal = parseInt(aVal) || 0;
          bVal = parseInt(bVal) || 0;
        } else {
          // String comparison for name, cate_id, etc.
          aVal = String(aVal || '').toLowerCase();
          bVal = String(bVal || '').toLowerCase();
        }

        if (aVal < bVal) return this.sortDirection === "asc" ? -1 : 1;
        if (aVal > bVal) return this.sortDirection === "asc" ? 1 : -1;
        return 0;
      });
      return list;
    },
    typeCounts() {
      const counts = {};
      for (const cat of this.categories) {
        const type = cat.type || 'Unknown';
        counts[type] = (counts[type] || 0) + 1;
      }
      return counts;
    },
    pageCount() {
      return Math.ceil(this.filteredCategories.length / this.pageSize) || 1;
    },
    pageStart() {
      return (this.currentPage - 1) * this.pageSize;
    },
    pageEnd() {
      const end = this.pageStart + this.pageSize;
      return Math.min(end, this.filteredCategories.length);
    },
    paginatedCategories() {
      return this.filteredCategories.slice(this.pageStart, this.pageEnd);
    },
    visiblePages() {
      const pages = [];
      const start = Math.max(1, this.currentPage - 2);
      const end = Math.min(this.pageCount, this.currentPage + 2);

      for (let i = start; i <= end; i++) {
        pages.push(i);
      }
      return pages;
    }
  },
  methods: {
    setSort(key) {
      if (this.sortKey === key) {
        this.sortDirection = this.sortDirection === "asc" ? "desc" : "asc";
      } else {
        this.sortKey = key;
        this.sortDirection = "asc";
      }
    },
    typeClass(type) {
      return type === 'Unknown'
        ? 'bg-gray-100 text-gray-800'
        : 'bg-green-100 text-green-800';
    },
    fetchCategories() {
      fetch("/api/categories")
        .then(res => res.json())
        .then(data => {
          this.categories = data || [];
          this.categoryTypes = [...new Set(this.categories.map(c => c.type || 'Unknown'))];
        });
    },
    fetchEquipments() {
      fetch("/api/equipments")
        .then(res => res.json())
        .then(data => {
          this.equipments = data || [];
        });
    },
    openModal(category) {
      this.selectedCategory = { ...category };
      this.isOpen = true;
    },
    updateCategoryFromModal(updatedCategory) {
      fetch(`/admin/category/update/${updatedCategory.id}`, {
        method: "PUT",
        headers: {
          "Content-Type": "application/json",
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify({ name: updatedCategory.name, type: updatedCategory.type })
      })
        .then(res => res.json())
        .then(data => {
          if (data.status) {
            const index = this.categories.findIndex(c => c.id === updatedCategory.id);
            if (index !== -1) this.categories[index] = { ...this.categories[index], ...updatedCategory };
            this.isOpen = false;
            Swal.fire('สำเร็จ!', 'แก้ไขหมวดหมู่เรียบร้อย', 'success');
          } else {
            Swal.fire('ผิดพลาด', 'ไม่สามารถแก้ไขหมวดหมู่ได้', 'error');
          }
        })
        .catch(() => Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดในการแก้ไขหมวดหมู่', 'error'));
    },
    deleteCategory(id) {
      Swal.fire({
        title: 'คุณแน่ใจหรือไม่?',
        text: "รายการนี้จะถูกลบถาวร!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#d33',
        cancelButtonColor: '#3085d6',
        confirmButtonText: 'ลบเลย',
        cancelButtonText: 'ยกเลิก'
      }).then((result) => {
        if (result.isConfirmed) {
          fetch(`/admin/category/destroy/${id}`, {
            method: "DELETE",
            headers: { "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content }
          }).then(() => {
            this.categories = this.categories.filter(c => c.id !== id);
            Swal.fire('ลบแล้ว!', 'หมวดหมู่ถูกลบเรียบร้อย', 'success');
          });
        }
      });
    },
    prevPage() {
      if (this.currentPage > 1) this.currentPage -= 1;
    },
    nextPage() {
      if (this.currentPage < this.pageCount) this.currentPage += 1;
    },
    goToPage(page) {
      this.currentPage = page;
    },
    handleCreateCategory(newCategory) {
      fetch('/admin/category/store', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
        },
        body: JSON.stringify(newCategory)
      })
        .then(async res => {
          if (!res.ok) throw new Error(await res.text() || 'Server error');
          return res.json();
        })
        .then(data => {
          if (data.status) {
            this.categories.push(data.category);
            this.categoryTypes = [...new Set(this.categories.map(c => c.type || 'Unknown'))];
            this.createModalOpen = false;
            Swal.fire('สำเร็จ!', 'สร้างหมวดหมู่เรียบร้อย', 'success');
          } else {
            Swal.fire('ผิดพลาด', data.message || 'ไม่สามารถสร้างหมวดหมู่ได้', 'error');
          }
        })
        .catch(err => {
          console.error(err);
          Swal.fire('ผิดพลาด', 'เกิดข้อผิดพลาดในการสร้างหมวดหมู่', 'error');
        });
    }
  },
  watch: {
    searchQuery() {
      this.currentPage = 1; // Reset to first page when search changes
    }
  },
  mounted() {
    const el = document.getElementById("category-table");
    if (el) {
      this.categories = JSON.parse(el.dataset.categories || "[]");
      this.userRole = el.dataset.role || '';
    }
    this.fetchCategories();
    this.fetchEquipments();
  }
};
</script>