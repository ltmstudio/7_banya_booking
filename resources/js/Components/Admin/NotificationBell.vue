<script setup>
/**
 * Колокол уведомлений админки: слушает Reverb канал и показывает список новых онлайн-броней.
 */
import { ref, onMounted, onUnmounted } from 'vue';
import { router } from '@inertiajs/vue3';
import { useNotificationStore } from '@/Stores/notificationStore';

const store = useNotificationStore();
const isOpen = ref(false);

onMounted(() => {
    window.Echo.channel('admin-notifications')
        .listen('.booking.created', (data) => {
            store.add(data);
            playSound();
        });
});

onUnmounted(() => {
    window.Echo.leaveChannel('admin-notifications');
});

function playSound() {
    try {
        const audio = new Audio('/sounds/notification.wav')
        // Браузер разрешает звук только после взаимодействия пользователя
        const playPromise = audio.play()
        if (playPromise !== undefined) {
            playPromise.catch(() => {
                // Тихо игнорируем — звук не критичен
            })
        }
    } catch {}
}

function toggle() {
    isOpen.value = !isOpen.value;
    if (isOpen.value) store.markAllRead();
}

function goToBooking(id) {
    isOpen.value = false;
    router.visit(route('panel.bookings.show', id));
}
</script>

<template>
    <div class="relative">
        <!-- Bell button -->
        <button
            @click="toggle"
            class="relative flex h-9 w-9 items-center justify-center
                   rounded-lg hover:bg-gray-100 transition text-gray-500"
        >
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                    d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118
                       14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0
                       10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0
                       .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3
                       0 11-6 0v-1m6 0H9"/>
            </svg>
            <!-- Badge -->
            <span
                v-if="store.unreadCount > 0"
                class="absolute -right-1 -top-1 flex h-4 w-4 items-center
                       justify-center rounded-full bg-red-500 text-xs
                       font-bold text-white"
            >
                {{ store.unreadCount > 9 ? '9+' : store.unreadCount }}
            </span>
        </button>

        <!-- Dropdown -->
        <Transition
            enter-active-class="transition duration-150 ease-out"
            enter-from-class="opacity-0 scale-95"
            enter-to-class="opacity-100 scale-100"
            leave-active-class="transition duration-100 ease-in"
            leave-from-class="opacity-100 scale-100"
            leave-to-class="opacity-0 scale-95"
        >
            <div
                v-if="isOpen"
                class="absolute right-0 top-11 z-50 w-80 rounded-2xl bg-white
                       shadow-xl border border-gray-100 overflow-hidden"
            >
                <div class="flex items-center justify-between px-4 py-3
                            border-b border-gray-100">
                    <span class="text-sm font-semibold text-gray-800">
                        Уведомления
                    </span>
                    <span v-if="store.unreadCount > 0"
                        class="text-xs text-gray-400">
                        {{ store.unreadCount }} новых
                    </span>
                </div>

                <!-- Empty -->
                <div v-if="!store.notifications.length"
                    class="py-10 text-center text-sm text-gray-400">
                    Нет уведомлений
                </div>

                <!-- List -->
                <div v-else class="max-h-72 overflow-y-auto divide-y divide-gray-50">
                    <div
                        v-for="notif in store.notifications"
                        :key="notif.id"
                        :class="[
                            'flex items-start gap-3 px-4 py-3 cursor-pointer',
                            'hover:bg-gray-50 transition',
                            !notif.read ? 'bg-orange-50' : ''
                        ]"
                        @click="goToBooking(notif.booking.id)"
                    >
                        <span class="text-xl mt-0.5 flex-shrink-0">🔔</span>
                        <div class="flex-1 min-w-0">
                            <p class="text-sm font-medium text-gray-800">
                                Новая заявка с сайта
                            </p>
                            <p class="text-xs text-gray-500 mt-0.5 truncate">
                                {{ notif.booking.guest_name }} •
                                {{ notif.booking.room_name }}
                            </p>
                            <p class="text-xs text-gray-400 mt-0.5">
                                {{ notif.booking.booking_date }}
                                в {{ notif.booking.start_time }}
                            </p>
                        </div>
                        <button
                            @click.stop="store.remove(notif.id)"
                            class="text-gray-300 hover:text-gray-500 flex-shrink-0"
                        >✕</button>
                    </div>
                </div>
            </div>
        </Transition>

        <!-- Backdrop -->
        <div v-if="isOpen"
            class="fixed inset-0 z-40"
            @click="isOpen = false"
        />
    </div>
</template>

