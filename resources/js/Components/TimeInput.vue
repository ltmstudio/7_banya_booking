<script setup>
/**
 * Универсальный input времени (HH:MM) для повторного использования в формах.
 */
const props = defineProps({
    modelValue: { type: String, default: '' },
    required: { type: Boolean, default: false },
    min: { type: String, default: '06:00' },
    max: { type: String, default: '22:00' },
    step: { type: Number, default: 60 },
});

const emit = defineEmits(['update:modelValue']);
</script>

<template>
    <input
        type="time"
        :value="modelValue"
        :required="required"
        :min="min"
        :max="max"
        :step="step"
        lang="en-GB"
        class="w-full rounded-lg border border-gray-200 bg-stone-50 px-4 py-2.5 text-sm text-stone-900 focus:border-orange-300 focus:outline-none focus:ring-2 focus:ring-orange-400/20 dark:border-stone-700 dark:bg-stone-900 dark:text-stone-100"
        @input="emit('update:modelValue', $event.target.value)"
    />
</template>
