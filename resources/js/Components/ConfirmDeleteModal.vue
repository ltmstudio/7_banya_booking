<script setup>
/**
 * Глобальный модальный диалог подтверждения удаления.
 * Двуязычный (RU/TK) через vue-i18n.
 */
import { computed } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    /** Показывать модал */
    modelValue: { type: Boolean, default: false },
    /** Кастомное сообщение (опционально). Иначе используется common.delete_confirm */
    message: { type: String, default: '' },
    /** Заголовок (опционально) */
    title: { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue', 'confirm', 'cancel']);

const { t } = useI18n();

const visible = computed({
    get: () => props.modelValue,
    set: (v) => emit('update:modelValue', v),
});

const displayMessage = computed(() => props.message || t('common.delete_confirm'));
const displayTitle = computed(() => props.title || t('common.delete_title'));

const close = () => {
    visible.value = false;
    emit('cancel');
};

const onConfirm = () => {
    visible.value = false;
    emit('confirm');
};
</script>

<template>
    <Teleport to="body">
        <Transition
            enter-active-class="transition ease-out duration-200"
            enter-from-class="opacity-0"
            enter-to-class="opacity-100"
            leave-active-class="transition ease-in duration-150"
            leave-from-class="opacity-100"
            leave-to-class="opacity-0"
        >
            <div
                v-if="modelValue"
                class="fixed inset-0 z-50 flex items-center justify-center p-4"
                role="dialog"
                aria-modal="true"
                aria-labelledby="confirm-title"
            >
                <div class="fixed inset-0 bg-black/50" @click="close" />
                <div
                    class="relative z-10 w-full max-w-md rounded-xl bg-white p-6 shadow-xl"
                    role="document"
                >
                    <h3 id="confirm-title" class="text-lg font-semibold text-gray-900">
                        {{ displayTitle }}
                    </h3>
                    <p class="mt-2 text-sm text-gray-600">
                        {{ displayMessage }}
                    </p>
                    <div class="mt-6 flex justify-end gap-3">
                        <button
                            type="button"
                            class="rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                            @click="close"
                        >
                            {{ t('common.cancel') }}
                        </button>
                        <button
                            type="button"
                            class="rounded-lg bg-red-500 px-4 py-2 text-sm font-medium text-white hover:bg-red-600"
                            @click="onConfirm"
                        >
                            {{ t('common.delete') }}
                        </button>
                    </div>
                </div>
            </div>
        </Transition>
    </Teleport>
</template>
