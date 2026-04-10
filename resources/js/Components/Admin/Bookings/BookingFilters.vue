<script setup>
/**
 * Фильтры списка бронирований: статус, комната, диапазон дат и поиск.
 */
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    rooms: { type: Array, default: () => [] },
    statuses: { type: Array, default: () => [] },
    filters: { type: Object, default: () => ({}) },
    exportUrl: { type: String, default: '' },
});
const emit = defineEmits(['submit']);

const { t } = useI18n();

const filterForm = useForm({
    status: props.filters?.status ?? '',
    room_id: props.filters?.room_id ?? '',
    date_from: props.filters?.date_from ?? '',
    date_to: props.filters?.date_to ?? '',
    search: props.filters?.search ?? '',
});

const submitFilters = () => {
    filterForm.get(route('panel.bookings.index'), { preserveState: true });
    emit('submit');
};

const _exportUrl = computed(() => {
    const params = new URLSearchParams();
    if (filterForm.status) params.set('status', filterForm.status);
    if (filterForm.room_id) params.set('room_id', filterForm.room_id);
    if (filterForm.date_from) params.set('date_from', filterForm.date_from);
    if (filterForm.date_to) params.set('date_to', filterForm.date_to);
    if (filterForm.search) params.set('search', filterForm.search);
    const qs = params.toString();
    return qs ? `${route('panel.bookings.export')}?${qs}` : route('panel.bookings.export');
});
</script>

<template>
    <form class="rounded-lg bg-white p-4 shadow" @submit.prevent="submitFilters">
        <div class="flex flex-wrap items-end gap-3">
            <div class="min-w-[120px]">
                <label class="mb-1 block text-xs font-medium text-gray-500">{{ t('bookings.filter_status') }}</label>
                <select
                    v-model="filterForm.status"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-300 focus:outline-none focus:ring-1 focus:ring-orange-400/20"
                >
                    <option value="">{{ t('bookings.filter_all') }}</option>
                    <option v-for="s in statuses" :key="s.value" :value="s.value">{{ t('bookings.statuses.' + s.value) }}</option>
                </select>
            </div>
            <div class="min-w-[140px]">
                <label class="mb-1 block text-xs font-medium text-gray-500">{{ t('bookings.filter_room') }}</label>
                <select
                    v-model="filterForm.room_id"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-300 focus:outline-none focus:ring-1 focus:ring-orange-400/20"
                >
                    <option value="">{{ t('bookings.filter_all') }}</option>
                    <option v-for="r in rooms" :key="r.id" :value="r.id">{{ r.name }}</option>
                </select>
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-gray-500">{{ t('bookings.filter_date_from') }}</label>
                <input
                    v-model="filterForm.date_from"
                    type="date"
                    class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-300 focus:outline-none focus:ring-1 focus:ring-orange-400/20"
                />
            </div>
            <div>
                <label class="mb-1 block text-xs font-medium text-gray-500">{{ t('bookings.filter_date_to') }}</label>
                <input
                    v-model="filterForm.date_to"
                    type="date"
                    class="rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-300 focus:outline-none focus:ring-1 focus:ring-orange-400/20"
                />
            </div>
            <div class="min-w-[160px] flex-1">
                <label class="mb-1 block text-xs font-medium text-gray-500">{{ t('bookings.filter_search') }}</label>
                <input
                    v-model="filterForm.search"
                    type="search"
                    :placeholder="t('bookings.filter_search')"
                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-orange-300 focus:outline-none focus:ring-1 focus:ring-orange-400/20"
                />
            </div>
            <button
                type="submit"
                class="rounded-lg bg-orange-500 px-4 py-2 text-sm font-medium text-white hover:bg-orange-600"
            >
                {{ t('client.search_btn') }}
            </button>
        </div>
    </form>
</template>
