<script setup>
import { computed, onMounted, ref } from "vue";

const currentPage = ref(1);
const totalPages = ref(1);
const perPage = ref(12);
const total = ref(0);

onMounted(() => {
  const paginationEl = document.getElementById('pagination');
  if (paginationEl) {
    currentPage.value = parseInt(paginationEl.dataset.currentPage) || 1;
    totalPages.value = parseInt(paginationEl.dataset.totalPages) || 1;
    perPage.value = parseInt(paginationEl.dataset.perPage) || 15;
    total.value = parseInt(paginationEl.dataset.total) || 0;
  }
});

const pages = computed(() => {
  let arr = [];
  for (let i = 1; i <= totalPages.value; i++) {
    arr.push(i);
  }
  return arr;
});

const changePage = (page) => {
  if (page >= 1 && page <= totalPages.value) {
    const url = new URL(window.location);
    url.searchParams.set('page', page);
    window.location.href = url.toString();
  }
};
</script>

<template>
    <hr>
  <nav class="flex max-w-screen-2xl items-center justify-center gap-1 sm:gap-2 m-4 sm:m-6">
    <div class="text-xs sm:text-sm text-gray-600 mb-4 text-center w-full">
      Showing {{ (currentPage - 1) * perPage + 1 }} to {{ Math.min(currentPage * perPage, total) }} of {{ total }} results
    </div>
    <button
      @click="changePage(currentPage - 1)"
      :disabled="currentPage === 1"
      class="px-2 py-1 text-sm sm:px-3 sm:text-base rounded-md border bg-white text-gray-600 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
    >
      Prev
    </button>

    <button
      v-for="page in pages"
      :key="page"
      @click="changePage(page)"
      class="px-2 py-1 text-sm sm:px-3 sm:text-base rounded-md border"
      :class="page === currentPage 
        ? 'bg-blue-500 text-white border-blue-500' 
        : 'bg-white text-gray-700 hover:bg-gray-100'"
    >
      {{ page }}
    </button>
    <button
      @click="changePage(currentPage + 1)"
      :disabled="currentPage === totalPages"
      class="px-2 py-1 text-sm sm:px-3 sm:text-base rounded-md border bg-white text-gray-600 hover:bg-gray-100 disabled:opacity-50 disabled:cursor-not-allowed"
    >
      Next
    </button>
  </nav>
  <hr>
</template>
