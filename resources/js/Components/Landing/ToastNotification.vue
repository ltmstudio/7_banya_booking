<script setup>
import { ref, watch, onUnmounted } from 'vue';

const props = defineProps({
    show: { type: Boolean, default: false },
    type: { type: String, default: 'success' }, // 'success' | 'error' | 'warning'
    message: { type: String, default: '' },
    duration: { type: Number, default: 4000 },
});
const emit = defineEmits(['close']);

let timer = null;
const progressKey = ref(0);

watch(() => props.show, (val) => {
    if (val) {
        clearTimeout(timer);
        progressKey.value += 1;
        timer = setTimeout(() => emit('close'), props.duration);
    }
});

onUnmounted(() => clearTimeout(timer));
</script>

<template>
    <Transition
        enter-active-class="transition duration-300 ease-out"
        enter-from-class="translate-y-4 opacity-0 scale-95"
        enter-to-class="translate-y-0 opacity-100 scale-100"
        leave-active-class="transition duration-200 ease-in"
        leave-from-class="translate-y-0 opacity-100 scale-100"
        leave-to-class="translate-y-4 opacity-0 scale-95"
    >
        <div
            v-if="show"
            class="fixed bottom-6 left-1/2 -translate-x-1/2 z-[100] flex items-start gap-3 px-5 py-4 rounded-2xl shadow-2xl min-w-[300px] max-w-[90vw] sm:max-w-md"
            :class="{
                'bg-white dark:bg-stone-900 border border-emerald-200 dark:border-emerald-800': type === 'success',
                'bg-white dark:bg-stone-900 border border-red-200 dark:border-red-800': type === 'error',
                'bg-white dark:bg-stone-900 border border-amber-200 dark:border-amber-800': type === 'warning',
            }"
            role="alert"
        >
            <div
                class="flex-shrink-0 flex items-center justify-center w-8 h-8 rounded-full text-sm"
                :class="{
                    'bg-emerald-100 dark:bg-emerald-900/40 text-emerald-600 dark:text-emerald-400': type === 'success',
                    'bg-red-100 dark:bg-red-900/40 text-red-600 dark:text-red-400': type === 'error',
                    'bg-amber-100 dark:bg-amber-900/40 text-amber-600 dark:text-amber-400': type === 'warning',
                }"
            >
                <span v-if="type === 'success'">✓</span>
                <span v-else-if="type === 'error'">✕</span>
                <span v-else>!</span>
            </div>

            <div class="flex-1 min-w-0">
                <p
                    class="text-sm font-medium"
                    :class="{
                        'text-emerald-800 dark:text-emerald-300': type === 'success',
                        'text-red-800 dark:text-red-300': type === 'error',
                        'text-amber-800 dark:text-amber-300': type === 'warning',
                    }"
                >
                    {{ message }}
                </p>
            </div>

            <button
                @click="emit('close')"
                class="flex-shrink-0 text-stone-400 hover:text-stone-600 dark:text-stone-500 dark:hover:text-stone-300 transition-colors ml-1"
            >
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>

            <div
                :key="progressKey"
                class="absolute bottom-0 left-0 h-0.5 rounded-b-2xl"
                :class="{
                    'bg-emerald-400 dark:bg-emerald-600': type === 'success',
                    'bg-red-400 dark:bg-red-600': type === 'error',
                    'bg-amber-400 dark:bg-amber-600': type === 'warning',
                }"
                :style="`animation: shrink ${duration}ms linear forwards`"
            />
        </div>
    </Transition>
</template>

<style scoped>
@keyframes shrink {
    from {
        width: 100%;
    }
    to {
        width: 0%;
    }
}
</style>
