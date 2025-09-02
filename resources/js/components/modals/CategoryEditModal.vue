<template>
    <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white w-full max-w-lg rounded-xl shadow-lg p-6 relative">
            <!-- Close button -->
            <button @click="$emit('close')" class="absolute top-3 right-3 text-gray-400 hover:text-gray-600">
                ✕
            </button>

            <!-- Modal content -->
            <h2 class="text-xl font-bold text-gray-800 mb-4">Edit Category</h2>

            <form @submit.prevent="onSave">
                <div class="mb-4">
                    <label class="block text-gray-700 font-semibold mb-1">Category Name</label>
                    <input type="text" v-model="category.name"
                        class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>

                <div class="flex justify-end gap-2">
                    <button type="button" @click="$emit('close')"
                        class="px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">Cancel</button>
                    <button type="submit"
                        class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700">Save</button>
                </div>
            </form>
        </div>
    </div>
</template>

<script>
export default {
    name: "CategoryEditModal",
    props: {
        isOpen: Boolean,
        category: Object
    },
    methods: {
        onSave() {
            this.$emit('save', { ...this.category });
        }
    }
};
</script>