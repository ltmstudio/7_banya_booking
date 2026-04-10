<script setup>
/**
 * Шапка лендинга: лого, навигация, переключатель TK/RU, переключатель темы.
 */
import { Link, usePage } from '@inertiajs/vue3';
import { ref, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const isDark = ref(false);

function toggleTheme() {
    isDark.value = !isDark.value;
    document.documentElement.classList.toggle('dark', isDark.value);
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
}

onMounted(() => {
    isDark.value = document.documentElement.classList.contains('dark');
});
</script>

<template>
    <header class="sticky top-0 z-50 flex flex-wrap items-center justify-between gap-4 border-b border-stone-200/60 bg-stone-50/95 py-4 backdrop-blur-md dark:border-stone-700/60 dark:bg-stone-900/95 md:py-5">
        <Link
            href="/"
            class="flex items-center gap-2 text-xl font-semibold text-stone-800 dark:text-stone-100 hover:text-amber-700 dark:hover:text-amber-400 transition-colors"
        >
            <span class="flex size-10 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400">
                <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 18.657A8 8 0 0 1 6.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0 1 20 13a7.975 7.975 0 0 1-2.343 5.657z" />
                </svg>
            </span>
            <span>{{ t('landing.header.brand') }}</span>
        </Link>

        <nav class="flex items-center gap-1 sm:gap-2">
            <a
                href="#hero"
                class="rounded-lg px-3 py-2 text-sm font-medium text-stone-600 hover:bg-stone-200/80 hover:text-stone-900 dark:text-stone-400 dark:hover:bg-stone-700 dark:hover:text-stone-100 transition-colors"
            >
                {{ t('landing.header.nav_home') }}
            </a>
            <a
                href="#about"
                class="rounded-lg px-3 py-2 text-sm font-medium text-stone-600 hover:bg-stone-200/80 hover:text-stone-900 dark:text-stone-400 dark:hover:bg-stone-700 dark:hover:text-stone-100 transition-colors"
            >
                {{ t('landing.header.nav_about') }}
            </a>
            <a
                href="#services"
                class="rounded-lg px-3 py-2 text-sm font-medium text-stone-600 hover:bg-stone-200/80 hover:text-stone-900 dark:text-stone-400 dark:hover:bg-stone-700 dark:hover:text-stone-100 transition-colors"
            >
                {{ t('landing.header.nav_services') }}
            </a>
            <a
                href="#gallery"
                class="rounded-lg px-3 py-2 text-sm font-medium text-stone-600 hover:bg-stone-200/80 hover:text-stone-900 dark:text-stone-400 dark:hover:bg-stone-700 dark:hover:text-stone-100 transition-colors"
            >
                {{ t('landing.header.nav_gallery') }}
            </a>
            <button
                type="button"
                @click="toggleTheme"
                class="ml-2 flex size-9 items-center justify-center rounded-lg border border-stone-300 dark:border-stone-600 bg-white dark:bg-stone-800 text-stone-600 dark:text-stone-400 hover:bg-stone-100 dark:hover:bg-stone-700 transition-colors"
                :title="isDark ? t('landing.header.theme_light') : t('landing.header.theme_dark')"
            >
                <!-- Sun icon (light mode) -->
                <svg v-if="isDark" class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <!-- Moon icon (dark mode) -->
                <svg v-else class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </button>
            <div class="ml-1 flex rounded-lg border border-stone-300 dark:border-stone-600 overflow-hidden">
                <Link
                    :href="route('change.lang', { lang: 'tk' })"
                    preserve-scroll
                    :class="[
                        'px-3 py-2 text-sm font-medium transition-colors block',
                        $page.props.locale === 'tk'
                            ? 'bg-amber-600 text-white'
                            : 'bg-white text-stone-600 hover:bg-stone-100 dark:bg-stone-800 dark:text-stone-400 dark:hover:bg-stone-700'
                    ]"
                >
                    TK
                </Link>
                <Link
                    :href="route('change.lang', { lang: 'ru' })"
                    preserve-scroll
                    :class="[
                        'px-3 py-2 text-sm font-medium transition-colors border-l border-stone-300 dark:border-stone-600 block',
                        $page.props.locale === 'ru'
                            ? 'bg-amber-600 text-white'
                            : 'bg-white text-stone-600 hover:bg-stone-100 dark:bg-stone-800 dark:text-stone-400 dark:hover:bg-stone-700'
                    ]"
                >
                    RU
                </Link>
            </div>
        </nav>
    </header>
</template>
