<script setup>
/**
 * Модальное окно промо-акции на лендинге.
 * Фиксированный оверлей, заголовок и текст по локали, кнопка закрытия.
 */
defineProps({
    promo: { type: Object, default: null },
    locale: { type: String, default: 'ru' },
});
const emit = defineEmits(['close']);
</script>

<template>
    <div
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 p-4"
        @click.self="emit('close')"
    >
        <div
            class="max-w-md rounded-2xl bg-white p-8 shadow-xl dark:bg-stone-800"
            role="dialog"
            aria-modal="true"
        >
            <h3 class="text-xl font-semibold text-stone-900 dark:text-stone-100">
                {{ promo?.title?.[locale] ?? '' }}
            </h3>
            <p class="mt-3 text-stone-600 dark:text-stone-300">
                {{ promo?.body?.[locale] ?? '' }}
            </p>
            <button
                type="button"
                class="mt-6 w-full rounded-xl bg-amber-600 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500"
                @click="emit('close')"
            >
                Закрыть
            </button>
        </div>
    </div>
</template>
