<script setup>
import { ref, computed } from 'vue';
import SearchBar from './SearchBar.vue';

// Demo items; replace with API-driven items later
const items = ref([
  { id: 1, code: 'EQ-001', name: 'Microscope', category: 'Lab', status: 'available' },
  { id: 2, code: 'EQ-002', name: 'Projector', category: 'AV', status: 'maintenance' },
  { id: 3, code: 'EQ-003', name: 'Camera', category: 'AV', status: 'available' },
  { id: 4, code: 'EQ-004', name: 'Oscilloscope', category: 'Lab', status: 'unavailable' }
]);

const query = ref('');
const filteredItems = computed(() => {
  const q = query.value.trim().toLowerCase();
  if (!q) return items.value;
  return items.value.filter((it) =>
    ['code', 'name', 'category', 'status'].some((f) => String(it[f]).toLowerCase().includes(q))
  );
});

function onSearch(val) {
  // placeholder if you want to trigger API search instead of local filter
}

function triggerSearch() {
  onSearch(query.value);
}
</script>

<template>
  <div class="w-full mb-5">
    <div class="flex items-center gap-3">
      <div class="flex-1">
        <SearchBar v-model="query" placeholder="ค้นหา..." :debounce="250" @search="onSearch" />
      </div>
      <!-- Search button - hidden on mobile, visible on desktop -->
      <button
        type="button"
        @click="triggerSearch"
        class="hidden md:flex bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-3 rounded-lg items-center justify-center transition duration-200 whitespace-nowrap"
      >
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        ค้นหา
      </button>
    </div>

    <div v-if="query" class="mt-2 bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
      <div v-if="filteredItems.length === 0" class="px-3 py-2 text-sm text-gray-500">ไม่พบข้อมูล</div>
      <ul v-else>
        <li v-for="item in filteredItems" :key="item.id" class="px-3 py-2 hover:bg-gray-50 cursor-pointer text-sm">
          <div class="font-medium text-gray-800">{{ item.name }}</div>
          <div class="text-gray-500">{{ item.code }} • {{ item.category }} • {{ item.status }}</div>
        </li>
      </ul>
    </div>
  </div>
</template>

<style scoped>
</style>


