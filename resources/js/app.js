import './bootstrap';
import Alpine from 'alpinejs';
window.Alpine = Alpine;
Alpine.start();

import { createApp } from 'vue';
import HeaderSearch from './components/HeaderSearch.vue';
import Filter from './components/Filter.vue';
import Pagination from './components/Pagination.vue';
import AdminApproveTable from './components/AdminApproveTable.vue';
import ChartDashboard from './components/ChartDashboard.vue';

// Header Search
const headerEl = document.getElementById('header-search');
if (headerEl) createApp(HeaderSearch).mount(headerEl);

// Filter
const filterEl = document.getElementById('filter');
if (filterEl) createApp(Filter).mount(filterEl);

// Pagination
const paginationEl = document.getElementById('pagination');
if (paginationEl) createApp(Pagination).mount(paginationEl);

// Admin Approve Table
const tableEl = document.getElementById('admin-table');
if (tableEl) {
    const requests = JSON.parse(tableEl.dataset.requests);
    createApp(AdminApproveTable, { requests }).mount(tableEl);
}

// Chart Dashboard
const chartEl = document.getElementById('dashboard-chart');
if (chartEl) {
    const available = parseInt(chartEl.dataset.available);
    const unavailable = parseInt(chartEl.dataset.unavailable);
    const maintenance = parseInt(chartEl.dataset.maintenance);
    createApp(ChartDashboard, { available, unavailable, maintenance }).mount(chartEl);
}
