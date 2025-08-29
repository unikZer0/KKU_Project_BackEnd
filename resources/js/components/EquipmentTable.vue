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
      <h2 class="text-lg font-semibold mb-4">
        อุปกรณ์รวมกันทั้งหมด: {{ filteredEquipments.length }} ชิ้น
      </h2>
      <button @click="goToCreate" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        เพิ่มอุปกรณ์ใหม่
      </button>
    </div>

    <!-- Table -->
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 border-b">
        <tr>
          <th class="px-4 py-2 text-left">ID</th>
          <th class="px-4 py-2 text-left">ชื่ออุปกรณ์</th>
          <th class="px-4 py-2 text-left">หมวดหมู่</th>
          <th class="px-4 py-2 text-left">สถานะ</th>
          <th class="px-4 py-2 text-left">แอคชั่น</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="equipment in filteredEquipments" :key="equipment.id" class="border-b">
          <td class="px-4 py-2">{{ equipment.code }}</td>
          <td class="px-4 py-2">{{ equipment.name }}</td>
          <td class="px-4 py-2">{{ equipment.category?.name || 'N/A' }}</td>
          <td class="px-4 py-2">{{ capitalize(equipment.status) }}</td>
          <td class="px-4 py-2 space-x-2">
  <button 
    @click="editEquipment(equipment.id)" 
    class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded"
  >
    แก้ไขข้อมูล
  </button>
  <button 
    @click="deleteEquipment(equipment.id)" 
    class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded"
  >
    ลบรายการ
  </button>
</td>

        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  name: "EquipmentTable",
  data() {
    return {
      equipments: [], // fetched data
      searchQuery: "" // <-- for the search input
    };
  },
  computed: {
    filteredEquipments() {
      if (!this.searchQuery) return this.equipments;
      const q = this.searchQuery.toLowerCase();
      return this.equipments.filter(e => 
        e.name.toLowerCase().includes(q) ||
        (e.category?.name || "").toLowerCase().includes(q) ||
        e.status.toLowerCase().includes(q) ||
        String(e.id).includes(q)
      );
    }
  },
  methods: {
    fetchEquipments() {
      fetch("/admin/equipment/index")
        .then(res => res.json())
        .then(data => {
          this.equipments = data.data || [];
        });
    },
    capitalize(str) {
      if (!str) return "";
      return str.charAt(0).toUpperCase() + str.slice(1);
    },
    goToCreate() {
      window.location.href = "/admin/equipment/create";
    },
    editEquipment(id) {
      window.location.href = `/admin/equipment/edit/${id}`;
    },
    deleteEquipment(id) {
      if (confirm("Are you sure you want to delete this equipment?")) {
        fetch(`/admin/equipment/destroy/${id}`, {
          method: "DELETE",
          headers: { "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content }
        }).then(() => {
          this.equipments = this.equipments.filter(e => e.id !== id);
        });
      }
    }
  },
  mounted() {
    const el = document.getElementById("equipment-table");
    if (el) {
      this.equipments = JSON.parse(el.dataset.equipments || "[]");
    }
    // Optionally: this.fetchEquipments()
  }
};
</script>
