<script setup>
/**
 * Секция «Галерея» лендинга: фото комнат с бэкенда.
 */
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    rooms: { type: Array, default: () => [] },
});
const currentLocale = computed(() => usePage().props.locale || 'ru');
const { t } = useI18n();
</script>

<template>
    <section id="gallery" class="scroll-mt-20 py-6 md:py-8">
        <h2 class="text-2xl font-bold text-stone-900 dark:text-white md:text-3xl">
            {{ t('landing.gallery.title') }}
        </h2>
        <p class="mt-2 text-stone-600 dark:text-stone-400">
            {{ t('landing.gallery.subtitle') }}
        </p>
        <div class="mt-8 grid gap-4 sm:grid-cols-2 lg:grid-cols-3">
            <template v-for="room in props.rooms" :key="room.id">
                <div
                    v-for="(photo, idx) in (room.photos || []).slice(0, 1)"
                    :key="photo.id"
                    class="aspect-[4/3] overflow-hidden rounded-2xl bg-stone-200 dark:bg-stone-700"
                >
                    <img
                        :src="photo.url"
                        :alt="room.name?.[currentLocale] ?? room.name ?? ''"
                        class="h-full w-full object-cover transition duration-300 hover:scale-105"
                    />
                </div>
            </template>
            <div
                v-if="!props.rooms.some(r => (r.photos || []).length)"
                v-for="n in 3"
                :key="'fb-' + n"
                class="flex aspect-[4/3] items-center justify-center rounded-2xl bg-stone-200 dark:bg-stone-700"
            >
                <span class="text-sm text-stone-500">{{ t('landing.gallery.photo') }} {{ n }}</span>
            </div>
        </div>
    </section>
</template>
