<template>
  <div class="bg-white p-6 rounded-lg shadow">
    <div class="flex justify-between items-center mb-4">
      <h2 class="text-lg font-semibold">Recent Activities</h2>
      <a href="/admin/requests" class="text-xs text-blue-600">View all</a>
    </div>
    <table class="min-w-full text-sm">
      <thead class="bg-gray-50 border-t border-b">
        <tr>
          <th class="text-left px-4 py-2">Request ID</th>
          <th class="text-left px-4 py-2">User</th>
          <th class="text-left px-4 py-2">Equipment</th>
          <th class="text-left px-4 py-2">Start</th>
          <th class="text-left px-4 py-2">End</th>
          <th class="text-left px-4 py-2">Status</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="request in requests" :key="request.id">
          <td class="px-4 py-2">{{ request.id }}</td>
          <td class="px-4 py-2">{{ request.user_name }}</td>
          <td class="px-4 py-2">{{ request.equipment_name }}</td>
          <td class="px-4 py-2">{{ request.start }}</td>
          <td class="px-4 py-2">{{ request.end }}</td>
          <td class="px-4 py-2">{{ request.status }}</td>
        </tr>
      </tbody>
    </table>
  </div>
</template>

<script>
export default {
  name: 'RecentAct',
  props: {
    requests: {
      type: Array,
      required: true
    }
  }
}
</script>
