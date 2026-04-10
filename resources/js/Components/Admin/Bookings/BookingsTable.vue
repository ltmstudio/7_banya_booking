<script setup>
/**
 * Таблица и мобильные карточки бронирований с быстрым изменением статуса и пагинацией.
 */
import { Link, router, usePage } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

defineProps({
    bookings: { type: Array, default: () => [] },
    meta: { type: Object, default: () => ({}) },
    filters: { type: Object, default: () => ({}) },
});
const emit = defineEmits(['status-changed']);

const { t } = useI18n();
const page = usePage();

const clientDisplay = (b) => {
    if (b.client?.full_name) return b.client.full_name;
    return b.guest_name ? [b.guest_name, b.guest_last_name].filter(Boolean).join(' ') : '—';
};

const roomName = (room) => {
    if (!room) return '—';
    const locale = page.props.locale || 'ru';
    return room.name?.[locale] || room.name?.ru || room.category_label || '—';
};

/** Формат времени без секунд: HH:MM. */
const shortTime = (value) => (value ? String(value).slice(0, 5) : '—');

/** Цвета бейджей статуса для быстрого изменения. */
const statusColors = {
    new: 'bg-blue-100 text-blue-800',
    preliminary: 'bg-yellow-100 text-yellow-800',
    confirmed: 'bg-green-100 text-green-800',
    paid: 'bg-emerald-100 text-emerald-800',
    completed: 'bg-gray-100 text-gray-700',
    cancelled: 'bg-red-100 text-red-800',
};

/** Разрешённые статусы для быстрого перехода из текущего. */
function allowedStatuses(booking) {
    const all = [
        { value: 'new' },
        { value: 'preliminary' },
        { value: 'confirmed' },
        { value: 'paid' },
        { value: 'completed' },
        { value: 'cancelled' },
    ];
    const flow = {
        new: ['new', 'preliminary', 'cancelled'],
        preliminary: ['preliminary', 'confirmed', 'cancelled'],
        confirmed: ['confirmed', 'paid', 'cancelled'],
        paid: ['paid', 'completed'],
        completed: ['completed'],
        cancelled: ['cancelled'],
    };
    const allowed = flow[booking.status] ?? [booking.status];
    return all.filter((s) => allowed.includes(s.value));
}

const updatingStatus = ref({});

function quickUpdateStatus(booking, newStatus) {
    if (newStatus === booking.status) return;

    if (newStatus === 'cancelled') {
        router.visit(route('panel.bookings.show', booking.id));
        return;
    }

    updatingStatus.value[booking.id] = true;

    router.patch(
        route('panel.bookings.updateStatus', booking.id),
        { status: newStatus },
        {
            preserveScroll: true,
            preserveState: true,
            onFinish: () => {
                delete updatingStatus.value[booking.id];
                emit('status-changed', { bookingId: booking.id, status: newStatus });
            },
        }
    );
}
</script>

<template>
    <div class="overflow-hidden rounded-lg bg-white shadow">
        <div class="hidden overflow-x-auto md:block">
            <table class="min-w-full">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ t('bookings.room') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ t('bookings.client') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ t('bookings.booking_date') }}</th>
                        <th class="pb-3 pr-4 font-medium">{{ t('bookings.col_time_range') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ t('bookings.col_guests') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ t('bookings.status') }}</th>
                        <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ t('bookings.total') }}</th>
                        <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">{{ t('bookings.actions') }}</th>
                    </tr>
                </thead>
                <tbody class="bg-white">
                    <tr
                        v-for="b in bookings"
                        :key="b.id"
                        class="border-b border-gray-100"
                        :class="[
                            'hover:opacity-90 cursor-pointer transition',
                            {
                                'bg-blue-50': b.status === 'new',
                                'bg-yellow-50': b.status === 'preliminary',
                                'bg-green-50': b.status === 'confirmed',
                                'bg-emerald-50': b.status === 'paid',
                                'bg-gray-50': b.status === 'completed',
                                'bg-red-50': b.status === 'cancelled',
                            },
                        ]"
                    >
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-900">{{ roomName(b.room) }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">{{ clientDisplay(b) }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">{{ b.booking_date }}</td>
                        <td class="py-3 pr-4 whitespace-nowrap">
                            <div class="font-medium text-gray-700">
                                {{ shortTime(b.start_time) }} — {{ shortTime(b.end_time) }}
                            </div>
                            <div class="mt-0.5 text-xs text-gray-400">
                                {{ b.duration_hours }} {{ t('bookings.hours') }}
                            </div>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">
                            <div class="flex items-center gap-1.5">
                                <span>{{ b.guests_count }}</span>
                                <span
                                    v-if="b.room?.capacity && b.guests_count > b.room.capacity"
                                    class="inline-flex cursor-help items-center rounded-full bg-amber-100 px-1.5 py-0.5 text-xs font-medium text-amber-700"
                                    :title="`⚠️ Превышение: макс. ${b.room.capacity} чел.`"
                                >
                                    ⚠️ +{{ b.guests_count - b.room.capacity }}
                                </span>
                            </div>
                        </td>
                        <td class="py-3 pr-4 whitespace-nowrap" @click.stop>
                            <select
                                :value="b.status"
                                class="rounded-full px-2 py-1 text-xs font-medium border-0 cursor-pointer transition appearance-none text-center"
                                :class="statusColors[b.status] || 'bg-gray-100 text-gray-700'"
                                :disabled="updatingStatus[b.id]"
                                @change="quickUpdateStatus(b, $event.target.value)"
                            >
                                <option
                                    v-for="s in allowedStatuses(b)"
                                    :key="s.value"
                                    :value="s.value"
                                >
                                    {{ t('bookings.statuses.' + s.value) }}
                                </option>
                            </select>
                        </td>
                        <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">{{ b.final_amount != null ? b.final_amount + ' TMT' : '—' }}</td>
                        <td class="whitespace-nowrap px-4 py-3 text-right">
                            <div class="flex items-center justify-end gap-2">
                                <Link
                                    :href="route('panel.bookings.show', b.id)"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-600 transition-colors hover:border-orange-300 hover:bg-orange-50 hover:text-orange-600"
                                    :title="t('bookings.show_btn')"
                                >
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                                    </svg>
                                    {{ t('bookings.show_btn') }}
                                </Link>
                                <Link
                                    :href="route('panel.bookings.edit', b.id)"
                                    class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-600 transition-colors hover:border-blue-300 hover:bg-blue-50 hover:text-blue-600"
                                    :title="t('bookings.edit_btn')"
                                >
                                    <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                    </svg>
                                    {{ t('bookings.edit_btn') }}
                                </Link>
                            </div>
                        </td>
                    </tr>
                    <tr v-if="bookings.length === 0">
                        <td colspan="9" class="px-4 py-12 text-center text-sm text-gray-500">
                            {{ t('bookings.empty_state') }}
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="md:hidden">
            <div
                v-for="b in bookings"
                :key="b.id"
                class="border-b border-gray-100 p-4 last:border-b-0"
            >
                <div class="flex items-start justify-between gap-2">
                    <p class="font-medium text-gray-900">{{ roomName(b.room) }}</p>
                    <span class="rounded-full bg-gray-100 px-2 py-0.5 text-xs text-gray-700">{{ b.status ? t('bookings.statuses.' + b.status) : b.status_label }}</span>
                </div>
                <p class="text-sm text-gray-600">{{ clientDisplay(b) }}</p>
                <p class="text-xs text-gray-500">{{ b.booking_date }} {{ shortTime(b.start_time) }} · {{ b.duration_hours }} {{ t('bookings.hours') }} · {{ b.final_amount != null ? b.final_amount + ' TMT' : '—' }}</p>
                <p class="mt-1 text-xs text-gray-500">
                    👥 {{ b.guests_count }} чел.
                    <span
                        v-if="b.room?.capacity && b.guests_count > b.room.capacity"
                        class="font-medium text-amber-600"
                    >
                        ⚠️ (+{{ b.guests_count - b.room.capacity }} сверх нормы)
                    </span>
                </p>
                <div class="mt-2 flex gap-2">
                    <Link :href="route('panel.bookings.show', b.id)" class="text-sm font-medium text-orange-600">
                        {{ t('bookings.show_btn') }}
                    </Link>
                    <Link :href="route('panel.bookings.edit', b.id)" class="text-sm font-medium text-gray-600">
                        {{ t('bookings.edit_btn') }}
                    </Link>
                </div>
            </div>
            <div v-if="bookings.length === 0" class="py-12 text-center text-sm text-gray-500">
                {{ t('bookings.empty_state') }}
            </div>
        </div>

        <div v-if="meta.last_page > 1" class="flex items-center justify-between border-t border-gray-200 px-4 py-3">
            <p class="text-sm text-gray-600">{{ meta.total }}</p>
            <div class="flex gap-2">
                <Link
                    v-if="meta.current_page > 1"
                    :href="route('panel.bookings.index', { page: meta.current_page - 1, status: filters.status, room_id: filters.room_id, date_from: filters.date_from, date_to: filters.date_to, search: filters.search })"
                    class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >←</Link>
                <span class="py-1.5 text-sm text-gray-600">{{ meta.current_page }} / {{ meta.last_page }}</span>
                <Link
                    v-if="meta.current_page < meta.last_page"
                    :href="route('panel.bookings.index', { page: meta.current_page + 1, status: filters.status, room_id: filters.room_id, date_from: filters.date_from, date_to: filters.date_to, search: filters.search })"
                    class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
                >→</Link>
            </div>
        </div>
    </div>
</template>
