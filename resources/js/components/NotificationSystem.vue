<template>
  <div class="relative inline-block">
    <!-- Notification Button -->
    <button @click="toggleDropdown" class="p-2 rounded-full hover:bg-gray-200 relative">
      <svg xmlns="http://www.w3.org/2000/svg" width="30" height="30" viewBox="0 0 64 64">
        <path d="M 32 10 C 29.662 10 28.306672 11.604938 27.638672 13.085938 C 24.030672 13.809937 17.737984 16.956187 16.958984 24.742188 C 16.665984 29.334188 16.1185 37.883781 13.0625 39.300781 C 12.8505 39.398781 12.655234 39.533219 12.490234 39.699219 C 12.235234 39.954219 10 42.294 10 46 C 10 47.104 10.896 48 12 48 L 25.257812 48 C 25.652433 51.372928 28.522752 54 32 54 C 35.477248 54 38.347567 51.372928 38.742188 48 L 52 48 C 53.104 48 54 47.104 54 46 C 54 42.294 51.764766 39.954219 51.509766 39.699219 C 51.344766 39.534219 51.1495 39.397828 50.9375 39.298828 C 47.8825 37.881828 47.333203 29.333922 47.033203 24.669922 C 46.258203 16.945922 39.966375 13.806984 36.359375 13.083984 C 35.692375 11.603984 34.338 10 32 10 z M 32 14 C 32.603 14 32.766719 14.619859 32.886719 15.255859 C 33.063719 16.190859 33.884422 16.914062 34.857422 16.914062 C 34.931422 16.914063 42.311828 17.650047 43.048828 24.998047 C 43.557828 32.932047 44.389891 40.250797 48.837891 42.716797 C 49.024891 42.956797 49.333937 43.401 49.585938 44 L 14.414062 44 C 14.667063 43.397 14.976203 42.95375 15.158203 42.71875 C 19.609203 40.25475 20.442312 32.935313 20.945312 25.070312 C 21.688313 17.650312 29.068578 16.914062 29.142578 16.914062 C 30.099578 16.914062 30.934375 16.156391 31.109375 15.275391 C 31.232375 14.660391 31.396 14 32 14 z M 29.335938 48 L 34.664062 48 C 34.319789 49.152328 33.262739 50 32 50 C 30.737261 50 29.680211 49.152328 29.335938 48 z"></path>
      </svg>
      
      <!-- Notification Count Badge -->
      <span v-if="unreadCount > 0" class="absolute top-0 right-0 inline-flex items-center justify-center px-1.5 py-0.5 text-xs font-bold leading-none text-white bg-red-600 rounded-full">
        {{ unreadCount }}
      </span>
    </button>

    <!-- Notification Dropdown -->
    <div v-show="isOpen" class="absolute right-0 mt-2 w-80 bg-white rounded-lg shadow-lg border border-gray-200 z-50">
      <div class="p-4 border-b flex justify-between items-center">
        <span class="font-semibold text-gray-700">Notifications</span>
        <button @click="markAllAsRead" class="text-sm text-blue-600 hover:underline">
          Mark all read
        </button>
      </div>

      <div class="max-h-72 overflow-y-auto">
        <!-- Loading State -->
        <div v-if="loading" class="p-4 text-center text-gray-500">
          <div class="animate-spin rounded-full h-6 w-6 border-b-2 border-blue-600 mx-auto"></div>
          <p class="mt-2">Loading notifications...</p>
        </div>

        <!-- Notifications List -->
        <div v-else-if="notifications.length > 0">
          <div 
            v-for="notification in notifications" 
            :key="notification.id"
            class="px-4 py-3 hover:bg-gray-100 border-b flex justify-between items-start cursor-pointer"
            :class="{ 'opacity-60 bg-gray-50': notification.read_at }"
            @click="handleNotificationClick(notification)"
          >
            <div class="flex-1">
              <div class="font-semibold text-gray-800">
                {{ notification.data.user || 'admin' }}
              </div>
              
              <!-- Status Message -->
              <div v-if="notification.data.status" class="text-sm" :class="getStatusColor(notification.data.status)">
                {{ notification.data.message }}
              </div>
              
              <div class="text-xs text-gray-400 mt-1">
                อุปกรณ์: {{ notification.data.equipment }} |
                {{ formatTime(notification.created_at) }}
              </div>
            </div>
            
            <button 
              v-if="!notification.read_at"
              @click.stop="markAsRead(notification.id)"
              class="text-xs text-blue-600 hover:underline ml-2"
            >
              Mark read
            </button>
          </div>
        </div>

        <!-- Empty State -->
        <div v-else class="p-4 text-gray-500 text-center">
          ยังไม่มีการแจ้งเตือน
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { ref, onMounted, onUnmounted } from 'vue'

export default {
  name: 'NotificationSystem',
  setup() {
    const isOpen = ref(false)
    const notifications = ref([])
    const unreadCount = ref(0)
    const loading = ref(false)
    let pollingInterval = null

    // Fetch notifications from API
    const fetchNotifications = async () => {
      try {
        loading.value = true
        const response = await fetch('/api/notifications', {
          method: 'GET',
          headers: {
            'Accept': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
          }
        })
        
        if (response.ok) {
          const data = await response.json()
          notifications.value = data.notifications || []
          unreadCount.value = data.unread_count || 0
        }
      } catch (error) {
        console.error('Error fetching notifications:', error)
      } finally {
        loading.value = false
      }
    }

    // Mark single notification as read
    const markAsRead = async (notificationId) => {
      try {
        const response = await fetch(`/notifications/mark-read/${notificationId}`, {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Accept': 'application/json'
          }
        })
        
        if (response.ok) {
          const data = await response.json()
          if (data.success) {
            // Update local state
            const notification = notifications.value.find(n => n.id === notificationId)
            if (notification) {
              notification.read_at = new Date().toISOString()
            }
            unreadCount.value = data.unread_count || 0
          }
        }
      } catch (error) {
        console.error('Error marking notification as read:', error)
      }
    }

    // Mark all notifications as read
    const markAllAsRead = async () => {
      try {
        const response = await fetch('/notifications/mark-all-read', {
          method: 'POST',
          headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '',
            'Accept': 'application/json'
          }
        })
        
        if (response.ok) {
          const data = await response.json()
          if (data.success) {
            // Update local state
            notifications.value.forEach(notification => {
              notification.read_at = new Date().toISOString()
            })
            unreadCount.value = 0
          }
        }
      } catch (error) {
        console.error('Error marking all notifications as read:', error)
      }
    }

    // Handle notification click
    const handleNotificationClick = async (notification) => {
      // Mark as read if not already read
      if (!notification.read_at) {
        await markAsRead(notification.id)
      }
      
      // Close dropdown
      isOpen.value = false
      
      // Redirect if URL is provided
      if (notification.data.url && notification.data.url !== '#') {
        window.location.href = notification.data.url
      }
    }

    // Toggle dropdown
    const toggleDropdown = () => {
      isOpen.value = !isOpen.value
      if (isOpen.value) {
        fetchNotifications()
      }
    }

    // Get status color class
    const getStatusColor = (status) => {
      switch (status) {
        case 'rejected':
          return 'text-red-600'
        case 'approved':
          return 'text-green-600'
        case 'cancelled':
          return 'text-red-600'
        case 'warning':
          return 'text-orange-600'
        case 'auto_rejected':
          return 'text-red-600'
        case 'verification_auto_rejected':
          return 'text-red-600'
        default:
          return 'text-yellow-600'
      }
    }

    // Format time for display
    const formatTime = (dateString) => {
      const date = new Date(dateString)
      const now = new Date()
      const diffInMinutes = Math.floor((now - date) / (1000 * 60))
      
      if (diffInMinutes < 1) return 'เมื่อสักครู่'
      if (diffInMinutes < 60) return `${diffInMinutes} นาทีที่แล้ว`
      
      const diffInHours = Math.floor(diffInMinutes / 60)
      if (diffInHours < 24) return `${diffInHours} ชั่วโมงที่แล้ว`
      
      const diffInDays = Math.floor(diffInHours / 24)
      return `${diffInDays} วันที่แล้ว`
    }

    // Start polling for new notifications
    const startPolling = () => {
      // Fetch immediately
      fetchNotifications()
      
      // Then poll every 30 seconds
      pollingInterval = setInterval(fetchNotifications, 30000)
    }

    // Stop polling
    const stopPolling = () => {
      if (pollingInterval) {
        clearInterval(pollingInterval)
        pollingInterval = null
      }
    }

    // Close dropdown when clicking outside
    const handleClickOutside = (event) => {
      if (!event.target.closest('.relative')) {
        isOpen.value = false
      }
    }

    // Lifecycle hooks
    onMounted(() => {
      startPolling()
      document.addEventListener('click', handleClickOutside)
    })

    onUnmounted(() => {
      stopPolling()
      document.removeEventListener('click', handleClickOutside)
    })

    return {
      isOpen,
      notifications,
      unreadCount,
      loading,
      toggleDropdown,
      markAsRead,
      markAllAsRead,
      handleNotificationClick,
      getStatusColor,
      formatTime
    }
  }
}
</script>

<style scoped>
/* Custom styles if needed */
</style>
