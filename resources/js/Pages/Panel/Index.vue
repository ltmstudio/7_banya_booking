<script setup>
/**
 * Страница панели управления (/panel).
 * Использует DashboardLayout. Требует авторизацию.
 */
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <DashboardLayout>
        <Head title="Панель управления" />
        <template #header>
            <h1 class="text-xl font-semibold text-gray-800">Панель управления</h1>
        </template>
        <p class="text-gray-600">Контент панели — доработаем позже.</p>
    </DashboardLayout>
</template>
