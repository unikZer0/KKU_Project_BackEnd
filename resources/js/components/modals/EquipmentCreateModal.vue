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
        <div class="bg-white rounded-lg shadow-lg w-11/12 sm:w-2/3 md:w-1/2 lg:w-1/3 p-6">
            <h3 class="text-lg font-semibold mb-4">เพิ่มอุปกรณ์ใหม่</h3>
            <form @submit.prevent="onCreate" novalidate>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-1">หมายเลขครุภัณฑ์</label>
                    <input required type="text" v-model.trim="form.code"
                        class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-1">ชื่ออุปกรณ์</label>
                    <input required type="text" v-model.trim="form.name"
                        class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-1">หมวดหมู่</label>
                    <select required v-model="form.categories_id"
                        class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="" disabled>เลือกหมวดหมู่</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                            {{ cat.name }}
                        </option>
                    </select>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-1">รายละเอียด</label>
                    <textarea v-model.trim="form.description"
                        class="w-full h-24 border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                        placeholder="กรอกรายละเอียดอุปกรณ์"></textarea>
                </div>
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-1">สถานะ</label>
                    <select required v-model="form.status"
                        class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option v-for="s in statuses" :key="s" :value="s">
                            {{ capitalize(s) }}
                        </option>
                    </select>
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
                categories_id: "",
                status: "available"
            },
            imageFiles: [],
            imagePreviewUrls: [],
            imageError: "",
            submitting: false,
            processingImages: false,
            selectedProfileImage: null, // Index of selected image as profile
        };
    },
    watch: {
        isOpen(newVal) {
            if (newVal) {
                // Resetting the form when the modal opens
                this.form = { code: "", name: "", description: "", categories_id: "", status: "available" };
                this.imageFiles = [];
                this.imagePreviewUrls = [];
                this.imageError = "";
                this.submitting = false;
                this.processingImages = false;
                this.selectedProfileImage = null;
            }
        }
    },
    computed: {
        isValid() {
            return !!(this.form.code && this.form.name && this.form.categories_id && this.form.status && this.imageFiles.length > 0 && !this.imageError);
        }
    },
    methods: {
        capitalize(str) {
            if (!str) return "";
            return str.charAt(0).toUpperCase() + str.slice(1);
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
            this.$emit('create', {
                ...this.form,
                imageFiles: this.imageFiles,
                selectedProfileImage: this.selectedProfileImage
            });
        },
    }
};
</script>
