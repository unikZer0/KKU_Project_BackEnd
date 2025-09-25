<template>
    <div v-if="isOpen" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">อัปเดตหลายรายการ</h3>
                    <button @click="$emit('cancel')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <div class="mb-4 p-3 bg-blue-50 rounded-md">
                    <p class="text-sm text-blue-800">
                        คุณกำลังจะอัปเดต <strong>{{ selectedCount }}</strong> รายการ
                    </p>
                </div>

                <form @submit.prevent="handleSubmit" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">สถานะใหม่ *</label>
                        <select v-model="form.status" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">เลือกสถานะ</option>
                            <option value="available">พร้อมใช้งาน (Available)</option>
                            <option value="unavailable">ไม่พร้อมใช้งาน (Unavailable)</option>
                            <option value="maintenance">อยู่ระหว่างการซ่อม (Maintenance)</option>
                            <option value="retired">ปลดประจำการ (Retired)</option>
                        </select>
                        <p v-if="errors.status" class="text-red-500 text-xs mt-1">{{ errors.status }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">สภาพใหม่ (ไม่บังคับ)</label>
                        <select v-model="form.condition" 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">ไม่เปลี่ยนแปลง</option>
                            <option value="Good">ดี (Good)</option>
                            <option value="Fair">ปานกลาง (Fair)</option>
                            <option value="Poor">แย่ (Poor)</option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">หากไม่เลือก จะไม่เปลี่ยนแปลงสภาพปัจจุบัน</p>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" @click="$emit('cancel')"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            ยกเลิก
                        </button>
                        <button type="submit" :disabled="loading"
                                class="px-4 py-2 text-sm font-medium text-white bg-green-600 border border-transparent rounded-md hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 disabled:opacity-50">
                            <span v-if="loading">กำลังอัปเดต...</span>
                            <span v-else>อัปเดต {{ selectedCount }} รายการ</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "BulkUpdateModal",
    props: {
        isOpen: {
            type: Boolean,
            default: false,
        },
        selectedCount: {
            type: Number,
            default: 0,
        },
    },
    data() {
        return {
            form: {
                status: '',
                condition: '',
            },
            errors: {},
            loading: false,
        };
    },
    methods: {
        handleSubmit() {
            this.errors = {};
            this.loading = true;

            // Basic validation
            if (!this.form.status) {
                this.errors.status = 'กรุณาเลือกสถานะใหม่';
                this.loading = false;
                return;
            }

            this.$emit('update', { ...this.form });
        },
        resetForm() {
            this.form = {
                status: '',
                condition: '',
            };
            this.errors = {};
            this.loading = false;
        },
    },
    watch: {
        isOpen(newVal) {
            if (!newVal) {
                this.resetForm();
            }
        },
    },
};
</script>
