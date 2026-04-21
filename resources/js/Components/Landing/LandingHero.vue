<script setup>
/**
 * Hero-секция лендинга (Главная).
 */
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

/**
 * Плавная прокрутка к секции бронирования (клик по индикатору «scroll»).
 * @param {MouseEvent} event
 */
function scrollToBooking(event) {
    const el = document.getElementById('booking');
    if (el) {
        event.preventDefault();
        el.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}
</script>

<template>
    <section
        id="hero"
        class="relative left-1/2 flex min-h-screen w-screen -translate-x-1/2 scroll-mt-20 flex-col justify-center overflow-hidden py-12 md:py-20 lg:py-28"
    >
        <!-- Фоновое видео hero-секции. -->
        <video
            class="absolute inset-0 z-0 h-full w-full object-cover"
            autoplay
            muted
            loop
            playsinline
            preload="auto"
        >
            <source src="/videos/hero.mp4" type="video/mp4" />
            <source src="/videos/hero.webm" type="video/webm" />
        </video>

        <!-- Основное затемнение поверх видео. -->
        <div class="pointer-events-none absolute inset-0 z-10 bg-black/50 dark:bg-black/65" />

        <!-- Радиальный градиент: мягкое затемнение к краям кадра. -->
        <div
            class="pointer-events-none absolute inset-0 z-10"
            style="
                background: radial-gradient(
                    ellipse at center,
                    transparent 40%,
                    rgba(0, 0, 0, 0.4) 70%,
                    rgba(0, 0, 0, 0.85) 100%
                );
            "
        />

        <!-- Верхний край — плавный переход к фону страницы. -->
        <div
            class="pointer-events-none absolute left-0 right-0 top-0 z-10 h-32"
            style="background: linear-gradient(to bottom, rgba(0, 0, 0, 0.6) 0%, transparent 100%)"
        />

        <!-- Нижний край. -->
        <div
            class="pointer-events-none absolute bottom-0 left-0 right-0 z-10 h-40"
            style="background: linear-gradient(to top, rgba(0, 0, 0, 0.7) 0%, transparent 100%)"
        />

        <!-- Левый край. -->
        <div
            class="pointer-events-none absolute bottom-0 left-0 top-0 z-10 w-32"
            style="background: linear-gradient(to right, rgba(0, 0, 0, 0.6) 0%, transparent 100%)"
        />

        <!-- Правый край. -->
        <div
            class="pointer-events-none absolute bottom-0 right-0 top-0 z-10 w-32"
            style="background: linear-gradient(to left, rgba(0, 0, 0, 0.6) 0%, transparent 100%)"
        />

        <!-- Контент hero-секции поверх видео. -->
        <div class="relative z-20 mx-auto max-w-3xl px-4 text-center">
            <h1 class="text-4xl font-bold tracking-tight text-white drop-shadow-lg sm:text-5xl md:text-6xl">
                {{ t('landing.hero.title') }}
                <span class="text-amber-400">{{ t('landing.hero.title_accent') }}</span>
            </h1>
            <p class="mt-6 text-lg text-stone-200 drop-shadow sm:text-xl">
                {{ t('landing.hero.subtitle') }}
            </p>
            <div class="mt-10 flex flex-wrap items-center justify-center gap-4">
                <a
                    href="#booking"
                    class="inline-flex items-center justify-center rounded-xl bg-amber-500 px-6 py-3 text-base font-semibold text-white shadow-lg transition-colors hover:bg-amber-600 focus:outline-none focus-visible:ring-2 focus-visible:ring-amber-400 focus-visible:ring-offset-2"
                >
                    {{ t('landing.hero.cta_book') }}
                </a>
                <a
                    href="#about"
                    class="inline-flex items-center justify-center rounded-xl border border-white/40 bg-white/10 px-6 py-3 text-base font-semibold text-white backdrop-blur-sm transition-colors hover:bg-white/20"
                >
                    {{ t('landing.hero.cta_about') }}
                </a>
            </div>
        </div>

        <!-- Индикатор прокрутки: ведёт к #booking (первая секция под hero). -->
        <a
            href="#booking"
            class="absolute bottom-8 left-1/2 z-20 flex -translate-x-1/2 cursor-pointer flex-col items-center gap-2 rounded-lg p-1 animate-bounce text-white/50 transition-colors hover:text-white/80 focus:outline-none focus-visible:ring-2 focus-visible:ring-white/50 focus-visible:ring-offset-2 focus-visible:ring-offset-transparent"
            aria-label="scroll"
            @click="scrollToBooking"
        >
            <span class="text-xs">scroll</span>
            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M19 9l-7 7-7-7"
                />
            </svg>
        </a>
    </section>
</template>
