<template>
  <div
    v-if="isOpen"
    class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50"
    @click.self="$emit('cancel')"
    @keydown.esc.prevent="$emit('cancel')"
    tabindex="0"
    role="dialog"
    aria-modal="true"
  >
    <div
      class="bg-white rounded-lg shadow-lg w-11/12 sm:w-2/3 md:w-1/2 lg:w-1/3 p-6 max-h-[90vh] overflow-y-auto"
    >
      <h3 class="text-lg font-semibold mb-4">แก้ไขอุปกรณ์</h3>

      <form @submit.prevent="onSave" v-if="form.id" novalidate>
        <div class="mb-4">
          <label class="block text-gray-700 font-semibold mb-1">หมายเลขครุภัณฑ์</label>
          <input
            required
            type="text"
            v-model.trim="form.code"
            class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 font-semibold mb-1">ชื่ออุปกรณ์</label>
          <input
            required
            type="text"
            v-model.trim="form.name"
            class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          />
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 font-semibold mb-1">หมวดหมู่</label>
          <select
            required
            v-model="form.categories_id"
            class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option v-for="cat in categories" :key="cat.id" :value="cat.id">
              {{ cat.name }}
            </option>
          </select>
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 font-semibold mb-1">รายละเอียด</label>
          <textarea
            v-model.trim="form.description"
            class="w-full h-24 border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 resize-none"
          ></textarea>
        </div>
        <div class="mb-4">
          <label class="block text-gray-700 font-semibold mb-1">สถานะ</label>
          <select
            required
            v-model="form.status"
            class="w-full border px-3 py-2 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
          >
            <option v-for="s in statuses" :key="s" :value="s">
              {{ capitalize(s) }}
            </option>
          </select>
        </div>

        <div class="mb-4 space-y-4">
          <h4 class="block text-gray-700 font-semibold">จัดการรูปภาพ</h4>

          <div v-if="existingImages.length > 0">
            <label class="block text-sm text-gray-600 mb-1">รูปภาพปัจจุบัน</label>
            <div class="flex flex-wrap gap-2">
              <div
                v-for="image in existingImages"
                :key="image.id"
                class="relative group"
              >
                <img
                  :src="image.url"
                  alt="Existing image"
                  class="w-24 h-24 object-cover rounded border-2 transition-all duration-200"
                  :class="{ 
                    'opacity-30': imagesToDelete.includes(image.url),
                    'border-blue-500 shadow-lg': selectedMainIdentifier === image.url && !imagesToDelete.includes(image.url),
                    'border-gray-200': selectedMainIdentifier !== image.url
                  }"
                />
                <div v-if="imagesToDelete.includes(image.url)" class="absolute inset-0 flex items-center justify-center bg-black bg-opacity-50 rounded">
                    <span class="text-white font-bold text-sm">ลบ</span>
                </div>
                <div v-if="!imagesToDelete.includes(image.url)" class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 flex items-center justify-center gap-2 transition-opacity">
                    <button type="button" @click="setAsMain(image.url)" class="w-8 h-8 flex items-center justify-center bg-white/80 rounded-full text-lg hover:bg-white" title="ตั้งเป็นภาพหลัก">★</button>
                    <button type="button" @click="toggleDeletion(image)" class="w-8 h-8 flex items-center justify-center bg-white/80 rounded-full text-lg hover:bg-white" title="ลบรูปภาพ">×</button>
                </div>
              </div>
            </div>
          </div>

          <div>
            <label class="block text-sm text-gray-600 mb-1 mt-4">เพิ่มรูปภาพใหม่</label>
            <input type="file" accept="image/*" multiple @change="onImageChange" class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100" :disabled="processingImages"/>
            <p v-if="processingImages" class="text-sm text-blue-600 mt-1">กำลังประมวลผลรูปภาพ...</p>
            <p v-if="imageError" class="text-sm text-red-600 mt-1">{{ imageError }}</p>

            <div v-if="newImagePreviewUrls.length > 0" class="mt-3 flex flex-wrap gap-2">
              <div v-for="(url, index) in newImagePreviewUrls" :key="index" class="relative group">
                <img :src="url" alt="New preview" class="w-24 h-24 object-cover rounded border-2" :class="selectedMainIdentifier === newImageFiles[index].name ? 'border-blue-500 shadow-lg' : 'border-gray-200'"/>
                <div class="absolute inset-0 bg-black/30 opacity-0 group-hover:opacity-100 flex items-center justify-center gap-2 transition-opacity">
                    <button type="button" @click="setAsMain(newImageFiles[index].name)" class="w-8 h-8 flex items-center justify-center bg-white/80 rounded-full text-lg hover:bg-white" title="ตั้งเป็นภาพหลัก">★</button>
                    <button type="button" @click="removeNewImage(index)" class="w-8 h-8 flex items-center justify-center bg-white/80 rounded-full text-lg hover:bg-white" title="ลบรูปภาพ">×</button>
                </div>
              </div>
            </div>
          </div>
        </div>

        <div class="flex justify-end space-x-2 border-t pt-4 mt-4">
          <button type="button" @click="$emit('cancel')" class="px-4 py-2 rounded bg-gray-300 hover:bg-gray-400 disabled:opacity-60" :disabled="submitting">Cancel</button>
          <button type="submit" class="px-4 py-2 rounded bg-blue-600 text-white hover:bg-blue-700 disabled:opacity-60" :disabled="submitting || processingImages || !canSave">
            <span v-if="submitting">Saving...</span><span v-else>Save Changes</span>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
export default {
  name: "EquipmentEditModal",
  props: {
    isOpen: Boolean,
    equipment: Object,
    categories: Array,
    statuses: Array,
  },
  data() {
    return {
      form: {},
      existingImages: [],
      imagesToDelete: [], 
      newImageFiles: [],
      newImagePreviewUrls: [],
      imageError: "",
      processingImages: false,
      submitting: false,
      selectedMainIdentifier: null, // CHANGED from selectedMainUrl
    };
  },
  watch: {
    isOpen(newVal) {
      if (newVal && this.equipment) this.setupForm();
    },
  },
  computed: {
    canSave() {
      return !!(this.form && this.form.code && this.form.name && this.form.categories_id && this.form.status && !this.imageError);
    },
  },
  methods: {
    capitalize(str) {
      return str ? str.charAt(0).toUpperCase() + str.slice(1) : "";
    },
    setupForm() {
      this.form = JSON.parse(JSON.stringify(this.equipment));
      this.form.categories_id = this.equipment.category?.id || null;
      this.existingImages = [];
      this.selectedMainIdentifier = null; // CHANGED

      if (this.equipment.photo_path) {
        try {
          const photos = JSON.parse(this.equipment.photo_path);
          if (Array.isArray(photos) && photos.length > 0) {
            this.existingImages = photos.map(url => ({ id: url, url: url }));
            this.selectedMainIdentifier = photos[0]; // The first image is the main one
          }
        } catch (e) {
          // Handle non-JSON string for backward compatibility
          this.existingImages = [{ id: this.equipment.photo_path, url: this.equipment.photo_path }];
          this.selectedMainIdentifier = this.equipment.photo_path;
        }
      }

      this.imagesToDelete = [];
      this.newImageFiles = [];
      this.newImagePreviewUrls = [];
      this.imageError = "";
      this.submitting = false;
      this.processingImages = false;
    },
    setAsMain(identifier) { // CHANGED parameter name
        this.selectedMainIdentifier = identifier;
    },
    toggleDeletion(image) {
      const imageUrl = image.url;
      const index = this.imagesToDelete.indexOf(imageUrl);
      if (index > -1) {
        this.imagesToDelete.splice(index, 1);
      } else {
        this.imagesToDelete.push(imageUrl);
        if (this.selectedMainIdentifier === imageUrl) {
            this.selectedMainIdentifier = null;
        }
      }
    },
    removeNewImage(index) {
      const removedFile = this.newImageFiles[index];
      this.newImageFiles.splice(index, 1);
      this.newImagePreviewUrls.splice(index, 1);
      
      if (this.selectedMainIdentifier === removedFile.name) {
          this.selectedMainIdentifier = null;
      }
    },
    async onImageChange(event) {
      const files = event.target.files;
      if (!files || files.length === 0) return;
      this.imageError = "";
      this.processingImages = true;
      const resizePromises = [];
      for (const file of files) {
        if (file.size > 5 * 1024 * 1024) {
          this.imageError = `ไฟล์ '${file.name}' ใหญ่เกินไป (จำกัด 5MB)`;
          this.processingImages = false;
          return;
        }
        resizePromises.push(this.resizeImage(file));
      }
      try {
        const resizedImages = await Promise.all(resizePromises);
        resizedImages.forEach(({ blob, dataUrl, originalName }) => {
          const newFile = new File([blob], originalName, { type: blob.type });
          this.newImageFiles.push(newFile);
          this.newImagePreviewUrls.push(dataUrl);
        });
      } catch (error) {
        this.imageError = "เกิดข้อผิดพลาดขณะประมวลผลรูปภาพ";
      } finally {
        this.processingImages = false;
        event.target.value = null;
      }
    },
    resizeImage(file, { maxWidth = 1280, quality = 0.85 } = {}) {
      return new Promise((resolve, reject) => {
        const reader = new FileReader();
        reader.readAsDataURL(file);
        reader.onload = (e) => {
          const img = new Image();
          img.src = e.target.result;
          img.onload = () => {
            const canvas = document.createElement("canvas");
            const ctx = canvas.getContext("2d");
            const ratio = Math.min(maxWidth / img.width, 1);
            canvas.width = img.width * ratio;
            canvas.height = img.height * ratio;
            ctx.drawImage(img, 0, 0, canvas.width, canvas.height);
            canvas.toBlob(
              (blob) => {
                if (!blob) return reject(new Error("Canvas to Blob failed."));
                const dataUrl = canvas.toDataURL("image/jpeg", quality);
                resolve({ blob, dataUrl, originalName: file.name });
              },
              "image/jpeg",
              quality
            );
          };
          img.onerror = reject;
        };
        reader.onerror = reject;
      });
    },
    onSave() {
      if (this.submitting || !this.canSave) return;
      this.submitting = true;
      this.$emit("save", {
        ...this.form,
        newImageFiles: this.newImageFiles,
        imagesToDelete: this.imagesToDelete, 
        selectedMainIdentifier: this.selectedMainIdentifier, 
      });
    },
  },
};
</script>
