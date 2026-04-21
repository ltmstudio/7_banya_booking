<script setup>
/**
 * Секция «О нас» лендинга.
 */
import { useI18n } from 'vue-i18n';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';

const { t } = useI18n();
const page = usePage();

const props = defineProps({
    content: { type: Object, default: () => ({}) },
});

/** Текущая локаль для выбора перевода контента из БД. */
const currentLocale = computed(() => page.props.locale || 'ru');
</script>

<template>
    <section id="about" class="scroll-mt-20 min-h-screen flex flex-col justify-center py-12 md:py-16">
        <div class="rounded-2xl bg-white p-6 shadow-sm ring-1 ring-stone-200/80 dark:bg-stone-800/50 dark:ring-stone-700 md:p-10">
            <h2 class="text-2xl font-bold text-stone-900 dark:text-white md:text-3xl">
                {{ props.content.title?.[currentLocale] || t('landing.about.title') }}
            </h2>
            <p class="mt-4 text-stone-600 dark:text-stone-400 md:mt-6 md:text-lg">
                {{ props.content.text?.[currentLocale] || t('landing.about.text') }}
            </p>
        </div>
    </section>
</template>
