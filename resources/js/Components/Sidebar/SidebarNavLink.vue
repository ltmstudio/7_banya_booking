<script setup>
/**
 * Ссылка пункта навигации в сайдбаре.
 * Подсвечивает активный маршрут.
 */
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    href: { type: String, required: true },
    active: { type: Boolean },
});

const classes = computed(() =>
    props.active
        ? 'flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-indigo-600 bg-indigo-50'
        : 'flex items-center gap-3 rounded-lg px-3 py-2 text-sm font-medium text-gray-600 hover:bg-gray-50 hover:text-gray-900'
);
</script>

<template>
    <Link :href="href" :class="classes">
        <slot />
    </Link>
</template>
