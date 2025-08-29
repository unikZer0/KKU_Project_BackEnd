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
      <h2 class="text-lg font-semibold">คำขอทั้งหมด</h2>
    </div>
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 border-t border-b">
        <tr>
          <th class="text-left px-4 py-2">Request ID</th>
          <th class="text-left px-4 py-2">User</th>
          <th class="text-left px-4 py-2">Equipment</th>
          <th class="text-left px-4 py-2">Date</th>
          <th class="text-left px-4 py-2">Status</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="request in requests" :key="request.id">
          <td class="px-4 py-2">{{ request.id }}</td>
          <td class="px-4 py-2">{{ request.user_name }}</td>
          <td class="px-4 py-2">{{ request.equipment_name }}</td>
          <td class="px-4 py-2">{{ request.date }}</td>
          <td class="px-4 py-2">{{ request.status }}</td>
          <td class="px-4 py-2 space-x-2">
              <button @click="approveRequest(request.id)" class="bg-green-500 text-white px-2 py-1 rounded">Approve</button>
              <button @click="rejectRequest(request.id)" class="bg-red-500 text-white px-2 py-1 rounded">Reject</button>
          </td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  name: 'AdminApproveTable',
  props: {
    requests: Array
  },

  data() {
    return {
      searchQuery: ''
    };
  },
methods: {
  approveRequest(id) {
    axios.post(`/admin/requests/${id}/approve`)
      .then(() => {
        alert('Request approved');
        window.location.reload(); // reload to see updated status
      })
      .catch(err => console.error(err));
  },
  rejectRequest(id) {
    const reason = prompt('Enter rejection reason:');
    if (!reason) return;

    axios.post(`/admin/requests/${id}/reject`, { reason })
      .then(() => {
        alert('Request rejected');
        window.location.reload();
      })
      .catch(err => console.error(err));
  }
}
}
</script>
