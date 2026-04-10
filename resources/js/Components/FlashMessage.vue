<script setup>
/**
 * Глобальное уведомление об ответе сервера.
 * Показывает flash.success (зелёный) или flash.error (красный).
 * Ключ success переводится через i18n (backend шлёт ключ, напр. settings.saved).
 * Автоскрытие через 5 сек с анимацией.
 */
import { computed, ref, watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const page = usePage();
const { t } = useI18n();
const visible = ref(false);
const timeoutId = ref(null);

const message = computed(() => page.props.flash?.success || page.props.flash?.error);
const isSuccess = computed(() => !!page.props.flash?.success);

function show() {
    if (timeoutId.value) clearTimeout(timeoutId.value);
    visible.value = true;
    timeoutId.value = setTimeout(() => {
        visible.value = false;
        timeoutId.value = null;
    }, 5000);
}

watch(() => [page.props.flash?.success, page.props.flash?.error], ([s, e]) => {
    if (s || e) show();
}, { immediate: true });
</script>

<template>
    <Transition
        enter-active-class="transition ease-out duration-300"
        enter-from-class="opacity-0 translate-y-2"
        enter-to-class="opacity-100 translate-y-0"
        leave-active-class="transition ease-in duration-200"
        leave-from-class="opacity-100 translate-y-0"
        leave-to-class="opacity-0 translate-y-2"
    >
        <div
            v-if="visible && message"
            role="alert"
            :class="[
                'fixed top-4 left-1/2 z-50 -translate-x-1/2 min-w-[280px] max-w-md rounded-lg px-4 py-3 text-sm shadow-lg',
                isSuccess ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200',
            ]"
        >
            {{ isSuccess ? t(message) : message }}
        </div>
    </Transition>
</template>
