<template>
  <div v-if="isOpen" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50" @click.self="$emit('close')">
    <div class="bg-white rounded-lg shadow-lg p-4 max-w-[90%] max-h-[90%] flex flex-col items-center">
      <img :src="url" alt="Equipment Photo" class="max-w-full max-h-full rounded mb-2" />
    </div>
  </div>
</template>
<script>
export default {
  name: "PhotoModal",
  props: {
    isOpen: Boolean,
    url: String
  }
};
</script>