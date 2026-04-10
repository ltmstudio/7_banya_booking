<script setup>
/**
 * Hero-секция лендинга (Главная).
 */
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
</script>

<template>
    <section id="hero" class="scroll-mt-20 min-h-screen flex flex-col justify-center py-12 md:py-20 lg:py-28">
        <div class="mx-auto max-w-3xl text-center">
            <h1 class="text-4xl font-bold tracking-tight text-stone-900 dark:text-white sm:text-5xl md:text-6xl">
                {{ t('landing.hero.title') }}
                <span class="text-amber-600 dark:text-amber-500">{{ t('landing.hero.title_accent') }}</span>
            </h1>
            <p class="mt-6 text-lg text-stone-600 dark:text-stone-400 sm:text-xl">
                {{ t('landing.hero.subtitle') }}
            </p>
            <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                <a
                    href="#booking"
                    class="inline-flex items-center justify-center rounded-xl bg-amber-600 px-6 py-3 text-base font-semibold text-white shadow-sm hover:bg-amber-700 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-500 focus-visible:ring-offset-2 transition-colors"
                >
                    {{ t('landing.hero.cta_book') }}
                </a>
                <a
                    href="#about"
                    class="inline-flex items-center justify-center rounded-xl border border-stone-300 bg-white px-6 py-3 text-base font-semibold text-stone-700 hover:bg-stone-50 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-200 dark:hover:bg-stone-700 transition-colors"
                >
                    {{ t('landing.hero.cta_about') }}
                </a>
            </div>
        </div>
    </section>
</template>
