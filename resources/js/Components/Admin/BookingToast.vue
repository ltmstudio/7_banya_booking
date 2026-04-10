<script setup>
/**
 * Toast для новых уведомлений о заявках с сайта (показывает первое непрочитанное).
 */
import { computed } from 'vue';
import { router } from '@inertiajs/vue3';
import { useNotificationStore } from '@/Stores/notificationStore';

const store = useNotificationStore();
const toast = computed(() =>
    store.notifications.find((n) => !n.read) ?? null
);
</script>

<template>
    <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="translate-y-4 opacity-0"
        enter-to-class="translate-y-0 opacity-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="translate-y-0 opacity-100"
        leave-to-class="translate-y-4 opacity-0"
    >
        <div
            v-if="toast"
            class="fixed bottom-6 right-6 z-50 w-80 rounded-2xl bg-white
                   shadow-2xl border border-orange-100 p-4 cursor-pointer"
            @click="router.visit(route('panel.bookings.show', toast.booking.id))"
        >
            <div class="flex items-start gap-3">
                <span class="text-2xl flex-shrink-0">🔔</span>
                <div class="flex-1 min-w-0">
                    <p class="font-semibold text-gray-800 text-sm">
                        Новая заявка с сайта!
                    </p>
                    <p class="text-xs text-gray-600 mt-1 truncate">
                        {{ toast.booking.guest_name }} •
                        {{ toast.booking.room_name }}
                    </p>
                    <p class="text-xs text-gray-400 mt-0.5">
                        {{ toast.booking.booking_date }}
                        в {{ toast.booking.start_time }} •
                        {{ toast.booking.amount }} TMT
                    </p>
                </div>
                <button
                    @click.stop="store.remove(toast.id)"
                    class="text-gray-300 hover:text-gray-500 flex-shrink-0"
                >✕</button>
            </div>
        </div>
    </Transition>
</template>

