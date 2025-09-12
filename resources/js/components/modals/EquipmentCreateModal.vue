<template>
    <div v-if="isOpen" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-40">
        <div class="bg-white rounded-lg shadow-lg w-1/3 p-6">
            <h3 class="text-lg font-semibold mb-4">เพิ่มอุปกรณ์ใหม่</h3>
            <form @submit.prevent="onCreate">
                <!--หมายเลขครุภัณฑ์-->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-1">หมายเลขครุภัณฑ์</label>
                    <input required type="text" v-model="form.code"
                        class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
                <!-- Name -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-1">ชื่ออุปกรณ์</label>
                    <input required type="text" v-model="form.name"
                        class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500" />
                </div>
                <!-- Category -->
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
                <!-- Description -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-1">รายละเอียด</label>
                    <textarea v-model="form.description"
                        class="w-full h-24 border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
                        placeholder="กรอกรายละเอียดอุปกรณ์"></textarea>
                </div>
                <!-- Status -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-1">สถานะ</label>
                    <select required v-model="form.status"
                        class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option v-for="s in statuses" :key="s" :value="s">
                            {{ capitalize(s) }}
                        </option>
                    </select>
                </div>
                <!-- Image -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-1">รูปภาพ</label>
                    <input required type="file" accept="image/*" @change="onImageChange" />
                </div>
                <div class="flex justify-end space-x-2">
                    <button type="button" @click="$emit('cancel')"
                        class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400">
                        Cancel
                    </button>
                    <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">
                        Create
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
        categories: Array,
        statuses: Array
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
            imageFile: null
        };
    },
    watch: {
        isOpen(newVal) {
            if (newVal) {
                // Reset form when modal opens
                this.form = {
                    code: "",
                    name: "",
                    description: "",
                    categories_id: "",
                    status: "available"
                };
                this.imageFile = null;
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
        onCreate() {
            // Optional: simple frontend validation
            if (!this.form.code) {
                alert("กรุณากรอกหมายเลขครุภัณฑ์");
                return;
            }

            // Emit the form data + image file to parent
            this.$emit('create', {
                ...this.form,      // code, name, description, categories_id, status
                imageFile: this.imageFile  // the selected image
            });
        },
    }
};
</script>
