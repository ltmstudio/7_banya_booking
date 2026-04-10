<script setup>
/**
 * Компонент карточек бронирования на лендинге.
 * Данные комнат и доп. услуг с бэкенда; проверка доступности по API; форма бронирования.
 */
import { computed, ref, onMounted } from 'vue';
import axios from 'axios';
import { useI18n } from 'vue-i18n';
import RoomCard from '@/Components/Landing/RoomCard.vue';
import BookingForm from '@/Components/Landing/BookingForm.vue';
import { buildHalfHourOptions, formatDurationLabel } from '@/Support/duration';
import TimeInput from '@/Components/TimeInput.vue';

const props = defineProps({
    rooms: { type: Array, default: () => [] },
    extraServices: { type: Array, default: () => [] },
    bookingSettings: { type: Object, default: () => ({ min_hours: 1, max_hours: 10 }) },
});
const { t } = useI18n();

const today = new Date().toISOString().slice(0, 10);
const date = ref(today);
const time = ref('14:00');
const duration = ref(2);
const hasChecked = ref(false);
const cardsVisible = ref(false);
const cardsRef = ref(null);
const availability = ref({});
const checking = ref(false);

onMounted(() => {
    const obs = new IntersectionObserver(
        ([e]) => {
            if (e?.isIntersecting) {
                cardsVisible.value = true;
                obs.disconnect();
            }
        },
        { threshold: 0.12, rootMargin: '0px 0px -40px 0px' }
    );
    if (cardsRef.value) obs.observe(cardsRef.value);

    // Первая проверка доступности при открытии блока бронирования.
    handleCheck();
});

const durationOptions = computed(() => {
    return buildHalfHourOptions(props.bookingSettings.min_hours, props.bookingSettings.max_hours);
});

/** Текст длительности: "1 ч" / "1 ч 30 мин". */
function durationLabel(hours) {
    return formatDurationLabel(hours, t('landing.booking.hours_short'), t('landing.booking.minutes_short'));
}

const filteredRooms = computed(() => props.rooms);

/** Комнаты с онлайн-бронированием (для модалки формы — без walk-in). */
const roomsForOnlineBooking = computed(() => props.rooms.filter((r) => !r.is_walk_in));

const selectedRoomId = ref(null);

const handleCheck = async () => {
    if (!date.value || !time.value || !duration.value) return;
    hasChecked.value = true;
    checking.value = true;
    try {
        const res = await axios.get(route('public.availability'), {
            params: {
                date: date.value,
                start_time: time.value,
                duration_hours: duration.value,
            },
        });
        const data = { ...res.data };
        props.rooms.filter((r) => r.is_walk_in).forEach((r) => {
            delete data[r.id];
        });
        availability.value = data;
    } catch (e) {
        console.error(e);
    } finally {
        checking.value = false;
    }
};

// Modal state
const showBookingForm = ref(false);
const selectedRoomForBooking = ref(null);

/** Открыть модалку оформления брони для выбранной комнаты. */
function onBookRoom(room) {
    selectedRoomForBooking.value = room;
    showBookingForm.value = true;
    document.body.style.overflow = 'hidden';
}

/** Закрыть модалку и вернуть прокрутку страницы. */
function onCloseForm() {
    showBookingForm.value = false;
    selectedRoomForBooking.value = null;
    document.body.style.overflow = '';
}
</script>

<template>
    <section id="booking" class="scroll-mt-20 py-12 md:py-16">
        <div class="mb-8 md:mb-10">
            <h2 class="text-center text-2xl font-bold tracking-tight text-stone-900 dark:text-stone-100 md:text-3xl">
                {{ t('landing.booking.title') }}
            </h2>
            <p class="mt-2 text-center text-sm text-stone-600 dark:text-stone-300 md:text-base">
                {{ t('landing.booking.subtitle') }}
            </p>
        </div>

        <!-- Фильтр -->
        <div
            class="mx-auto mb-10 flex w-full max-w-5xl flex-col gap-4 rounded-2xl bg-white p-4 shadow-sm ring-1 ring-stone-200 dark:bg-stone-900/80 dark:ring-stone-700 md:flex-row md:items-end md:gap-6 md:p-5"
        >
            <div class="flex-1 space-y-1">
                <label class="text-xs font-medium uppercase tracking-wide text-stone-500">{{ t('landing.booking.date') }}</label>
                <input
                    v-model="date"
                    type="date"
                    class="w-full rounded-xl border border-stone-200 bg-stone-50 px-3 py-2 text-sm text-stone-900 shadow-sm outline-none ring-amber-500/0 transition focus:border-amber-500 focus:bg-white focus:ring-2 dark:border-stone-700 dark:bg-stone-900 dark:text-stone-100"
                />
            </div>

            <div class="flex-1 space-y-1">
                <label class="text-xs font-medium uppercase tracking-wide text-stone-500">{{ t('landing.booking.start_time') }}</label>
                <TimeInput v-model="time" />
            </div>

            <div class="flex-1 space-y-1">
                <label class="text-xs font-medium uppercase tracking-wide text-stone-500">{{ t('landing.booking.duration') }}</label>
                <select
                    v-model="duration"
                    class="w-full rounded-xl border border-stone-200 bg-stone-50 px-3 py-2 text-sm text-stone-900 shadow-sm outline-none ring-amber-500/0 transition focus:border-amber-500 focus:bg-white focus:ring-2 dark:border-stone-700 dark:bg-stone-900 dark:text-stone-100"
                >
                    <option v-for="d in durationOptions" :key="d" :value="d">
                        {{ durationLabel(d) }}
                    </option>
                </select>
            </div>

            <div class="flex items-end">
                <button
                    type="button"
                    class="inline-flex w-full items-center justify-center rounded-xl bg-amber-600 px-5 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:focus-visible:ring-offset-stone-900 md:w-auto disabled:opacity-60"
                    :disabled="checking"
                    @click="handleCheck"
                >
                    {{ checking ? t('landing.booking.checking') : t('landing.booking.check') }}
                </button>
            </div>
        </div>

        <!-- Карточки комнат -->
        <div ref="cardsRef" class="grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <RoomCard
                v-for="(room, i) in filteredRooms"
                :key="room.id"
                :room="room"
                :has-checked="hasChecked"
                :availability="availability"
                :index="i"
                :cards-visible="cardsVisible"
                @book="onBookRoom"
            />
        </div>
    </section>

    <BookingForm
        v-if="showBookingForm"
        :rooms="roomsForOnlineBooking"
        :extra-services="props.extraServices"
        :selected-room="selectedRoomForBooking"
        :date="date"
        :start-time="time"
        :duration-hours="duration"
        :min-hours="bookingSettings.min_hours"
        :max-hours="bookingSettings.max_hours"
        @close="onCloseForm"
        @success="onCloseForm"
    />
</template>

