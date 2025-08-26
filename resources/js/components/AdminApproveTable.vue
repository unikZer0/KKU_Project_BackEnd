<template>
  <div class="overflow-x-auto p-4">
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
          <td class="px-4 py-2">{{ request.user.name }}</td>
          <td class="px-4 py-2">{{ request.equipment.name }}</td>
          <td class="px-4 py-2">{{ request.created_at }}</td>
          <td class="px-4 py-2">{{ request.status }}</td>
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
  }
}
</script>
