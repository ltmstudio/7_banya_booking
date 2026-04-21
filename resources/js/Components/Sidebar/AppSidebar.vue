<script setup>
/**
 * Боковая панель навигации.
 * Используется в DashboardLayout. На desktop — фиксированный sidebar,
 * на mobile — выдвижной overlay.
 */
import { Link } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import SidebarNavLink from './SidebarNavLink.vue';

defineProps({
    /** Открыт ли сайдбар на мобильных (для overlay) */
    open: { type: Boolean, default: false },
});

const page = usePage();

const { t } = useI18n(); // Краткие ключи переводов для сайдбара

/** Проверяет активный маршрут (поддержка prefix для panel.bookings.* и т.п.) */
const isActive = (nameOrPrefix) => {
    const current = route().current();
    return current === nameOrPrefix || (nameOrPrefix.endsWith('.*') && current?.startsWith(nameOrPrefix.slice(0, -2)));
};
</script>

<template>
    <aside
        :class="[
            'flex shrink-0 flex-col bg-white border-r border-gray-200 transition-transform duration-200 ease-in-out',
            'fixed inset-y-0 left-0 z-40 w-64 lg:relative lg:inset-auto lg:left-auto lg:z-auto lg:h-screen lg:max-h-screen lg:min-h-0 lg:w-64 lg:translate-x-0',
            open ? 'translate-x-0' : '-translate-x-full',
        ]"
    >
        <!-- Logo -->
        <div class="flex h-16 shrink-0 items-center gap-2 border-b border-gray-200 px-4">
            <Link :href="route('panel.index')" class="flex items-center gap-2">
                <ApplicationLogo class="h-8 w-auto fill-current text-indigo-600" />
                <span class="hidden font-semibold text-gray-800 sm:inline">{{ t('sidebar.panel') }}</span>
            </Link>
        </div>

        <!-- Nav -->
        <nav class="flex-1 space-y-1 overflow-y-auto px-3 py-4">
            <SidebarNavLink :href="route('panel.index')" :active="isActive('panel.index')">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                {{ t('sidebar.dashboard') }}
            </SidebarNavLink>
            <SidebarNavLink :href="route('panel.bookings.index')" :active="isActive('panel.bookings.*')">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" />
                </svg>
                {{ t('sidebar.bookings') }}
            </SidebarNavLink>
            <SidebarNavLink :href="route('panel.clients.index')" :active="isActive('panel.clients.*')">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                {{ t('sidebar.clients') }}
            </SidebarNavLink>
            <SidebarNavLink :href="route('panel.rooms.index')" :active="isActive('panel.rooms.*')">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4" />
                </svg>
                {{ t('sidebar.rooms') }}
            </SidebarNavLink>
            <SidebarNavLink :href="route('panel.extra-services.index')" :active="isActive('panel.extra-services.*')">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                </svg>
                {{ t('sidebar.services') }}
            </SidebarNavLink>
            <SidebarNavLink :href="route('panel.users.index')" :active="isActive('panel.users.*')">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
                </svg>
                {{ t('sidebar.users') }}
            </SidebarNavLink>
            <SidebarNavLink :href="route('panel.settings.index')" :active="isActive('panel.settings.*')">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" />
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                </svg>
                {{ t('sidebar.settings') }}
            </SidebarNavLink>
            <SidebarNavLink :href="route('panel.site-content.index')" :active="isActive('panel.site-content.*')">
                <svg class="h-5 w-5 shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m-7 5h8a2 2 0 002-2V5a2 2 0 00-2-2H8a2 2 0 00-2 2v14a2 2 0 002 2z" />
                </svg>
                {{ t('site_content.nav_label') }}
            </SidebarNavLink>
        </nav>

        <!-- User -->
        <div class="border-t border-gray-200 p-4">
            <div class="flex items-center gap-3">
                <div class="min-w-0 flex-1">
                    <p class="truncate text-sm font-medium text-gray-900">{{ page.props.auth?.user?.name }}</p>
                    <p class="truncate text-xs text-gray-500">{{ page.props.auth?.user?.email }}</p>
                </div>
                <Dropdown align="right" width="48" direction="up">
                    <template #trigger>
                        <button
                            type="button"
                            class="rounded-md p-1.5 text-gray-400 hover:bg-gray-100 hover:text-gray-600"
                        >
                            <svg class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 5v.01M12 12v.01M12 19v.01M12 6a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2zm0 7a1 1 0 110-2 1 1 0 010 2z" />
                            </svg>
                        </button>
                    </template>
                    <template #content>
                        <DropdownLink :href="route('profile.edit')">Профиль</DropdownLink>
                        <DropdownLink :href="route('logout')" method="post" as="button">Выход</DropdownLink>
                    </template>
                </Dropdown>
            </div>
        </div>
    </aside>
</template>
