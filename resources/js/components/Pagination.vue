<script setup>
import { computed } from "vue";

const props = defineProps({
  currentPage: { type: Number, default: 1 },
  totalPages: { type: Number, required: true },
});

const emit = defineEmits(["update:page"]);

const pages = computed(() => {
  let arr = [];
  for (let i = 1; i <= props.totalPages; i++) {
    arr.push(i);
  }
  return arr;
});

const changePage = (page) => {
  if (page >= 1 && page <= props.totalPages) {
    emit("update:page", page);
  }
};
</script>

<template>
    <hr>
  <nav class="flex items-center justify-center gap-2 m-6">
    <!-- Previous Button -->
    <button
      @click="changePage(currentPage - 1)"
      :disabled="currentPage === 1"
      class="px-3 py-1 rounded-md border bg-white text-gray-600 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
    >
      Prev
    </button>

    <!-- Page Numbers -->
    <button
      v-for="page in pages"
      :key="page"
      @click="changePage(page)"
      class="px-3 py-1 rounded-md border"
      :class="page === currentPage 
        ? 'bg-blue-500 text-white border-blue-500' 
        : 'bg-white text-gray-700 hover:bg-gray-100'"
    >
      {{ page }}
    </button>

    <!-- Next Button -->
    <button
      @click="changePage(currentPage + 1)"
      :disabled="currentPage === totalPages"
      class="px-3 py-1 rounded-md border bg-white text-gray-600 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
    >
      Next
    </button>
  </nav>
  <hr>
</template>
