<template>
    <div class="bg-white p-6 rounded-lg shadow">
        <!-- Search Bar -->
        <div class="relative mb-4">
            <input type="text" v-model="searchQuery" placeholder="Search"
                class="pl-10 pr-3 py-2 text-sm border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
            </svg>
        </div>

        <!-- Header -->
        <div class="flex justify-between items-center mb-4">
            <h2 class="text-lg font-semibold mb-4">
                อุปกรณ์รวมกันทั้งหมด: {{ filteredEquipments.length }} ชิ้น
            </h2>
            <button @click="openCreateModal" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
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
                <tr v-for="equipment in paginatedEquipments" :key="equipment.id" class="border-b">
                    <!-- Photo -->
                    <td class="px-4 py-2 flex items-center space-x-2">
                        <img v-if="equipment.photo_path" :src="equipment.photo_path" alt="Equipment Photo"
                            class="w-8 h-8 object-cover rounded cursor-pointer"
                            @click="openPhotoModal(equipment.photo_path)" />
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
                        <button @click="openModal(equipment)"
                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">
                            แก้ไขข้อมูล
                        </button>
                        <button @click="deleteEquipment(equipment)"
                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">
                            ลบรายการ
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <!-- Pagination Controls -->
        <div class="mt-4 flex items-center justify-between">
            <div class="text-sm text-gray-600">
                แสดง {{ pageStart + 1 }} - {{ pageEnd }} จากทั้งหมด {{ filteredEquipments.length }} รายการ
            </div>
            <div class="flex items-center space-x-1">
                <button class="px-3 py-1 border rounded disabled:opacity-50" :disabled="currentPage === 1"
                    @click="prevPage">ก่อนหน้า</button>
                <button v-for="p in pageCount" :key="p" class="px-3 py-1 border rounded"
                    :class="{ 'bg-blue-600 text-white': currentPage === p }" @click="goToPage(p)">{{ p }}</button>
                <button class="px-3 py-1 border rounded disabled:opacity-50"
                    :disabled="currentPage === pageCount || pageCount === 0" @click="nextPage">ถัดไป</button>
            </div>
        </div>

        <!-- Edit Modal -->
        <EquipmentEditModal :isOpen="isOpen" :equipment="selectedEquipment" :categories="categories"
            :statuses="statuses" @cancel="isOpen = false" @save="updateEquipment"
            @image-change="selectedImageFile = $event" />

        <!-- Create Modal -->
        <EquipmentCreateModal :isOpen="createModal.isOpen" :categories="categories" :statuses="statuses"
            @cancel="closeCreateModal" @create="createEquipment" @image-change="createModal.imageFile = $event" />

        <!-- Photo Modal -->
        <PhotoModal :isOpen="photoModal.isOpen" :url="photoModal.url" @close="closePhotoModal" />
    </div>
</template>

<script>
import EquipmentEditModal from '../modals/EquipmentEditModal.vue';
import EquipmentCreateModal from '../modals/EquipmentCreateModal.vue';
import PhotoModal from '../modals/PhotoModal.vue';

export default {
    name: "EquipmentTable",
    components: {
        EquipmentEditModal,
        EquipmentCreateModal,
        PhotoModal
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

            photoModal: {
                isOpen: false,
                url: "",
            },

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
            if (!this.searchQuery) return this.equipments;
            const q = this.searchQuery.toLowerCase();
            return this.equipments.filter(
                (e) =>
                    e.name.toLowerCase().includes(q) ||
                    (e.category?.name || "").toLowerCase().includes(q) ||
                    e.status.toLowerCase().includes(q) ||
                    String(e.id).includes(q)
            );
        },
        pageCount() {
            return Math.ceil(this.filteredEquipments.length / this.pageSize) || 0;
        },
        pageStart() {
            return (this.currentPage - 1) * this.pageSize;
        },
        pageEnd() {
            const end = this.pageStart + this.pageSize;
            return Math.min(end, this.filteredEquipments.length);
        },
        paginatedEquipments() {
            return this.filteredEquipments.slice(this.pageStart, this.pageEnd);
        },
    },
    methods: {
        capitalize(str) {
            if (!str) return "";
            return str.charAt(0).toUpperCase() + str.slice(1);
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
        openCreateModal() {
            this.createModal.isOpen = true;
        },
        closeCreateModal() {
            this.createModal.isOpen = false;
            this.createModal.form = {
                name: "",
                description: "",
                categories_id: "",
                status: "available",
            };
            this.createModal.imageFile = null;
        },
        openModal(equipment) {
            this.selectedEquipment = { ...equipment };
            this.selectedCategoryId = equipment.category
                ? equipment.category.id
                : null;
            this.isOpen = true;
        },
        onEditImageChange(event) {
            const files = event.target.files;
            this.selectedImageFile = files && files[0] ? files[0] : null;
        },
        updateEquipment(payload) {
            // Use payload from modal
            const formData = new FormData();
            formData.append("name", payload.name || "");
            formData.append("description", payload.description || "");
            formData.append("categories_id", String(payload.categories_id ?? ""));
            formData.append("status", payload.status || "available");
            if (payload.imageFile) {
                formData.append("image", payload.imageFile);
            }
            formData.append("_method", "PUT");
            fetch(`/admin/equipment/update/${this.selectedEquipment.id}`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    "Accept": "application/json",
                },
                body: formData,
            })
                .then(async (res) => {
                    if (!res.ok) {
                        let msg = "Update failed";
                        try {
                            const j = await res.json();
                            msg = j.message || JSON.stringify(j);
                        } catch (e) { }
                        throw new Error(msg);
                    }
                    return res.json();
                })
                .then((data) => {
                    const updated = data.data;
                    const idx = this.equipments.findIndex(
                        (e) => e.id === updated.id
                    );
                    if (idx !== -1) {
                        this.equipments.splice(idx, 1, updated);
                    }
                    this.isOpen = false;
                    this.selectedImageFile = null;
                    this.ensureSwal().then(() => {
                        window.Swal.fire({
                            title: "อัปเดตสำเร็จ",
                            text: "อัปเดตละนะ",
                            icon: "success",
                            timer: 1200,
                            showConfirmButton: false,
                        });
                    });
                })
                .catch((err) => {
                    this.notifyError(err.message || "ไม่สามารถอัปเดตได้");
                });
        },
deleteEquipment(equipment) {
  this.ensureSwal().then(() => {
    window.Swal.fire({
      title: "ลบรายการ?",
      text: `คุณกำลังจะลบ: ${equipment.name} (ID: ${equipment.code})`,
      icon: "warning",
      imageUrl: equipment.photo_path || null,   // cloud URL from DB
      imageWidth: 120,                          // adjust size if needed
      imageHeight: 120,
      imageAlt: equipment.name,
      showCancelButton: true,
      confirmButtonText: "ลบ",
      cancelButtonText: "ยกเลิก",
      confirmButtonColor: "#ef4444",
    }).then((result) => {
      if (result.isConfirmed) {
        fetch(`/admin/equipment/destroy/${equipment.id}`, {
          method: "DELETE",
          headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
            "Accept": "application/json",
          },
        })
          .then(async (res) => {
            if (!res.ok) {
              let msg = "Delete failed";
              try {
                const j = await res.json();
                msg = j.message || JSON.stringify(j);
              } catch (e) {}
              throw new Error(msg);
            }
            return res.json();
          })
          .then(() => {
            this.equipments = this.equipments.filter(
              (e) => e.id !== equipment.id
            );
            window.Swal.fire({
              title: "ลบแล้ว",
              text: `${equipment.name} ถูกลบเรียบร้อย`,
              icon: "success",
              timer: 1200,
              showConfirmButton: false,
            });
          })
          .catch((err) => {
            this.notifyError(err.message || "ลบไม่สำเร็จ");
          });
      }
    });
  });
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
            if (window.Swal) {
                window.Swal.fire({ title: "เกิดข้อผิดพลาด", text: message, icon: "error" });
            } else {
                alert(message);
            }
        },
        onCreateImageChange(event) {
            const files = event.target.files;
            this.createModal.imageFile = files && files[0] ? files[0] : null;
        },
        createEquipment(payload) {
            // Use payload from modal
            const formData = new FormData();
            formData.append("code", payload.code ||"");
            formData.append("name", payload.name || "");
            formData.append("description", payload.description || "");
            formData.append("categories_id", payload.categories_id || "");
            formData.append("status", payload.status || "available");
            if (payload.imageFile) {
                formData.append("image", payload.imageFile);
            }
            
            fetch(`/admin/equipment/store`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
                    Accept: "application/json",
                },
                body: formData,
            })
                .then(async (res) => {
                    if (!res.ok) {
                        let msg = "Create failed";
                        try {
                            const j = await res.json();
                            msg = j.message || JSON.stringify(j);
                        } catch (e) { }
                        throw new Error(msg);
                    }
                    return res.json();
                })
                .then((data) => {
                    let created = data.data;
                    if (!created.category && created.categories_id) {
                        const found = this.categories.find(
                            (c) => c.id === created.categories_id
                        );
                        if (found) {
                            created = { ...created, category: found };
                        }
                    }
                    this.equipments.unshift(created);
                    this.closeCreateModal();
                    this.ensureSwal().then(() => {
                        window.Swal.fire({
                            title: "เพิ่มข้อมูลสำเร็จ",
                            text: "เพิ่มละนะ",
                            icon: "success",
                            timer: 1200,
                            showConfirmButton: false,
                        });
                    });
                })
.catch(async (err) => {
    console.log(" err : ",err);
    
    if (err.response) {
        this.createModal.errors = err.response.data.errors || {};
    } else {
        try {
            const resJson = await err.response.json();
            this.createModal.errors = resJson.errors || {};
        } catch (e) {
            console.error(e);
        }
    }
    this.notifyError(err.message || "ไม่สามารถเพิ่มข้อมูลได้");
});
        },

        openPhotoModal(url) {
            this.photoModal.url = url;
            this.photoModal.isOpen = true;
        },
        closePhotoModal() {
            this.photoModal.url = "";
            this.photoModal.isOpen = false;
        },
    },
    mounted() {
        this.$watch(
            () => this.searchQuery,
            () => {
                this.currentPage = 1;
            }
        );
        this.$watch(
            () => this.filteredEquipments.length,
            () => {
                if (this.currentPage > this.pageCount) this.currentPage = 1;
            }
        );
    },
};
</script>
