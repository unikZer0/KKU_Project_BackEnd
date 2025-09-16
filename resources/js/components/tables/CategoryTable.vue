<template>
  <div class="bg-white p-6 rounded-lg shadow">

    <!-- Header + Counts -->
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-4">
      <h2 class="text-lg font-semibold">
        หมวดหมู่ทั้งหมด: {{ filteredCategories.length }}
      </h2>
      <button @click="createModalOpen = true" class="ml-2 bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        เพิ่มหมวดหมู่ใหม่
      </button>
    </div>

    <!-- Search Bar -->
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

    <!-- Table -->
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 border-b">
        <tr>
          <th class="px-4 py-2 text-left">รหัสหมวดหมู่</th>
          <th class="px-4 py-2 text-left">ชื่อหมวดหมู่</th>
          <th class="px-4 py-2 text-left">ประเภท</th>
          <th class="px-4 py-2 text-left">แอคชั่น</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="category in filteredCategories" :key="category.id" class="border-b">
          <td class="px-4 py-2">{{ category.cate_id }}</td>
          <td class="px-4 py-2">{{ category.name }}</td>
          <td class="px-4 py-2 space-x-2">
            <button
              @click="openModal(category)"
              class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded"
            >
              แก้ไขข้อมูล
            </button>
            <button
              @click="deleteCategory(category.id)"
              class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded"
            >
              ลบรายการ
            </button>
          </td>
        </tr>
      </tbody>
    </table>

    <!-- Edit Modal -->
    <CategoryEditModal
      :isOpen="isOpen"
      :category="selectedCategory"
      @close="isOpen = false"
      @save="updateCategoryFromModal"
    />

    <!-- Create Modal -->
    <CategoryCreateModal
      :isOpen="createModalOpen"
      @close="createModalOpen = false"
      @create="handleCreateCategory"
    />
  </div>
</template>

<script>
import CategoryEditModal from '../modals/CategoryEditModal.vue';
import CategoryCreateModal from '../modals/CategoryCreateModal.vue';
export default {
  name: "CategoriesTable",
  components: { CategoryEditModal, CategoryCreateModal },
  data() {
    return {
      categories: [],
      searchQuery: "",
      isOpen: false,
      createModalOpen: false,
      selectedCategory: null,
      categoryTypes: [], // Unique types for badges
    };
  },
  computed: {
    filteredCategories() {
      if (!this.searchQuery) return this.categories;
      const q = this.searchQuery.toLowerCase();
      return this.categories.filter(c => c.name.toLowerCase().includes(q));
    },
    typeCounts() {
      const counts = {};
      for (const cat of this.categories) {
        const type = cat.type || 'Unknown';
        counts[type] = (counts[type] || 0) + 1;
      }
      return counts;
    }
  },
  methods: {
  typeClass(type) {
    return type === 'Unknown' ? 'bg-gray-100 text-gray-800' : 'bg-green-100 text-green-800';
  },
  fetchCategories() {
    fetch("/admin/category")
      .then(res => res.json())
      .then(data => {
        this.categories = data.data || [];
        this.categoryTypes = [...new Set(this.categories.map(c => c.type || 'Unknown'))];
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
  mounted() {
    const el = document.getElementById("category-table");
    if (el) this.categories = JSON.parse(el.dataset.categories || "[]");
    this.fetchCategories();
  }
};
</script>
