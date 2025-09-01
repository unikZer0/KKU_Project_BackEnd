<template>
  <div class="bg-white p-6 rounded-lg shadow">
    <div class="relative mb-4">
            <input 
        type="text" 
        v-model="searchQuery"
        placeholder="Search"
        class="pl-10 pr-3 py-2 text-sm border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500"
      >
      <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" 
          d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z"/>
      </svg>
    </div>
        <div class="flex justify-between items-center mb-4">
      <h2 class="text-lg font-semibold">คำขอทั้งหมด: {{ filteredRequests.length }}</h2>
    </div>
<div>
    <!-- Table -->
    <table class="min-w-full text-sm border">
      <thead class="bg-gray-50 border-b">
        <tr>
          <th class="text-left px-4 py-2">Request ID</th>
          <th class="text-left px-4 py-2">User</th>
          <th class="text-left px-4 py-2">Equipment</th>
          <th class="text-left px-4 py-2">Date</th>
          <th class="text-left px-4 py-2">Status</th>
          <th class="text-left px-4 py-2">Action</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="request in filteredRequests" :key="request.id">
          <td class="px-4 py-2">{{ request.id }}</td>
          <td class="px-4 py-2">{{ request.user_name }}</td>
          <td class="px-4 py-2">{{ request.equipment_name }}</td>
          <td class="px-4 py-2">{{ request.date }}</td>
          <td class="px-4 py-2">{{ request.status }}</td>
          <td class="px-4 py-2">
            <button @click="openDetails(request)" class="bg-blue-500 text-white px-2 py-1 rounded">
              View
            </button>
          </td>
        </tr>
      </tbody>
    </table>
        <!-- Card / Modal -->
    <div v-if="selectedRequest" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
      <div class="bg-white rounded-lg p-6 w-96 relative">
        <button @click="selectedRequest = null" class="absolute top-2 right-2 text-gray-500">✕</button>
        <h2 class="text-lg font-semibold mb-2">Request #{{ selectedRequest.id }}</h2>
        <p><strong>User:</strong> {{ selectedRequest.user_name }}</p>
        <p><strong>Equipment:</strong> {{ selectedRequest.equipment_name }}</p>
        <p v-if="selectedRequest.equipment_photo">
          <img :src="selectedRequest.equipment_photo" class="h-32 w-32 object-cover rounded mt-2" />
        </p>
        <p><strong>Start At:</strong> {{ selectedRequest.start_at }}</p>
        <p><strong>End At:</strong> {{ selectedRequest.end_at }}</p>
        <p><strong>Status:</strong> {{ selectedRequest.status }}</p>
        <p><strong>Reason:</strong> {{ selectedRequest.reason }}</p>
        <div class="flex justify-end space-x-2 mt-4">
          <button @click="approveRequest(selectedRequest.id)" class="bg-green-500 text-white px-3 py-1 rounded">Approve</button>
          <button @click="rejectRequest(selectedRequest.id)" class="bg-red-500 text-white px-3 py-1 rounded">Reject</button>
        </div>
      </div>
    </div>
  </div>
</div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'AdminApproveTable',
  props: { requests: Array },
  data() {
    return {
      selectedRequest: null
    };
  },
  computed: {
  filteredRequests() {
    if (!this.searchQuery) {
      return this.requests;
    }
    const q = this.searchQuery.toLowerCase();
    return this.requests.filter(r =>
      r.user_name.toLowerCase().includes(q) ||
      r.equipment_name.toLowerCase().includes(q) ||
      r.status.toLowerCase().includes(q)
    );
  }
},
  methods: {
    openDetails(request) {
      this.selectedRequest = request;
    },
approveRequest(id) {
  Swal.fire({
    title: 'อนุมัติคำขอ?',
    icon: 'question',
    showCancelButton: true,
    confirmButtonText: 'ใช่, อนุมัติ!',
    cancelButtonText: 'ยกเลิก',
    reverseButtons: true
  }).then((result) => {
    if (result.isConfirmed) {
      axios.post(`/admin/requests/${id}/approve`).then(() => {
        Swal.fire({
          icon: 'success',
          title: 'คำขอได้รับการอนุมัติ',
          text: 'คำขอได้รับการอนุมัติเรียบร้อยแล้ว',
          confirmButtonColor: '#3085d6'
        }).then(() => {
          window.location.reload(); // or update your table dynamically
        });
      });
    }
  });
},

rejectRequest(id) {
  Swal.fire({
    title: 'ปฏิเสธคำขอ',
    text: 'กรุณาใส่เหตุผลสำหรับการปฏิเสธ:',
    input: 'text',
    inputPlaceholder: 'กรุณาใส่เหตุผลที่นี่...',
    icon: 'warning',
    showCancelButton: true,
    confirmButtonText: 'ปฏิเสธ',
    cancelButtonText: 'ยกเลิก',
    reverseButtons: true,
    inputValidator: (value) => {
      if (!value) {
        return 'คุณต้องระบุเหตุผล!'
      }
    }
  }).then((result) => {
    if (result.isConfirmed) {
      axios.post(`/admin/requests/${id}/reject`, { reason: result.value })
        .then(() => {
          Swal.fire({
            icon: 'success',
            title: 'คำขอถูกปฏิเสธ',
            text: 'คำขอถูกปฏิเสธเรียบร้อยแล้ว',
            confirmButtonColor: '#3085d6'
          }).then(() => {
            window.location.reload(); // or update your table dynamically
          });
        });
    }
  });
}

  }
};
</script>
