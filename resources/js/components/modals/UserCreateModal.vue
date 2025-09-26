<template>
    <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-2xl rounded-xl shadow-lg p-6 relative max-h-[90vh] overflow-y-auto">
            <!-- Close button -->
            <button @click="$emit('close')" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600">
                ✕
            </button>

            <!-- Modal content -->
            <h2 class="text-xl font-bold text-gray-800 mb-4">เพิ่มผู้ใช้ใหม่</h2>

            <form @submit.prevent="onSave">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <!-- UID -->
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-1">รหัสผู้ใช้ <span class="text-red-500">*</span></label>
                        <input type="text" v-model="newUser.uid"
                            class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="ป้อนรหัสผู้ใช้"
                            required>
                    </div>

                    <!-- Name -->
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-1">ชื่อ <span class="text-red-500">*</span></label>
                        <input type="text" v-model="newUser.name"
                            class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="ป้อนชื่อ"
                            required>
                    </div>

                    <!-- Email -->
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-1">อีเมล <span class="text-red-500">*</span></label>
                        <input type="email" v-model="newUser.email"
                            class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="ป้อนอีเมล"
                            required>
                    </div>

                    <!-- Phone -->
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-1">เบอร์โทร</label>
                        <input type="text" v-model="newUser.phonenumber"
                            class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="ป้อนเบอร์โทร">
                    </div>

                    <!-- Password -->
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-1">รหัสผ่าน <span class="text-red-500">*</span></label>
                        <input type="password" v-model="newUser.password"
                            class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="ป้อนรหัสผ่าน (อย่างน้อย 8 ตัวอักษร)"
                            required
                            minlength="8">
                    </div>

                    <!-- Role -->
                    <div class="mb-4">
                        <label class="block text-gray-700 font-semibold mb-1">บทบาท <span class="text-red-500">*</span></label>
                        <select v-model="newUser.role"
                            class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                            required>
                            <option value="">เลือกบทบาท</option>
                            <option value="borrower">ผู้ยืม</option>
                            <option value="staff">เจ้าหน้าที่</option>
                            <option value="admin">ผู้ดูแลระบบ</option>
                        </select>
                    </div>
                </div>


                <div class="flex justify-end gap-2">
                    <button type="button" @click="onCancel"
                        class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">ยกเลิก</button>
                    <button type="submit"
                        class="px-4 py-2 rounded bg-green-600 text-white hover:bg-green-700">เพิ่มผู้ใช้</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    name: "UserCreateModal",
    props: {
        isOpen: Boolean
    },
    data() {
        return {
            newUser: {
                uid: '',
                name: '',
                email: '',
                phonenumber: '',
                password: '',
                role: ''
            }
        };
    },
    methods: {
        onSave() {
            this.$emit('create', { ...this.newUser });
            this.resetForm();
        },
        onCancel() {
            this.resetForm();
            this.$emit('close');
        },
        resetForm() {
            this.newUser = {
                uid: '',
                name: '',
                email: '',
                phonenumber: '',
                password: '',
                role: ''
            };
        }
    },
    watch: {
        isOpen(newVal) {
            if (!newVal) {
                this.resetForm();
            }
        }
    }
};
</script>
