<script setup>
/**
 * Layout для гостевых страниц (login). Всегда светлая тема.
 */
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import { Link } from '@inertiajs/vue3';
import { onMounted } from 'vue';

onMounted(() => {
    document.documentElement.classList.remove('dark');
    localStorage.setItem('theme', 'light');
});
</script>

<template>
    <div
        class="relative flex min-h-screen flex-col items-center bg-stone-100 pt-6 sm:justify-center sm:pt-0"
    >
        <Link href="/" class="flex items-center gap-2 text-xl font-semibold text-stone-800">
            <ApplicationLogo class="h-12 w-12 fill-current text-amber-600" />
            <span class="hidden sm:inline">Банный комплекс</span>
        </Link>

        <div
            class="mt-6 w-full overflow-hidden rounded-2xl bg-white px-6 py-8 shadow-lg ring-1 ring-stone-200/80 sm:max-w-md"
        >
            <slot />
        </div>
    </div>
</template>
