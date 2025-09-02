<template>
    <div class="bg-white p-6 rounded-lg shadow">
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
                            @click="deleteEquipment(equipment.id)"
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
                แสดง {{ pageStart + 1 }} - {{ pageEnd }} จากทั้งหมด {{ filteredEquipments.length }} รายการ
            </div>
            <div class="flex items-center space-x-1">
                <button
                    class="px-3 py-1 border rounded disabled:opacity-50"
                    :disabled="currentPage === 1"
                    @click="prevPage"
                >ก่อนหน้า</button>
                <button
                    v-for="p in pageCount"
                    :key="p"
                    class="px-3 py-1 border rounded"
                    :class="{ 'bg-blue-600 text-white': currentPage === p }"
                    @click="goToPage(p)"
                >{{ p }}</button>
                <button
                    class="px-3 py-1 border rounded disabled:opacity-50"
                    :disabled="currentPage === pageCount || pageCount === 0"
                    @click="nextPage"
                >ถัดไป</button>
            </div>
        </div>

        <!-- Edit Modal -->
        <div
            v-if="isOpen"
            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-40"
        >
            <div class="bg-white rounded-lg shadow-lg w-1/3 p-6">
                <h3 class="text-lg font-semibold mb-4">แก้ไขอุปกรณ์</h3>

                <!-- Name -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-1"
                        >ชื่ออุปกรณ์</label
                    >
                    <input
                    
                        type="text"
                        v-model="selectedEquipment.name"
                        class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>

                <!-- Category -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-1"
                        >หมวดหมู่</label
                    >
                    <select
                    
                        v-model="selectedCategoryId"
                        class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option
                            v-for="cat in categories"
                            :key="cat.id"
                            :value="cat.id"
                        >
                            {{ cat.name }}
                        </option>
                    </select>
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-1"
                        >รายละเอียด</label
                    >
                    <textarea
                    
                        v-model="selectedEquipment.description"
                        class="w-full h-24 border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                        placeholder="กรอกรายละเอียดอุปกรณ์"
                    ></textarea>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-1"
                        >สถานะ</label
                    >
                    <select
                        v-model="selectedEquipment.status"
                        class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option v-for="s in statuses" :key="s" :value="s">
                            {{ capitalize(s) }}
                        </option>
                    </select>
                </div>

                <!-- Image -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-1"
                        >รูปภาพ</label
                    >
                    <input
                    
                        type="file"
                        accept="image/*"
                        @change="onEditImageChange"
                    />
                    <div v-if="selectedEquipment.photo_path" class="mt-2">
                        <p class="text-sm text-gray-600">รูปปัจจุบัน:</p>
                        <img
                            :src="selectedEquipment.photo_path"
                            alt="Current Image"
                            class="w-24 h-24 object-cover rounded"
                        />
                    </div>
                </div>

                <div class="flex justify-end space-x-2">
                    <button
                        @click="isOpen = false"
                        class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400"
                    >
                        Cancel
                    </button>
                    <button
                        @click="updateEquipment"
                        class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700"
                    >
                        Save
                    </button>
                </div>
            </div>
        </div>

        <!-- Create Modal -->
        <div
            v-if="createModal.isOpen"
            class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-40"
        >
            <div class="bg-white rounded-lg shadow-lg w-1/3 p-6">
                <h3 class="text-lg font-semibold mb-4">เพิ่มอุปกรณ์ใหม่</h3>

                <!-- Name -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-1"
                        >ชื่ออุปกรณ์</label
                    >
                    <input required
                        type="text"
                        v-model="createModal.form.name"
                        class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    />
                </div>

                <!-- Category -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-1"
                        >หมวดหมู่</label
                    >
                    <select required
                        v-model="createModal.form.categories_id"
                        class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option value="" disabled>เลือกหมวดหมู่</option>
                        <option
                            v-for="cat in categories"
                            :key="cat.id"
                            :value="cat.id"
                        >
                            {{ cat.name }}
                        </option>
                    </select>
                </div>

                <!-- Description -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-1"
                        >รายละเอียด</label
                    >
                    <textarea
                    
                        v-model="createModal.form.description"
                        class="w-full h-24 border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                        placeholder="กรอกรายละเอียดอุปกรณ์"
                    ></textarea>
                </div>

                <!-- Status -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-1"
                        >สถานะ</label
                    >
                    <select required
                        v-model="createModal.form.status"
                        class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    >
                        <option v-for="s in statuses" :key="s" :value="s">
                            {{ capitalize(s) }}
                        </option>
                    </select>
                </div>

                <!-- Image -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-1"
                        >รูปภาพ</label
                    >
                    <input required
                        type="file"
                        accept="image/*"
                        @change="onCreateImageChange"
                    />
                </div>

                <div class="flex justify-end space-x-2">
                    <button
                        @click="closeCreateModal"
                        class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400"
                    >
                        Cancel
                    </button>
                    <button
                        @click="createEquipment"
                        class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700"
                    >
                        Create
                    </button>
                </div>
            </div>
        </div>
        <div
            v-if="photoModal.isOpen"
            class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50"
            @click.self="closePhotoModal"
        >
            <div
                class="bg-white rounded-lg shadow-lg p-4 max-w-[90%] max-h-[90%] flex flex-col items-center"
            >
                <img
                    :src="photoModal.url"
                    alt="Equipment Photo"
                    class="max-w-full max-h-full rounded mb-2"
                />
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "EquipmentTable",
    data() {
        const el = document.getElementById("equipment-table");
        return {
            equipments: JSON.parse(el.dataset.equipments || "[]"),
            categories: JSON.parse(el.dataset.categories || "[]"),
            statuses: ["available", "unavailable", "maintenance"],
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
        updateEquipment() {
            const formData = new FormData();
            formData.append("name", this.selectedEquipment.name || "");
            formData.append(
                "description",
                this.selectedEquipment.description || ""
            );
            const categoryId = this.selectedCategoryId == null ? "" : this.selectedCategoryId;
            formData.append("categories_id", String(categoryId));
            formData.append(
                "status",
                this.selectedEquipment.status || "available"
            );
            
            if (this.selectedImageFile) {
                formData.append("image", this.selectedImageFile);
            }
            formData.append("_method", "PUT");
            fetch(`/admin/equipment/update/${this.selectedEquipment.id}`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
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
                        } catch (e) {}
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
        deleteEquipment(id) {
            this.ensureSwal().then(() => {
                window.Swal.fire({
                    title: "ลบรายการ?",
                    text: "การกระทำนี้ไม่สามารถย้อนกลับได้",
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "ลบ",
                    cancelButtonText: "ยกเลิก",
                    confirmButtonColor: "#ef4444",
                }).then((result) => {
                    if (result.isConfirmed) {
                        fetch(`/admin/equipment/destroy/${id}`, {
                            method: "DELETE",
                            headers: {
                                "X-CSRF-TOKEN": document.querySelector(
                                    'meta[name="csrf-token"]'
                                ).content,
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
                                    (e) => e.id !== id
                                );
                                window.Swal.fire({
                                    title: "ลบแล้ว",
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
        createEquipment() {
            const formData = new FormData();
            formData.append("name", this.createModal.form.name || "");
            formData.append(
                "description",
                this.createModal.form.description || ""
            );
            formData.append(
                "categories_id",
                this.createModal.form.categories_id || ""
            );
            formData.append(
                "status",
                this.createModal.form.status || "available"
            );
            if (this.createModal.imageFile) {
                formData.append("image", this.createModal.imageFile);
            }

            fetch(`/admin/equipment/store`, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": document.querySelector(
                        'meta[name="csrf-token"]'
                    ).content,
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
                        } catch (e) {}
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
                            icon: "success",
                            timer: 1200,
                            showConfirmButton: false,
                        });
                    });
                })
                .catch((err) => {
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
