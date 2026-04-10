<script setup>
/**
 * Дашборд админки: статистика, заявки на сегодня, именинники и мини-календарь.
 */
import { computed, ref } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import MiniCalendar from '@/Components/Admin/MiniCalendar.vue';

const { t } = useI18n();
const currentLocale = computed(() => usePage().props.locale || 'ru');

const props = defineProps({
    todayBookings: { type: Array, default: () => [] },
    birthdayClients: { type: Array, default: () => [] },
    bookingsByDate: { type: Object, default: () => ({}) },
    stats: { type: Object, default: () => ({}) },
});

const selectedDate = ref(null);

/** Переход к списку броней, отфильтрованных по выбранной дате. */
function onCalendarSelect(date) {
    selectedDate.value = date;
    if (date) {
        router.visit(route('panel.bookings.index'), {
            data: { date_from: date, date_to: date },
        });
    }
}

/** Цвета бейджей статуса брони. */
const statusColors = {
    new: 'bg-blue-100 text-blue-700',
    preliminary: 'bg-yellow-100 text-yellow-700',
    confirmed: 'bg-green-100 text-green-700',
    paid: 'bg-emerald-100 text-emerald-700',
    completed: 'bg-gray-100 text-gray-600',
    cancelled: 'bg-red-100 text-red-700',
};
</script>

<template>
    <DashboardLayout>
        <!-- Page title -->
        <div class="mb-6 flex items-center justify-between">
            <h1 class="text-xl font-semibold text-gray-800">
                {{ t('dashboard.title') }}
            </h1>
            <span class="text-sm text-gray-400">
                {{
                    new Date().toLocaleDateString(currentLocale === 'tk' ? 'tk' : 'ru-RU', {
                        weekday: 'long',
                        day: 'numeric',
                        month: 'long',
                    })
                }}
            </span>
        </div>

        <!-- Stats + Calendar (one row on xl) -->
        <div class="grid grid-cols-1 xl:grid-cols-3 gap-6 mb-6">
            <!-- Stats cards -->
            <div class="xl:col-span-2 grid grid-cols-2 lg:grid-cols-4 gap-4">
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400 mb-1">
                        {{ t('dashboard.today_bookings') }}
                    </p>
                    <p class="text-3xl font-bold text-gray-800">
                        {{ stats.today_count }}
                    </p>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400 mb-1">
                        {{ t('dashboard.today_revenue') }}
                    </p>
                    <p class="text-3xl font-bold text-orange-500">
                        {{ stats.today_revenue }} <span class="text-lg">TMT</span>
                    </p>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400 mb-1">
                        {{ t('dashboard.month_revenue') }}
                    </p>
                    <p class="text-3xl font-bold text-gray-800">
                        {{ stats.month_revenue }} <span class="text-lg text-gray-400">TMT</span>
                    </p>
                </div>

                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-5">
                    <p class="text-xs font-medium uppercase tracking-wide text-gray-400 mb-1">
                        {{ t('dashboard.total_clients') }}
                    </p>
                    <p class="text-3xl font-bold text-gray-800">
                        {{ stats.total_clients }}
                    </p>
                </div>
            </div>

            <!-- Calendar -->
            <div class="xl:col-span-1">
                <MiniCalendar
                    :bookings-by-date="bookingsByDate"
                    :selected-date="selectedDate"
                    @select-date="onCalendarSelect"
                />
            </div>
        </div>

        <!-- Main grid -->
        <div class="flex flex-col gap-6">
            <!-- Bookings table + birthdays -->
            <div class="flex flex-col gap-6">
                <!-- Quick actions -->
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-3">
                    <button
                        v-for="action in [
                            { label: t('dashboard.new_booking'), icon: '📅', route: 'panel.bookings.create', color: 'bg-orange-50 hover:bg-orange-100 text-orange-700' },
                            { label: t('dashboard.new_client'), icon: '👤', route: 'panel.clients.create', color: 'bg-blue-50 hover:bg-blue-100 text-blue-700' },
                            { label: t('dashboard.rooms'), icon: '🛁', route: 'panel.rooms.index', color: 'bg-purple-50 hover:bg-purple-100 text-purple-700' },
                            { label: t('dashboard.all_bookings'), icon: '📋', route: 'panel.bookings.index', color: 'bg-green-50 hover:bg-green-100 text-green-700' },
                        ]"
                        :key="action.route"
                        type="button"
                        :class="['flex flex-col items-center gap-2 p-4 rounded-2xl text-sm font-medium transition', action.color]"
                        @click="router.visit(route(action.route))"
                    >
                        <span class="text-2xl">{{ action.icon }}</span>
                        <span class="text-center leading-tight">{{ action.label }}</span>
                    </button>
                </div>

                <!-- Today bookings table -->
                <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-6">
                    <div class="flex items-center justify-between mb-4">
                        <h2 class="font-semibold text-gray-800">
                            {{ t('dashboard.today_bookings_title') }}
                        </h2>
                        <span class="text-xs text-gray-400">
                            {{ todayBookings.length }} {{ t('dashboard.bookings_count') }}
                        </span>
                    </div>

                    <!-- Empty state -->
                    <div v-if="!todayBookings.length" class="py-10 text-center text-sm text-gray-400">
                        {{ t('dashboard.no_bookings_today') }}
                    </div>

                    <!-- Table -->
                    <div v-else class="overflow-x-auto">
                        <table class="w-full text-sm">
                            <thead>
                                <tr class="text-left text-xs text-gray-400 border-b border-gray-100">
                                    <th class="pb-3 pr-4 font-medium">{{ t('dashboard.col_time') }}</th>
                                    <th class="pb-3 pr-4 font-medium">{{ t('dashboard.col_room') }}</th>
                                    <th class="pb-3 pr-4 font-medium">{{ t('dashboard.col_client') }}</th>
                                    <th class="pb-3 pr-4 font-medium">{{ t('dashboard.col_status') }}</th>
                                    <th class="pb-3 font-medium">{{ t('dashboard.col_amount') }}</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr
                                    v-for="booking in todayBookings"
                                    :key="booking.id"
                                    class="hover:bg-gray-50 cursor-pointer transition"
                                    @click="router.visit(route('panel.bookings.show', booking.id))"
                                >
                                    <td class="py-3 pr-4 font-medium text-gray-700">
                                        {{ booking.start_time }} — {{ booking.end_time }}
                                    </td>
                                    <td class="py-3 pr-4 text-gray-600">
                                        {{ booking.room?.name?.[currentLocale] ?? booking.room?.name }}
                                    </td>
                                    <td class="py-3 pr-4 text-gray-600">
                                        {{ booking.client?.full_name ?? booking.guest_name ?? '—' }}
                                        <span v-if="booking.source === 'online'" class="ml-1 text-xs text-orange-500">🌐</span>
                                    </td>
                                    <td class="py-3 pr-4">
                                        <span class="px-2 py-1 rounded-full text-xs font-medium" :class="statusColors[booking.status]">
                                            {{ t('bookings.statuses.' + booking.status) }}
                                        </span>
                                    </td>
                                    <td class="py-3 font-medium text-gray-700">
                                        {{ booking.final_amount }} TMT
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Birthday clients -->
                <div
                    v-if="birthdayClients.length"
                    class="bg-white rounded-2xl shadow-sm border border-pink-100 p-6"
                >
                    <h2 class="font-semibold text-gray-800 mb-4">
                        🎂 {{ t('dashboard.birthdays_today') }}
                    </h2>
                    <div class="flex flex-col gap-3">
                        <div
                            v-for="client in birthdayClients"
                            :key="client.id"
                            class="flex items-center justify-between p-3 bg-pink-50
                                   rounded-xl cursor-pointer hover:bg-pink-100 transition"
                            @click="router.visit(route('panel.clients.show', client.id))"
                        >
                            <div class="flex items-center gap-3">
                                <span class="text-2xl">🎂</span>
                                <div>
                                    <p class="font-medium text-gray-800 text-sm">
                                        {{ client.full_name }}
                                    </p>
                                    <p class="text-xs text-gray-500">
                                        {{ client.phone }}
                                    </p>
                                </div>
                            </div>
                            <span class="text-xs text-gray-400">
                                {{ client.bookings_count }}
                                {{ t('dashboard.bookings_count') }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>

