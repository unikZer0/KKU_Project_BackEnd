<template>
    <!-- Breadcrumb -->
    <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
        <a href="/admin" class="hover:text-gray-700">แดชบอร์ด</a>
        <span>/</span>
        <span class="font-semibold text-gray-900">หน้าจัดการอุปกรณ์</span>
    </nav>

    <div class="bg-white p-6 rounded-lg shadow">
        <div class="relative mb-4">
            <input type="text" v-model="searchQuery" placeholder="Search"
                class="pl-10 pr-3 py-2 text-sm border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
            <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor"
                viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
            </svg>
        </div>

        <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-4">
            <h2 class="text-lg font-semibold">
                อุปกรณ์รวมกันทั้งหมด: {{ filteredEquipments.length }} ชิ้น
            </h2>
            <div class="flex flex-wrap gap-2 items-center relative" ref="filtersWrap">
                <button @click="filtersOpen = !filtersOpen" class="px-3 py-1 border rounded">
                    ตัวกรอง
                    <span class="text-xs text-gray-500 ml-1">
                        {{ filterStatus ? filterStatus : "all" }} ·
                        {{
                            filterCategoryId
                                ? categories.find(
                                    (c) =>
                                        String(c.id) ===
                                        String(filterCategoryId)
                                )?.name || "category"
                                : "all categories"
                        }}
                    </span>
                </button>

                <button v-if="userRole === 'admin'" @click="openCreateModal"
                    class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                    เพิ่มอุปกรณ์ใหม่
                </button>

                <!-- Dropdown panel -->
                <div v-if="filtersOpen" class="absolute right-0 top-10 z-10 bg-white border rounded shadow p-3 w-72">
                    <div class="mb-2">
                        <div class="text-sm font-semibold mb-1">สถานะ</div>
                        <select v-model="filterStatus" class="w-full px-2 py-1 border rounded">
                            <option value="">
                                ทั้งหมด ({{ statusCounts.all }})
                            </option>
                            <option v-for="s in statuses" :key="s" :value="s">
                                {{ capitalize(s) }} ({{ statusCounts[s] || 0 }})
                            </option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <div class="text-sm font-semibold mb-1">หมวดหมู่</div>
                        <select v-model="filterCategoryId" class="w-full px-2 py-1 border rounded">
                            <option value="">
                                ทุกหมวดหมู่ ({{ categoryCounts.all }})
                            </option>
                            <option v-for="c in validCategories" :key="c.id" :value="String(c.id)">
                                {{ c.name }} ({{
                                    categoryCounts[String(c.id)] || 0
                                }})
                            </option>
                        </select>
                    </div>
                    <div class="flex justify-between">
                        <button class="px-3 py-1 border rounded" @click="clearFilters">
                            ล้างตัวกรอง
                        </button>
                        <button class="px-3 py-1 bg-gray-900 text-white rounded" @click="filtersOpen = false">
                            เสร็จสิ้น
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <table class="min-w-full text-sm">
            <thead class="bg-gray-50 border-b">
                <tr>
                    <th class="px-4 py-2 text-left">รูป</th>
                    <th class="px-4 py-2 text-left" @click="setSort('id')">ID
                        <span v-if="sortKey === 'name'">{{ sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    </th>
                    <th class="px-4 py-2 text-left cursor-pointer" @click="setSort('name')">
                        ชื่ออุปกรณ์
                        <span v-if="sortKey === 'name'">{{ sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    </th>
                    <th class=" px-4 py-2 text-left" @click="setSort('description')">รายละเอียด
                        <span v-if="sortKey === 'description'">{{ sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    </th>
                    <th class="px-4 py-2 text-left" @click="setSort('category')">หมวดหมู่
                        <span v-if="sortKey === 'category'">{{ sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    </th>
                    <th class="px-4 py-2 text-left" @click="setSort('brand')">แบรนด์
                        <span v-if="sortKey === 'brand'">{{ sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    </th>
                    <th class="px-4 py-2 text-left" @click="setSort('model')">รุ่น
                        <span v-if="sortKey === 'model'">{{ sortDirection === 'asc' ? '↑' : '↓' }}</span>
                    </th>
                    <th class="px-4 py-2 text-left">จำนวน</th>
                    <th class="px-4 py-2 text-left" v-if="userRole === 'admin'">
                        แอคชั่น
                    </th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="equipment in paginatedEquipments" :key="equipment.id" class="border-b">
                    <td class="px-4 py-2 flex items-center space-x-2">
                        <img v-if="getFirstPhoto(equipment)" :src="getFirstPhoto(equipment)" alt="Equipment Photo"
                            class="w-8 h-8 object-cover rounded cursor-pointer"
                            @click="openPhotoModal(getFirstPhoto(equipment))" /> NA
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
                        {{ equipment.brand || "N/A" }}
                    </td>
                    <td class="px-4 py-2">
                        {{ equipment.model || "N/A" }}
                    </td>
                    <td class="px-4 py-2 text-center">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium"
                              :class="getAmountClass(equipment.items?.length || 0)">
                            {{ equipment.items?.length || 0 }} ชิ้น
                        </span>
                    </td>
                    <td class="px-4 py-2 space-x-2">
                        <button v-if="userRole === 'admin'" @click="openModal(equipment)"
                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded">
                            แก้ไขข้อมูล
                        </button>
                        <button v-if="userRole === 'admin'" @click="deleteEquipment(equipment)"
                            class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded">
                            ลบรายการ
                        </button>
                    </td>
                </tr>
            </tbody>
        </table>

        <div class="mt-4 flex items-center justify-between">
            <div class="text-sm text-gray-600">
                แสดง {{ pageStart + 1 }} - {{ pageEnd }} จากทั้งหมด
                {{ filteredEquipments.length }} รายการ
            </div>
            <div class="flex items-center space-x-1">
                <button class="px-3 py-1 border rounded disabled:opacity-50" :disabled="currentPage === 1"
                    @click="prevPage">
                    ก่อนหน้า
                </button>
                <button v-for="p in pageCount" :key="p" class="px-3 py-1 border rounded"
                    :class="{ 'bg-blue-600 text-white': currentPage === p }" @click="goToPage(p)">
                    {{ p }}
                </button>
                <button class="px-3 py-1 border rounded disabled:opacity-50"
                    :disabled="currentPage === pageCount || pageCount === 0" @click="nextPage">
                    ถัดไป
                </button>
            </div>
        </div>

        <EquipmentEditModal :isOpen="isOpen" :equipment="selectedEquipment" :categories="categories"
            :statuses="statuses" @cancel="isOpen = false" @save="updateEquipment"
            @image-change="selectedImageFile = $event" />

        <EquipmentCreateModal :isOpen="createModal.isOpen" :categories="categories" :statuses="statuses"
            @cancel="closeCreateModal" @create="createEquipment" />

        <PhotoModal :isOpen="photoModal.isOpen" :url="photoModal.url" @close="closePhotoModal" />
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
            userRole: el?.dataset?.role || "",
            equipments: JSON.parse(el.dataset.equipments || "[]"),
            categories: JSON.parse(el.dataset.categories || "[]"),
            sortKey: "created_at",
            sortDirection: "asc",
            statuses: ["available", "retired", "maintenance"],
            searchQuery: "",
            currentPage: 1,
            pageSize: 15,
            // filters
            filtersOpen: false,
            filterStatus: "",
            filterCategoryId: "",
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
            },
        };
    },
    computed: {
        statusCounts() {
            const counts = { all: (this.equipments || []).length };
            for (const e of this.equipments || []) {
                const k = e?.status || "unknown";
                counts[k] = (counts[k] || 0) + 1;
            }
            return counts;
        },
        categoryCounts() {
            const counts = { all: (this.equipments || []).length };
            for (const e of this.equipments || []) {
                const id = String(e?.categories_id || e?.category?.id || "");
                if (!id) continue;
                counts[id] = (counts[id] || 0) + 1;
            }
            return counts;
        },
        validCategories() {
            return (this.categories || []).filter((c) => c && c.id != null);
        },
        filteredEquipments() {
            const q = (this.searchQuery || "").toLowerCase();
            const catId = this.filterCategoryId
                ? String(this.filterCategoryId)
                : "";
            const status = this.filterStatus;
            let list = (this.equipments || []).filter((e) => {
                const matchesSearch =
                    !q ||
                    String(e?.name || "")
                        .toLowerCase()
                        .includes(q) ||
                    String(e?.category?.name || "")
                        .toLowerCase()
                        .includes(q) ||
                    String(e?.status || "")
                        .toLowerCase()
                        .includes(q) ||
                    String(e?.code || e?.id || "").includes(q);
                const matchesStatus =
                    !status || String(e?.status || "") === status;
                const matchesCategory =
                    !catId ||
                    String(e?.categories_id || e?.category?.id || "") === catId;
                return matchesSearch && matchesStatus && matchesCategory;
            });
            list.sort((a, b) => {
                let x = a[this.sortKey] ?? "";
                let y = b[this.sortKey] ?? "";

                if (this.sortKey === "created_at") {
                    x = new Date(x);
                    y = new Date(y);
                }
                else {
                    x = String(x).toLowerCase();
                    y = String(y).toLowerCase();
                }
                if (x < y) return this.sortDirection === "asc" ? -1 : 1;
                if (x > y) return this.sortDirection === "asc" ? 1 : -1;
                return 0;
            });
            return list;
        },
        pageCount() {
            return (
                Math.ceil(this.filteredEquipments.length / this.pageSize) || 0
            );
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
        getAmountClass(amount) {
            if (amount === 0) {
                return 'bg-red-100 text-red-800';
            } else if (amount <= 2) {
                return 'bg-yellow-100 text-yellow-800';
            } else if (amount <= 5) {
                return 'bg-blue-100 text-blue-800';
            } else {
                return 'bg-green-100 text-green-800';
            }
        },
        statusClass(status) {
            switch (status) {
                case "available":
                    return "bg-green-100 text-green-800";
                case "retired":
                    return "bg-red-100 text-red-800";
                case "maintenance":
                    return "bg-yellow-100 text-yellow-800";
                default:
                    return "bg-gray-100 text-gray-800";
            }
        },
        getFirstPhoto(equipment) {
            if (!equipment.photo_path) return null;
            try {
                const photos = JSON.parse(equipment.photo_path);
                return Array.isArray(photos) && photos.length > 0
                    ? photos[0]
                    : null;
            } catch (e) {
                return equipment.photo_path;
            }
        },
        setSort(key) {
            if (this.sortKey === key) {
                this.sortDirection = this.sortDirection === "asc" ? "desc" : "asc";
            } else {
                this.sortKey = key;
                this.sortDirection = "asc";
            }
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
            const formData = new FormData();
            
            // Basic equipment data
            formData.append("code", payload.code || "");
            formData.append("name", payload.name || "");
            formData.append("description", payload.description || "");
            formData.append("category_id", String(payload.category_id ?? ""));
            formData.append("brand", payload.brand || "");
            formData.append("model", payload.model || "");

            // Equipment items
            if (payload.items && payload.items.length > 0) {
                payload.items.forEach((item, index) => {
                    if (item.id) formData.append(`items[${index}][id]`, item.id);
                    formData.append(`items[${index}][serial_number]`, item.serial_number || "");
                    formData.append(`items[${index}][condition]`, item.condition || "Good");
                    formData.append(`items[${index}][status]`, item.status || "available");
                });
            }

            // Accessories
            if (payload.accessories && payload.accessories.length > 0) {
                payload.accessories.forEach((accessory, index) => {
                    if (accessory.id) formData.append(`accessories[${index}][id]`, accessory.id);
                    formData.append(`accessories[${index}][name]`, accessory.name || "");
                    formData.append(`accessories[${index}][description]`, accessory.description || "");
                    formData.append(`accessories[${index}][serial_number]`, accessory.serial_number || "");
                    formData.append(`accessories[${index}][equipment_item_id]`, accessory.equipment_item_id || "");
                    formData.append(`accessories[${index}][condition]`, accessory.condition || "Good");
                    formData.append(`accessories[${index}][status]`, accessory.status || "available");
                });
            }

            // Specifications
            if (payload.specifications && payload.specifications.length > 0) {
                payload.specifications.forEach((spec, index) => {
                    if (spec.id) formData.append(`specifications[${index}][id]`, spec.id);
                    formData.append(`specifications[${index}][spec_key]`, spec.spec_key || "");
                    formData.append(`specifications[${index}][spec_value_text]`, spec.spec_value_text || "");
                    formData.append(`specifications[${index}][spec_value_number]`, spec.spec_value_number || "");
                    formData.append(`specifications[${index}][spec_value_bool]`, spec.spec_value_bool || "");
                });
            }

            // Images
            if (payload.imagesToDelete && payload.imagesToDelete.length > 0) {
                payload.imagesToDelete.forEach((url) => {
                    formData.append("images_to_delete[]", url);
                });
            }
            if (payload.newImageFiles && payload.newImageFiles.length > 0) {
                payload.newImageFiles.forEach((file) => {
                    formData.append("images[]", file);
                });
            }
            if (payload.selectedMainIdentifier) {
                formData.append(
                    "selected_main_identifier",
                    payload.selectedMainIdentifier
                );
            }

            formData.append("_method", "PUT");
            fetch(`/admin/equipment/update/${payload.id}`, {
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
                        let msg = "Update failed";
                        try {
                            const errorData = await res.json();
                            msg =
                                errorData.message ||
                                JSON.stringify(errorData.errors || errorData);
                        } catch (e) {
                            msg = `HTTP ${res.status}: ${res.statusText}`;
                        }
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
                    this.ensureSwal().then(() => {
                        window.Swal.fire({
                            title: "อัปเดตสำเร็จ",
                            text: `ทำการอัพเดทเรียบร้อย ${payload.name} (ID: ${payload.code})`,
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
                    imageUrl: this.getFirstPhoto(equipment),
                    imageWidth: 120,
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
                                "X-CSRF-TOKEN": document.querySelector(
                                    'meta[name="csrf-token"]'
                                ).content,
                                Accept: "application/json",
                            },
                        })
                            .then(async (res) => {
                                if (!res.ok) {
                                    let msg = "Delete failed";
                                    try {
                                        const j = await res.json();
                                        msg = j.message || JSON.stringify(j);
                                    } catch (e) { }
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
            let errorMessage = message;
            if (typeof message === "object") {
                if (message.message) {
                    errorMessage = message.message;
                } else {
                    errorMessage = JSON.stringify(message);
                }
            }

            if (window.Swal) {
                window.Swal.fire({
                    title: "เกิดข้อผิดพลาด",
                    text: errorMessage,
                    icon: "error",
                });
            } else {
                alert(errorMessage);
            }
        },
        createEquipment(payload) {
            const formData = new FormData();
            
            // Basic equipment data
            formData.append("code", payload.code || "");
            formData.append("name", payload.name || "");
            formData.append("description", payload.description || "");
            formData.append("category_id", payload.category_id || "");
            formData.append("brand", payload.brand || "");
            formData.append("model", payload.model || "");

            // Equipment items
            if (payload.items && payload.items.length > 0) {
                payload.items.forEach((item, index) => {
                    formData.append(`items[${index}][serial_number]`, item.serial_number || "");
                    formData.append(`items[${index}][condition]`, item.condition || "Good");
                    formData.append(`items[${index}][status]`, item.status || "available");
                });
            }

            // Accessories
            if (payload.accessories && payload.accessories.length > 0) {
                payload.accessories.forEach((accessory, index) => {
                    formData.append(`accessories[${index}][name]`, accessory.name || "");
                    formData.append(`accessories[${index}][description]`, accessory.description || "");
                    formData.append(`accessories[${index}][serial_number]`, accessory.serial_number || "");
                    formData.append(`accessories[${index}][condition]`, accessory.condition || "Good");
                    formData.append(`accessories[${index}][status]`, accessory.status || "available");
                });
            }

            // Specifications
            if (payload.specifications && payload.specifications.length > 0) {
                payload.specifications.forEach((spec, index) => {
                    formData.append(`specifications[${index}][spec_key]`, spec.spec_key || "");
                    formData.append(`specifications[${index}][spec_value_text]`, spec.spec_value_text || "");
                    formData.append(`specifications[${index}][spec_value_number]`, spec.spec_value_number || "");
                    formData.append(`specifications[${index}][spec_value_bool]`, spec.spec_value_bool || "");
                });
            }

            // Images
            if (payload.imageFiles && payload.imageFiles.length > 0) {
                for (const file of payload.imageFiles) {
                    formData.append("images[]", file);
                }
            }
            
            if (
                payload.selectedProfileImage !== null &&
                payload.selectedProfileImage !== undefined
            ) {
                formData.append(
                    "selectedProfileImage",
                    payload.selectedProfileImage
                );
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
                        } catch (e) { }
                        throw new Error(msg);
                    }
                    return res.json();
                })
                .then((data) => {
                    let created = data.data;
                    if (!created.category && created.category_id) {
                        const found = this.categories.find(
                            (c) => c.id === created.category_id
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
                            text: `ทำการเพี่มข้อมูลเรียบร้อย ${payload.name} (ID: ${payload.code})`,
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
        this._onClickOutside = (e) => {
            const wrap = this.$refs.filtersWrap;
            if (!wrap) return;
            if (this.filtersOpen && !wrap.contains(e.target))
                this.filtersOpen = false;
        };
        document.addEventListener("click", this._onClickOutside);
    },
    beforeUnmount() {
        document.removeEventListener("click", this._onClickOutside);
    },
};
</script>
