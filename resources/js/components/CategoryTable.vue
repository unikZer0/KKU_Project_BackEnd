<template>
  <div class="bg-white p-6 rounded-lg shadow">
    <!-- Search Bar -->
    <div class="relative mb-4">
      <input 
        type="text" 
        v-model="searchQuery"
        placeholder="Search"
        class="pl-10 pr-3 py-2 text-sm border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
      <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
          d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"/>
      </svg>
    </div>

    <!-- Header -->
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-lg font-semibold">หมวดหมู่ทั้งหมด</h2>
      <button @click="goToCreate" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        Assign New Category
      </button>
    </div>

    <!-- Count -->
    <h2 class="text-lg font-semibold mb-4">
      Total Categories: {{ filteredCategories.length }}
    </h2>

    <!-- Table -->
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 border-b">
        <tr>
          <th class="px-4 py-2 text-left">ID</th>
          <th class="px-4 py-2 text-left">ชื่อหมวดหมู่</th>
          <th class="px-4 py-2 text-left">แอคชั่น</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="category in filteredCategories" :key="category.id" class="border-b">
          <td class="px-4 py-2">{{ category.cate_id }}</td>
          <td class="px-4 py-2">{{ category.name }}</td>
          <td class="px-4 py-2">
            <button @click="editCategory(category.id)" class="text-blue-600">แก้ไขข้อมูล</button>
            |
            <button @click="deleteCategory(category.id)" class="text-blue-600">ลบรายการ</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  name: "CategoriesTable",
  data() {
    return {
      categories: [], // fetched data
      searchQuery: "" // <-- for the search input
    };
  },
  computed: {
    filteredCategories() {
      if (!this.searchQuery) return this.categories;
      const q = this.searchQuery.toLowerCase();
      return this.categories.filter(c => 
        c.name.toLowerCase().includes(q)
      );
    }
  },
  methods: {
    fetchCategories() {
      fetch("/admin/category/index")
        .then(res => res.json())
        .then(data => {
          this.categories = data.data || [];
        });
    },
    capitalize(str) {
      if (!str) return "";
      return str.charAt(0).toUpperCase() + str.slice(1);
    },
    goToCreate() {
      window.location.href = "/admin/category/create";
    },
    editCategory(id) {
      window.location.href = `/admin/category/edit/${id}`;
    },
    deleteCategory(id) {
      if (confirm("Are you sure you want to delete this category?")) {
        fetch(`/admin/category/destroy/${id}`, {
          method: "DELETE",
          headers: { "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content }
        }).then(() => {
          this.categories = this.categories.filter(c => c.id !== id);
        });
      }
    }
  },
  mounted() {
    const el = document.getElementById("category-table");
    if (el) {
      this.categories = JSON.parse(el.dataset.categories || "[]");
    }
    // Optionally: this.fetchCategories()
  }
};
</script>
