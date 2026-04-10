<script setup>
/**
 * Список бронирований: календарь, фильтры (статус, комната, даты, поиск), таблица, пагинация.
 */
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import MiniCalendar from '@/Components/Admin/MiniCalendar.vue';
import BookingFilters from '@/Components/Admin/Bookings/BookingFilters.vue';
import BookingsTable from '@/Components/Admin/Bookings/BookingsTable.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    bookings: { type: Array, required: true },
    meta: { type: Object, required: true },
    filters: { type: Object, default: () => ({}) },
    rooms: { type: Array, default: () => [] },
    statuses: { type: Array, default: () => [] },
    bookingsByDate: { type: Object, default: () => ({}) },
    stats: {
        type: Object,
        default: () => ({
            new_count: 0,
            paid_count: 0,
            total_revenue: 0,
        }),
    },
});

const { t } = useI18n();

const selectedCalendarDate = ref(
    props.filters?.date_from && props.filters?.date_from === props.filters?.date_to
        ? props.filters.date_from
        : null
);

watch(
    () => [props.filters?.date_from, props.filters?.date_to],
    () => {
        if (props.filters?.date_from && props.filters?.date_from === props.filters?.date_to) {
            selectedCalendarDate.value = props.filters.date_from;
        } else {
            selectedCalendarDate.value = null;
        }
    }
);

const onCalendarSelect = (date) => {
    selectedCalendarDate.value = date;
    router.get(route('panel.bookings.index'), {
        status: props.filters?.status ?? '',
        room_id: props.filters?.room_id ?? '',
        date_from: date ?? '',
        date_to: date ?? '',
        search: props.filters?.search ?? '',
    }, {
        preserveState: true,
        preserveScroll: true,
    });
};

/** URL экспорта с учётом текущих фильтров. */
const exportUrl = computed(() => {
    const params = new URLSearchParams();
    if (props.filters?.status) params.set('status', props.filters.status);
    if (props.filters?.room_id) params.set('room_id', props.filters.room_id);
    if (props.filters?.date_from) params.set('date_from', props.filters.date_from);
    if (props.filters?.date_to) params.set('date_to', props.filters.date_to);
    if (props.filters?.search) params.set('search', props.filters.search);
    const qs = params.toString();
    return qs ? `${route('panel.bookings.export')}?${qs}` : route('panel.bookings.export');
});
</script>

<template>
    <DashboardLayout>
        <Head :title="t('bookings.title')" />
        <template #header>
            <div class="flex flex-1 items-center justify-between gap-4">
                <h1 class="text-xl font-semibold text-gray-800">{{ t('bookings.title') }}</h1>
                <div class="flex items-center gap-2">
                    <a
                        :href="exportUrl"
                        class="inline-flex items-center gap-2 rounded-xl border border-gray-200 bg-white px-4 py-2.5 text-sm font-medium text-gray-600 hover:bg-gray-50 transition"
                    >
                        📊 {{ t('bookings.export') }}
                    </a>
                    <Link
                        :href="route('panel.bookings.create')"
                        class="inline-flex items-center justify-center rounded-lg bg-orange-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2"
                    >
                        {{ t('bookings.new') }}
                    </Link>
                </div>
            </div>
        </template>

        <div class="grid grid-cols-1 gap-6 xl:grid-cols-4">
            <div class="xl:col-span-1">
                <MiniCalendar
                    :bookings-by-date="bookingsByDate"
                    :selected-date="selectedCalendarDate"
                    @select-date="onCalendarSelect"
                />
            </div>
            <div class="xl:col-span-3 space-y-4">
                <!-- Статистика по бронированиям -->
                <div class="grid grid-cols-2 gap-3 mb-1 sm:grid-cols-4">
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-4 py-3">
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">
                            {{ t('bookings.stats_total') }}
                        </p>
                        <p class="text-xl font-bold text-gray-800">{{ meta.total }}</p>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-4 py-3">
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">
                            {{ t('bookings.stats_new') }}
                        </p>
                        <p class="text-xl font-bold text-blue-600">{{ stats.new_count }}</p>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-4 py-3">
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">
                            {{ t('bookings.stats_paid') }}
                        </p>
                        <p class="text-xl font-bold text-emerald-600">{{ stats.paid_count }}</p>
                    </div>
                    <div class="bg-white rounded-xl border border-gray-100 shadow-sm px-4 py-3">
                        <p class="text-xs text-gray-400 uppercase tracking-wide mb-1">
                            {{ t('bookings.stats_revenue') }}
                        </p>
                        <p class="text-xl font-bold text-orange-500">
                            {{ stats.total_revenue }} TMT
                        </p>
                    </div>
                </div>

                <BookingFilters
                    :rooms="rooms"
                    :statuses="statuses"
                    :filters="filters"
                    :export-url="exportUrl"
                />

                <BookingsTable
                    :bookings="bookings"
                    :meta="meta"
                    :filters="filters"
                />
            </div>
        </div>
    </DashboardLayout>
</template>
