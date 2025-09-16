<template>
  <div class="bg-white p-6 rounded-lg shadow">
    <!-- Search -->
    <div class="relative mb-4">
      <input
        type="text"
        v-model="searchQuery"
        placeholder="Search"
        class="pl-10 pr-3 py-2 text-sm border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
      />
      <svg
        class="w-4 h-4 absolute left-3 top-2.5 text-gray-400"
        fill="none"
        stroke="currentColor"
        viewBox="0 0 24 24"
      >
        <path
          stroke-linecap="round"
          stroke-linejoin="round"
          stroke-width="2"
          d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"
        />
      </svg>
    </div>

    <!-- Header -->
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-lg font-semibold">
        คำขอทั้งหมด: {{ filteredRequests.length }}
      </h2>
    </div>

    <!-- Table -->
    <div>
      <table class="min-w-full text-sm border">
        <thead class="bg-gray-50 border-b">
          <tr>
            <th class="text-left px-4 py-2">Request ID</th>
            <th class="text-left px-4 py-2">ผู้ขอ</th>
            <th class="text-left px-4 py-2">อุปกรณ์</th>
            <th class="text-left px-4 py-2">วันที่ส่งคำขอ</th>
            <th class="text-left px-4 py-2">สถานะ</th>
            <th class="text-left px-4 py-2">การดำเนินการ</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="request in paginatedRequests"
            :key="request.id"
            class="border-b"
          >
            <td class="px-4 py-2">{{ request.req_id }}</td>
            <td class="px-4 py-2">{{ request.user_name }}</td>
            <td class="px-4 py-2">{{ request.equipment_name }}</td>
            <td class="px-4 py-2">{{ request.date }}</td>
            <td class="px-4 py-2">{{ capitalize(request.status) }}</td>
            <td class="px-4 py-2">
              <button
                @click="openDetails(request)"
                class="bg-blue-500 text-white px-2 py-1 rounded"
              >
                <a :href="'/admin/requests/' + request.req_id">ดูรายละเอียด</a>
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  
    <div class="mt-4 flex justify-between items-center">
      <div class="text-sm text-gray-600">
        แสดง {{ pageStart + 1 }} - {{ pageEnd }} จากทั้งหมด
        {{ filteredRequests.length }} รายการ
      </div>
      <div class="flex items-center space-x-1">
        <button
          class="px-3 py-1 border rounded disabled:opacity-50"
          :disabled="currentPage === 1"
          @click="prevPage"
        >
          ก่อนหน้า
        </button>
        <button
          v-for="p in visiblePageNumbers"
          :key="p"
          class="px-3 py-1 border rounded"
          :class="{ 'bg-blue-600 text-white': currentPage === p }"
          @click="goToPage(p)"
        >
          {{ p }}
        </button>
        <button
          class="px-3 py-1 border rounded disabled:opacity-50"
          :disabled="currentPage === pageCount || pageCount === 0"
          @click="nextPage"
        >
          ถัดไป
        </button>
      </div>
    </div>
  </div>
</template>
<script>
import axios from "axios";

export default {
  name: "AdminApproveTable",
  props: { requests: Array },
  data() {
    return {
      searchQuery: "",
      currentPage: 1,
      pageSize: 10,
    };
  },
  computed: {
    filteredRequests() {
      const filtered = this.requests.filter(
        (r) =>
          r.status.toLowerCase() !== "rejected" &&
          r.status.toLowerCase() !== "check_in"
      );
      if (!this.searchQuery) return filtered;
      const q = this.searchQuery.toLowerCase();
      return filtered.filter(
        (r) =>
          r.user_name.toLowerCase().includes(q) ||
          r.equipment_name.toLowerCase().includes(q) ||
          r.status.toLowerCase().includes(q) ||
          String(r.req_id).includes(q)
      );
    },
    pageCount() {
      return Math.ceil(this.filteredRequests.length / this.pageSize) || 0;
    },
    pageStart() {
      return (this.currentPage - 1) * this.pageSize;
    },
    pageEnd() {
      return Math.min(this.pageStart + this.pageSize, this.filteredRequests.length);
    },
    paginatedRequests() {
      return this.filteredRequests.slice(this.pageStart, this.pageEnd);
    },
    visiblePageNumbers() {
      const pages = [];
      const total = this.pageCount;
      const maxVisible = 7;
      let start = Math.max(1, this.currentPage - 3);
      let end = Math.min(total, start + maxVisible - 1);
      start = Math.max(1, end - maxVisible + 1);
      for (let i = start; i <= end; i++) pages.push(i);
      return pages;
    },
  },
  methods: {
    capitalize(str) {
      return str ? str.charAt(0).toUpperCase() + str.slice(1) : "";
    },
    goToPage(p) {
      this.currentPage = p;
    },
    nextPage() {
      if (this.currentPage < this.pageCount) this.currentPage++;
    },
    prevPage() {
      if (this.currentPage > 1) this.currentPage--;
    },
    openDetails(request) {
      window.location.href = `/admin/requests/${request.req_id}`;
    },
  },
  watch: {
    searchQuery() {
      this.currentPage = 1;
    },
    filteredRequests() {
      if (this.currentPage > this.pageCount) this.currentPage = 1;
    },
  },
};
</script>
