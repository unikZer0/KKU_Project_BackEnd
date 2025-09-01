<script setup>
import { ref } from 'vue';
import axios from 'axios';
import SearchBar from './SearchBar.vue';

const items = ref([]);
const query = ref('');
const loading = ref(false);

async function onSearch(val) {
  if (!val) {
    items.value = [];
    return;
  }

  loading.value = true;
  try {
    console.log('Searching for:', val);
    const res = await axios.get('/equipments/search', {
      params: { q: val },
      withCredentials: true
    });
    console.log('Search response:', res.data);
    items.value = res.data.data || [];
  } catch (e) {
    console.error('Search error:', e);
    items.value = [];
  } finally {
    loading.value = false;
  }
}

async function triggerSearch() {
  if (query.value.trim()) {
    await onSearch(query.value);
  } else {
    await showAllEquipment();
  }
}

async function showAllEquipment() {
  loading.value = true;
  try {
    console.log('Fetching all equipment');
    const res = await axios.get('/equipments/all', {
      withCredentials: true
    });
    console.log('All equipment response:', res.data);
    items.value = res.data.data || [];
  } catch (e) {
    console.error('Error fetching all equipment:', e);
    items.value = [];
  } finally {
    loading.value = false;
  }
}

function getEquipmentUrl(equipment) {
  return `/equipments/${equipment.code}`;
}

function handleImageError(event) {
  event.target.style.display = 'none';
  const fallback = event.target.nextElementSibling;
  if (fallback) {
    fallback.style.display = 'flex';
  }
}
</script>

<template>
  <div class="w-full mb-5">
    <div class="flex items-center gap-3">
      <div class="flex-1">
        <SearchBar
          v-model="query"
          placeholder="ค้นหาอุปกรณ์..."
          :debounce="250"
          @search="onSearch"
        />
      </div>
      <button
        type="button"
        @click="triggerSearch"
        class="hidden md:flex bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-3 rounded-lg items-center justify-center transition duration-200 whitespace-nowrap"
      >
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
            d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
        </svg>
        ค้นหา
      </button>
    </div>
    <div v-if="query || items.length > 0" class="mt-2 bg-white border border-gray-200 rounded-lg shadow-sm overflow-hidden">
      <div v-if="loading" class="p-4">
        <div class="space-y-3">
          <div v-for="i in 3" :key="i" class="flex items-start gap-3">
            <div class="w-16 h-16 bg-gray-200 rounded-lg animate-pulse"></div>
            <div class="flex-1 space-y-2">
              <div class="h-4 bg-gray-200 rounded w-3/4 animate-pulse"></div>
              <div class="h-3 bg-gray-200 rounded w-1/2 animate-pulse"></div>
              <div class="h-3 bg-gray-200 rounded w-2/3 animate-pulse"></div>
            </div>
          </div>
        </div>
      </div>
      <div v-else-if="items.length === 0" class="px-3 py-2 text-sm text-gray-500">ไม่พบข้อมูล</div>
      <div v-else>
        <div class="px-3 py-2 bg-gray-50 border-b border-gray-200 flex justify-between items-center">
          <span class="text-sm font-medium text-gray-700">
            {{ query ? `ผลการค้นหา "${query}"` : 'อุปกรณ์ทั้งหมด' }} ({{ items.length }} รายการ)
          </span>
          <a 
            href="/equipments" 
            class="text-sm text-blue-600 hover:text-blue-800 hover:underline"
          >
            ดูทั้งหมด
          </a>
        </div>
        <ul class="max-h-96 overflow-y-auto">
          <li
            v-for="item in items"
            :key="item.id"
            class="px-3 py-3 hover:bg-gray-50 cursor-pointer text-sm border-b border-gray-100 last:border-b-0 transition-colors duration-200"
          >
            <a :href="getEquipmentUrl(item)" class="block">
              <div class="flex items-start gap-3">
                <!-- <div class="flex-shrink-0 w-16 h-16 rounded-lg overflow-hidden bg-gray-100">
                  <img 
                    v-if="item.photo_path" 
                    :src="item.photo_path" 
                    :alt="item.name"
                    class="w-full h-full object-cover"
                    loading="lazy"
                    @error="handleImageError"
                  />
                  <div v-else class="w-full h-full flex items-center justify-center bg-gray-200">
                    <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                      <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                  </div>
                </div> -->
                <div class="flex-1 min-w-0">
                  <div class="font-medium text-gray-800 hover:text-blue-600 transition-colors duration-200">
                    {{ item.name }}
                  </div>
                  <!-- <div class="text-gray-500 text-xs mt-1">
                    <span class="inline-block px-2 py-1 rounded mr-2">{{ item.code }}</span>
                    <span class="inline-block px-2 py-1 rounded mr-2">
                      {{ item.category?.name || 'ไม่มีหมวดหมู่' }}
                    </span>
                  </div>
                  <div v-if="item.description" class="text-gray-600 text-xs mt-1 line-clamp-2">
                    {{ item.description }}
                  </div> -->
                </div>
              </div>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </div>
</template>

<style scoped>
.line-clamp-2 {
  display: -webkit-box;
  -webkit-line-clamp: 2;
  -webkit-box-orient: vertical;
  overflow: hidden;
}
</style>
