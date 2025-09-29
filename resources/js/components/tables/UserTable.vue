<template>
  <div class="bg-white p-6 rounded-lg shadow">
      <!-- Breadcrumb -->
  <nav class="flex items-center space-x-2 text-sm text-gray-500 mb-4" aria-label="Breadcrumb">
    <a href="/admin" class="hover:text-gray-700">แดชบอร์ด</a>
    <span>/</span>
    <span class="font-semibold text-gray-900">หน้าจัดการผู้ใช้</span>
  </nav>
    <!-- Search Bar -->
    <div class="relative mb-4">
      <input type="text" v-model="searchQuery" placeholder="ค้นหาผู้ใช้..."
        class="pl-10 pr-3 py-2 text-sm border rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 w-full sm:w-auto" />
      <svg class="w-4 h-4 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
          d="M21 21l-4.35-4.35m0 0A7.5 7.5 0 104.5 4.5a7.5 7.5 0 0012.15 12.15z" />
      </svg>
    </div>

    <!-- Header + Counts -->
    <div class="flex flex-col gap-3 md:flex-row md:items-center md:justify-between mb-4">
      <h2 class="text-lg font-semibold">
        ผู้ใช้ทั้งหมด: {{ filteredUsers.length }} คน
      </h2>
      <div class="flex flex-wrap gap-2 items-center">
        <!-- Role Filter -->
        <div class="flex gap-2">
          <button 
            v-for="role in roleFilters" 
            :key="role.key"
            @click="setRoleFilter(role.key)"
            :class="[
              'px-4 py-2 text-sm rounded-md font-medium transition-colors',
              activeRoleFilter === role.key 
                ? 'bg-blue-600 text-white' 
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200'
            ]"
          >
            {{ role.label }} ({{ getRoleCount(role.key) }})
          </button>
        </div>
        <!-- Add button (only admin) -->
        <button v-if="userRole === 'admin'" @click="createModalOpen = true"
          class="ml-2 bg-green-600 text-white px-4 py-2 rounded hover:bg-green-700">
          เพิ่มผู้ใช้ใหม่
        </button>
      </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="min-w-full text-sm">
        <thead class="bg-gray-50 border-b">
          <tr>
            <th class="px-4 py-2 text-left cursor-pointer" @click="setSort('uid')">
              รหัสผู้ใช้
              <span v-if="sortKey === 'uid'">{{ sortDirection === 'asc' ? '↑' : '↓' }}</span>
            </th>
            <th class="px-4 py-2 text-left cursor-pointer" @click="setSort('name')">
              ชื่อ
              <span v-if="sortKey === 'name'">{{ sortDirection === 'asc' ? '↑' : '↓' }}</span>
            </th>
            <th class="px-4 py-2 text-left cursor-pointer" @click="setSort('email')">
              อีเมล
              <span v-if="sortKey === 'email'">{{ sortDirection === 'asc' ? '↑' : '↓' }}</span>
            </th>
            <th class="px-4 py-2 text-left">เบอร์โทร</th>
            <th class="px-4 py-2 text-left cursor-pointer" @click="setSort('role')">
              บทบาท
              <span v-if="sortKey === 'role'">{{ sortDirection === 'asc' ? '↑' : '↓' }}</span>
            </th>
            <th class="px-4 py-2 text-left">สถานะ</th>
            <th class="px-4 py-2 text-left cursor-pointer" @click="setSort('created_at')">
              วันที่สร้าง
              <span v-if="sortKey === 'created_at'">{{ sortDirection === 'asc' ? '↑' : '↓' }}</span>
            </th>
            <th v-if="userRole === 'admin'" class="px-4 py-2 text-left">แอคชั่น</th>
          </tr>
        </thead>
        <tbody>
          <tr v-for="user in paginatedUsers" :key="user.id" class="border-b hover:bg-gray-50">
            <td class="px-4 py-2 font-mono">{{ user.uid }}</td>
            <td class="px-4 py-2">{{ user.name }}</td>
            <td class="px-4 py-2">{{ user.email }}</td>
            <td class="px-4 py-2">{{ user.phonenumber || '-' }}</td>
            <td class="px-4 py-2">
              <span :class="getRoleClass(user.role)" class="px-2 py-1 rounded-full text-xs font-medium">
                {{ getRoleLabel(user.role) }}
              </span>
            </td>
            <td class="px-4 py-2">
              <span :class="user.verified ? 'text-green-600' : 'text-red-600'" class="flex items-center">
                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                  <path v-if="user.verified" fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd" />
                  <path v-else fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd" />
                </svg>
                {{ user.verified ? 'ยืนยันแล้ว' : 'ยังไม่ยืนยัน' }}
              </span>
            </td>
            <td class="px-4 py-2">{{ formatDate(user.created_at) }}</td>
            <td class="px-4 py-2 space-x-2">
              <button v-if="userRole === 'admin'" @click="openEditModal(user)"
                class="bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded text-xs">
                แก้ไข
              </button>
              <button v-if="userRole === 'admin' && canDeleteUser(user)" @click="deleteUser(user.id)"
                class="bg-red-500 hover:bg-red-600 text-white px-3 py-1 rounded text-xs">
                ลบ
              </button>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div class="mt-4 flex items-center justify-between">
      <div class="text-sm text-gray-600">
        แสดง {{ pageStart + 1 }} - {{ pageEnd }} จากทั้งหมด {{ filteredUsers.length }} รายการ
      </div>
      <div class="flex items-center space-x-1">
        <button class="px-3 py-1 border rounded disabled:opacity-50" :disabled="currentPage === 1" @click="prevPage">
          ก่อนหน้า
        </button>
        <button v-for="p in visiblePages" :key="p" class="px-3 py-1 border rounded"
          :class="{ 'bg-blue-600 text-white': currentPage === p }" @click="goToPage(p)">
          {{ p }}
        </button>
        <button class="px-3 py-1 border rounded disabled:opacity-50"
          :disabled="currentPage === pageCount || pageCount === 0" @click="nextPage">
          ถัดไป
        </button>
      </div>
    </div>

    <!-- Edit Modal -->
    <UserEditModal :isOpen="editModalOpen" :user="selectedUser" @close="editModalOpen = false"
      @save="updateUserFromModal" />

    <!-- Create Modal -->
    <UserCreateModal :isOpen="createModalOpen" @close="createModalOpen = false" @create="handleCreateUser" />
  </div>
</template>

<script>
import UserEditModal from "../modals/UserEditModal.vue";
import UserCreateModal from "../modals/UserCreateModal.vue";

export default {
  name: "UserTable",
  components: {
    UserEditModal,
    UserCreateModal,
  },
  data() {
    const el = document.getElementById("user-table");
    return {
      users: JSON.parse(el?.dataset?.users || "[]"),
      userRole: el?.dataset?.role || "staff",
      searchQuery: "",
      currentPage: 1,
      pageSize: 15,
      sortKey: "created_at",
      sortDirection: "desc",
      activeRoleFilter: "all",
      editModalOpen: false,
      createModalOpen: false,
      selectedUser: {},
      roleFilters: [
        { key: "all", label: "ทั้งหมด" },
        { key: "admin", label: "ผู้ดูแลระบบ" },
        { key: "staff", label: "เจ้าหน้าที่" },
        { key: "borrower", label: "ผู้ยืม" },
      ],
    };
  },
  computed: {
    filteredUsers() {
      const query = this.searchQuery.toLowerCase();
      let filtered = this.users.filter((user) => {
        const matchesSearch = !query ||
          (user.name && user.name.toLowerCase().includes(query)) ||
          (user.email && user.email.toLowerCase().includes(query)) ||
          (user.uid && user.uid.toLowerCase().includes(query)) ||
          (user.phonenumber && user.phonenumber.includes(query));
        
        const matchesRole = this.activeRoleFilter === "all" || user.role === this.activeRoleFilter;
        
        return matchesSearch && matchesRole;
      });

      // Sort
      filtered.sort((a, b) => {
        let aVal = a[this.sortKey] || "";
        let bVal = b[this.sortKey] || "";
        
        if (this.sortKey === "created_at") {
          aVal = new Date(aVal);
          bVal = new Date(bVal);
        } else {
          aVal = String(aVal).toLowerCase();
          bVal = String(bVal).toLowerCase();
        }
        
        if (this.sortDirection === "asc") {
          return aVal > bVal ? 1 : -1;
        } else {
          return aVal < bVal ? 1 : -1;
        }
      });

      return filtered;
    },
    pageCount() {
      return Math.ceil(this.filteredUsers.length / this.pageSize) || 1;
    },
    pageStart() {
      return (this.currentPage - 1) * this.pageSize;
    },
    pageEnd() {
      const end = this.pageStart + this.pageSize;
      return Math.min(end, this.filteredUsers.length);
    },
    paginatedUsers() {
      return this.filteredUsers.slice(this.pageStart, this.pageEnd);
    },
    visiblePages() {
      const pages = [];
      const start = Math.max(1, this.currentPage - 2);
      const end = Math.min(this.pageCount, this.currentPage + 2);
      
      for (let i = start; i <= end; i++) {
        pages.push(i);
      }
      return pages;
    },
  },
  methods: {
    setSort(key) {
      if (this.sortKey === key) {
        this.sortDirection = this.sortDirection === "asc" ? "desc" : "asc";
      } else {
        this.sortKey = key;
        this.sortDirection = "asc";
      }
    },
    setRoleFilter(role) {
      this.activeRoleFilter = role;
      this.currentPage = 1;
    },
    getRoleCount(role) {
      if (role === "all") return this.users.length;
      return this.users.filter(user => user.role === role).length;
    },
    getRoleClass(role) {
      switch (role) {
        case "admin":
          return "bg-red-100 text-red-800";
        case "staff":
          return "bg-blue-100 text-blue-800";
        case "borrower":
          return "bg-green-100 text-green-800";
        default:
          return "bg-gray-100 text-gray-800";
      }
    },
    getRoleLabel(role) {
      switch (role) {
        case "admin":
          return "ผู้ดูแลระบบ";
        case "staff":
          return "เจ้าหน้าที่";
        case "borrower":
          return "ผู้ยืม";
        default:
          return role;
      }
    },
    formatDate(dateString) {
      if (!dateString) return "-";
      const date = new Date(dateString);
      if (isNaN(date.getTime())) return dateString; // Return original if invalid date
      
      const day = date.getDate().toString().padStart(2, '0');
      const month = (date.getMonth() + 1).toString().padStart(2, '0');
      const year = date.getFullYear();
      
      return `${day}/${month}/${year}`;
    },
    canDeleteUser(user) {
      // Can't delete yourself or the last admin
      if (user.role === "admin") {
        const adminCount = this.users.filter(u => u.role === "admin").length;
        return adminCount > 1;
      }
      return true;
    },
    goToPage(p) {
      this.currentPage = p;
    },
    nextPage() {
      if (this.currentPage < this.pageCount) this.currentPage += 1;
    },
    prevPage() {
      if (this.currentPage > 1) this.currentPage -= 1;
    },
    openEditModal(user) {
      this.selectedUser = { ...user };
      this.editModalOpen = true;
    },
    updateUserFromModal(updatedUser) {
      const formData = new FormData();
      formData.append("uid", updatedUser.uid || "");
      formData.append("name", updatedUser.name || "");
      formData.append("email", updatedUser.email || "");
      formData.append("phonenumber", updatedUser.phonenumber || "");
      formData.append("role", updatedUser.role || "borrower");
      
      if (updatedUser.password) {
        formData.append("password", updatedUser.password);
      }
      
      formData.append("_method", "PUT");

      fetch(`/admin/user/update/${updatedUser.id}`, {
        method: "POST",
        headers: {
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
          Accept: "application/json",
        },
        body: formData,
      })
        .then(async (res) => {
          if (!res.ok) {
            const errorData = await res.json();
            throw new Error(errorData.message || "Update failed");
          }
          return res.json();
        })
        .then((data) => {
          const idx = this.users.findIndex((u) => u.id === updatedUser.id);
          if (idx !== -1) {
            this.users.splice(idx, 1, data.data);
          }
          this.editModalOpen = false;
          this.showSuccessMessage("อัปเดตข้อมูลผู้ใช้สำเร็จ");
        })
        .catch((err) => {
          this.showErrorMessage(err.message || "ไม่สามารถอัปเดตข้อมูลได้");
        });
    },
    handleCreateUser(newUser) {
      const formData = new FormData();
      formData.append("uid", newUser.uid || "");
      formData.append("name", newUser.name || "");
      formData.append("email", newUser.email || "");
      formData.append("phonenumber", newUser.phonenumber || "");
      formData.append("password", newUser.password || "");
      formData.append("role", newUser.role || "borrower");

      fetch("/admin/user/store", {
        method: "POST",
        headers: {
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
          Accept: "application/json",
        },
        body: formData,
      })
        .then(async (res) => {
          if (!res.ok) {
            const errorData = await res.json();
            throw new Error(errorData.message || "Create failed");
          }
          return res.json();
        })
        .then((data) => {
          this.users.unshift(data.data);
          this.createModalOpen = false;
          this.showSuccessMessage("เพิ่มผู้ใช้ใหม่สำเร็จ");
        })
        .catch((err) => {
          this.showErrorMessage(err.message || "ไม่สามารถเพิ่มผู้ใช้ได้");
        });
    },
    deleteUser(userId) {
      if (!window.Swal) {
        if (confirm("คุณต้องการลบผู้ใช้นี้ใช่หรือไม่?")) {
          this.performDelete(userId);
        }
        return;
      }

      window.Swal.fire({
        title: "ยืนยันการลบ",
        text: "คุณต้องการลบผู้ใช้นี้ใช่หรือไม่?",
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "ลบ",
        cancelButtonText: "ยกเลิก",
        confirmButtonColor: "#ef4444",
      }).then((result) => {
        if (result.isConfirmed) {
          this.performDelete(userId);
        }
      });
    },
    performDelete(userId) {
      fetch(`/admin/user/destroy/${userId}`, {
        method: "DELETE",
        headers: {
          "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]').content,
          Accept: "application/json",
        },
      })
        .then(async (res) => {
          if (!res.ok) {
            const errorData = await res.json();
            throw new Error(errorData.message || "Delete failed");
          }
          return res.json();
        })
        .then(() => {
          this.users = this.users.filter((u) => u.id !== userId);
          this.showSuccessMessage("ลบผู้ใช้สำเร็จ");
        })
        .catch((err) => {
          this.showErrorMessage(err.message || "ไม่สามารถลบผู้ใช้ได้");
        });
    },
    showSuccessMessage(message) {
      if (window.Swal) {
        window.Swal.fire({
          title: "สำเร็จ",
          text: message,
          icon: "success",
          timer: 2000,
          showConfirmButton: false,
        });
      } else {
        alert(message);
      }
    },
    showErrorMessage(message) {
      if (window.Swal) {
        window.Swal.fire({
          title: "เกิดข้อผิดพลาด",
          text: message,
          icon: "error",
        });
      } else {
        alert(message);
      }
    },
  },
};
</script>
