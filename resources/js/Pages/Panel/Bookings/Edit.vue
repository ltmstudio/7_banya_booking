<script setup>
/**
 * Форма редактирования бронирования.
 */
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';
import PhoneInput from '@/Components/PhoneInput.vue';
import BirthdayInput from '@/Components/BirthdayInput.vue';
import TimeInput from '@/Components/TimeInput.vue';
import { formatGuestPhone } from '@/Composables/useGuestPhone';
import { buildHalfHourOptions, formatDurationLabel } from '@/Support/duration';

const props = defineProps({
    booking: { type: Object, required: true },
    rooms: { type: Array, required: true },
    clients: { type: Array, required: true },
    extraServices: { type: Array, default: () => [] },
    bookingSettings: { type: Object, default: () => ({ min_hours: 1, max_hours: 10 }) },
});

const { t } = useI18n();
const page = usePage();
const tBooking = page.props.booking || {};

const currentLocale = computed(() => page.props.locale || 'ru');

const roomName = (room) => {
    return room?.name?.[currentLocale.value] || room?.name?.ru || room?.category_label || '';
};

/** Выбранная комната по form.room_id для отображения вместимости. */
const selectedRoom = computed(() =>
    props.rooms.find((r) => String(r.id) === String(form.room_id))
);

const dateToInput = (d) => {
    if (!d) return '';
    const parts = String(d).split('.');
    if (parts.length === 3) return `${parts[2]}-${parts[1]}-${parts[0]}`;
    return d;
};
const timeToInput = (t) => (t ? String(t).slice(0, 5) : '10:00');
/** Опции длительности из настроек с шагом 30 минут. */
const durationOptions = computed(() =>
    buildHalfHourOptions(props.bookingSettings.min_hours, props.bookingSettings.max_hours)
);
/** Подпись опции длительности: "N ч" / "N ч 30 мин". */
const durationLabel = (hours) => formatDurationLabel(hours, t('bookings.hours'), t('settings.minutes_short'));

const form = useForm({
    room_id: props.booking.room?.id ?? '',
    client_id: props.booking.client?.id ?? '',
    booking_date: dateToInput(props.booking.booking_date),
    start_time: timeToInput(props.booking.start_time),
    duration_hours: props.booking.duration_hours ?? 1,
    guests_count: props.booking.guests_count ?? 1,
    extra_service_ids: (props.booking.extra_services || []).map((s) => s.id),
    guest_name: props.booking.guest_name ?? '',
    guest_phone: formatGuestPhone(props.booking.guest_phone ?? '') || '',
    guest_last_name: props.booking.guest_last_name ?? '',
    guest_birthday: dateToInput(props.booking.guest_birthday),
});

const submit = () => {
    form.put(route('panel.bookings.update', props.booking.id));
};

const toggleExtra = (id) => {
    const idx = form.extra_service_ids.indexOf(id);
    if (idx === -1) form.extra_service_ids.push(id);
    else form.extra_service_ids.splice(idx, 1);
};
</script>

<template>
    <DashboardLayout>
        <Head :title="tBooking.edit" />
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h1 class="text-xl font-semibold text-gray-800">{{ tBooking.edit }}</h1>
                <Link :href="route('panel.bookings.show', booking.id)" class="text-sm text-gray-500 hover:text-orange-600">← {{ t('common.cancel') }}</Link>
            </div>
        </template>

        <div class="mx-auto max-w-2xl px-4 pb-12 pt-8">
            <form class="rounded-2xl border border-gray-200 bg-white p-8 shadow-md" @submit.prevent="submit">
                <div class="space-y-4">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">
                            {{ t('bookings.room') }}
                            <span class="text-orange-500">*</span>
                        </label>
                        <select
                            v-model="form.room_id"
                            required
                            class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-orange-300 focus:outline-none focus:ring-2 focus:ring-orange-400/20"
                        >
                            <option value="">—</option>
                            <option v-for="r in rooms" :key="r.id" :value="r.id">
                                {{ roomName(r) }}
                            </option>
                        </select>
                        <p v-if="form.errors.room_id" class="mt-1 text-xs text-red-500">{{ form.errors.room_id }}</p>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">
                            {{ t('bookings.client') }}
                        </label>
                        <select
                            v-model="form.client_id"
                            class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-orange-300 focus:outline-none focus:ring-2 focus:ring-orange-400/20"
                        >
                            <option value="">
                                {{ t('bookings.guest') }}
                            </option>
                            <option v-for="c in clients" :key="c.id" :value="c.id">
                                {{ c.full_name }} ({{ c.phone }})
                            </option>
                        </select>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                {{ t('bookings.date') }}
                                <span class="text-orange-500">*</span>
                            </label>
                            <input
                                v-model="form.booking_date"
                                type="date"
                                class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-orange-300 focus:outline-none focus:ring-2 focus:ring-orange-400/20"
                            />
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                {{ t('bookings.start_time') }}
                                <span class="text-orange-500">*</span>
                            </label>
                            <TimeInput v-model="form.start_time" :required="true" />
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                {{ t('bookings.duration') }}
                                <span class="text-orange-500">*</span>
                            </label>
                            <select
                                v-model="form.duration_hours"
                                required
                                class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-orange-300 focus:outline-none focus:ring-2 focus:ring-orange-400/20"
                            >
                                <option v-for="h in durationOptions" :key="h" :value="h">
                                    {{ durationLabel(h) }}
                                </option>
                            </select>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                {{ t('bookings.guests_count') }}
                                <span class="text-orange-500">*</span>
                            </label>
                            <input
                                v-model.number="form.guests_count"
                                type="number"
                                min="1"
                                required
                                class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-orange-300 focus:outline-none focus:ring-2 focus:ring-orange-400/20"
                            />
                            <div
                                v-if="selectedRoom?.capacity && form.guests_count > selectedRoom.capacity"
                                class="mt-2 flex items-start gap-2 rounded-xl border border-amber-200
                                       bg-amber-50 px-3 py-2.5"
                            >
                                <span class="flex-shrink-0 text-amber-500">⚠️</span>
                                <p class="text-xs text-amber-700">
                                    Вместимость комнаты: {{ selectedRoom.capacity }} чел.
                                    Превышение на {{ form.guests_count - selectedRoom.capacity }} чел.
                                    — может взиматься доплата.
                                </p>
                            </div>
                        </div>
                    </div>
                    <div v-if="extraServices.length">
                        <label class="mb-2 block text-sm font-medium text-gray-700">{{ tBooking.extra_services }}</label>
                        <div class="flex flex-wrap gap-3">
                            <label
                                v-for="s in extraServices"
                                :key="s.id"
                                class="flex cursor-pointer items-center gap-2 rounded-lg border border-gray-200 px-4 py-2 hover:bg-gray-50"
                            >
                                <input
                                    type="checkbox"
                                    :checked="form.extra_service_ids.includes(s.id)"
                                    @change="toggleExtra(s.id)"
                                />
                                <span>{{ s.name }}</span>
                                <span class="text-gray-500">{{ s.price }} TMT</span>
                            </label>
                        </div>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                {{ t('bookings.guest_name') }}
                            </label>
                            <input v-model="form.guest_name" type="text" maxlength="150" :placeholder="t('bookings.guest_name_placeholder')" class="input-field" />
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                {{ t('bookings.guest_phone') }}
                            </label>
                            <PhoneInput v-model="form.guest_phone" :error="form.errors.guest_phone" />
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                {{ t('bookings.guest_last_name') }}
                            </label>
                            <input v-model="form.guest_last_name" type="text" maxlength="100" :placeholder="t('bookings.guest_last_name_placeholder')" class="input-field" />
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                {{ t('bookings.guest_birthday') }}
                            </label>
                            <BirthdayInput v-model="form.guest_birthday" :error="form.errors.guest_birthday" />
                        </div>
                    </div>
                </div>
                <div class="mt-8 flex items-center justify-between border-t border-gray-200 pt-8">
                    <Link
                        :href="route('panel.bookings.index')"
                        class="text-sm text-gray-500 hover:text-gray-700"
                    >
                        {{ t('common.cancel') }}
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-lg bg-orange-500 px-6 py-2.5 text-sm font-medium text-white hover:bg-orange-600 disabled:opacity-50"
                    >
                        {{ form.processing ? '...' : t('common.save') }}
                    </button>
                </div>
            </form>
        </div>
    </DashboardLayout>
</template>

<style scoped>
/* Общий класс полей ввода (правило: не дублировать код > 2 раз). */
.input-field {
    @apply w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-orange-300 focus:outline-none focus:ring-2 focus:ring-orange-400/20;
}
</style>
