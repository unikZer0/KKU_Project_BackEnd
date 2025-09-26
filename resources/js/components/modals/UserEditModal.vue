<template>
    <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-2xl rounded-xl shadow-lg p-6 relative max-h-[90vh] overflow-y-auto">
            <!-- Close button -->
            <button @click="$emit('close')" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600">
                ✕
            </button>

            <!-- Modal content -->
            <h2 class="text-xl font-bold text-gray-800 mb-4">แก้ไขข้อมูลผู้ใช้</h2>

            <form @submit.prevent="onSave">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- UID -->
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-1">รหัสผู้ใช้ <span class="text-red-500">*</span></label>
                        <input type="text" v-model="localUser.uid"
                            class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required>
                    </div>

                    <!-- Name -->
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-1">ชื่อ <span class="text-red-500">*</span></label>
                        <input type="text" v-model="localUser.name"
                            class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required>
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-1">อีเมล <span class="text-red-500">*</span></label>
                        <input type="email" v-model="localUser.email"
                            class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required>
                    </div>

                    <!-- Phone -->
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-1">เบอร์โทร</label>
                        <input type="text" v-model="localUser.phonenumber"
                            class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>

                    <!-- Role -->
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-1">บทบาท <span class="text-red-500">*</span></label>
                        <select v-model="localUser.role"
                            class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required>
                            <option value="borrower">ผู้ยืม</option>
                            <option value="staff">เจ้าหน้าที่</option>
                            <option value="admin">ผู้ดูแลระบบ</option>
                        </select>
                    </div>

                </div>

                <!-- Password -->
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-1">รหัสผ่านใหม่ (เว้นว่างถ้าไม่ต้องการเปลี่ยน)</label>
                    <input type="password" v-model="localUser.password"
                        class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="ป้อนรหัสผ่านใหม่หากต้องการเปลี่ยนแปลง">
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" @click="$emit('close')"
                        class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">ยกเลิก</button>
                    <button type="submit"
                        class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    name: "UserEditModal",
    props: {
        isOpen: Boolean,
        user: Object
    },
    data() {
        return {
            localUser: {}
        };
    },
    watch: {
        user: {
            handler(newUser) {
                this.localUser = { ...newUser };
                // Clear password field when opening modal
                this.localUser.password = '';
            },
            immediate: true
        }
    },
    methods: {
        onSave() {
            this.$emit('save', { ...this.localUser });
        }
    }
};
</script>
