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
            <h3 class="text-lg font-semibold mb-4">เพิ่มอุปกรณ์ใหม่</h3>
            <form @submit.prevent="onCreate" novalidate>
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
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
                        <div>
                            <label class="block text-gray-700 font-semibold mb-1">รุ่น</label>
                            <input type="text" v-model.trim="form.model"
                                class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>
                        <div>
                            <label class="block text-gray-700 font-semibold mb-1">จำนวนอุปกรณ์</label>
                            <input required type="number" min="1" v-model.number="form.itemCount"
                                class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                        </div>
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
                        </div>
                    </div>
                    <div class="space-y-3">
                        <div v-for="(item, index) in filteredItems" :key="index" 
                             class="grid grid-cols-1 md:grid-cols-4 gap-3 p-3 border rounded-lg bg-gray-50">
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
                                        {{ getItemAccessoriesCount(index) }}
                                    </span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>


                <!-- Specifications -->
                <div class="mb-6">
                    <h4 class="text-md font-semibold mb-3 text-gray-800 border-b pb-2">ข้อมูลจำเพาะ</h4>
                    <div class="space-y-3">
                        <div v-for="(spec, index) in form.specifications" :key="index" 
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
                        <button type="button" @click="addSpecification"
                            class="px-4 py-2 bg-green-500 text-white rounded-md hover:bg-green-600 text-sm">
                            + เพิ่มข้อมูลจำเพาะ
                        </button>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-1">รูปภาพ (เลือกได้หลายรูป)</label>
                    <input 
                        required 
                        type="file" 
                        accept="image/*" 
                        multiple 
                        @change="onImageChange" 
                        class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100"
                        :disabled="processingImages"
                    />
                    <p v-if="processingImages" class="text-sm text-blue-600 mt-1">
                        Processing images...
                    </p>
                    <p v-if="imageError" class="text-sm text-red-600 mt-1">{{ imageError }}</p>
                    
                    <div v-if="imagePreviewUrls.length > 0" class="mt-3 flex flex-wrap gap-2">
                        <div v-for="(url, index) in imagePreviewUrls" :key="index" class="relative group">
                            <img 
                                :src="url" 
                                alt="preview" 
                                class="w-24 h-24 object-cover rounded border-2 transition-all duration-200"
                                :class="{
                                    'border-blue-500 shadow-lg': selectedProfileImage === index,
                                    'border-gray-200 hover:border-gray-400': selectedProfileImage !== index
                                }"
                            />
                            
                            <!-- Profile image indicator -->
                            <div
                                v-if="selectedProfileImage === index"
                                class="absolute top-1 left-1 bg-blue-500 text-white text-xs px-1 py-0.5 rounded"
                            >
                                หลัก
                            </div>
                            
                            <!-- Action buttons -->
                            <div class="absolute top-1 right-1 flex gap-1 opacity-0 group-hover:opacity-100 transition-opacity">
                                <button
                                    type="button"
                                    @click="setAsProfile(index)"
                                    class="bg-blue-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs"
                                    title="Set as profile image"
                                >
                                    ★
                                </button>
                                <button
                                    type="button"
                                    @click="removeImage(index)"
                                    class="bg-red-500 text-white rounded-full w-5 h-5 flex items-center justify-center text-xs"
                                    title="Remove image"
                                >
                                    ×
                                </button>
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
                        <span v-else>Create</span>
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
                    <div class="flex items-end">
                        <button type="button" @click="removeAccessory(index)"
                            class="w-full px-3 py-2 bg-red-500 text-white rounded-md hover:bg-red-600 text-sm">
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
</template>

<script>
export default {
    name: "EquipmentCreateModal",
    props: {
        isOpen: Boolean,
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
                code: "",
                name: "",
                description: "",
                category_id: "",
                brand: "",
                model: "",
                itemCount: 1,
                items: [],
                accessories: [],
                specifications: []
            },
            imageFiles: [],
            imagePreviewUrls: [],
            imageError: "",
            submitting: false,
            processingImages: false,
            selectedProfileImage: null, // Index of selected image as profile
            itemSearch: "",
            accessorySearch: "",
            showAccessoriesModal: false,
            selectedItem: null,
            selectedItemIndex: -1,
        };
    },
    watch: {
        isOpen(newVal) {
            if (newVal) {
                // Resetting the form when the modal opens
                this.resetForm();
            }
        },
        'form.itemCount': {
            handler(newVal, oldVal) {
                if (newVal > oldVal) {
                    // Add new items
                    for (let i = this.form.items.length; i < newVal; i++) {
                        this.form.items.push({
                            serial_number: "",
                            condition: "Good",
                            status: "available"
                        });
                    }
                } else if (newVal < oldVal) {
                    // Remove excess items
                    this.form.items = this.form.items.slice(0, newVal);
                }
            },
            immediate: true
        }
    },
    computed: {
        isValid() {
            return !!(this.form.code && this.form.name && this.form.category_id && this.form.itemCount > 0 && this.imageFiles.length > 0 && !this.imageError);
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
                accessory.equipment_item_id === this.selectedItemIndex
            );
            if (!this.accessorySearch) return itemAccessories;
            return itemAccessories.filter(accessory => 
                accessory.name.toLowerCase().includes(this.accessorySearch.toLowerCase()) ||
                (accessory.description && accessory.description.toLowerCase().includes(this.accessorySearch.toLowerCase())) ||
                (accessory.serial_number && accessory.serial_number.toLowerCase().includes(this.accessorySearch.toLowerCase()))
            );
        }
    },
    methods: {
        capitalize(str) {
            if (!str) return "";
            return str.charAt(0).toUpperCase() + str.slice(1);
        },
        resetForm() {
            this.form = {
                code: "",
                name: "",
                description: "",
                category_id: "",
                brand: "",
                model: "",
                itemCount: 1,
                items: [],
                accessories: [],
                specifications: []
            };
            this.imageFiles = [];
            this.imagePreviewUrls = [];
            this.imageError = "";
            this.submitting = false;
            this.processingImages = false;
            this.selectedProfileImage = null;
            this.itemSearch = "";
            this.accessorySearch = "";
            this.showAccessoriesModal = false;
            this.selectedItem = null;
            this.selectedItemIndex = -1;
        },
        getItemAccessoriesCount(itemIndex) {
            return this.form.accessories.filter(accessory => 
                accessory.equipment_item_id === itemIndex
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
                equipment_item_id: this.selectedItemIndex,
                condition: "Good",
                status: "available"
            });
        },
        removeAccessory(index) {
            this.form.accessories.splice(index, 1);
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
        removeImage(index) {
            this.imageFiles.splice(index, 1);
            this.imagePreviewUrls.splice(index, 1);
            
            // Reset profile selection if the selected image was removed
            if (this.selectedProfileImage === index) {
                this.selectedProfileImage = null;
            } else if (this.selectedProfileImage > index) {
                this.selectedProfileImage--;
            }
        },
        setAsProfile(index) {
            this.selectedProfileImage = index;
        },
        async onImageChange(event) {
            const files = event.target.files;
            if (!files || files.length === 0) return;

            this.imageError = "";
            this.processingImages = true;

            const maxMb = 5;
            const okTypes = ['image/jpeg', 'image/png', 'image/webp', 'image/gif'];
            const resizePromises = [];
            
            for (const file of files) {
                if (!okTypes.includes(file.type)) {
                    this.imageError = `File '${file.name}' is not a supported image type.`;
                    this.processingImages = false;
                    event.target.value = null; 
                    return;
                }
                if (file.size > maxMb * 1024 * 1024) {
                    this.imageError = `File '${file.name}' is too large (max ${maxMb}MB).`;
                    this.processingImages = false;
                    event.target.value = null;
                    return;
                }
                resizePromises.push(this.resizeImage(file, { maxWidth: 1280, maxHeight: 960, quality: 0.85 }));
            }

            try {
                const resizedImages = await Promise.all(resizePromises);
                
                resizedImages.forEach(({ blob, dataUrl, originalName }) => {
                    const newFile = new File([blob], this.deriveFileName(originalName), { type: blob.type });
                    this.imageFiles.push(newFile);
                    this.imagePreviewUrls.push(dataUrl);
                });
            } catch (error) {
                console.error("Image processing failed:", error);
                this.imageError = "An error occurred while processing images.";
            } finally {
                this.processingImages = false;
                event.target.value = null;
            }
        },
        deriveFileName(name) {
            const dot = name.lastIndexOf('.');
            const base = dot !== -1 ? name.slice(0, dot) : name;
            return base + '.jpg';
        },
        resizeImage(file, { maxWidth = 1280, maxHeight = 960, quality = 0.85 } = {}) {
            return new Promise((resolve, reject) => {
                const reader = new FileReader();
                reader.onload = e => {
                    const img = new Image();
                    img.onload = () => {
                        const { width, height } = img;
                        const ratio = Math.min(maxWidth / width, maxHeight / height, 1);
                        const targetW = Math.round(width * ratio);
                        const targetH = Math.round(height * ratio);
                        const canvas = document.createElement('canvas');
                        canvas.width = targetW;
                        canvas.height = targetH;
                        const ctx = canvas.getContext('2d');
                        ctx.imageSmoothingQuality = 'high';
                        ctx.drawImage(img, 0, 0, targetW, targetH);
                        
                        const mime = file.type === 'image/png' ? 'image/png' : 'image/jpeg';

                        canvas.toBlob(blob => {
                            if (!blob) return reject(new Error('Resize failed'));
                            const dataUrl = canvas.toDataURL(mime, quality);
                            resolve({ blob, dataUrl, originalName: file.name });
                        }, mime, quality);
                    };
                    img.onerror = reject;
                    img.src = e.target.result;
                };
                reader.onerror = reject;
                reader.readAsDataURL(file);
            });
        },
        onCreate() {
            if (this.submitting || !this.isValid) return;
            this.submitting = true;
            
            // Prepare the data to send
            const equipmentData = {
                code: this.form.code,
                name: this.form.name,
                description: this.form.description,
                category_id: this.form.category_id,
                brand: this.form.brand,
                model: this.form.model,
                items: this.form.items,
                accessories: this.form.accessories,
                specifications: this.form.specifications,
                imageFiles: this.imageFiles,
                selectedProfileImage: this.selectedProfileImage
            };
            
            this.$emit('create', equipmentData);
        },
    }
};
</script>
