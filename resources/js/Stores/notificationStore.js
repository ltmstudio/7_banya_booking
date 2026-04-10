import { defineStore } from 'pinia';
import { ref } from 'vue';

/**
 * Хранилище уведомлений админки: список, счётчик непрочитанных и авто-удаление.
 */
export const useNotificationStore = defineStore('notifications', () => {
    const notifications = ref([]);
    const unreadCount = ref(0);

    function add(data) {
        const notif = {
            id: Date.now(),
            read: false,
            createdAt: new Date(),
            booking: data,
        };
        notifications.value.unshift(notif);
        unreadCount.value++;

        // Auto remove after 8 seconds
        setTimeout(() => remove(notif.id), 8000);
    }

    function remove(id) {
        const idx = notifications.value.findIndex((n) => n.id === id);
        if (idx !== -1) notifications.value.splice(idx, 1);
    }

    function markAllRead() {
        notifications.value.forEach((n) => (n.read = true));
        unreadCount.value = 0;
    }

    return { notifications, unreadCount, add, remove, markAllRead };
});

