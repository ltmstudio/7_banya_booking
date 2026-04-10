<script setup>
/**
 * Форма создания пользователя. Локализация через vue-i18n (RU/TK).
 */
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const form = useForm({
    name: '',
    email: '',
    password: '',
    password_confirmation: '',
    role: 'cashier',
});

const showPassword = ref(false);
const showPasswordConfirm = ref(false);

const roleOptions = [
    { value: 'manager', label: 'role_manager' },
    { value: 'cashier', label: 'role_cashier' },
];
</script>

<template>
    <DashboardLayout>
        <Head :title="t('user.create')" />
        <template #header>
            <h1 class="text-xl font-semibold text-gray-800">{{ t('user.page_title_new') }}</h1>
        </template>

        <div class="max-w-md mx-auto mt-10 px-4 pb-12">
            <form
                class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8"
                @submit.prevent="form.post(route('panel.users.store'))"
            >
                <div class="mb-5">
                    <label for="name" class="mb-2 block text-sm font-medium text-gray-700">{{ t('user.name') }} <span class="text-orange-500">*</span></label>
                    <div class="flex rounded-lg border border-gray-200 bg-white">
                        <span class="flex items-center justify-center pl-3 text-gray-400">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <path d="M19 21v-2a4 4 0 0 0-4-4H9a4 4 0 0 0-4 4v2" />
                                <circle cx="12" cy="7" r="4" />
                            </svg>
                        </span>
                        <input
                            id="name"
                            v-model="form.name"
                            type="text"
                            required
                            autocomplete="name"
                            :placeholder="t('user.name_placeholder')"
                            class="min-w-0 flex-1 rounded-lg border-0 bg-transparent py-2.5 pr-4 pl-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-orange-400"
                        />
                    </div>
                    <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</p>
                </div>

                <div class="mb-5">
                    <label for="email" class="mb-2 block text-sm font-medium text-gray-700">{{ t('user.email') }} <span class="text-orange-500">*</span></label>
                    <div class="flex rounded-lg border border-gray-200 bg-white">
                        <span class="flex items-center justify-center pl-3 text-gray-400">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="20" height="16" x="2" y="4" rx="2" />
                                <path d="m22 7-8.97 5.7a1.94 1.94 0 0 1-2.06 0L2 7" />
                            </svg>
                        </span>
                        <input
                            id="email"
                            v-model="form.email"
                            type="email"
                            required
                            autocomplete="username"
                            :placeholder="t('user.email_placeholder')"
                            class="min-w-0 flex-1 rounded-lg border-0 bg-transparent py-2.5 pr-4 pl-2 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-orange-400"
                        />
                    </div>
                    <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
                </div>

                <div class="mb-5">
                    <label for="password" class="mb-2 block text-sm font-medium text-gray-700">{{ t('user.password') }} <span class="text-orange-500">*</span></label>
                    <div class="flex rounded-lg border border-gray-200 bg-white">
                        <span class="flex items-center justify-center pl-3 text-gray-400">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                        </span>
                        <input
                            id="password"
                            v-model="form.password"
                            :type="showPassword ? 'text' : 'password'"
                            required
                            autocomplete="new-password"
                            :placeholder="t('user.password_placeholder')"
                            class="min-w-0 flex-1 rounded-lg border-0 bg-transparent py-2.5 pl-2 pr-10 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-orange-400"
                        />
                        <button
                            type="button"
                            class="flex items-center justify-center pr-3 text-gray-400 hover:text-gray-600"
                            @click="showPassword = !showPassword"
                        >
                            <svg v-if="showPassword" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M10.733 5.076a10.744 10.744 0 0 1 11.205 6.575 1 1 0 0 1 0 .698 10.747 10.747 0 0 1-1.444 2.49" />
                                <path d="M14.084 14.158a3 3 0 0 1-4.242-4.242" />
                                <path d="M17.479 17.499a10.75 10.75 0 0 1-15.417-5.151 1 1 0 0 1 0-.698 10.75 10.75 0 0 1 4.446-5.143" />
                                <path d="m2 2 20 20" />
                            </svg>
                            <svg v-else class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </button>
                    </div>
                    <p v-if="form.errors.password" class="mt-1 text-xs text-red-500">{{ form.errors.password }}</p>
                </div>

                <div class="mb-5">
                    <label for="password_confirmation" class="mb-2 block text-sm font-medium text-gray-700">{{ t('user.password_confirmation') }} <span class="text-orange-500">*</span></label>
                    <div class="flex rounded-lg border border-gray-200 bg-white">
                        <span class="flex items-center justify-center pl-3 text-gray-400">
                            <svg class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                <rect width="18" height="11" x="3" y="11" rx="2" ry="2" />
                                <path d="M7 11V7a5 5 0 0 1 10 0v4" />
                            </svg>
                        </span>
                        <input
                            id="password_confirmation"
                            v-model="form.password_confirmation"
                            :type="showPasswordConfirm ? 'text' : 'password'"
                            required
                            autocomplete="new-password"
                            class="min-w-0 flex-1 rounded-lg border-0 bg-transparent py-2.5 pl-2 pr-10 text-sm text-gray-900 focus:outline-none focus:ring-2 focus:ring-orange-400"
                        />
                        <button
                            type="button"
                            class="flex items-center justify-center pr-3 text-gray-400 hover:text-gray-600"
                            @click="showPasswordConfirm = !showPasswordConfirm"
                        >
                            <svg v-if="showPasswordConfirm" class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M10.733 5.076a10.744 10.744 0 0 1 11.205 6.575 1 1 0 0 1 0 .698 10.747 10.747 0 0 1-1.444 2.49" />
                                <path d="M14.084 14.158a3 3 0 0 1-4.242-4.242" />
                                <path d="M17.479 17.499a10.75 10.75 0 0 1-15.417-5.151 1 1 0 0 1 0-.698 10.75 10.75 0 0 1 4.446-5.143" />
                                <path d="m2 2 20 20" />
                            </svg>
                            <svg v-else class="h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                                <path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z" />
                                <circle cx="12" cy="12" r="3" />
                            </svg>
                        </button>
                    </div>
                    <p v-if="form.errors.password_confirmation" class="mt-1 text-xs text-red-500">{{ form.errors.password_confirmation }}</p>
                </div>

                <div class="mb-5">
                    <label for="role" class="mb-2 block text-sm font-medium text-gray-700">{{ t('user.role') }} <span class="text-orange-500">*</span></label>
                    <select
                        id="role"
                        v-model="form.role"
                        required
                        class="w-full rounded-lg border border-gray-200 bg-white py-2.5 px-4 text-sm text-gray-900 focus:border-transparent focus:outline-none focus:ring-2 focus:ring-orange-400"
                    >
                        <option v-for="opt in roleOptions" :key="opt.value" :value="opt.value">
                            {{ t('user.' + opt.label) }}
                        </option>
                    </select>
                    <p v-if="form.errors.role" class="mt-1 text-xs text-red-500">{{ form.errors.role }}</p>
                </div>

                <div class="mt-8 flex flex-wrap items-center gap-4 border-t border-gray-100 pt-8">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="bg-orange-500 hover:bg-orange-600 disabled:opacity-50 text-white font-medium px-6 py-2.5 rounded-lg text-sm transition"
                    >
                        {{ form.processing ? t('common.saving') : t('user.save') }}
                    </button>
                    <Link
                        :href="route('panel.users.index')"
                        class="text-sm text-gray-500 hover:text-gray-700"
                    >
                        {{ t('user.cancel') }}
                    </Link>
                </div>
            </form>
        </div>
    </DashboardLayout>
</template>
