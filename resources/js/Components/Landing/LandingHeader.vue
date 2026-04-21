<script setup>
/**
 * Шапка лендинга: лого, навигация, переключатель TK/RU, переключатель темы.
 */
import { Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref, onMounted, onBeforeUnmount } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
const isDark = ref(false);
const homeUrl = route('home');
const page = usePage();
const isHomePage = computed(() => page.url === '/' || page.url.startsWith('/?'));
const isOverHero = ref(true);
const headerRef = ref(null);

/**
 * Возвращает ссылку на секцию главной страницы, чтобы меню работало и вне лендинга.
 */
function homeAnchor(hash) {
    return `${homeUrl}${hash}`;
}

/**
 * Плавно скроллит к указанной секции главной страницы.
 */
function smoothScrollToSection(hash) {
    const id = hash.replace('#', '');
    const target = document.getElementById(id);
    if (target) {
        target.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }
}

/**
 * Обрабатывает переход по пунктам меню с плавной прокруткой.
 */
function handleLandingNavClick(event, hash) {
    event.preventDefault();

    if (isHomePage.value) {
        smoothScrollToSection(hash);
        return;
    }

    sessionStorage.setItem('landing_anchor', hash);
    router.visit(homeUrl);
}

function toggleTheme() {
    isDark.value = !isDark.value;
    document.documentElement.classList.toggle('dark', isDark.value);
    localStorage.setItem('theme', isDark.value ? 'dark' : 'light');
}

/**
 * Обновляет стиль шапки: прозрачная над hero, обычная после hero.
 */
function updateHeaderState() {
    const heroSection = document.getElementById('hero');
    if (!heroSection) {
        isOverHero.value = false;
        return;
    }

    const heroRect = heroSection.getBoundingClientRect();
    const headerHeight = headerRef.value?.offsetHeight ?? 0;
    // Меняем фон только когда хедер полностью вышел из hero-видео секции.
    isOverHero.value = heroRect.bottom > headerHeight;
}

/**
 * Базовый стиль пунктов меню: белый над видео, обычный после hero.
 */
const navLinkClass = computed(() => (
    isOverHero.value
        ? 'rounded-lg px-2 py-1.5 text-xs font-medium text-white/90 hover:bg-white/15 hover:text-white transition-colors sm:px-3 sm:py-2 sm:text-sm'
        : 'rounded-lg px-2 py-1.5 text-xs font-medium text-stone-600 hover:bg-stone-200/80 hover:text-stone-900 dark:text-stone-400 dark:hover:bg-stone-700 dark:hover:text-stone-100 transition-colors sm:px-3 sm:py-2 sm:text-sm'
));

onMounted(() => {
    isDark.value = document.documentElement.classList.contains('dark');
    updateHeaderState();
    window.addEventListener('scroll', updateHeaderState, { passive: true });
    window.addEventListener('resize', updateHeaderState, { passive: true });

    const pendingAnchor = sessionStorage.getItem('landing_anchor') || window.location.hash;
    if (pendingAnchor) {
        if (sessionStorage.getItem('landing_anchor')) {
            sessionStorage.removeItem('landing_anchor');
        }

        // Даем странице отрисовать секции перед скроллом.
        requestAnimationFrame(() => {
            smoothScrollToSection(pendingAnchor);
        });
    }
});

onBeforeUnmount(() => {
    window.removeEventListener('scroll', updateHeaderState);
    window.removeEventListener('resize', updateHeaderState);
});
</script>

<template>
    <header
        ref="headerRef"
        :class="[
            'fixed left-1/2 top-0 z-50 flex w-full -translate-x-1/2 flex-wrap items-start justify-between gap-3 px-4 py-4 sm:items-center sm:gap-4 sm:px-6 md:py-5 lg:px-8',
            isOverHero
                ? 'border-b border-transparent bg-transparent'
                : 'border-b border-stone-200/60 bg-stone-50/95 backdrop-blur-md dark:border-stone-700/60 dark:bg-stone-900/95',
        ]"
    >
        <Link
            href="/"
            :class="[
                'flex items-center gap-2 text-xl font-semibold transition-colors',
                isOverHero
                    ? 'text-white hover:text-amber-300'
                    : 'text-stone-800 hover:text-amber-700 dark:text-stone-100 dark:hover:text-amber-400',
            ]"
        >
            <span class="flex size-10 items-center justify-center rounded-lg bg-amber-100 dark:bg-amber-900/40 text-amber-700 dark:text-amber-400">
                <svg class="size-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M17.657 18.657A8 8 0 0 1 6.343 7.343S7 9 9 10c0-2 .5-5 2.986-7C14 5 16.09 5.777 17.656 7.343A7.975 7.975 0 0 1 20 13a7.975 7.975 0 0 1-2.343 5.657z" />
                </svg>
            </span>
            <span>{{ t('landing.header.brand') }}</span>
        </Link>

        <!-- На узких экранах: тема и язык справа от логотипа. -->
        <div class="ml-auto flex items-center gap-2 sm:hidden">
            <button
                type="button"
                @click="toggleTheme"
                class="flex size-8 items-center justify-center rounded-lg border border-stone-300 bg-white text-stone-600 transition-colors hover:bg-stone-100 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-400 dark:hover:bg-stone-700"
                :title="isDark ? t('landing.header.theme_light') : t('landing.header.theme_dark')"
            >
                <svg v-if="isDark" class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                <svg v-else class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                </svg>
            </button>
            <div class="flex overflow-hidden rounded-lg border border-stone-300 dark:border-stone-600">
                <Link
                    :href="route('change.lang', { lang: 'tk' })"
                    preserve-scroll
                    :class="[
                        'block px-2 py-1.5 text-xs font-medium transition-colors',
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
                        'block border-l border-stone-300 px-2 py-1.5 text-xs font-medium transition-colors dark:border-stone-600',
                        $page.props.locale === 'ru'
                            ? 'bg-amber-600 text-white'
                            : 'bg-white text-stone-600 hover:bg-stone-100 dark:bg-stone-800 dark:text-stone-400 dark:hover:bg-stone-700'
                    ]"
                >
                    RU
                </Link>
            </div>
        </div>

        <!-- Навигация с горизонтальным скроллом на телефонах. -->
        <nav class="flex w-full flex-wrap items-center justify-center gap-1 pb-1 sm:w-auto sm:justify-start sm:gap-2 sm:pb-0">
            <a
                :href="homeAnchor('#hero')"
                @click="handleLandingNavClick($event, '#hero')"
                :class="navLinkClass"
            >
                {{ t('landing.header.nav_home') }}
            </a>
            <a
                :href="homeAnchor('#about')"
                @click="handleLandingNavClick($event, '#about')"
                :class="navLinkClass"
            >
                {{ t('landing.header.nav_about') }}
            </a>
            <a
                :href="homeAnchor('#services')"
                @click="handleLandingNavClick($event, '#services')"
                :class="navLinkClass"
            >
                {{ t('landing.header.nav_services') }}
            </a>
            <a
                :href="homeAnchor('#gallery')"
                @click="handleLandingNavClick($event, '#gallery')"
                :class="navLinkClass"
            >
                {{ t('landing.header.nav_gallery') }}
            </a>
            <Link
                v-if="!$page.props.clientAuth"
                :href="route('client.login')"
                class="hidden rounded-lg px-3 py-2 text-sm font-medium text-stone-600 hover:bg-stone-200/80 hover:text-stone-900 transition-colors dark:text-stone-400 dark:hover:bg-stone-700 dark:hover:text-stone-100"
            >
                👤 {{ t('landing.header.cabinet') }}
            </Link>
            <Link
                v-else
                :href="route('client.cabinet')"
                class="rounded-lg px-3 py-2 text-sm font-medium text-amber-600 transition-colors hover:bg-amber-50 dark:hover:bg-amber-900/20"
            >
                👤 {{ $page.props.clientAuth.first_name }}
            </Link>
            <div class="ml-2 hidden items-center gap-2 sm:flex">
                <button
                    type="button"
                    @click="toggleTheme"
                    class="flex size-9 items-center justify-center rounded-lg border border-stone-300 bg-white text-stone-600 transition-colors hover:bg-stone-100 dark:border-stone-600 dark:bg-stone-800 dark:text-stone-400 dark:hover:bg-stone-700"
                    :title="isDark ? t('landing.header.theme_light') : t('landing.header.theme_dark')"
                >
                    <svg v-if="isDark" class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z" />
                    </svg>
                    <svg v-else class="size-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z" />
                    </svg>
                </button>
                <div class="flex overflow-hidden rounded-lg border border-stone-300 dark:border-stone-600">
                    <Link
                        :href="route('change.lang', { lang: 'tk' })"
                        preserve-scroll
                        :class="[
                            'block px-3 py-2 text-sm font-medium transition-colors',
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
                            'block border-l border-stone-300 px-3 py-2 text-sm font-medium transition-colors dark:border-stone-600',
                            $page.props.locale === 'ru'
                                ? 'bg-amber-600 text-white'
                                : 'bg-white text-stone-600 hover:bg-stone-100 dark:bg-stone-800 dark:text-stone-400 dark:hover:bg-stone-700'
                        ]"
                    >
                        RU
                    </Link>
                </div>
            </div>
        </nav>
    </header>
</template>
