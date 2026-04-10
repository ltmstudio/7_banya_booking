<script setup>
/**
 * Карточка комнаты на лендинге: фото‑слайдшоу, статус доступности и кнопка "Забронировать".
 * Управляет собственным слайдшоу независимо от других карточек.
 */
import { computed, onBeforeUnmount, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    room: { type: Object, required: true },
    hasChecked: { type: Boolean, default: false },
    availability: { type: Object, default: () => ({}) },
    index: { type: Number, default: 0 },
    cardsVisible: { type: Boolean, default: false },
});

const emit = defineEmits(['book']);

const currentLocale = computed(() => usePage().props.locale || 'ru');
const { t } = useI18n();

/**
 * Возвращает локализованное название категории комнаты по `room.category`.
 * Фолбэк: `room.category_label` (как пришло с бэкенда) или сам `room.category`.
 */
function categoryLabel(room) {
    const keyByCategory = {
        standard: 'rooms.category_standard',
        family: 'rooms.category_family',
        vip: 'rooms.category_vip',
    };

    const key = keyByCategory?.[room?.category];
    if (key) return t(key);

    return room?.category_label ?? room?.category ?? '';
}

/**
 * Слайдшоу фото для карточки: индекс текущего фото на комнату.
 */
const photoIndexes = ref({});

/**
 * Идентификаторы таймеров слайдшоу: независимый таймер на каждую комнату.
 */
const slideshowTimers = ref({});

watch(
    () => props.room,
    (room) => {
        if (!room?.id) return;

        if (photoIndexes.value[room.id] === undefined) {
            photoIndexes.value[room.id] = 0;
        }

        const hasManyPhotos = Array.isArray(room.photos) && room.photos.length > 1;
        const hasTimer = slideshowTimers.value[room.id] !== undefined;

        if (hasManyPhotos && !hasTimer) {
            slideshowTimers.value[room.id] = setInterval(() => {
                const current = photoIndexes.value[room.id] ?? 0;
                const len = room.photos?.length ?? 0;
                if (len > 1) {
                    photoIndexes.value[room.id] = (current + 1) % len;
                }
            }, 3000);
        }

        if (!hasManyPhotos && hasTimer) {
            clearInterval(slideshowTimers.value[room.id]);
            delete slideshowTimers.value[room.id];
            photoIndexes.value[room.id] = 0;
        }
    },
    { immediate: true, deep: true }
);

onBeforeUnmount(() => {
    Object.values(slideshowTimers.value).forEach((timerId) => clearInterval(timerId));
    slideshowTimers.value = {};
});

/**
 * Ручное управление слайдшоу для конкретной комнаты (стрелки/точки).
 */
function setPhotoIndex(roomId, idx) {
    const len = props.room?.photos?.length ?? 0;
    if (len <= 0) return;
    const safeIdx = ((idx % len) + len) % len;
    photoIndexes.value[roomId] = safeIdx;
}

function nextPhoto(roomId) {
    const current = photoIndexes.value[roomId] ?? 0;
    setPhotoIndex(roomId, current + 1);
}

function prevPhoto(roomId) {
    const current = photoIndexes.value[roomId] ?? 0;
    setPhotoIndex(roomId, current - 1);
}

/** Текст статуса комнаты в карточке. */
function statusLabel(room) {
    if (room.is_walk_in) return t('landing.booking.walk_in_badge');
    if (!props.hasChecked) return t('landing.booking.check_availability');
    const s = props.availability?.[room.id] ?? 'free';
    if (s === 'free') return t('landing.booking.free');
    if (s === 'busy') return t('landing.booking.busy');
    if (s === 'cleaning') return t('landing.booking.cleaning');
    return '';
}

/** CSS‑классы бейджа статуса комнаты в карточке. */
function statusClasses(room) {
    if (room.is_walk_in) return 'bg-blue-500 text-white';
    if (!props.hasChecked) return 'bg-slate-700/80 text-slate-50';
    const s = props.availability?.[room.id] ?? 'free';
    if (s === 'free') return 'bg-emerald-500 text-white';
    if (s === 'busy') return 'bg-rose-500 text-white';
    if (s === 'cleaning') return 'bg-amber-500 text-white';
    return 'bg-slate-600 text-white';
}
</script>

<template>
    <article
        :key="room.id"
        :style="{ transitionDelay: cardsVisible ? `${index * 80}ms` : '0ms' }"
        :class="[
            'flex h-full flex-col overflow-hidden rounded-2xl bg-white shadow-sm ring-1 ring-stone-200 dark:bg-stone-900/80 dark:ring-stone-700',
            'transition-[transform,opacity,box-shadow] duration-300 ease-in-out',
            'hover:-translate-y-1 hover:shadow-lg',
            cardsVisible
                ? 'translate-y-0 opacity-100'
                : 'translate-y-6 opacity-0'
        ]"
    >
        <div class="relative h-44 overflow-hidden bg-stone-200 dark:bg-stone-800">

            <!-- Slides -->
            <template v-if="room.photos && room.photos.length > 0">
                <div
                    v-for="(photo, idx) in room.photos"
                    :key="photo.id"
                    class="absolute inset-0 bg-cover bg-center bg-no-repeat transition-opacity duration-700"
                    :style="`background-image: url('${photo.url}')`"
                    :class="idx === (photoIndexes[room.id] ?? 0) ? 'opacity-100' : 'opacity-0'"
                    aria-hidden="true"
                />
            </template>

            <!-- Fallback if no photos -->
            <div
                v-else
                class="absolute inset-0 bg-cover bg-center bg-no-repeat"
                style="background-image: url('/static_images/placeholder.png')"
                aria-hidden="true"
            />

            <!-- Gradient overlay -->
            <div class="absolute inset-0 bg-gradient-to-t from-black/40 via-black/10 to-transparent" />

            <!-- Arrows (show only if more than 1 photo) -->
            <template v-if="room.photos && room.photos.length > 1">
                <button
                    type="button"
                    class="absolute left-2 top-1/2 z-10 -translate-y-1/2 rounded-full bg-black/35 p-2 text-white backdrop-blur-sm transition hover:bg-black/50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60"
                    aria-label="Предыдущее фото"
                    @click.stop="prevPhoto(room.id)"
                >
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M12.78 15.53a.75.75 0 01-1.06 0l-5-5a.75.75 0 010-1.06l5-5a.75.75 0 111.06 1.06L8.31 10l4.47 4.47a.75.75 0 010 1.06z" clip-rule="evenodd" />
                    </svg>
                </button>
                <button
                    type="button"
                    class="absolute right-2 top-1/2 z-10 -translate-y-1/2 rounded-full bg-black/35 p-2 text-white backdrop-blur-sm transition hover:bg-black/50 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60"
                    aria-label="Следующее фото"
                    @click.stop="nextPhoto(room.id)"
                >
                    <svg class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                        <path fill-rule="evenodd" d="M7.22 4.47a.75.75 0 011.06 0l5 5a.75.75 0 010 1.06l-5 5a.75.75 0 11-1.06-1.06L11.69 10 7.22 5.53a.75.75 0 010-1.06z" clip-rule="evenodd" />
                    </svg>
                </button>
            </template>

            <!-- Status badge -->
            <div class="absolute left-3 top-3">
                <span
                    class="inline-flex items-center rounded-full px-3 py-1 text-xs font-semibold shadow-sm"
                    :class="statusClasses(room)"
                >
                    {{ statusLabel(room) }}
                </span>
            </div>

            <!-- Dots indicator (show only if more than 1 photo) -->
            <div
                v-if="room.photos && room.photos.length > 1"
                class="absolute bottom-2 left-0 right-0 flex justify-center gap-1"
            >
                <button
                    v-for="(photo, idx) in room.photos"
                    :key="photo.id"
                    type="button"
                    class="h-1.5 rounded-full transition-all duration-300 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-white/60"
                    :class="idx === (photoIndexes[room.id] ?? 0)
                        ? 'w-4 bg-white'
                        : 'w-1.5 bg-white/50'"
                    :aria-label="`Показать фото ${idx + 1}`"
                    @click.stop="setPhotoIndex(room.id, idx)"
                />
            </div>
        </div>

        <div class="flex flex-1 flex-col px-4 py-4">
            <div class="space-y-1">
                <h3 class="text-sm font-semibold text-stone-900 dark:text-stone-50">
                    {{ room.name?.[currentLocale] ?? room.name }}
                </h3>
                <p class="text-xs font-medium uppercase tracking-wide text-amber-700 dark:text-amber-400">
                    {{ categoryLabel(room) }}
                </p>
            </div>

            <div class="mt-3 space-y-1 text-sm">
                <div v-if="room.promo_price_per_hour" class="flex items-baseline gap-2">
                    <span class="text-xs text-stone-400 line-through">{{ room.price_per_hour }} TMT</span>
                    <span class="text-base font-semibold text-amber-700 dark:text-amber-400">
                        {{ room.promo_price_per_hour }} TMT
                        <span class="text-xs font-normal">
                            {{ room.is_walk_in ? t('landing.booking.per_person') : t('landing.booking.per_hour') }}
                        </span>
                    </span>
                </div>
                <div v-else class="text-base font-semibold text-stone-900 dark:text-stone-50">
                    {{ room.price_per_hour }} TMT
                    <span class="text-xs font-normal text-stone-500">
                        {{ room.is_walk_in ? t('landing.booking.per_person') : t('landing.booking.per_hour') }}
                    </span>
                </div>
                <p class="mt-1 flex items-center gap-1 text-xs text-stone-600 dark:text-stone-300">
                    <span aria-hidden="true">👥</span>
                    <span v-if="room.is_walk_in" class="text-blue-500 font-semibold text-sm">∞</span>
                    <span v-else-if="room.capacity">
                        {{ t('landing.booking.capacity', { n: room.capacity }) }}
                    </span>
                </p>
            </div>

            <div class="mt-4 border-t border-stone-100 pt-3 dark:border-stone-700">
                <div v-if="room.is_walk_in" class="space-y-2">
                    <div
                        class="flex items-center gap-2 rounded-xl border border-blue-200 bg-blue-50 px-3 py-2 dark:border-blue-800 dark:bg-blue-900/20"
                    >
                        <span class="text-sm text-blue-500">🚶</span>
                        <p class="text-xs font-medium text-blue-700 dark:text-blue-400">
                            {{ t('landing.booking.walk_in_label') }}
                        </p>
                    </div>
                </div>
                <button
                    v-else
                    type="button"
                    class="inline-flex w-full items-center justify-center rounded-xl bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition hover:bg-amber-700 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2 focus-visible:ring-offset-white dark:focus-visible:ring-offset-stone-900"
                    @click="emit('book', room)"
                >
                    {{ t('landing.booking.book_button') }}
                </button>
            </div>
        </div>
    </article>
</template>

