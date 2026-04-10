<script setup>
/**
 * Страница входа. Dark mode, без «Забыли пароль».
 */
import Checkbox from '@/Components/Checkbox.vue';
import GuestLayout from '@/Layouts/GuestLayout.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

defineProps({
    status: { type: String },
});

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

/** Переключение видимости пароля (показать/скрыть). */
const passwordVisible = ref(false);

const submit = () => {
    form.post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <GuestLayout>
        <Head title="Вход" />

        <div v-if="status" class="mb-4 rounded-lg bg-green-100 p-3 text-sm font-medium text-green-700 dark:bg-green-900/30 dark:text-green-400">
            {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-5">
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput
                    id="email"
                    type="email"
                    class="mt-1 block w-full"
                    v-model="form.email"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="Email"
                />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <div>
                <InputLabel for="password" value="Пароль" />
                <div class="relative mt-1">
                    <input
                        id="password"
                        v-model="form.password"
                        :type="passwordVisible ? 'text' : 'password'"
                        required
                        autocomplete="current-password"
                        placeholder="Пароль"
                        class="block w-full rounded-lg border-stone-300 bg-white py-2.5 pl-4 pr-11 shadow-sm transition-colors focus:border-amber-500 focus:ring-amber-500 dark:border-stone-600 dark:bg-stone-700 dark:text-stone-100 dark:focus:border-amber-400 dark:focus:ring-amber-400"
                    />
                    <button
                        type="button"
                        :aria-label="passwordVisible ? 'Скрыть пароль' : 'Показать пароль'"
                        class="absolute right-2.5 top-1/2 -translate-y-1/2 rounded p-1.5 text-stone-400 hover:bg-stone-100 hover:text-stone-600 dark:hover:bg-stone-600 dark:hover:text-stone-300"
                        @click="passwordVisible = !passwordVisible"
                    >
                        <!-- Eye (show password) -->
                        <svg v-if="!passwordVisible" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" />
                        </svg>
                        <!-- Eye off (hide password) -->
                        <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.875 18.825A10.05 10.05 0 0112 19c-4.478 0-8.268-2.943-9.543-7a9.97 9.97 0 011.563-3.029m5.858.908a3 3 0 114.243 4.243M9.878 9.878l4.242 4.242M9.88 9.88l-3.29-3.29m7.532 7.532l3.29 3.29M3 3l3.59 3.59m0 0A9.953 9.953 0 0112 5c4.478 0 8.268 2.943 9.543 7a10.025 10.025 0 01-4.132 5.411m0 0L21 21" />
                        </svg>
                    </button>
                </div>
                <InputError class="mt-2" :message="form.errors.password" />
            </div>

            <label class="flex cursor-pointer items-center gap-2">
                <Checkbox name="remember" v-model:checked="form.remember" />
                <span class="text-sm text-stone-600 dark:text-stone-400">Запомнить меня</span>
            </label>

            <PrimaryButton
                type="submit"
                class="w-full"
                :class="{ 'opacity-25': form.processing }"
                :disabled="form.processing"
            >
                Войти
            </PrimaryButton>
        </form>
    </GuestLayout>
</template>
