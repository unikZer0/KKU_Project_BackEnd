<template>
    <div v-if="isOpen" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <div class="flex items-center justify-between mb-4">
                    <h3 class="text-lg font-medium text-gray-900">เพิ่มรายการอุปกรณ์ใหม่</h3>
                    <button @click="$emit('cancel')" class="text-gray-400 hover:text-gray-600">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>

                <form @submit.prevent="handleSubmit" class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">อุปกรณ์ *</label>
                        <select v-model="form.equipment_id" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">เลือกอุปกรณ์</option>
                            <option v-for="equipment in equipments" :key="equipment.id" :value="equipment.id">
                                {{ equipment.name }} ({{ equipment.code }})
                            </option>
                        </select>
                        <p v-if="errors.equipment_id" class="text-red-500 text-xs mt-1">{{ errors.equipment_id }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">หมายเลขซีเรียล</label>
                        <input type="text" v-model="form.serial_number" 
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="เช่น SN123456">
                        <p v-if="errors.serial_number" class="text-red-500 text-xs mt-1">{{ errors.serial_number }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">สภาพ *</label>
                        <select v-model="form.condition" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">เลือกสภาพ</option>
                            <option value="สภาพดี">สภาพดี</option>
                            <option value="สามารถซ่อมได้">สามารถซ่อมได้</option>
                            <option value="ไม่สามารถซ่อมได้">ไม่สามารถซ่อมได้</option>
                            <option value="พัง">พัง</option>
                            <option value="อุปกรณ์ไม่พร้อมใช้งาน">ไม่พร้อมใช้งาน</option>
                        </select>
                        <p v-if="errors.condition" class="text-red-500 text-xs mt-1">{{ errors.condition }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">สถานะ *</label>
                        <select v-model="form.status" required 
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">เลือกสถานะ</option>
                            <option value="available">พร้อมใช้งาน</option>
                            <option value="unavailable">ไม่พร้อมใช้งาน</option>
                            <option value="maintenance">อยู่ระหว่างการซ่อม</option>
                            <option value="retired">ปลดประจำการ</option>
                        </select>
                        <p v-if="errors.status" class="text-red-500 text-xs mt-1">{{ errors.status }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">หมายเหตุ</label>
                        <textarea v-model="form.notes" rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="หมายเหตุเพิ่มเติม..."></textarea>
                        <p v-if="errors.notes" class="text-red-500 text-xs mt-1">{{ errors.notes }}</p>
                    </div>

                    <div class="flex justify-end space-x-3 pt-4">
                        <button type="button" @click="$emit('cancel')"
                                class="px-4 py-2 text-sm font-medium text-gray-700 bg-gray-100 border border-gray-300 rounded-md hover:bg-gray-200 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            ยกเลิก
                        </button>
                        <button type="submit" :disabled="loading"
                                class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:opacity-50">
                            <span v-if="loading">กำลังบันทึก...</span>
                            <span v-else>บันทึก</span>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "EquipmentItemCreateModal",
    props: {
        isOpen: {
            type: Boolean,
            default: false,
        },
        equipments: {
            type: Array,
            default: () => [],
        },
    },
    data() {
        return {
            form: {
                equipment_id: '',
                serial_number: '',
                condition: '',
                status: '',
                notes: '',
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
            if (!this.form.equipment_id) {
                this.errors.equipment_id = 'กรุณาเลือกอุปกรณ์';
            }
            if (!this.form.condition) {
                this.errors.condition = 'กรุณาเลือกสภาพ';
            }
            if (!this.form.status) {
                this.errors.status = 'กรุณาเลือกสถานะ';
            }

            if (Object.keys(this.errors).length > 0) {
                this.loading = false;
                return;
            }

            this.$emit('create', { ...this.form });
            this.resetForm();
        },
        resetForm() {
            this.form = {
                equipment_id: '',
                serial_number: '',
                condition: '',
                status: '',
                notes: '',
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
