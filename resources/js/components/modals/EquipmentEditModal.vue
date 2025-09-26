<template>
    <div
        v-if="isOpen"
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-40"
        @keydown.esc.prevent="$emit('cancel')"
        tabindex="0"
        @click.self="$emit('cancel')"
        aria-modal="true"
        role="dialog"
    >
        <div class="bg-white rounded-lg shadow-lg w-11/12 sm:w-4/5 md:w-3/4 lg:w-2/3 xl:w-1/2 p-6 max-h-[90vh] overflow-y-auto">
            <h3 class="text-lg font-semibold mb-4">แก้ไขอุปกรณ์</h3>
            <form @submit.prevent="onSave" novalidate v-if="form.id">
                <!-- Basic Equipment Information -->
                <div class="mb-6">
                    <h4 class="text-md font-semibold mb-3 text-gray-800 border-b pb-2">ข้อมูลอุปกรณ์พื้นฐาน</h4>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-1">หมายเลขครุภัณฑ์</label>
                            <input required type="text" v-model.trim="form.code"
                                class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-1">ชื่ออุปกรณ์</label>
                            <input required type="text" v-model.trim="form.name"
                                class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>
                    </div>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-1">หมวดหมู่</label>
                            <select required v-model="form.category_id"
                                class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                                <option value="" disabled>เลือกหมวดหมู่</option>
                                <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                                    {{ cat.name }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-1">แบรนด์</label>
                            <input type="text" v-model.trim="form.brand"
                                class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>
                    </div>
                    
                    <div class="mt-4">
                        <label class="block text-gray-700 font-semibold mb-1">รุ่น</label>
                        <input type="text" v-model.trim="form.model"
                            class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                    </div>
                    
                    <div class="mt-4">
                        <label class="block text-gray-700 font-semibold mb-1">รายละเอียด</label>
                        <textarea v-model.trim="form.description"
                            class="w-full h-24 border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                            placeholder="กรอกรายละเอียดอุปกรณ์"></textarea>
                    </div>
                </div>

                <!-- Equipment Items -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="text-md font-semibold text-gray-800">รายการอุปกรณ์</h4>
                        <div class="flex items-center space-x-2">
                            <input type="text" v-model="itemSearch" 
                                class="px-3 py-1 text-sm border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="ค้นหารายการอุปกรณ์..." />
                            <button type="button" @click="addItem"
                                class="px-3 py-1 bg-green-500 text-white rounded-md hover:bg-green-600 text-sm">
                                + เพิ่มรายการ
                            </button>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div v-for="(item, index) in filteredItems" :key="index" 
                             class="grid grid-cols-1 md:grid-cols-5 gap-3 p-3 border rounded-lg bg-gray-50">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">หมายเลขซีเรียล</label>
                                <input type="text" v-model.trim="item.serial_number"
                                    class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm"
                                    :placeholder="`${form.code}-${String(index + 1).padStart(3, '0')}`" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">สภาพ</label>
                                <select v-model="item.condition"
                                    class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                    <option value="Good">ดี</option>
                                    <option value="Fair">พอใช้</option>
                                    <option value="Poor">ชำรุด</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">สถานะ</label>
                                <select v-model="item.status"
                                    class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                                    <option value="available">พร้อมใช้งาน</option>
                                    <option value="unavailable">ไม่พร้อมใช้งาน</option>
                                    <option value="maintenance">ซ่อมบำรุง</option>
                                </select>
                            </div>
                            <div class="flex items-end">
                                <button type="button" @click="showItemAccessories(item, index)"
                                    class="w-full px-3 py-2 bg-blue-500 text-white rounded-md hover:bg-blue-600 text-sm flex items-center justify-center gap-1">
                                    <span>อุปกรณ์เสริม</span>
                                    <span class="bg-blue-300 text-blue-800 px-1 rounded-full text-xs">
                                        {{ getItemAccessoriesCount(item.id || index) }}
                                    </span>
                                </button>
                            </div>
                            <div class="flex items-end">
                                <button type="button" @click="deleteItem(item, index)"
                                    class="w-full px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 text-sm">
                                    ลบรายการ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Specifications -->
                <div class="mb-6">
                    <div class="flex justify-between items-center mb-3">
                        <h4 class="text-md font-semibold text-gray-800">ข้อมูลจำเพาะ</h4>
                        <div class="flex items-center space-x-2">
                            <input type="text" v-model="specificationSearch" 
                                class="px-3 py-1 text-sm border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                placeholder="ค้นหาข้อมูลจำเพาะ..." />
                            <button type="button" @click="addSpecification"
                                class="px-3 py-1 bg-green-500 text-white rounded-md hover:bg-green-600 text-sm">
                                + เพิ่ม
                            </button>
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div v-for="(spec, index) in filteredSpecifications" :key="index" 
                             class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-3 p-3 border rounded-lg bg-gray-50">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ชื่อข้อมูล</label>
                                <input type="text" v-model.trim="spec.spec_key"
                                    class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ค่า</label>
                                <input type="text" v-model.trim="spec.spec_value_text"
                                    class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" />
                            </div>
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1">ค่าตัวเลข</label>
                                <input type="number" step="0.01" v-model.number="spec.spec_value_number"
                                    class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" />
                            </div>
                            <div class="flex items-end">
                                <button type="button" @click="removeSpecification(index)"
                                    class="w-full px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 text-sm">
                                    ลบ
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Images -->
                <div class="mb-6">
                    <h4 class="text-md font-semibold mb-3 text-gray-800 border-b pb-2">รูปภาพ</h4>
                    
                    <!-- Existing Images -->
                    <div v-if="existingImages.length > 0" class="mb-4">
                        <label class="block text-sm text-gray-600 mb-2">รูปภาพปัจจุบัน</label>
                        <div class="flex flex-wrap gap-2">
                            <div v-for="image in existingImages" :key="image.id" class="relative group">
                                <img :src="image.url" alt="Existing image" 
                                     class="w-24 h-24 object-cover rounded border-2 transition-all duration-200"
                                     :class="{ 
                                         'opacity-30': imagesToDelete.includes(image.url),
                                         'border-blue-500 shadow-lg': selectedMainIdentifier === image.url && !imagesToDelete.includes(image.url),
                                         'border-gray-200': selectedMainIdentifier !== image.url
                                     }" />
                                <div v-if="imagesToDelete.includes(image.url)" class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 rounded">
                                    <span class="text-white font-bold text-sm">ลบ</span>
                                </div>
                                <div v-if="!imagesToDelete.includes(image.url)" class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 flex items-center justify-center gap-2 transition-opacity">
                                    <button type="button" @click="setAsMain(image.url)" class="w-8 h-8 flex items-center justify-center bg-white/80 rounded-full text-lg hover:bg-white" title="ตั้งเป็นภาพหลัก">★</button>
                                    <button type="button" @click="toggleDeletion(image)" class="w-8 h-8 flex items-center justify-center bg-white/80 rounded-full text-lg hover:bg-white" title="ลบรูปภาพ">×</button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- New Images -->
                    <div>
                        <label class="block text-sm text-gray-600 mb-2">เพิ่มรูปภาพใหม่</label>
                        <input type="file" accept="image/*" multiple @change="onImageChange" 
                               class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" 
                               :disabled="processingImages"/>
                        <p v-if="processingImages" class="text-sm text-blue-600 mt-1">กำลังประมวลผลรูปภาพ...</p>
                        <p v-if="imageError" class="text-sm text-red-600 mt-1">{{ imageError }}</p>

                        <div v-if="newImagePreviewUrls.length > 0" class="mt-3 flex flex-wrap gap-2">
                            <div v-for="(url, index) in newImagePreviewUrls" :key="index" class="relative group">
                                <img :src="url" alt="New preview" 
                                     class="w-24 h-24 object-cover rounded border-2"
                                     :class="selectedMainIdentifier === newImageFiles[index].name ? 'border-blue-500 shadow-lg' : 'border-gray-200'"/>
                                <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 flex items-center justify-center gap-2 transition-opacity">
                                    <button type="button" @click="setAsMain(newImageFiles[index].name)" class="w-8 h-8 flex items-center justify-center bg-white/80 rounded-full text-lg hover:bg-white" title="ตั้งเป็นภาพหลัก">★</button>
                                    <button type="button" @click="removeNewImage(index)" class="w-8 h-8 flex items-center justify-center bg-white/80 rounded-full text-lg hover:bg-white" title="ลบรูปภาพ">×</button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex justify-end space-x-2">
                    <button type="button" @click="$emit('cancel')"
                        class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400 disabled:opacity-60"
                        :disabled="submitting || processingImages">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-60"
                        :disabled="submitting || processingImages || !isValid">
                        <span v-if="submitting">Saving...</span>
                        <span v-else>Save Changes</span>
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Item Accessories Modal -->
    <div v-if="showAccessoriesModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50" 
         style="z-index: 9998;"
         @click.self="closeAccessoriesModal" @keydown.esc.prevent="closeAccessoriesModal" tabindex="0" role="dialog" aria-modal="true">
        <div class="bg-white rounded-lg shadow-lg w-11/12 sm:w-4/5 md:w-3/4 lg:w-2/3 xl:w-1/2 p-6 max-h-[90vh] overflow-y-auto">
            <h3 class="text-lg font-semibold mb-4">
                อุปกรณ์เสริม - {{ selectedItem?.serial_number || `${form.code}-${selectedItemIndex + 1}` }}
            </h3>
            
            <div class="flex justify-between items-center mb-4">
                <input type="text" v-model="accessorySearch" 
                    class="px-3 py-2 border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="ค้นหาอุปกรณ์เสริม..." />
                <button type="button" @click="addAccessoryToItem"
                    class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600">
                    + เพิ่มอุปกรณ์เสริม
                </button>
            </div>

            <div class="space-y-3">
                <div v-for="(accessory, index) in filteredItemAccessories" :key="index" 
                     class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-3 p-3 border rounded-lg bg-gray-50">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">ชื่ออุปกรณ์เสริม</label>
                        <input type="text" v-model.trim="accessory.name"
                            class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">รายละเอียด</label>
                        <input type="text" v-model.trim="accessory.description"
                            class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">หมายเลขซีเรียล</label>
                        <input type="text" v-model.trim="accessory.serial_number"
                            class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm" />
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">สภาพ</label>
                        <select v-model="accessory.condition"
                            class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">
                            <option value="Good">ดี</option>
                            <option value="Fair">พอใช้</option>
                            <option value="Poor">ชำรุด</option>
                        </select>
                    </div>
                    <div class="flex items-end gap-2">
                        <button type="button" @click="moveAccessory(accessory, index)"
                            class="flex-1 px-3 py-2 bg-yellow-500 text-white rounded-md hover:bg-yellow-600 text-sm">
                            ย้าย
                        </button>
                        <button type="button" @click="removeAccessory(index)"
                            class="flex-1 px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 text-sm">
                            ลบ
                        </button>
                    </div>
                </div>
                <div v-if="filteredItemAccessories.length === 0" class="text-center py-8 text-gray-500">
                    ไม่มีอุปกรณ์เสริมสำหรับรายการนี้
                </div>
            </div>

            <div class="flex justify-end space-x-2 mt-6">
                <button type="button" @click="closeAccessoriesModal"
                    class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">
                    ปิด
                </button>
            </div>
        </div>
    </div>

    <!-- Move Accessory Modal -->
    <div v-if="showMoveModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50" 
         style="z-index: 9999;"
         @click.self="closeMoveModal" @keydown.esc.prevent="closeMoveModal" tabindex="0" role="dialog" aria-modal="true">
        <div class="bg-white rounded-lg shadow-lg w-96 p-6">
            <h3 class="text-lg font-semibold mb-4">ย้ายอุปกรณ์เสริม</h3>
            <p class="mb-4">เลือกรายการอุปกรณ์ที่ต้องการย้าย "{{ accessoryToMove?.name }}" ไป</p>
            
            <select v-model="targetItemId" class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 mb-4">
                <option value="">-- เลือกรายการอุปกรณ์ --</option>
                <option v-for="item in form.items" :key="item.id || form.items.indexOf(item)" :value="item.id || form.items.indexOf(item)">
                    {{ item.serial_number || `${form.code}-${String(form.items.indexOf(item) + 1).padStart(3, '0')}` }}
                </option>
            </select>

            <div class="flex justify-end space-x-2">
                <button type="button" @click="closeMoveModal"
                    class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">
                    ยกเลิก
                </button>
                <button type="button" @click="confirmMoveAccessory"
                    class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
                    ย้าย
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "EquipmentEditModal",
    props: {
        isOpen: Boolean,
        equipment: Object,
        categories: {
            type: Array,
            default: () => []
        },
        statuses: {
            type: Array,
            default: () => ["available", "retired", "maintenance"]
        }
    },
    data() {
        return {
            form: {
                id: null,
                code: "",
                name: "",
                description: "",
                category_id: "",
                brand: "",
                model: "",
                items: [],
                accessories: [],
                specifications: []
            },
            existingImages: [],
            imagesToDelete: [], 
            newImageFiles: [],
            newImagePreviewUrls: [],
            imageError: "",
            submitting: false,
            processingImages: false,
            selectedMainIdentifier: null,
            itemSearch: "",
            accessorySearch: "",
            specificationSearch: "",
            showAccessoriesModal: false,
            selectedItem: null,
            selectedItemIndex: -1,
            showMoveModal: false,
            accessoryToMove: null,
            accessoryToMoveIndex: -1,
            targetItemId: "",
            deletedItems: [], // Track items that were deleted
        };
    },
    watch: {
        isOpen(newVal) {
            if (newVal && this.equipment) {
                this.setupForm();
            }
        }
    },
    computed: {
        isValid() {
            return !!(this.form.id && this.form.code && this.form.name && this.form.category_id);
        },
        filteredItems() {
            if (!this.itemSearch) return this.form.items;
            return this.form.items.filter(item => 
                (item.serial_number && item.serial_number.toLowerCase().includes(this.itemSearch.toLowerCase())) ||
                (item.condition && item.condition.toLowerCase().includes(this.itemSearch.toLowerCase())) ||
                (item.status && item.status.toLowerCase().includes(this.itemSearch.toLowerCase()))
            );
        },
        filteredItemAccessories() {
            if (!this.selectedItem) return [];
            const itemAccessories = this.form.accessories.filter(accessory => 
                accessory.equipment_item_id === (this.selectedItem.id || this.selectedItemIndex)
            );
            if (!this.accessorySearch) return itemAccessories;
            return itemAccessories.filter(accessory => 
                accessory.name.toLowerCase().includes(this.accessorySearch.toLowerCase()) ||
                (accessory.description && accessory.description.toLowerCase().includes(this.accessorySearch.toLowerCase())) ||
                (accessory.serial_number && accessory.serial_number.toLowerCase().includes(this.accessorySearch.toLowerCase()))
            );
        },
        filteredSpecifications() {
            if (!this.specificationSearch) return this.form.specifications;
            return this.form.specifications.filter(spec => 
                spec.spec_key.toLowerCase().includes(this.specificationSearch.toLowerCase()) ||
                (spec.spec_value_text && spec.spec_value_text.toLowerCase().includes(this.specificationSearch.toLowerCase()))
            );
        }
    },
    methods: {
        capitalize(str) {
            if (!str) return "";
            return str.charAt(0).toUpperCase() + str.slice(1);
        },
        setupForm() {
            // Basic equipment data
            this.form = {
                id: this.equipment.id,
                code: this.equipment.code || "",
                name: this.equipment.name || "",
                description: this.equipment.description || "",
                category_id: this.equipment.category_id || this.equipment.category?.id || "",
                brand: this.equipment.brand || "",
                model: this.equipment.model || "",
                items: this.equipment.items ? [...this.equipment.items] : [],
                accessories: this.equipment.accessories ? [...this.equipment.accessories] : [],
                specifications: this.equipment.specifications ? [...this.equipment.specifications] : []
            };


            // Setup images
            this.existingImages = [];
            this.selectedMainIdentifier = null;

            if (this.equipment.photo_path) {
                try {
                    const photos = JSON.parse(this.equipment.photo_path);
                    if (Array.isArray(photos) && photos.length > 0) {
                        this.existingImages = photos.map(url => ({ id: url, url: url }));
                        this.selectedMainIdentifier = photos[0];
                    }
                } catch (e) {
                    this.existingImages = [{ id: this.equipment.photo_path, url: this.equipment.photo_path }];
                    this.selectedMainIdentifier = this.equipment.photo_path;
                }
            }

            this.imagesToDelete = [];
            this.newImageFiles = [];
            this.newImagePreviewUrls = [];
            this.imageError = "";
            this.submitting = false;
            this.processingImages = false;
            
            // Reset search fields
            this.itemSearch = "";
            this.accessorySearch = "";
            this.specificationSearch = "";
            
            // Reset modal states
            this.showAccessoriesModal = false;
            this.selectedItem = null;
            this.selectedItemIndex = -1;
            this.showMoveModal = false;
            this.accessoryToMove = null;
            this.accessoryToMoveIndex = -1;
            this.targetItemId = "";
            this.deletedItems = []; // Reset deleted items
        },
        getItemAccessoriesCount(itemIdOrIndex) {
            return this.form.accessories.filter(accessory => 
                accessory.equipment_item_id === itemIdOrIndex
            ).length;
        },
        showItemAccessories(item, index) {
            this.selectedItem = item;
            this.selectedItemIndex = index;
            this.showAccessoriesModal = true;
            this.accessorySearch = "";
        },
        closeAccessoriesModal() {
            this.showAccessoriesModal = false;
            this.selectedItem = null;
            this.selectedItemIndex = -1;
            this.accessorySearch = "";
        },
        addAccessoryToItem() {
            this.form.accessories.push({
                name: "",
                description: "",
                serial_number: "",
                equipment_item_id: this.selectedItem.id || this.selectedItemIndex,
                condition: "Good",
                status: "available"
            });
        },
        removeAccessory(index) {
            this.form.accessories.splice(index, 1);
        },
        moveAccessory(accessory, index) {
            this.accessoryToMove = accessory;
            this.accessoryToMoveIndex = index;
            this.showMoveModal = true;
            this.targetItemId = "";
        },
        closeMoveModal() {
            this.showMoveModal = false;
            this.accessoryToMove = null;
            this.accessoryToMoveIndex = -1;
            this.targetItemId = "";
        },
        confirmMoveAccessory() {
            if (this.targetItemId && this.accessoryToMove) {
                this.accessoryToMove.equipment_item_id = this.targetItemId;
                this.closeMoveModal();
            }
        },
        addSpecification() {
            this.form.specifications.push({
                spec_key: "",
                spec_value_text: "",
                spec_value_number: null,
                spec_value_bool: null
            });
        },
        removeSpecification(index) {
            this.form.specifications.splice(index, 1);
        },
        setAsMain(identifier) {
            this.selectedMainIdentifier = identifier;
        },
        toggleDeletion(image) {
            const imageUrl = image.url;
            const index = this.imagesToDelete.indexOf(imageUrl);
            if (index > -1) {
                this.imagesToDelete.splice(index, 1);
            } else {
                this.imagesToDelete.push(imageUrl);
                if (this.selectedMainIdentifier === imageUrl) {
                    this.selectedMainIdentifier = null;
                }
            }
        },
        removeNewImage(index) {
            const removedFile = this.newImageFiles[index];
            this.newImageFiles.splice(index, 1);
            this.newImagePreviewUrls.splice(index, 1);
            
            if (this.selectedMainIdentifier === removedFile.name) {
                this.selectedMainIdentifier = null;
            }
        },
        async onImageChange(event) {
            const files = event.target.files;
            if (!files || files.length === 0) return;
            this.imageError = "";
            this.processingImages = true;
            const resizePromises = [];
            for (const file of files) {
                if (file.size > 5 * 1024 * 1024) {
                    this.imageError = `ไฟล์ '${file.name}' ใหญ่เกินไป (จำกัด 5MB)`;
                    this.processingImages = false;
                    return;
                }
                resizePromises.push(this.resizeImage(file));
            }
            try {
                const resizedImages = await Promise.all(resizePromises);
                resizedImages.forEach(({ blob, dataUrl, originalName }) => {
                    const newFile = new File([blob], originalName, { type: blob.type });
                    this.newImageFiles.push(newFile);
                    this.newImagePreviewUrls.push(dataUrl);
                });
            } catch (error) {
                this.imageError = "เกิดข้อผิดพลาดขณะประมวลผลรูปภาพ";
            } finally {
                this.processingImages = false;
                event.target.value = null;
            }
        },
        resizeImage(file, { maxWidth = 1280, quality = 0.85 } = {}) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.readAsDataURL(file);
                reader.onload = (e) => {
                    const img = new Image();
                    img.src = e.target.result;
                    img.onload = () => {
                        const canvas = document.createElement("canvas");
                        const ctx = canvas.getContext("2d");
                        const ratio = Math.min(maxWidth / img.width, 1);
                        canvas.width = img.width * ratio;
                        canvas.height = img.height * ratio;
                        ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
                        canvas.toBlob(
                            (blob) => {
                                if (!blob) return reject(new Error("Canvas to Blob failed."));
                                const dataUrl = canvas.toDataURL("image/jpeg", quality);
                                resolve({ blob, dataUrl, originalName: file.name });
                            },
                            "image/jpeg",
                            quality
                        );
                    };
                    img.onerror = reject;
                };
                reader.onerror = reject;
            });
        },
        onSave() {
            if (this.submitting || !this.isValid) return;
            this.submitting = true;
            
            const equipmentData = {
                ...this.form,
                newImageFiles: this.newImageFiles,
                imagesToDelete: this.imagesToDelete,
                selectedMainIdentifier: this.selectedMainIdentifier,
                deletedItems: this.deletedItems, // Include deleted items
            };
            
            this.$emit('save', equipmentData);
        },
        addItem() {
            // Add a new item to the form
            this.form.items.push({
                serial_number: "",
                condition: "Good",
                status: "available"
            });
        },
        deleteItem(item, index) {
            // Use SweetAlert for confirmation
            if (window.Swal) {
                window.Swal.fire({
                    title: "ลบรายการอุปกรณ์?",
                    text: `คุณกำลังจะลบรายการ: ${item.serial_number || `${this.form.code}-${String(index + 1).padStart(3, '0')}`}`,
                    icon: "warning",
                    showCancelButton: true,
                    confirmButtonText: "ลบ",
                    cancelButtonText: "ยกเลิก",
                    confirmButtonColor: "#ef4444",
                }).then((result) => {
                    if (result.isConfirmed) {
                        // If this is an existing item (has an ID), add it to deleted items
                        if (item.id) {
                            this.deletedItems.push(item.id);
                        }
                        
                        // Remove the item from the form
                        this.form.items.splice(index, 1);
                        
                        // Also remove any accessories associated with this item
                        this.form.accessories = this.form.accessories.filter(accessory => 
                            accessory.equipment_item_id !== (item.id || index)
                        );
                        
                        // Show success message
                        window.Swal.fire({
                            title: "ลบแล้ว",
                            text: "รายการอุปกรณ์ถูกลบเรียบร้อย",
                            icon: "success",
                            timer: 1200,
                            showConfirmButton: false,
                        });
                    }
                });
            } else {
                // Fallback to confirm if SweetAlert is not available
                if (confirm(`คุณต้องการลบรายการ: ${item.serial_number || `${this.form.code}-${String(index + 1).padStart(3, '0')}`}?`)) {
                    // If this is an existing item (has an ID), add it to deleted items
                    if (item.id) {
                        this.deletedItems.push(item.id);
                    }
                    
                    this.form.items.splice(index, 1);
                    this.form.accessories = this.form.accessories.filter(accessory => 
                        accessory.equipment_item_id !== (item.id || index)
                    );
                }
            }
        },
    }
};
</script>