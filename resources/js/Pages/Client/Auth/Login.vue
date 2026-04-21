<script setup>
/**
 * Страница входа клиента в личный кабинет по телефону и паролю.
 */
import LandingLayout from '@/Layouts/LandingLayout.vue';
import PhoneInput from '@/Components/PhoneInput.vue';
import { Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const form = useForm({
    phone: '',
    password: '',
    remember: false,
});
const showPassword = ref(false);

/** Отправляет форму входа клиента. */
function submit() {
    form.post(route('client.login.post'));
}
</script>

<template>
    <LandingLayout>
        <div class="flex min-h-screen items-center justify-center px-4 py-12">
            <div class="w-full max-w-md">
                <div class="rounded-2xl border border-stone-200 bg-white p-8 shadow-xl dark:border-stone-700 dark:bg-stone-900">
                    <div class="mb-8 text-center">
                        <div class="mb-3 text-4xl">🛁</div>
                        <h1 class="text-2xl font-bold text-stone-900 dark:text-white">
                            {{ t('cabinet.login_title') }}
                        </h1>
                        <p class="mt-1 text-sm text-stone-500 dark:text-stone-400">
                            {{ t('cabinet.login_subtitle') }}
                        </p>
                    </div>

                    <form class="space-y-4" @submit.prevent="submit">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-stone-700 dark:text-stone-300">
                                {{ t('cabinet.phone') }} <span class="text-orange-500">*</span>
                            </label>
                            <PhoneInput
                                v-model="form.phone"
                                :placeholder="t('cabinet.phone_digits_placeholder')"
                                :error="form.errors.phone"
                            />
                        </div>

                        <div>
                            <label class="mb-2 block text-sm font-medium text-stone-700 dark:text-stone-300">
                                {{ t('cabinet.password') }} <span class="text-orange-500">*</span>
                            </label>
                            <div class="relative">
                                <input
                                    v-model="form.password"
                                    :type="showPassword ? 'text' : 'password'"
                                    :placeholder="t('cabinet.password_placeholder')"
                                    class="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-3 pr-11 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100"
                                >
                                <button
                                    type="button"
                                    class="absolute inset-y-0 right-0 flex w-10 items-center justify-center text-stone-500 transition-colors hover:text-stone-700 dark:text-stone-400 dark:hover:text-stone-200"
                                    :aria-label="showPassword ? t('cabinet.hide_password') : t('cabinet.show_password')"
                                    @click="showPassword = !showPassword"
                                >
                                    <svg v-if="showPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 3l18 18M10.58 10.58A3 3 0 0014.42 14.42M9.88 5.09A10.94 10.94 0 0112 5c5.05 0 9.27 3.11 10.5 7.5a11.16 11.16 0 01-4.08 5.8M6.23 6.23A11.1 11.1 0 001.5 12.5 10.94 10.94 0 006 17.91" />
                                    </svg>
                                    <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M2.25 12S5.25 5.25 12 5.25 21.75 12 21.75 12 18.75 18.75 12 18.75 2.25 12 2.25 12z" />
                                        <circle cx="12" cy="12" r="3" stroke-width="1.7" />
                                    </svg>
                                </button>
                            </div>
                            <p v-if="form.errors.password" class="mt-1 text-xs text-red-500">
                                {{ form.errors.password }}
                            </p>
                        </div>

                        <label class="flex items-center gap-2 text-sm text-stone-600 dark:text-stone-300">
                            <input v-model="form.remember" type="checkbox" class="rounded border-stone-300 text-amber-600 focus:ring-amber-500">
                            {{ t('cabinet.remember') }}
                        </label>

                        <button
                            type="submit"
                            :disabled="form.processing"
                            class="w-full rounded-xl bg-amber-500 px-4 py-3 text-sm font-semibold text-white transition hover:bg-amber-600 disabled:opacity-50"
                        >
                            {{ form.processing ? '...' : t('cabinet.login_btn') }}
                        </button>
                    </form>

                    <p class="mt-6 text-center text-sm text-stone-500">
                        {{ t('cabinet.no_account') }}
                        <Link :href="route('client.register')" class="font-medium text-amber-600 hover:text-amber-700">
                            {{ t('cabinet.register_link') }}
                        </Link>
                    </p>
                </div>
            </div>
        </div>
    </LandingLayout>
</template>
