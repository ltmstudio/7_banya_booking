<script setup>
/**
 * Модальная форма бронирования на лендинге: мастер из 3 шагов (время → услуги → контакты).
 * Отправляет заявку на `public.booking.store` и показывает успех.
 */
import { ref, computed, watch } from 'vue';
import { useForm, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import { buildHalfHourOptions, formatDurationLabel } from '@/Support/duration';
import PhoneInput from '@/Components/PhoneInput.vue';
import BirthdayInput from '@/Components/BirthdayInput.vue';
import ToastNotification from '@/Components/Landing/ToastNotification.vue';
import TimeInput from '@/Components/TimeInput.vue';
import axios from 'axios';

const props = defineProps({
    rooms: { type: Array, default: () => [] },
    extraServices: { type: Array, default: () => [] },
    selectedRoom: { type: Object, default: null },
    date: { type: String, default: '' },
    startTime: { type: String, default: '14:00' },
    durationHours: { type: Number, default: 2 },
    minHours: { type: Number, default: 1 },
    maxHours: { type: Number, default: 10 },
});

const emit = defineEmits(['close', 'success']);

const { t } = useI18n();
const currentLocale = computed(() => usePage().props.locale || 'ru');
const today = new Date().toISOString().slice(0, 10);
const currentStep = ref(1);
const showSuccess = ref(false);
const toast = ref({ show: false, type: 'success', message: '' });
const busySlots = ref([]);
const loadingSlots = ref(false);
const bufferMinutes = ref(15);
const timeOptions = ['10:00', '12:00', '14:00', '16:00', '18:00', '20:00', '22:00'];

const durationOptions = computed(() => {
    return buildHalfHourOptions(props.minHours, props.maxHours);
});

/** Текст опции длительности в формате "1 ч" или "1 ч 30 мин". */
function durationLabel(hours) {
    return formatDurationLabel(hours, t('landing.booking.hours_short'), t('landing.booking.minutes_short'));
}

/** Конвертирует HH:MM в минуты от начала суток. */
function timeToMinutes(time) {
    const [h, m] = String(time).split(':').map(Number);
    return h * 60 + m;
}

/** Проверяет конфликт нового интервала со слотами комнаты (включая буфер уборки). */
function isTimeBusy(startTime, durationHours) {
    if (!busySlots.value.length || !startTime) return false;

    const newStart = timeToMinutes(startTime);
    const newEnd = newStart + Number(durationHours) * 60;
    const newCleaningEnd = newEnd + bufferMinutes.value;

    return busySlots.value.some((slot) => {
        const slotStart = timeToMinutes(slot.start);
        const slotCleaningEnd = timeToMinutes(slot.cleaning_end);
        return newStart < slotCleaningEnd && newCleaningEnd > slotStart;
    });
}

/** Возвращает подпись занятого интервала для конкретной кнопки времени. */
function getConflictLabel(startTime) {
    if (!busySlots.value.length) return '';

    const slotMinutes = timeToMinutes(startTime);
    const conflict = busySlots.value.find((slot) => {
        const slotStart = timeToMinutes(slot.start);
        const slotCleaningEnd = timeToMinutes(slot.cleaning_end);
        return slotMinutes >= slotStart && slotMinutes < slotCleaningEnd;
    });

    if (!conflict) return '';
    return `${conflict.start}–${conflict.cleaning_end}`;
}

/** Список конфликтующих слотов для текущего интервала брони. */
function getConflictingSlots(startTime, durationHours) {
    if (!busySlots.value.length || !startTime) return [];

    const newStart = timeToMinutes(startTime);
    const newEnd = newStart + Number(durationHours) * 60;
    const newCleaningEnd = newEnd + bufferMinutes.value;

    return busySlots.value.filter((slot) => {
        const slotStart = timeToMinutes(slot.start);
        const slotCleaningEnd = timeToMinutes(slot.cleaning_end);
        return newStart < slotCleaningEnd && newCleaningEnd > slotStart;
    });
}

/** Подсказка следующего потенциально свободного времени для выбранного интервала. */
const nextAvailableTime = computed(() => {
    if (!form.start_time || !busySlots.value.length) return null;
    if (!isTimeBusy(form.start_time, form.duration_hours)) return null;

    const newStart = timeToMinutes(form.start_time);

    const conflictingSlot = busySlots.value.find((slot) => {
        const slotStart = timeToMinutes(slot.start);
        const slotCleaningEnd = timeToMinutes(slot.cleaning_end);
        const newEnd = newStart + Number(form.duration_hours) * 60;
        const newCleaningEnd = newEnd + bufferMinutes.value;
        return newStart < slotCleaningEnd && newCleaningEnd > slotStart;
    });

    if (!conflictingSlot) return null;

    const nextMinutes = timeToMinutes(conflictingSlot.cleaning_end);
    const h = Math.floor(nextMinutes / 60);
    const m = nextMinutes % 60;

    if (h >= 22) return null;

    return `${String(h).padStart(2, '0')}:${String(m).padStart(2, '0')}`;
});

/** Применяет предложенное ближайшее свободное время. */
function applyNextAvailableTime() {
    if (nextAvailableTime.value) {
        form.start_time = nextAvailableTime.value;
    }
}

const form = useForm({
    room_id: props.selectedRoom?.id ?? null,
    booking_date: props.date || today,
    start_time: props.startTime || '14:00',
    duration_hours: props.durationHours ?? props.minHours,
    guests_count: 1,
    extra_service_ids: [],
    guest_name: '',
    guest_phone: '',
    guest_last_name: '',
    guest_birthday: '',
    source: 'online',
});

// Initialize form with selected room from parent
watch(
    () => props.selectedRoom,
    (room) => {
        if (room) form.room_id = room.id;
    },
    { immediate: true }
);

watch(
    [() => form.room_id, () => form.booking_date, () => form.duration_hours],
    async ([roomId, date]) => {
        if (!roomId || !date) {
            busySlots.value = [];
            return;
        }
        loadingSlots.value = true;
        try {
            const res = await axios.get(route('public.busy-slots'), {
                params: {
                    room_id: roomId,
                    date: date,
                    duration_hours: form.duration_hours,
                },
            });
            busySlots.value = res.data.busy_slots;
            bufferMinutes.value = res.data.buffer_minutes;
        } catch {
            busySlots.value = [];
        } finally {
            loadingSlots.value = false;
        }
    },
    { immediate: false }
);

const selectedRoomData = computed(() =>
    props.rooms.find((r) => r.id === form.room_id) ?? props.selectedRoom
);

const roomPrice = computed(() => {
    if (!selectedRoomData.value) return 0;
    const price = selectedRoomData.value.promo_price_per_hour
        ?? selectedRoomData.value.price_per_hour;
    return price * form.duration_hours;
});

const totalAmount = computed(() => {
    const servicesTotal = form.extra_service_ids.reduce((sum, id) => {
        const s = props.extraServices.find((x) => x.id === id);
        return sum + (s?.price ?? 0);
    }, 0);
    return roomPrice.value + servicesTotal;
});

const isTodayBirthday = computed(() => {
    if (!form.guest_birthday) return false;
    const bday = new Date(form.guest_birthday);
    const now = new Date();
    return bday.getMonth() === now.getMonth() && bday.getDate() === now.getDate();
});

/** Переключает услугу в списке выбранных. */
function toggleService(id) {
    const idx = form.extra_service_ids.indexOf(id);
    if (idx === -1) form.extra_service_ids.push(id);
    else form.extra_service_ids.splice(idx, 1);
}

/** Отправляет заявку на бронирование и показывает успешный экран. */
function showToast(type, message) {
    toast.value = { show: true, type, message };
}

function hideToast() {
    toast.value.show = false;
}

/** Переход к шагу 2 только если выбранный слот не конфликтует. */
function goToStep2() {
    if (isTimeBusy(form.start_time, form.duration_hours)) {
        const nextHint = nextAvailableTime.value ? ` (${nextAvailableTime.value})` : '';
        showToast('warning', `${t('landing.booking.busy')}${nextHint}`);
        return;
    }
    currentStep.value = 2;
}

function submitBooking() {
    form.post(route('public.booking.store'), {
        onSuccess: () => {
            showToast('success', t('landing.booking.success_toast'));
            setTimeout(() => {
                emit('success');
                emit('close');
            }, 2500);
        },
        onError: (errors) => {
            const firstErrorValue = Object.values(errors)[0];
            const firstError = Array.isArray(firstErrorValue) ? firstErrorValue[0] : firstErrorValue;
            showToast('error', firstError ?? t('landing.booking.error_toast'));
        },
    });
}
</script>

<template>
    <!-- Modal overlay -->
    <div
        class="fixed inset-0 z-50 flex items-center justify-center p-4 bg-black/50 backdrop-blur-sm"
        @click.self="emit('close')"
    >
        <div class="relative w-full max-w-2xl max-h-[90vh] overflow-y-auto rounded-2xl bg-white dark:bg-stone-900 shadow-2xl">

            <!-- Header -->
            <div class="sticky top-0 z-10 flex items-center justify-between border-b border-stone-200 dark:border-stone-700 bg-white dark:bg-stone-900 px-6 py-4">
                <h3 class="text-lg font-semibold text-stone-900 dark:text-white">
                    {{ t('landing.booking.form_title') }}
                </h3>
                <button
                    @click="emit('close')"
                    class="rounded-lg p-2 hover:bg-stone-100 dark:hover:bg-stone-800 transition"
                >
                    ✕
                </button>
            </div>

            <!-- Step indicator -->
            <div class="flex items-center gap-2 px-6 py-4 border-b border-stone-100 dark:border-stone-800">
                <div v-for="n in 3" :key="n" class="flex items-center gap-2">
                    <div :class="[
                        'flex h-7 w-7 items-center justify-center rounded-full text-xs font-semibold transition',
                        currentStep === n ? 'bg-amber-600 text-white' : '',
                        currentStep > n ? 'bg-emerald-500 text-white' : '',
                        currentStep < n ? 'bg-stone-200 text-stone-500 dark:bg-stone-700' : '',
                    ]">
                        <span v-if="currentStep > n">✓</span>
                        <span v-else>{{ n }}</span>
                    </div>
                    <span class="text-xs text-stone-500 hidden sm:block">
                        {{ [t('landing.booking.step1'), t('landing.booking.step2'), t('landing.booking.step3')][n-1] }}
                    </span>
                    <div v-if="n < 3" class="h-px w-6 bg-stone-200 dark:bg-stone-700" />
                </div>
            </div>

            <div class="px-6 py-6">

                <!-- STEP 1: Room & Time confirmation -->
                <div v-show="currentStep === 1">

                    <!-- Selected room preview -->
                    <div
                        v-if="form.room_id && selectedRoomData"
                        class="mb-4 flex items-center gap-3 rounded-xl bg-amber-50 dark:bg-amber-900/20 p-4 border border-amber-200 dark:border-amber-800"
                    >
                        <img
                            v-if="selectedRoomData.photos?.[0]?.url"
                            :src="selectedRoomData.photos[0].url"
                            class="h-14 w-14 rounded-lg object-cover flex-shrink-0"
                        />
                        <div>
                            <p class="font-semibold text-stone-900 dark:text-white">
                                {{ selectedRoomData.name?.[currentLocale] }}
                            </p>
                            <p class="text-sm text-amber-700 dark:text-amber-400">
                                {{ selectedRoomData.promo_price_per_hour ?? selectedRoomData.price_per_hour }} TMT/ч
                            </p>
                        </div>
                    </div>

                    <!-- Date -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-stone-700 dark:text-stone-300 mb-2">
                            {{ t('landing.booking.date') }} <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="date"
                            v-model="form.booking_date"
                            :min="today"
                            class="w-full rounded-xl border border-stone-200 bg-stone-50 px-3 py-2.5 text-sm
                                   focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20
                                   dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100"
                        />
                        <p v-if="form.errors.booking_date" class="mt-1 text-xs text-red-500">
                            {{ form.errors.booking_date }}
                        </p>
                    </div>

                    <!-- Time + Duration row -->
                    <div class="mb-4 grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-stone-700 dark:text-stone-300 mb-2">
                                {{ t('landing.booking.start_time') }} <span class="text-red-500">*</span>
                            </label>
                            <TimeInput v-model="form.start_time" :required="true" />
                            <div class="mt-3 grid grid-cols-3 gap-2 sm:grid-cols-4">
                                <button
                                    v-for="opt in timeOptions"
                                    :key="opt"
                                    type="button"
                                    @click="!isTimeBusy(opt, form.duration_hours) && (form.start_time = opt)"
                                    :disabled="isTimeBusy(opt, form.duration_hours)"
                                    :title="isTimeBusy(opt, form.duration_hours) ? `Занято: ${getConflictLabel(opt)}` : opt"
                                    :class="[
                                        'relative rounded-lg border px-2 py-2.5 text-center text-xs font-medium transition',
                                        form.start_time === opt
                                            ? 'bg-amber-500 text-white border-amber-500'
                                            : isTimeBusy(opt, form.duration_hours)
                                                ? 'bg-red-50 text-red-400 border-red-200 cursor-not-allowed dark:bg-red-900/20 dark:text-red-500 dark:border-red-800'
                                                : 'bg-stone-50 text-stone-700 border-stone-200 hover:border-amber-400 hover:bg-amber-50 dark:bg-stone-800 dark:text-stone-300 dark:border-stone-600',
                                    ]"
                                >
                                    <span v-if="!isTimeBusy(opt, form.duration_hours)">
                                        {{ opt }}
                                    </span>
                                    <template v-else>
                                        <span class="block line-through">{{ opt }}</span>
                                        <span class="mt-0.5 block text-[9px] leading-tight text-red-400 dark:text-red-500">🔴</span>
                                    </template>
                                </button>
                            </div>
                            <p v-if="loadingSlots" class="mt-1 text-xs text-stone-500">
                                {{ t('landing.booking.checking') }}
                            </p>
                            <div
                                v-else-if="form.start_time && isTimeBusy(form.start_time, form.duration_hours)"
                                class="mt-3 space-y-1.5 rounded-xl border border-red-200 bg-red-50 px-3 py-2.5 dark:border-red-800 dark:bg-red-900/20"
                            >
                                <p class="text-xs font-semibold text-red-700 dark:text-red-400">
                                    ❌ {{ t('landing.booking.time_busy') }}
                                </p>

                                <div
                                    v-for="slot in getConflictingSlots(form.start_time, form.duration_hours)"
                                    :key="slot.start"
                                    class="text-xs text-red-600 dark:text-red-500"
                                >
                                    🕐 {{ t('landing.booking.busy_period') }}:
                                    <strong>{{ slot.start }} — {{ slot.end }}</strong>
                                    <span class="text-red-400">
                                        ({{ t('landing.booking.cleaning_until') }} {{ slot.cleaning_end }})
                                    </span>
                                </div>

                                <div
                                    v-if="nextAvailableTime"
                                    class="flex items-center gap-2 border-t border-red-200 pt-1.5 dark:border-red-800"
                                >
                                    <p class="flex-1 text-xs text-red-600 dark:text-red-500">
                                        ✅ {{ t('landing.booking.next_available') }}:
                                        <strong class="text-amber-600 dark:text-amber-400">{{ nextAvailableTime }}</strong>
                                    </p>
                                    <button
                                        type="button"
                                        @click="applyNextAvailableTime"
                                        class="shrink-0 rounded-lg bg-amber-500 px-3 py-1 text-xs font-semibold text-white transition hover:bg-amber-600"
                                    >
                                        {{ t('landing.booking.use_this_time') }}
                                    </button>
                                </div>
                            </div>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-stone-700 dark:text-stone-300 mb-2">
                                {{ t('landing.booking.duration') }} <span class="text-red-500">*</span>
                            </label>
                            <select
                                v-model="form.duration_hours"
                                class="w-full rounded-xl border border-stone-200 bg-stone-50 px-3 py-2.5 text-sm
                                       focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20
                                       dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100"
                            >
                                <option v-for="d in durationOptions" :key="d" :value="d">
                                    {{ durationLabel(d) }}
                                </option>
                            </select>
                        </div>
                    </div>

                    <!-- Guests count -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-stone-700 dark:text-stone-300 mb-2">
                            {{ t('landing.booking.guests') }}
                        </label>
                        <input
                            type="number"
                            v-model="form.guests_count"
                            min="1"
                            class="w-full rounded-xl border border-stone-200 bg-stone-50 px-3 py-2.5 text-sm
                                   focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20
                                   dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100"
                        />
                        <p
                            v-if="selectedRoomData?.capacity && form.guests_count <= selectedRoomData.capacity"
                            class="mt-1 text-xs text-stone-500"
                        >
                            {{ t('landing.booking.capacity', { n: selectedRoomData.capacity }) }}
                        </p>
                        <div
                            v-if="selectedRoomData?.capacity && form.guests_count > selectedRoomData.capacity"
                            class="mt-2 flex items-start gap-2 rounded-xl border border-amber-200
                                   bg-amber-50 dark:bg-amber-900/20 dark:border-amber-800 px-3 py-2.5"
                        >
                            <span class="text-amber-500 flex-shrink-0 mt-0.5">⚠️</span>
                            <div>
                                <p class="text-xs font-medium text-amber-700 dark:text-amber-400">
                                    {{ t('landing.booking.over_capacity_title') }}
                                </p>
                                <p class="text-xs text-amber-600 dark:text-amber-500 mt-0.5">
                                    {{ t('landing.booking.over_capacity_hint', {
                                        max: selectedRoomData.capacity,
                                        extra: form.guests_count - selectedRoomData.capacity,
                                    }) }}
                                </p>
                            </div>
                        </div>
                    </div>

                    <button
                        type="button"
                        @click="goToStep2"
                        :disabled="!form.booking_date || !form.start_time || !form.room_id"
                        class="w-full rounded-xl bg-amber-600 px-4 py-3 text-sm font-semibold text-white
                               transition hover:bg-amber-700 disabled:opacity-50 disabled:cursor-not-allowed"
                    >
                        {{ t('landing.booking.next') }} →
                    </button>
                </div>

                <!-- STEP 2: Extra services -->
                <div v-show="currentStep === 2">
                    <p class="mb-4 text-sm text-stone-500">{{ t('landing.booking.services_hint') }}</p>

                    <div class="grid grid-cols-2 gap-3 mb-6">
                        <button
                            v-for="service in extraServices"
                            :key="service.id"
                            type="button"
                            @click="toggleService(service.id)"
                            :class="[
                                'flex flex-col items-center gap-2 rounded-xl border-2 p-4 text-center transition',
                                form.extra_service_ids.includes(service.id)
                                    ? 'border-amber-500 bg-amber-50 dark:bg-amber-900/20'
                                    : 'border-stone-200 dark:border-stone-700 hover:border-stone-300'
                            ]"
                        >
                            <img v-if="service.icon_url" :src="service.icon_url" class="h-10 w-10 object-contain" />
                            <div v-else class="text-2xl">🛁</div>
                            <span class="text-xs font-medium text-stone-700 dark:text-stone-300">
                                {{ service.name?.[currentLocale] ?? service.name }}
                            </span>
                            <span class="text-xs font-semibold text-amber-600">{{ service.price }} TMT</span>
                        </button>
                    </div>

                    <!-- Summary -->
                    <div
                        v-if="form.extra_service_ids.length"
                        class="mb-4 rounded-xl bg-stone-50 dark:bg-stone-800 p-4 text-sm"
                    >
                        <p class="font-medium text-stone-700 dark:text-stone-300 mb-2">
                            {{ t('landing.booking.selected_services') }}:
                        </p>
                        <div
                            v-for="id in form.extra_service_ids"
                            :key="id"
                            class="flex justify-between text-stone-600 dark:text-stone-400"
                        >
                            <span>{{ extraServices.find(s => s.id === id)?.name?.[currentLocale] }}</span>
                            <span>{{ extraServices.find(s => s.id === id)?.price }} TMT</span>
                        </div>
                    </div>

                    <div class="flex gap-3">
                        <button
                            type="button"
                            @click="currentStep = 1"
                            class="flex-1 rounded-xl border border-stone-200 px-4 py-3 text-sm font-medium
                                   text-stone-600 transition hover:bg-stone-50 dark:border-stone-700 dark:text-stone-400"
                        >
                            ← {{ t('landing.booking.back') }}
                        </button>
                        <button
                            type="button"
                            @click="currentStep = 3"
                            class="flex-1 rounded-xl bg-amber-600 px-4 py-3 text-sm font-semibold text-white
                                   transition hover:bg-amber-700"
                        >
                            {{ t('landing.booking.next') }} →
                        </button>
                    </div>
                </div>

                <!-- STEP 3: Contact info -->
                <div v-show="currentStep === 3">

                    <!-- Price summary -->
                    <div class="mb-6 rounded-xl bg-stone-50 dark:bg-stone-800 p-4 text-sm">
                        <div class="flex justify-between mb-1 text-stone-600 dark:text-stone-400">
                            <span>{{ selectedRoomData?.name?.[currentLocale] }} × {{ form.duration_hours }}ч</span>
                            <span>{{ roomPrice }} TMT</span>
                        </div>
                        <div
                            v-for="id in form.extra_service_ids"
                            :key="id"
                            class="flex justify-between text-stone-600 dark:text-stone-400 mb-1"
                        >
                            <span>{{ extraServices.find(s => s.id === id)?.name?.[currentLocale] }}</span>
                            <span>{{ extraServices.find(s => s.id === id)?.price }} TMT</span>
                        </div>
                        <div class="mt-2 flex justify-between font-semibold border-t border-stone-200 dark:border-stone-700 pt-2">
                            <span>{{ t('landing.booking.total') }}</span>
                            <span class="text-amber-600">{{ totalAmount }} TMT</span>
                        </div>
                    </div>

                    <!-- Name -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-stone-700 dark:text-stone-300 mb-2">
                            {{ t('landing.booking.guest_name') }} <span class="text-red-500">*</span>
                        </label>
                        <input
                            type="text"
                            v-model="form.guest_name"
                            :placeholder="t('bookings.guest_name_placeholder')"
                            class="w-full rounded-xl border border-stone-200 bg-stone-50 px-3 py-2.5 text-sm
                                   focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20
                                   dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100"
                        />
                        <p v-if="form.errors.guest_name" class="mt-1 text-xs text-red-500">
                            {{ form.errors.guest_name }}
                        </p>
                    </div>

                    <!-- Phone -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-stone-700 dark:text-stone-300 mb-2">
                            {{ t('landing.booking.guest_phone') }} <span class="text-red-500">*</span>
                        </label>
                        <PhoneInput v-model="form.guest_phone" :error="form.errors.guest_phone" />
                    </div>

                    <!-- Last name (optional) -->
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-stone-700 dark:text-stone-300 mb-2">
                            {{ t('landing.booking.guest_last_name') }}
                            <span class="text-stone-400 text-xs">({{ t('landing.booking.optional') }})</span>
                        </label>
                        <input
                            type="text"
                            v-model="form.guest_last_name"
                            :placeholder="t('bookings.guest_last_name_placeholder')"
                            class="w-full rounded-xl border border-stone-200 bg-stone-50 px-3 py-2.5 text-sm
                                   focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20
                                   dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100"
                        />
                    </div>

                    <!-- Birthday (optional) -->
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-stone-700 dark:text-stone-300 mb-2">
                            {{ t('landing.booking.guest_birthday') }}
                            <span class="text-stone-400 text-xs">({{ t('landing.booking.optional') }})</span>
                        </label>
                        <BirthdayInput v-model="form.guest_birthday" :error="form.errors.guest_birthday" />
                        <!-- Birthday hint -->
                        <p v-if="form.guest_birthday && isTodayBirthday" class="mt-1 text-xs text-amber-600">
                            🎂 {{ t('landing.booking.birthday_hint') }}
                        </p>
                    </div>

                    <div class="flex gap-3">
                        <button
                            type="button"
                            @click="currentStep = 2"
                            class="flex-1 rounded-xl border border-stone-200 px-4 py-3 text-sm font-medium
                                   text-stone-600 transition hover:bg-stone-50 dark:border-stone-700"
                        >
                            ← {{ t('landing.booking.back') }}
                        </button>
                        <button
                            type="button"
                            @click="submitBooking"
                            :disabled="form.processing || !form.guest_name || !form.guest_phone"
                            class="flex-1 rounded-xl bg-amber-600 px-4 py-3 text-sm font-semibold text-white
                                   transition hover:bg-amber-700 disabled:opacity-50 disabled:cursor-not-allowed"
                        >
                            {{ form.processing ? '...' : t('landing.booking.submit') }}
                        </button>
                    </div>
                </div>

                <!-- Success message -->
                <div
                    v-if="showSuccess"
                    class="absolute inset-0 flex flex-col items-center justify-center
                           rounded-2xl bg-white dark:bg-stone-900 p-8 text-center"
                >
                    <div class="mb-4 text-6xl">✅</div>
                    <h3 class="text-xl font-bold text-stone-900 dark:text-white mb-2">
                        {{ t('landing.booking.success_title') }}
                    </h3>
                    <p class="text-stone-600 dark:text-stone-400 mb-6">
                        {{ t('landing.booking.success_text') }}
                    </p>
                    <button
                        @click="emit('close')"
                        class="rounded-xl bg-amber-600 px-6 py-3 text-sm font-semibold text-white hover:bg-amber-700"
                    >
                        {{ t('landing.booking.close') }}
                    </button>
                </div>

                <ToastNotification
                    :show="toast.show"
                    :type="toast.type"
                    :message="toast.message"
                    :duration="4000"
                    @close="hideToast"
                />

            </div>
        </div>
    </div>
</template>


