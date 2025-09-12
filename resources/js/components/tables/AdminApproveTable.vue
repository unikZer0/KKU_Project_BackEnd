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
          <tr v-for="request in filteredRequests" :key="request.id">
            <td class="px-4 py-2">{{ request.req_id }}</td>
            <td class="px-4 py-2">{{ request.user_name }}</td>
            <td class="px-4 py-2">{{ request.equipment_name }}</td>
            <td class="px-4 py-2">{{ request.date }}</td>
            <td class="px-4 py-2">{{ request.status }}</td>
            <td class="px-4 py-2">
              <!-- <button
                v-if="request.status.toLowerCase() === 'pending'"
                @click="rejectRequest(request.req_id)"
                class="bg-red-500 text-white mx-2 px-2 py-1 rounded"
              >
                ปฏิเสธ
              </button> -->
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
  </div>
</template>

<script>
import axios from "axios";

export default {
  name: "AdminApproveTable",
  props: { requests: Array },
  data() {
    return {
      selectedRequest: null,
      searchQuery: "",
    };
  },
  computed: {
    filteredRequests() {
      // filter out rejected and check_in
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
          r.status.toLowerCase().includes(q)
      );
    },
  },
  methods: {
    openDetails(request) {
      this.selectedRequest = request;
    },
    rejectRequest(req_id) {
      Swal.fire({
        title: "ปฏิเสธคำขอ",
        html: `
          <div class="text-left space-y-2">
            <label class="flex items-center gap-2">
              <input type="radio" name="reason" value="ไม่ตรงตามเงื่อนไขการยืม"> ไม่ตรงตามเงื่อนไขการยืม
            </label>
            <label class="flex items-center gap-2">
              <input type="radio" name="reason" value="อุปกรณ์ไม่พร้อมใช้งาน"> อุปกรณ์ไม่พร้อมใช้งาน
            </label>
            <label class="flex items-center gap-2">
              <input type="radio" name="reason" value="เอกสารไม่ครบถ้วน"> เอกสารไม่ครบถ้วน
            </label>
            <label class="flex items-center gap-2">
              <input type="radio" name="reason" value="อื่นๆ"> อื่นๆ
            </label>
            <input id="reason-text" type="text" placeholder="ระบุเหตุผลเพิ่มเติม (ถ้าเลือก อื่นๆ)" class="w-full border rounded px-2 py-1" />
          </div>
        `,
        showCancelButton: true,
        confirmButtonText: "ปฏิเสธ",
        cancelButtonText: "ยกเลิก",
      }).then((result) => {
        if (result.isConfirmed) {
          const reasonInput = document.querySelector(
            'input[name="reason"]:checked'
          )?.value;
          const extra = document.getElementById("reason-text").value;
          const reason = reasonInput === "อื่นๆ" ? extra : reasonInput;

          axios
            .post(`/admin/requests/${req_id}/reject`, { reason })
            .then(() => {
              Swal.fire({
                icon: "success",
                title: "คำขอถูกปฏิเสธ",
                text: "คำขอถูกปฏิเสธเรียบร้อยแล้ว",
              }).then(() => window.location.reload());
            });
        }
      });
    },
  },
};
</script>
