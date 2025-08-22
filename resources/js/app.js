import './bootstrap';

import Alpine from 'alpinejs';

window.Alpine = Alpine;

Alpine.start();

// Mount Vue search in header
import { createApp } from 'vue';
import HeaderSearch from './components/HeaderSearch.vue';
import Filter from './components/Filter.vue';


const headerSearchEl = document.getElementById('header-search');
if (headerSearchEl) {
    createApp(HeaderSearch).mount('#header-search');
}


const filterEl = document.getElementById('filter');
if (filterEl) {
    createApp(Filter).mount(filterEl);
}
