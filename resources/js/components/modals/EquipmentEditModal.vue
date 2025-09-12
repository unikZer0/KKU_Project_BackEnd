<template>
    <div v-if="isOpen" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-40">
        <div class="bg-white rounded-lg shadow-lg w-1/3 p-6">
            <h3 class="text-lg font-semibold mb-4">แก้ไขอุปกรณ์</h3>
            <form @submit.prevent="onSave">
                <!-- Code -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-1">หมายเลขครุภัณฑ์</label>
                    <input type="text" v-model="equipment.code"
                        class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
                <!-- Name -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-1">ชื่ออุปกรณ์</label>
                    <input type="text" v-model="equipment.name"
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
                    <div v-if="equipment.photo_path" class="mt-2">
                        <p class="text-sm text-gray-600">รูปปัจจุบัน:</p>
                        <img :src="equipment.photo_path" alt="Current Image" class="w-24 h-24 object-cover rounded" />
                    </div>
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" @click="$emit('cancel')"
                        class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
                        Save
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
            imageFile: null
        };
    },
    watch: {
        equipment: {
            immediate: true,
            handler(newVal) {
                this.selectedCategoryId = newVal?.category?.id || null;
            }
        }
    },
    methods: {
        capitalize(str) {
            if (!str) return "";
            return str.charAt(0).toUpperCase() + str.slice(1);
        },
        onImageChange(event) {
            const files = event.target.files;
            this.imageFile = files && files[0] ? files[0] : null;
            this.$emit('image-change', this.imageFile);
        },
        onSave() {
            if (!this.equipment.code) {
                alert("กรุณากรอกหมายเลขครุภัณฑ์");
                return;
            }

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