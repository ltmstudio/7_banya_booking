<script setup>
/**
 * Главная страница личного кабинета клиента: брони, профиль и пароль.
 */
import LandingLayout from '@/Layouts/LandingLayout.vue';
import BirthdayInput from '@/Components/BirthdayInput.vue';
import { router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    client: { type: Object, required: true },
    bookings: { type: Object, required: true },
    stats: { type: Object, required: true },
});

const activeTab = ref('bookings');
const showCurrentPassword = ref(false);
const showNewPassword = ref(false);
const showConfirmPassword = ref(false);

const profileForm = useForm({
    first_name: props.client.first_name ?? '',
    last_name: props.client.last_name ?? '',
    birthday: props.client.birthday ?? '',
});

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const statusColors = {
    new: 'bg-blue-100 text-blue-700',
    preliminary: 'bg-yellow-100 text-yellow-700',
    confirmed: 'bg-green-100 text-green-700',
    paid: 'bg-emerald-100 text-emerald-700',
    completed: 'bg-gray-100 text-gray-600',
    cancelled: 'bg-red-100 text-red-700',
};

/** Перенаправляет к форме бронирования на лендинге. */
function repeatBooking() {
    router.visit('/#booking');
}

/** Выполняет выход клиента из кабинета. */
function logout() {
    router.post(route('client.logout'));
}
</script>

<template>
    <LandingLayout>
        <div class="mx-auto max-w-4xl px-4 py-10">
            <div class="mb-8 flex items-start justify-between">
                <div>
                    <h1 class="flex items-center gap-2 text-2xl font-bold text-stone-900 dark:text-white">
                        {{ client.full_name }}
                        <span v-if="client.is_birthday_today">🎂</span>
                    </h1>
                    <p class="mt-1 text-sm text-stone-500 dark:text-stone-400">
                        {{ client.phone }}
                    </p>
                </div>
                <button
                    class="text-sm text-stone-500 transition hover:text-red-600"
                    @click="logout"
                >
                    {{ t('cabinet.logout') }}
                </button>
            </div>

            <div class="mb-8 grid grid-cols-1 gap-4 md:grid-cols-3">
                <div class="rounded-2xl border border-stone-200 bg-white p-4 text-center dark:border-stone-700 dark:bg-stone-900">
                    <p class="text-2xl font-bold text-stone-900 dark:text-white">{{ stats.total }}</p>
                    <p class="mt-1 text-xs text-stone-500">{{ t('cabinet.stats_total') }}</p>
                </div>
                <div class="rounded-2xl border border-stone-200 bg-white p-4 text-center dark:border-stone-700 dark:bg-stone-900">
                    <p class="text-2xl font-bold text-emerald-600">{{ stats.completed }}</p>
                    <p class="mt-1 text-xs text-stone-500">{{ t('cabinet.stats_completed') }}</p>
                </div>
                <div class="rounded-2xl border border-stone-200 bg-white p-4 text-center dark:border-stone-700 dark:bg-stone-900">
                    <p class="text-2xl font-bold text-amber-600">{{ stats.upcoming }}</p>
                    <p class="mt-1 text-xs text-stone-500">{{ t('cabinet.stats_upcoming') }}</p>
                </div>
            </div>

            <div class="mb-6 flex w-fit gap-1 rounded-xl bg-stone-100 p-1 dark:bg-stone-800">
                <button
                    v-for="tab in ['bookings', 'profile', 'password']"
                    :key="tab"
                    :class="[
                        'rounded-lg px-4 py-2 text-sm font-medium transition',
                        activeTab === tab
                            ? 'bg-white text-stone-900 shadow-sm dark:bg-stone-900 dark:text-white'
                            : 'text-stone-500 hover:text-stone-700 dark:hover:text-stone-300',
                    ]"
                    @click="activeTab = tab"
                >
                    {{ t(`cabinet.tab_${tab}`) }}
                </button>
            </div>

            <div v-show="activeTab === 'bookings'">
                <div v-if="!bookings.data.length" class="py-16 text-center text-stone-400">
                    {{ t('cabinet.no_bookings') }}
                </div>

                <div v-else class="space-y-4">
                    <div
                        v-for="booking in bookings.data"
                        :key="booking.id"
                        class="rounded-2xl border border-stone-200 bg-white p-5 dark:border-stone-700 dark:bg-stone-900"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex items-start gap-3">
                                <img
                                    v-if="booking.room?.photos?.[0]?.url"
                                    :src="booking.room.photos[0].url"
                                    class="h-16 w-16 flex-shrink-0 rounded-xl object-cover"
                                >
                                <div
                                    v-else
                                    class="flex h-16 w-16 flex-shrink-0 items-center justify-center rounded-xl bg-stone-100 text-2xl dark:bg-stone-800"
                                >
                                    🛁
                                </div>
                                <div>
                                    <p class="font-semibold text-stone-900 dark:text-white">{{ booking.room?.name?.ru ?? booking.room?.name }}</p>
                                    <p class="text-sm text-stone-500 dark:text-stone-400">📅 {{ booking.booking_date }} · {{ booking.start_time }} — {{ booking.end_time }}</p>
                                    <p class="text-sm text-stone-500 dark:text-stone-400">⏱ {{ booking.duration_hours }} ч · 💰 {{ booking.final_amount }} TMT</p>
                                </div>
                            </div>

                            <div class="flex flex-col items-end gap-2">
                                <span class="rounded-full px-2.5 py-1 text-xs font-medium" :class="statusColors[booking.status]">
                                    {{ t(`bookings.statuses.${booking.status}`) }}
                                </span>
                                <button
                                    v-if="booking.status === 'completed'"
                                    class="text-xs font-medium text-amber-600 transition hover:text-amber-700"
                                    @click="repeatBooking"
                                >
                                    🔄 {{ t('cabinet.repeat_booking') }}
                                </button>
                            </div>
                        </div>

                        <div
                            v-if="booking.extra_services?.length"
                            class="mt-3 flex flex-wrap gap-2 border-t border-stone-100 pt-3 dark:border-stone-800"
                        >
                            <span
                                v-for="service in booking.extra_services"
                                :key="service.id"
                                class="rounded-full bg-stone-100 px-2 py-0.5 text-xs text-stone-600 dark:bg-stone-800 dark:text-stone-400"
                            >
                                {{ service.name }}
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <div v-show="activeTab === 'profile'">
                <div class="rounded-2xl border border-stone-200 bg-white p-6 dark:border-stone-700 dark:bg-stone-900">
                    <h2 class="mb-5 font-semibold text-stone-900 dark:text-white">{{ t('cabinet.profile_title') }}</h2>
                    <form class="space-y-4" @submit.prevent="profileForm.put(route('client.profile.update'))">
                        <div class="grid grid-cols-1 gap-4 md:grid-cols-2">
                            <div>
                                <label class="mb-2 block text-sm font-medium text-stone-700 dark:text-stone-300">{{ t('cabinet.first_name') }} <span class="text-orange-500">*</span></label>
                                <input v-model="profileForm.first_name" type="text" :placeholder="t('cabinet.first_name_placeholder')" class="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-2.5 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100">
                            </div>
                            <div>
                                <label class="mb-2 block text-sm font-medium text-stone-700 dark:text-stone-300">{{ t('cabinet.last_name') }}</label>
                                <input v-model="profileForm.last_name" type="text" :placeholder="t('cabinet.last_name_placeholder')" class="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-2.5 text-sm focus:border-amber-500 focus:outline-none focus:ring-2 focus:ring-amber-500/20 dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100">
                            </div>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-stone-700 dark:text-stone-300">{{ t('cabinet.birthday') }}</label>
                            <BirthdayInput v-model="profileForm.birthday" :error="profileForm.errors.birthday" />
                        </div>
                        <button type="submit" :disabled="profileForm.processing" class="rounded-xl bg-amber-500 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-amber-600 disabled:opacity-50">
                            {{ t('cabinet.save') }}
                        </button>
                    </form>
                </div>
            </div>

            <div v-show="activeTab === 'password'">
                <div class="rounded-2xl border border-stone-200 bg-white p-6 dark:border-stone-700 dark:bg-stone-900">
                    <h2 class="mb-5 font-semibold text-stone-900 dark:text-white">{{ t('cabinet.password_title') }}</h2>
                    <form class="space-y-4" @submit.prevent="passwordForm.put(route('client.password.update'))">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-stone-700 dark:text-stone-300">{{ t('cabinet.current_password') }}</label>
                            <div class="relative">
                                <input v-model="passwordForm.current_password" :type="showCurrentPassword ? 'text' : 'password'" :placeholder="t('cabinet.current_password_placeholder')" class="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-2.5 pr-11 text-sm focus:border-amber-500 focus:outline-none dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100">
                                <button
                                    type="button"
                                    class="absolute inset-y-0 right-0 flex w-10 items-center justify-center text-stone-500 transition-colors hover:text-stone-700 dark:text-stone-400 dark:hover:text-stone-200"
                                    :aria-label="showCurrentPassword ? t('cabinet.hide_password') : t('cabinet.show_password')"
                                    @click="showCurrentPassword = !showCurrentPassword"
                                >
                                    <svg v-if="showCurrentPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 3l18 18M10.58 10.58A3 3 0 0014.42 14.42M9.88 5.09A10.94 10.94 0 0112 5c5.05 0 9.27 3.11 10.5 7.5a11.16 11.16 0 01-4.08 5.8M6.23 6.23A11.1 11.1 0 001.5 12.5 10.94 10.94 0 006 17.91" />
                                    </svg>
                                    <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M2.25 12S5.25 5.25 12 5.25 21.75 12 21.75 12 18.75 18.75 12 18.75 2.25 12 2.25 12z" />
                                        <circle cx="12" cy="12" r="3" stroke-width="1.7" />
                                    </svg>
                                </button>
                            </div>
                            <p v-if="passwordForm.errors.current_password" class="mt-1 text-xs text-red-500">{{ passwordForm.errors.current_password }}</p>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-stone-700 dark:text-stone-300">{{ t('cabinet.new_password') }}</label>
                            <div class="relative">
                                <input v-model="passwordForm.password" :type="showNewPassword ? 'text' : 'password'" :placeholder="t('cabinet.new_password_placeholder')" class="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-2.5 pr-11 text-sm focus:border-amber-500 focus:outline-none dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100">
                                <button
                                    type="button"
                                    class="absolute inset-y-0 right-0 flex w-10 items-center justify-center text-stone-500 transition-colors hover:text-stone-700 dark:text-stone-400 dark:hover:text-stone-200"
                                    :aria-label="showNewPassword ? t('cabinet.hide_password') : t('cabinet.show_password')"
                                    @click="showNewPassword = !showNewPassword"
                                >
                                    <svg v-if="showNewPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 3l18 18M10.58 10.58A3 3 0 0014.42 14.42M9.88 5.09A10.94 10.94 0 0112 5c5.05 0 9.27 3.11 10.5 7.5a11.16 11.16 0 01-4.08 5.8M6.23 6.23A11.1 11.1 0 001.5 12.5 10.94 10.94 0 006 17.91" />
                                    </svg>
                                    <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M2.25 12S5.25 5.25 12 5.25 21.75 12 21.75 12 18.75 18.75 12 18.75 2.25 12 2.25 12z" />
                                        <circle cx="12" cy="12" r="3" stroke-width="1.7" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-stone-700 dark:text-stone-300">{{ t('cabinet.confirm_password') }}</label>
                            <div class="relative">
                                <input v-model="passwordForm.password_confirmation" :type="showConfirmPassword ? 'text' : 'password'" :placeholder="t('cabinet.confirm_password_placeholder')" class="w-full rounded-xl border border-stone-200 bg-stone-50 px-4 py-2.5 pr-11 text-sm focus:border-amber-500 focus:outline-none dark:border-stone-700 dark:bg-stone-800 dark:text-stone-100">
                                <button
                                    type="button"
                                    class="absolute inset-y-0 right-0 flex w-10 items-center justify-center text-stone-500 transition-colors hover:text-stone-700 dark:text-stone-400 dark:hover:text-stone-200"
                                    :aria-label="showConfirmPassword ? t('cabinet.hide_password') : t('cabinet.show_password')"
                                    @click="showConfirmPassword = !showConfirmPassword"
                                >
                                    <svg v-if="showConfirmPassword" class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M3 3l18 18M10.58 10.58A3 3 0 0014.42 14.42M9.88 5.09A10.94 10.94 0 0112 5c5.05 0 9.27 3.11 10.5 7.5a11.16 11.16 0 01-4.08 5.8M6.23 6.23A11.1 11.1 0 001.5 12.5 10.94 10.94 0 006 17.91" />
                                    </svg>
                                    <svg v-else class="h-5 w-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" aria-hidden="true">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.7" d="M2.25 12S5.25 5.25 12 5.25 21.75 12 21.75 12 18.75 18.75 12 18.75 2.25 12 2.25 12z" />
                                        <circle cx="12" cy="12" r="3" stroke-width="1.7" />
                                    </svg>
                                </button>
                            </div>
                        </div>
                        <button type="submit" :disabled="passwordForm.processing" class="rounded-xl bg-amber-500 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-amber-600 disabled:opacity-50">
                            {{ t('cabinet.save') }}
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </LandingLayout>
</template>
