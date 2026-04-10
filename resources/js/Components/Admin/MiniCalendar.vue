<script setup>
/**
 * Мини-календарь для выбора даты; отображает точки по дням с бронированиями.
 */
import { ref, computed } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    bookingsByDate: { type: Object, default: () => ({}) },
    selectedDate: { type: String, default: null },
});
const emit = defineEmits(['select-date']);

const { t, locale } = useI18n();

const today = new Date();
const currentMonth = ref(new Date(today.getFullYear(), today.getMonth(), 1));

const monthLabel = computed(() =>
    currentMonth.value.toLocaleString(locale.value || 'ru', { month: 'long', year: 'numeric' })
);

const calendarDays = computed(() => {
    const year = currentMonth.value.getFullYear();
    const month = currentMonth.value.getMonth();
    const firstDow = (new Date(year, month, 1).getDay() + 6) % 7;
    const daysInMonth = new Date(year, month + 1, 0).getDate();
    const days = [];
    for (let i = 0; i < firstDow; i++) days.push(null);
    for (let d = 1; d <= daysInMonth; d++) days.push(d);
    return days;
});

const dateKey = (day) => {
    const y = currentMonth.value.getFullYear();
    const m = String(currentMonth.value.getMonth() + 1).padStart(2, '0');
    return `${y}-${m}-${String(day).padStart(2, '0')}`;
};

const isToday = (day) => dateKey(day) === today.toISOString().slice(0, 10);

const selectDay = (day) => {
    const key = dateKey(day);
    emit('select-date', props.selectedDate === key ? null : key);
};

const prevMonth = () => {
    currentMonth.value = new Date(
        currentMonth.value.getFullYear(),
        currentMonth.value.getMonth() - 1,
        1
    );
};
const nextMonth = () => {
    currentMonth.value = new Date(
        currentMonth.value.getFullYear(),
        currentMonth.value.getMonth() + 1,
        1
    );
};
</script>

<template>
    <div class="select-none rounded-2xl border border-gray-100 bg-white p-4 shadow-sm">
        <div class="mb-4 flex items-center justify-between">
            <button
                type="button"
                class="rounded-lg p-1.5 text-gray-500 transition hover:bg-gray-100"
                @click="prevMonth"
            >
                ‹
            </button>
            <span class="text-sm font-semibold capitalize text-gray-800">
                {{ monthLabel }}
            </span>
            <button
                type="button"
                class="rounded-lg p-1.5 text-gray-500 transition hover:bg-gray-100"
                @click="nextMonth"
            >
                ›
            </button>
        </div>

        <div class="mb-2 grid grid-cols-7">
            <div
                v-for="d in [
                    t('bookings.calendar.mon'),
                    t('bookings.calendar.tue'),
                    t('bookings.calendar.wed'),
                    t('bookings.calendar.thu'),
                    t('bookings.calendar.fri'),
                    t('bookings.calendar.sat'),
                    t('bookings.calendar.sun'),
                ]"
                :key="d"
                class="py-1 text-center text-xs font-medium text-gray-400"
            >
                {{ d }}
            </div>
        </div>

        <div class="grid grid-cols-7 gap-0.5">
            <div
                v-for="(day, idx) in calendarDays"
                :key="idx"
                :class="[
                    'relative flex h-9 w-full flex-col items-center justify-center rounded-lg text-sm transition cursor-pointer',
                    !day ? 'invisible' : '',
                    day && isToday(day) && selectedDate !== dateKey(day)
                        ? 'bg-orange-50 font-semibold text-orange-600'
                        : '',
                    day && selectedDate === dateKey(day)
                        ? 'bg-orange-500 font-semibold text-white'
                        : '',
                    day && selectedDate !== dateKey(day) && !isToday(day)
                        ? 'text-gray-700 hover:bg-gray-100'
                        : '',
                ]"
                @click="day && selectDay(day)"
            >
                <span v-if="day">{{ day }}</span>
                <span
                    v-if="day && bookingsByDate[dateKey(day)]"
                    :class="[
                        'absolute bottom-1 h-1 w-1 rounded-full',
                        selectedDate === dateKey(day) ? 'bg-white' : 'bg-orange-400',
                    ]"
                />
            </div>
        </div>

        <button
            v-if="selectedDate"
            type="button"
            class="mt-3 w-full text-center text-xs text-gray-400 transition hover:text-gray-600"
            @click="$emit('select-date', null)"
        >
            {{ t('bookings.calendar.clear') }} ×
        </button>
    </div>
</template>
