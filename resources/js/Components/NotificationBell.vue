<script>
import { ref, onMounted } from 'vue';
import axios from 'axios';

export default {
    setup() {
        const notifications = ref([]);
        const unreadCount = ref(0);
        const dropdownOpen = ref(false);

        const fetchNotifications = async () => {
            const response = await axios.get('/notifications');
            notifications.value = response.data;
            unreadCount.value = notifications.value.filter(n => !n.read).length;
        };

        const toggleDropdown = () => {
            dropdownOpen.value = !dropdownOpen.value;
        };

        onMounted(fetchNotifications);

        return { notifications, unreadCount, dropdownOpen, toggleDropdown };
    }
};
</script>
<template>
    <div class="relative">
        <button @click="toggleDropdown" class="relative">
            <svg class="w-6 h-6 text-gray-700" fill="currentColor" viewBox="0 0 20 20">
                <path d="M10 2a6 6 0 00-6 6v4H3a1 1 0 000 2h14a1 1 0 000-2h-1V8a6 6 0 00-6-6zM8 18a2 2 0 104 0H8z"/>
            </svg>
            <span v-if="unreadCount > 0" class="absolute top-0 right-0 inline-block w-2 h-2 bg-red-600 rounded-full"></span>
        </button>
        <div v-if="dropdownOpen" class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-lg">
            <ul>
                <li v-for="notification in notifications" :key="notification.id" class="px-4 py-2">
                    <a :href="`/applicant/${notification.applicant_id}/show`">
                        {{ notification.message }}
                    </a>
                </li>
            </ul>
        </div>
    </div>
</template>