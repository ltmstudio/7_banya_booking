<script setup>
/**
 * Маска телефона: префикс +993, 8 цифр.
 * v-model — полный номер (+993XXXXXXXX).
 */
import { computed } from 'vue';

const props = defineProps({
    modelValue: { type: String, default: '' },
    error: { type: String, default: '' },
});

const emit = defineEmits(['update:modelValue']);

const digits = computed({
    get: () => {
        const p = props.modelValue || '';
        if (p.startsWith('+993')) return p.slice(4).replace(/\D/g, '').slice(0, 8);
        return p.replace(/\D/g, '').slice(-8);
    },
    set: (v) => {
        const d = String(v).replace(/\D/g, '').slice(0, 8);
        emit('update:modelValue', d ? `+993${d}` : '');
    },
});
</script>

<template>
    <div class="flex overflow-hidden rounded-xl border border-stone-200 bg-stone-50 dark:border-stone-700 dark:bg-stone-800 focus-within:border-amber-500 focus-within:ring-2 focus-within:ring-amber-500/20">
        <span class="flex items-center border-r border-stone-200 bg-stone-100 px-3 py-2.5 text-sm text-stone-500 dark:border-stone-600 dark:bg-stone-700 dark:text-stone-400">+993</span>
        <input
            v-model="digits"
            type="tel"
            inputmode="numeric"
            maxlength="8"
            autocomplete="tel"
            placeholder="61234567"
            class="block w-full border-0 bg-transparent px-3 py-2.5 text-sm text-stone-900 placeholder-stone-400 focus:ring-0 dark:text-stone-100 dark:placeholder-stone-500"
        />
    </div>
    <p v-if="error" class="mt-1 text-xs text-red-500">{{ error }}</p>
</template>
