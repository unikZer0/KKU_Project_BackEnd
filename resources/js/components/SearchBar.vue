<script setup>
import { ref, watch, onBeforeUnmount } from 'vue';

const props = defineProps({
  modelValue: { type: String, default: '' },
  placeholder: { type: String, default: 'Search...' },
  debounce: { type: Number, default: 300 }
});

const emit = defineEmits(['update:modelValue', 'search']);

const localValue = ref(props.modelValue);
let debounceTimer = null;

watch(
  () => props.modelValue,
  (val) => {
    if (val !== localValue.value) {
      localValue.value = val;
    }
  }
);

function onInput(e) {
  const value = e.target.value;
  localValue.value = value;
  emit('update:modelValue', value);
  scheduleSearch(value);
}

function scheduleSearch(value) {
  if (debounceTimer) clearTimeout(debounceTimer);
  debounceTimer = setTimeout(() => {
    emit('search', value);
  }, props.debounce);
}

function clearInput() {
  localValue.value = '';
  emit('update:modelValue', '');
  emit('search', '');
}

onBeforeUnmount(() => {
  if (debounceTimer) clearTimeout(debounceTimer);
});
</script>

<template>
  <div class="relative">
    <input
      :value="localValue"
      type="text"
      :placeholder="placeholder"
      @input="onInput"
      class="block w-full pl-10 pr-10 py-3 border border-gray-300 rounded-lg text-gray-700 placeholder-gray-400 bg-white focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
    />
    <!-- <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
      <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z" />
      </svg>
    </div>
    <button
      v-if="localValue"
      @click="clearInput"
      aria-label="Clear search"
      class="absolute right-2 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-600 focus:outline-none"
    >
      ✕
    </button> -->
  </div>
</template>

<style scoped>
</style>


