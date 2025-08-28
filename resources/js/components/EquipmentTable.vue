<template>
  <div class="bg-white p-6 rounded-lg shadow">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-lg font-semibold">อุปกรณ์ทั้งหมด</h2>
      <button @click="goToCreate" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
        Assign New Equipment
      </button>
    </div>

    <h2 class="text-lg font-semibold mb-4">Total Equipments: {{ equipments.length }}</h2>

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
        <tr v-for="equipment in equipments" :key="equipment.id" class="border-b">
          <td class="px-4 py-2">{{ equipment.id }}</td>
          <td class="px-4 py-2">{{ equipment.name }}</td>
          <td class="px-4 py-2">{{ equipment.category?.name || 'N/A' }}</td>
          <td class="px-4 py-2">{{ capitalize(equipment.status) }}</td>
          <td class="px-4 py-2">
            <button @click="editEquipment(equipment.id)" class="text-blue-600">แก้ไขข้อมูล</button>
            |
            <button @click="deleteEquipment(equipment.id)" class="text-blue-600">ลบรายการ</button>
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
      equipments: [] // Array to store equipment fetched from API
    };
  },
  methods: {
    fetchEquipments() {
      // Replace with your actual API endpoint
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
      // Navigate to the create page (replace with Vue router if you have)
      window.location.href = "/admin/equipment/create";
    },
    editEquipment(id) {
      window.location.href = `/admin/equipment/edit/${id}`;
    },
    deleteEquipment(id) {
      if (confirm("Are you sure you want to delete this equipment?")) {
        fetch(`/admin/equipment/${id}`, {
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
  },
};
</script>

<style scoped>
/* Add custom styles if needed */
</style>