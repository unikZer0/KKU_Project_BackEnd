<template>
    <div
        v-if="isOpen"
        class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-40"
        @keydown.esc.prevent="$emit('close')"
        tabindex="0"
        @click.self="$emit('close')"
        aria-modal="true"
        role="dialog"
    >
        <div class="bg-white rounded-lg shadow-lg w-11/12 sm:w-4/5 md:w-3/4 lg:w-2/3 xl:w-1/2 p-6 max-h-[90vh] overflow-y-auto">
            <h3 class="text-lg font-semibold mb-4">แก้ไขอุปกรณ์เสริม</h3>
            <form @submit.prevent="onUpdate" novalidate>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <!-- Equipment Selection -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            อุปกรณ์หลัก <span class="text-red-500">*</span>
                        </label>
                        <select v-model="form.equipment_id" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">เลือกอุปกรณ์หลัก</option>
                            <option v-for="equipment in equipments" :key="equipment.id" :value="equipment.id">
                                {{ equipment.name }} ({{ equipment.category?.name || 'ไม่มีหมวดหมู่' }})
                            </option>
                        </select>
                    </div>

                    <!-- Equipment Item Selection -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            อุปกรณ์ย่อย
                        </label>
                        <select v-model="form.equipment_item_id"
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">อุปกรณ์เสริมแบบแยกอิสระ (ไม่ผูกกับอุปกรณ์ย่อย)</option>
                            <option v-for="item in availableItems" :key="item.id" :value="item.id">
                                ผูกกับอุปกรณ์ย่อย: {{ item.serial_number }}
                            </option>
                        </select>
                        <p class="text-xs text-gray-500 mt-1">
                            เลือก "อุปกรณ์เสริมแบบแยกอิสระ" หากต้องการให้อุปกรณ์เสริมนี้เป็นอิสระ หรือเลือกอุปกรณ์ย่อยที่ต้องการผูก
                        </p>
                    </div>

                    <!-- Accessory Name -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            ชื่ออุปกรณ์เสริม <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               v-model="form.name"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="ชื่ออุปกรณ์เสริม">
                    </div>

                    <!-- Serial Number -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            หมายเลขซีเรียล <span class="text-red-500">*</span>
                        </label>
                        <input type="text" 
                               v-model="form.serial_number"
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                               placeholder="หมายเลขซีเรียล">
                    </div>

                    <!-- Condition -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            สภาพ <span class="text-red-500">*</span>
                        </label>
                        <select v-model="form.condition" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">เลือกสภาพ</option>
                            <option value="good">ดี</option>
                            <option value="fair">พอใช้</option>
                            <option value="poor">ชำรุด</option>
                        </select>
                    </div>

                    <!-- Status -->
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            สถานะ <span class="text-red-500">*</span>
                        </label>
                        <select v-model="form.status" 
                                required
                                class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">เลือกสถานะ</option>
                            <option value="available">พร้อมใช้งาน</option>
                            <option value="unavailable">ไม่พร้อมใช้งาน</option>
                            <option value="maintenance">ซ่อมบำรุง</option>
                        </select>
                    </div>

                    <!-- Description -->
                    <div class="md:col-span-2">
                        <label class="block text-sm font-medium text-gray-700 mb-2">
                            คำอธิบาย
                        </label>
                        <textarea v-model="form.description" 
                                  rows="3"
                                  class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                                  placeholder="คำอธิบายเพิ่มเติม"></textarea>
                    </div>
                </div>

                <!-- Form Actions -->
                <div class="flex justify-end space-x-4 pt-6 border-t border-gray-200 mt-6">
                    <button type="button" @click="$emit('close')" 
                            class="px-4 py-2 text-sm font-medium text-gray-700 bg-white border border-gray-300 rounded-md hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">
                        ยกเลิก
                    </button>
                    <button type="submit" 
                            :disabled="submitting"
                            class="px-4 py-2 text-sm font-medium text-white bg-blue-600 border border-transparent rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50">
                        {{ submitting ? 'กำลังอัปเดต...' : 'อัปเดต' }}
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    name: "AccessoryEditModal",
    props: {
        isOpen: Boolean,
        accessory: {
            type: Object,
            default: () => ({})
        },
        equipments: {
            type: Array,
            default: () => []
        }
    },
    data() {
        return {
            form: {
                id: null,
                equipment_id: "",
                equipment_item_id: "",
                name: "",
                serial_number: "",
                condition: "",
                status: "",
                description: ""
            },
            availableItems: [],
            submitting: false
        };
    },
    watch: {
        accessory: {
            handler(newAccessory) {
                if (newAccessory && newAccessory.id) {
                    this.form = { ...newAccessory };
                    this.loadEquipmentItems(newAccessory.equipment_id);
                }
            },
            immediate: true
        },
        'form.equipment_id'(newValue) {
            this.loadEquipmentItems(newValue);
        }
    },
    methods: {
        async loadEquipmentItems(equipmentId) {
            if (equipmentId) {
                try {
                    const response = await fetch(`/admin/accessories/by-equipment?equipment_id=${equipmentId}`);
                    if (response.ok) {
                        this.availableItems = await response.json();
                    }
                } catch (error) {
                    console.error('Error loading equipment items:', error);
                }
            } else {
                this.availableItems = [];
            }
        },
        async onUpdate() {
            this.submitting = true;
            try {
                await this.$emit('update', { ...this.form });
            } catch (error) {
                console.error('Error updating accessory:', error);
            } finally {
                this.submitting = false;
            }
        }
    }
};
</script>
