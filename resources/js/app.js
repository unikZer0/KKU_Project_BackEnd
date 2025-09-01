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
import EquipmentTable from './components/EquipmentTable.vue';
import CategoriesTable from './components/CategoryTable.vue';
import RecentAct from './components/RecentAct.vue';

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

const recentActivitiesEl = document.getElementById('recent-activities');
if (recentActivitiesEl) {
    const requests = JSON.parse(recentActivitiesEl.dataset.requests);
    createApp(RecentAct, { requests }).mount(recentActivitiesEl);
}

// Chart Dashboard
const chartEl = document.getElementById('dashboard-chart');
if (chartEl) {
  const available = JSON.parse(chartEl.dataset.available || "[]");
  const maintenance = JSON.parse(chartEl.dataset.maintenance || "[]");
  const retired = JSON.parse(chartEl.dataset.retired || "[]");
  const months = JSON.parse(chartEl.dataset.months || "[]");

  createApp(ChartDashboard, {
    availableData: available,
    maintenanceData: maintenance,
    retiredData: retired,
    months: months,
  }).mount(chartEl);
}


// Equipment Table
const equipmentTableEl = document.getElementById('equipment-table');
if (equipmentTableEl) {
    createApp(EquipmentTable).mount(equipmentTableEl);
}

// Category Table
const categoryTableEl = document.getElementById('category-table');
if (categoryTableEl) {
    createApp(CategoriesTable).mount(categoryTableEl);
}
