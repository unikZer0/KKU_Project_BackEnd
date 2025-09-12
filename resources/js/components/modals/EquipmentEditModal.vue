<template>
    <div v-if="isOpen" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-40"
        @click.self="$emit('cancel')" @keydown.esc.prevent="$emit('cancel')" tabindex="0" role="dialog"
        aria-modal="true">
        <div class="bg-white rounded-lg shadow-lg w-11/12 sm:w-2/3 md:w-1/2 lg:w-1/3 p-6">
            <h3 class="text-lg font-semibold mb-4">แก้ไขอุปกรณ์</h3>
            <form @submit.prevent="onSave" novalidate>
                <!-- Code -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-1">หมายเลขครุภัณฑ์</label>
                    <input type="text" v-model.trim="equipment.code"
                        class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
                <!-- Name -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-1">ชื่ออุปกรณ์</label>
                    <input type="text" v-model.trim="equipment.name"
                        class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
                <!-- Category -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-1">หมวดหมู่</label>
                    <select v-model="selectedCategoryId"
                        class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">
                            {{ cat.name }}
                        </option>
                    </select>
                </div>
                <!-- Description -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-1">รายละเอียด</label>
                    <textarea v-model="equipment.description"
                        class="w-full h-24 border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                        placeholder="กรอกรายละเอียดอุปกรณ์"></textarea>
                </div>
                <!-- Status -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-1">สถานะ</label>
                    <select v-model="equipment.status"
                        class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option v-for="s in statuses" :key="s" :value="s">
                            {{ capitalize(s) }}
                        </option>
                    </select>
                </div>
                <!-- Image -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-1">รูปภาพ</label>
                    <input type="file" accept="image/*" @change="onImageChange" />
                    <p v-if="imageError" class="text-sm text-red-600 mt-1">{{ imageError }}</p>
                    <div v-if="imagePreviewUrl || equipment.photo_path" class="mt-2 space-y-2">
                        <p class="text-sm text-gray-600">รูปปัจจุบัน / ตัวอย่าง:</p>
                        <img :src="imagePreviewUrl || equipment.photo_path" alt="Preview"
                            class="w-24 h-24 object-cover rounded" />
                    </div>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" @click="$emit('cancel')"
                        class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400 disabled:opacity-60"
                        :disabled="submitting">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-60"
                        :disabled="submitting || !canSave">
                        <span v-if="submitting">Saving...</span>
                        <span v-else>Save</span>
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
<script>
export default {
    name: "EquipmentEditModal",
    props: {
        isOpen: Boolean,
        equipment: Object,
        categories: Array,
        statuses: Array
    },
    data() {
        return {
            selectedCategoryId: this.equipment?.category?.id || null,
            imageFile: null,
            imagePreviewUrl: null,
            imageError: "",
            submitting: false
        };
    },
    watch: {
        equipment: {
            immediate: true,
            handler(newVal) {
                this.selectedCategoryId = newVal?.category?.id || null;
                this.imagePreviewUrl = null;
                this.imageError = "";
                this.submitting = false;
            }
        }
    },
    computed: {
        canSave() {
            return !!(this.equipment && this.equipment.code && this.equipment.name && (this.selectedCategoryId || this.equipment.categories_id) && this.equipment.status && !this.imageError);
        }
    },
    methods: {
        capitalize(str) {
            if (!str) return "";
            return str.charAt(0).toUpperCase() + str.slice(1);
        },
        onImageChange(event) {
            const files = event.target.files;
            const file = files && files[0] ? files[0] : null;
            this.imageFile = file;
            this.imagePreviewUrl = null;
            this.imageError = "";

            if (!file) {
                this.$emit('image-change', null);
                return;
            }

            const maxMb = 5;
            const okTypes = ['image/jpeg','image/png','image/webp','image/gif'];
            if (!okTypes.includes(file.type)) {
                this.imageError = "รองรับเฉพาะไฟล์ภาพ (jpg, png, webp, gif)";
                this.imageFile = null;
                this.$emit('image-change', null);
                return;
            }
            if (file.size > maxMb * 1024 * 1024) {
                this.imageError = `ไฟล์ใหญ่เกินไป (จำกัด ${maxMb}MB)`;
                this.imageFile = null;
                this.$emit('image-change', null);
                return;
            }

            this.resizeImage(file, { maxWidth: 1280, maxHeight: 960, quality: 0.85 })
                .then(({ blob, dataUrl }) => {
                    const newFile = new File([blob], this.deriveFileName(file.name), { type: blob.type });
                    this.imageFile = newFile;
                    this.imagePreviewUrl = dataUrl;
                    this.$emit('image-change', this.imageFile);
                })
                .catch(() => {
                    this.imagePreviewUrl = URL.createObjectURL(file);
                    this.$emit('image-change', this.imageFile);
                });
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
                        let { width, height } = img;
                        const ratio = Math.min(maxWidth / width, maxHeight / height, 1);
                        const targetW = Math.round(width * ratio);
                        const targetH = Math.round(height * ratio);

                        const canvas = document.createElement('canvas');
                        canvas.width = targetW;
                        canvas.height = targetH;
                        const ctx = canvas.getContext('2d');
                        ctx.imageSmoothingQuality = 'high';
                        ctx.drawImage(img, 0, 0, targetW, targetH);

                        const wantsPng = file.type === 'image/png';
                        const mime = wantsPng ? 'image/png' : 'image/jpeg';
                        canvas.toBlob(blob => {
                            if (!blob) return reject(new Error('Resize failed'));
                            const dataUrl = canvas.toDataURL(mime, quality);
                            resolve({ blob: new Blob([blob], { type: mime }), dataUrl });
                        }, mime, quality);
                    };
                    img.onerror = reject;
                    img.src = e.target.result;
                };
                reader.onerror = reject;
                reader.readAsDataURL(file);
            });
        },
        onSave() {
            if (this.submitting) return;
            if (!this.equipment.code) {
                alert("กรุณากรอกหมายเลขครุภัณฑ์");
                return;
            }
            this.submitting = true;

            // Ensure status is lowercase to match MySQL ENUM
            this.$emit('save', {
                ...this.equipment,
                categories_id: this.selectedCategoryId,
                status: (this.equipment.status || 'available').toLowerCase(),
                imageFile: this.imageFile
            });
        }
    }
};
</script>
