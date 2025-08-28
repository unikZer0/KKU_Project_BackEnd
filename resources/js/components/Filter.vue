<template>
    <div class="max-w-screen-2xl mx-auto py-4 sm:py-6 px-4 sm:px-6 lg:px-8 flex justify-end">
        <div class="flex space-x-2">
            <!--  applied-filters  -->
            <div class="relative inline-block text-left" v-if="appliedCount > 0">
                <button @click="appliedOpen = !appliedOpen" class="inline-flex items-center px-3 py-1.5 text-sm bg-gray-100 text-gray-900 rounded-full border border-black">
                    <span>{{ appliedCount }} filters applied</span>
                    <svg :class="['ml-2 h-3 w-3 transition-transform', appliedOpen ? 'rotate-180' : 'rotate-0']" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                    </svg>
                </button>
                <div v-if="appliedOpen" class="absolute left-0 mt-2 w-64 bg-white rounded-lg shadow-lg ring-1 ring-black ring-opacity-5 p-3" style="z-index:9999;">
                    <div class="space-y-2">
                        <div v-for="(label, idx) in appliedLabels" :key="idx" class="flex items-center justify-between px-3 py-2 bg-gray-100 rounded-full border border-black">
                            <span class="text-sm">{{ label }}</span>
                            <button @click="removeFilter(idx)" class="ml-3 text-gray-700">×</button>
                        </div>
                        <button @click="clearAll" class="text-blue-600 text-sm hover:underline">Clear All</button>
                    </div>
                </div>
            </div>
        </div>

        <button @click="openFilterPanel" class="px-3 py-2 text-sm sm:px-4 sm:py-2 sm:text-base mx-2 bg-blue-500 text-white rounded-full hover:bg-blue-600 flex items-center gap-1.5 sm:gap-2 shadow ">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 sm:h-5 sm:w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h18M7 8h10M10 12h4"/>
            </svg>
            Filter
        </button>
    </div>

    <!-- Full-screen -->
    <div v-if="filterPanelOpen" class="fixed inset-0 z-[9999]">
        <div class="absolute inset-0 bg-black bg-opacity-40" @click="closeFilterPanel"></div>
        <!-- Mobile-->
        <div class="absolute bg-white shadow-xl flex flex-col transform -translate-x-1/2 left-1/2 top-16 w-80 max-w-[90vw] max-h-[80vh] rounded-xl overflow-hidden
                    sm:inset-y-0 sm:left-0 sm:top-0 sm:translate-x-0 sm:w-80 sm:max-w-none sm:max-h-none sm:rounded-none">
            <div class="flex items-center justify-between px-4 py-4 border-b">
                <h2 class="text-lg font-semibold">Filter</h2>
                <button @click="closeFilterPanel" class="text-2xl leading-none" aria-label="Close">×</button>
            </div>
            <div class="flex-1 overflow-y-auto px-4 py-4 space-y-4">
                <!-- Status filter -->
                <div>
                    <button @click="accCondition = !accCondition" class="w-full flex items-center justify-between py-3">
                        <span class="font-medium">Status</span>
                        <span class="text-gray-500">▾</span>
                    </button>
                    <div v-show="accCondition" class="space-y-2 pl-1">
                        <label class="flex items-center space-x-2 p-2 hover:bg-gray-50 rounded cursor-pointer">
                            <input type="radio" name="status_panel" class="form-radio text-blue-600" v-model="selectedStatus" value=""/>
                            <span>All</span>
                        </label>
                        <label class="flex items-center space-x-2 p-2 hover:bg-gray-50 rounded cursor-pointer">
                            <input type="radio" name="status_panel" class="form-radio text-blue-600" v-model="selectedStatus" value="available"/>
                            <span>Available</span>
                        </label>
                        <label class="flex items-center space-x-2 p-2 hover:bg-gray-50 rounded cursor-pointer">
                            <input type="radio" name="status_panel" class="form-radio text-blue-600" v-model="selectedStatus" value="maintenance"/>
                            <span>Maintenance</span>
                        </label>
                    </div>
                </div>

                <!-- Category filter -->
                <div>
                    <button @click="accPrice = !accPrice" class="w-full flex items-center justify-between py-3">
                        <span class="font-medium">Category</span>
                        <span class="text-gray-500">▾</span>
                    </button>
                    <div v-show="accPrice" class="space-y-2 pl-1 max-h-60 overflow-y-auto">
                        <label class="flex items-center space-x-2 p-2 hover:bg-gray-50 rounded cursor-pointer">
                            <input type="radio" name="category_panel" class="form-radio text-blue-600" v-model="selectedCategory" value=""/>
                            <span>All</span>
                        </label>
                        <label v-for="cat in categories" :key="cat.cate_id" class="flex items-center space-x-2 p-2 hover:bg-gray-50 rounded cursor-pointer">
                            <input type="radio" name="category_panel" class="form-radio text-blue-600" v-model="selectedCategory" :value="cat.cate_id"/>
                            <span>{{ cat.name }}</span>
                        </label>
                    </div>
                </div>
            </div>
            <div class="px-4 py-4 border-t flex items-center gap-3">
                <button @click="resetFilters" class="px-4 py-2 rounded-full bg-gray-100 hover:bg-gray-200">Reset</button>
                <button @click="applyFilters" class="ml-auto px-5 py-2 rounded-full bg-blue-600 text-white hover:bg-blue-700">Apply</button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data() {
        return {
            conditionOpen: false,
            priceOpen: false,
            // filters
            categories: [],
            selectedCategory: '',
            selectedStatus: '',
            filterPanelOpen: false,
            accCondition: true,
            accPrice: true,
            appliedOpen: false,
        };
    },
    computed: {
        appliedLabels() {
            const labels = [];
            if (this.selectedStatus) labels.push(`Status: ${this.selectedStatus}`);
            if (this.selectedCategory) {
                const cat = this.categories.find(c => c.cate_id === this.selectedCategory);
                labels.push(`Category: ${cat ? cat.name : this.selectedCategory}`);
            }
            return labels;
        },
        appliedCount() {
            return this.appliedLabels.length;
        },
    },
    mounted() {
        document.addEventListener('click', this.handleClickOutside);
        const mountEl = document.getElementById('filter');
        if (mountEl) {
            try {
                this.categories = JSON.parse(mountEl.dataset.categories || '[]');
            } catch (e) { this.categories = []; }
            this.selectedCategory = mountEl.dataset.currentCategory || '';
            this.selectedStatus = mountEl.dataset.currentStatus || '';
        }
    },
    beforeUnmount() {
        document.removeEventListener('click', this.handleClickOutside);
    },
    methods: {
        handleClickOutside(event) {
            if (!this.$el.contains(event.target)) {
                this.conditionOpen = false;
                this.priceOpen = false;
            }
        },
        clearCondition() {
            this.selectedStatus = '';
            this.$emit('update', { category: this.selectedCategory, status: this.selectedStatus });
        },
        clearPrice() {
            this.selectedCategory = '';
            this.$emit('update', { category: this.selectedCategory, status: this.selectedStatus });
        },
        openFilterPanel() {
            this.filterPanelOpen = true;
        },
        closeFilterPanel() {
            this.filterPanelOpen = false;
        },
        applyFilters() {
            // push filters to URL and reload (server handles pagination withQueryString)
            const url = new URL(window.location);
            if (this.selectedCategory) url.searchParams.set('category', this.selectedCategory); else url.searchParams.delete('category');
            if (this.selectedStatus) url.searchParams.set('status', this.selectedStatus); else url.searchParams.delete('status');
            url.searchParams.delete('page');
            window.location.href = url.toString();
        },
        resetFilters() {
            this.selectedCategory = '';
            this.selectedStatus = '';
            const url = new URL(window.location);
            url.searchParams.delete('category');
            url.searchParams.delete('status');
            url.searchParams.delete('page');
            window.location.href = url.toString();
        },
        clearAll() {
            this.selectedCategory = '';
            this.selectedStatus = '';
            this.appliedOpen = false;
            const url = new URL(window.location);
            url.searchParams.delete('category');
            url.searchParams.delete('status');
            url.searchParams.delete('page');
            window.location.href = url.toString();
        },
        removeFilter(index) {
            const label = this.appliedLabels[index];
            if (!label) return;
            if (label.startsWith('Category:')) this.selectedCategory = '';
            if (label.startsWith('Status:')) this.selectedStatus = '';
            this.applyFilters();
        },
    },
};
</script>
