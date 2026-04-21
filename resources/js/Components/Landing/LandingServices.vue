<script setup>
/**
 * Секция «Услуги» лендинга: 3 статичные карточки + список доп. услуг с бэкенда.
 */
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    extraServices: { type: Array, default: () => [] },
});
const currentLocale = computed(() => usePage().props.locale || 'ru');
const { t } = useI18n();
</script>

<template>
    <section id="services" class="scroll-mt-20 py-6 md:py-8">
        <h2 class="text-2xl font-bold text-stone-900 dark:text-white md:text-3xl">
            {{ t('landing.services.title') }}
        </h2>
        <p class="mt-2 text-stone-600 dark:text-stone-400">
            {{ t('landing.services.subtitle') }}
        </p>
        <div class="mt-8 grid gap-6 sm:grid-cols-2 lg:grid-cols-3">
            <div class="flex flex-col rounded-2xl bg-white p-6 shadow-sm ring-1 ring-stone-200/80 dark:bg-stone-800/50 dark:ring-stone-700">
                <div class="flex size-12 items-center justify-center rounded-xl bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400">
                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-stone-900 dark:text-white">{{ t('landing.services.rooms_title') }}</h3>
                <p class="mt-2 text-sm text-stone-600 dark:text-stone-400">{{ t('landing.services.rooms_desc') }}</p>
            </div>
            <div class="flex flex-col rounded-2xl bg-white p-6 shadow-sm ring-1 ring-stone-200/80 dark:bg-stone-800/50 dark:ring-stone-700">
                <div class="flex size-12 items-center justify-center rounded-xl bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400">
                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4" />
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-stone-900 dark:text-white">{{ t('landing.services.extra_title') }}</h3>
                <p class="mt-2 text-sm text-stone-600 dark:text-stone-400">{{ t('landing.services.extra_desc') }}</p>
            </div>
            <div class="flex flex-col rounded-2xl bg-white p-6 shadow-sm ring-1 ring-stone-200/80 dark:bg-stone-800/50 dark:ring-stone-700 sm:col-span-2 lg:col-span-1">
                <div class="flex size-12 items-center justify-center rounded-xl bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400">
                    <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                    </svg>
                </div>
                <h3 class="mt-4 text-lg font-semibold text-stone-900 dark:text-white">{{ t('landing.services.booking_title') }}</h3>
                <p class="mt-2 text-sm text-stone-600 dark:text-stone-400">{{ t('landing.services.booking_desc') }}</p>
            </div>
        </div>
        <div v-if="props.extraServices.length" class="mt-10">
            <h3 class="mb-4 text-lg font-semibold text-stone-900 dark:text-white">
                {{ t('landing.services.extra_services_title') }}
            </h3>
            <div class="grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div
                    v-for="service in props.extraServices"
                    :key="service.id"
                    class="flex flex-col items-center rounded-2xl bg-white p-5 shadow-sm ring-1 ring-stone-200/80 dark:bg-stone-800/50 dark:ring-stone-700 text-center"
                >
                    <img
                        v-if="service.icon_url"
                        :src="service.icon_url"
                        :alt="service.name?.[currentLocale] ?? ''"
                        class="h-12 w-12 object-contain"
                    />
                    <div
                        v-else
                        class="flex h-12 w-12 items-center justify-center rounded-xl bg-amber-100 dark:bg-amber-900/40 text-amber-700 text-2xl"
                    >
                        🛁
                    </div>
                    <p class="mt-3 text-sm font-medium text-stone-900 dark:text-white">
                        {{ service.name?.[currentLocale] ?? service.name }}
                    </p>
                    <p class="mt-1 text-sm font-semibold text-amber-600">
                        {{ service.price }} TMT
                    </p>
                </div>
            </div>
        </div>
    </section>
</template>
