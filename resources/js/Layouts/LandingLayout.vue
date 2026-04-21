<script setup>
/**
 * Layout для публичных страниц (лендинг).
 */
import LandingHeader from '@/Components/Landing/LandingHeader.vue';
import LandingFooter from '@/Components/Landing/LandingFooter.vue';
import LandingMap from '@/Components/Landing/LandingMap.vue';
import { usePage } from '@inertiajs/vue3';
import { computed } from 'vue';

defineProps({
    canLogin: { type: Boolean, default: false },
});
const t = computed(() => usePage().props.landing?.map || {});
</script>

<template>
    <div class="min-h-screen bg-stone-50 text-stone-800 selection:bg-amber-600 selection:text-white dark:bg-stone-900 dark:text-stone-200">
        <!-- Декоративный фон -->
        <div
            class="pointer-events-none fixed inset-0 bg-[radial-gradient(ellipse_80%_50%_at_50%_-20%,rgba(217,119,6,0.12),transparent)] dark:bg-[radial-gradient(ellipse_80%_50%_at_50%_-20%,rgba(217,119,6,0.08),transparent)]"
            aria-hidden="true"
        />

        <div class="relative mx-auto w-full max-w-7xl px-4 sm:px-6 lg:px-8">
            <LandingHeader />
            <main class="pb-16 md:pb-24">
                <slot />
            </main>
            <div class="pb-8">
                <h3 class="mb-4 text-lg font-semibold text-stone-800 dark:text-stone-200">{{ t.how_to_find || 'Как нас найти' }}</h3>
                <LandingMap :latitude="37.92160928021097" :longitude="58.41173710301474" title="7-ой бани" />
            </div>
        </div>
        <LandingFooter />
    </div>
</template>
