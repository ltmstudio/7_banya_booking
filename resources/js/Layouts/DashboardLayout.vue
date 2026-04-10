<script setup>
/**
 * Layout панели администратора с сайдбаром.
 * Desktop: фиксированный sidebar слева.
 * Mobile: hamburger, выдвижной sidebar + overlay.
 */
import { ref, watch } from 'vue';
import { router, usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import AppSidebar from '@/Components/Sidebar/AppSidebar.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import FlashMessage from '@/Components/FlashMessage.vue';
import NotificationBell from '@/Components/Admin/NotificationBell.vue';
import BookingToast from '@/Components/Admin/BookingToast.vue';

const sidebarOpen = ref(false);
const page = usePage();
const { locale, t } = useI18n(); // t используем для подпунктов профиля

/** Переключение языка: vue-i18n, localStorage и сессия на сервере (чтобы page.props.locale и контент обновились). */
const setLocale = (l) => {
    locale.value = l;
    localStorage.setItem('locale', l);
    router.get(route('change.lang', { lang: l }));
};

// Закрывать сайдбар при смене страницы (mobile)
watch(() => page.url, () => {
    sidebarOpen.value = false;
});
</script>

<template>
    <div class="min-h-screen overflow-x-hidden bg-gray-100 lg:flex lg:h-screen">
        <!-- Overlay на mobile при открытом sidebar -->
        <div
            v-show="sidebarOpen"
            class="fixed inset-0 z-30 bg-gray-900/50 lg:hidden"
            aria-hidden="true"
            @click="sidebarOpen = false"
        />

        <AppSidebar :open="sidebarOpen" />
        <FlashMessage />

        <!-- Top bar + контент: flex-1 для заполнения на desktop -->
        <div class="flex min-w-0 flex-1 flex-col lg:pl-0">
            <header class="sticky top-0 z-20 flex h-16 items-center justify-between gap-4 border-b border-gray-200 bg-white px-4 lg:px-8">
                <div class="flex min-w-0 flex-1 items-center gap-4">
                    <div class="flex gap-1 rounded-lg border border-gray-200 bg-white p-1">
                        <button
                            type="button"
                            :class="['rounded-md px-2 py-1 text-sm font-medium', locale === 'ru' ? 'bg-orange-500 text-white' : 'text-gray-600 hover:bg-gray-100']"
                            @click="setLocale('ru')"
                        >
                            RU
                        </button>
                        <button
                            type="button"
                            :class="['rounded-md px-2 py-1 text-sm font-medium', locale === 'tk' ? 'bg-orange-500 text-white' : 'text-gray-600 hover:bg-gray-100']"
                            @click="setLocale('tk')"
                        >
                            TK
                        </button>
                    </div>
                    <button
                        type="button"
                        class="rounded-md p-2 text-gray-500 hover:bg-gray-100 hover:text-gray-700 lg:hidden"
                        aria-label="Открыть меню"
                        @click="sidebarOpen = !sidebarOpen"
                    >
                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        </svg>
                    </button>
                    <slot name="header" />
                </div>
                <div class="flex items-center gap-2">
                    <NotificationBell />
                    <!-- Профиль пользователя (dropdown справа) -->
                    <Dropdown align="right" width="48">
                        <template #trigger>
                            <button
                                type="button"
                                class="flex items-center gap-2 rounded-lg border border-gray-200 bg-white px-3 py-2 text-sm font-medium text-gray-700 shadow-sm hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-1"
                            >
                                <span class="hidden truncate max-w-[8rem] sm:inline">{{ page.props.auth?.user?.name }}</span>
                                <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z" />
                                </svg>
                                <svg class="h-4 w-4 shrink-0 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                                </svg>
                            </button>
                        </template>
                        <template #content>
                            <div class="border-b border-gray-100 px-4 py-2">
                                <p class="truncate text-sm font-medium text-gray-900">{{ page.props.auth?.user?.name }}</p>
                                <p class="truncate text-xs text-gray-500">{{ page.props.auth?.user?.email }}</p>
                            </div>
                            <DropdownLink :href="route('profile.edit')">{{ t('sidebar.profile') }}</DropdownLink>
                            <DropdownLink :href="route('logout')" method="post" as="button">{{ t('sidebar.logout') }}</DropdownLink>
                        </template>
                    </Dropdown>
                </div>
            </header>

            <!-- Page content -->
            <main class="min-h-0 flex-1 overflow-y-auto p-4 lg:p-8">
                <slot />
            </main>
        </div>
    </div>

    <BookingToast />
</template>
