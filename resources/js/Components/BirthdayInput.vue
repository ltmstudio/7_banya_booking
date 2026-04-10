<script setup>
/**
 * Выбор даты рождения: 3 селекта (день, месяц, год).
 * min 1950, max = (текущий год − 3). Двуязычный.
 */
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    modelValue: { type: String, default: '' },
    error: { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue']);
const { t } = useI18n();

const nowYear = () => new Date().getFullYear();
const minYear = 1950;
const maxYear = computed(() => nowYear() - 3);

const months = computed(() =>
    [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12].map((v) => ({ v, l: t(`client.month_${v}`) }))
);

const years = computed(() => {
    const arr = [];
    for (let y = maxYear.value; y >= minYear; y--) arr.push(y);
    return arr;
});

const daysInMonth = (month, year) => new Date(year, month, 0).getDate();

const parsed = computed(() => {
    const v = props.modelValue || '';
    if (!/^\d{4}-\d{2}-\d{2}$/.test(v)) return { d: '', m: '', y: '' };
    const [y, m, d] = v.split('-').map(Number);
    return { d: d || '', m: m || '', y: y || '' };
});

const local = computed({
    get: () => parsed.value,
    set: ({ d, m, y }) => {
        if (d && m && y) {
            const dd = String(d).padStart(2, '0');
            const mm = String(m).padStart(2, '0');
            emit('update:modelValue', `${y}-${mm}-${dd}`);
        } else {
            emit('update:modelValue', '');
        }
    },
});

const selDay = computed({
    get: () => parsed.value.d,
    set: (v) => {
        const m = parsed.value.m || 1;
        const y = parsed.value.y || minYear;
        local.value = { ...parsed.value, d: v ? Number(v) : '' };
    },
});
const selMonth = computed({
    get: () => parsed.value.m,
    set: (v) => {
        const m = v ? Number(v) : '';
        const y = parsed.value.y || minYear;
        let d = parsed.value.d;
        if (d && m) {
            const maxD = daysInMonth(m, y);
            if (d > maxD) d = maxD;
        }
        local.value = { ...parsed.value, m, d: d || '' };
    },
});
const selYear = computed({
    get: () => parsed.value.y,
    set: (v) => {
        local.value = { ...parsed.value, y: v ? Number(v) : '' };
    },
});

const dayOptions = computed(() => {
    const m = parsed.value.m || 12;
    const y = parsed.value.y || minYear;
    const max = daysInMonth(m, y);
    return Array.from({ length: max }, (_, i) => i + 1);
});
</script>

<template>
    <div class="flex gap-2">
        <select
            :value="selDay"
            class="flex-1 rounded-xl border border-stone-200 bg-stone-50 px-3 py-2.5 text-sm text-stone-900 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100"
            @change="selDay = $event.target.value ? Number($event.target.value) : ''"
        >
            <option value="">{{ t('client.birthday_day') }}</option>
            <option v-for="d in dayOptions" :key="d" :value="d">{{ d }}</option>
        </select>
        <select
            :value="selMonth"
            class="flex-1 rounded-xl border border-stone-200 bg-stone-50 px-3 py-2.5 text-sm text-stone-900 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100"
            @change="selMonth = $event.target.value ? Number($event.target.value) : ''"
        >
            <option value="">{{ t('client.birthday_month') }}</option>
            <option v-for="mo in months" :key="mo.v" :value="mo.v">{{ mo.l }}</option>
        </select>
        <select
            :value="selYear"
            class="flex-1 rounded-xl border border-stone-200 bg-stone-50 px-3 py-2.5 text-sm text-stone-900 focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100"
            @change="selYear = $event.target.value ? Number($event.target.value) : ''"
        >
            <option value="">{{ t('client.birthday_year') }}</option>
            <option v-for="y in years" :key="y" :value="y">{{ y }}</option>
        </select>
    </div>
    <p v-if="error" class="mt-1 text-xs text-red-500">{{ error }}</p>
</template>
