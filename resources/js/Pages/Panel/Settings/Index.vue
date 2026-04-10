<script setup>
/**
 * Страница настроек системы: бронирования, локаль, касса, уведомления.
 */
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed, watch } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    settings: { type: Array, default: () => [] },
});

const { t } = useI18n();

const settingsMap = Object.fromEntries(props.settings.map((s) => [s.key, s.value]));

const form = useForm({
    cleaning_buffer_minutes: parseInt(settingsMap.cleaning_buffer_minutes, 10) || 15,
    booking_min_hours: parseInt(settingsMap.booking_min_hours, 10) || 1,
    booking_max_hours: parseInt(settingsMap.booking_max_hours, 10) || 10,
    online_payment_enabled: settingsMap.online_payment_enabled === 'true',
    sms_enabled: settingsMap.sms_enabled === 'true',
    site_locale_default: settingsMap.site_locale_default || 'ru',
    fiscal_device_type: settingsMap.fiscal_device_type || 'atol',
    fiscal_device_url: settingsMap.fiscal_device_url || 'http://localhost:16732',
});

/** Буфер уборки: часы и минуты для ввода, синхрон с form.cleaning_buffer_minutes. */
const initialBuffer = parseInt(settingsMap.cleaning_buffer_minutes, 10) || 15;
const bufferHours = ref(Math.floor(initialBuffer / 60));
const bufferMinutes = ref(initialBuffer % 60);
const totalBufferMinutes = computed(() => bufferHours.value * 60 + bufferMinutes.value);
watch(totalBufferMinutes, (val) => {
    form.cleaning_buffer_minutes = val;
});

const submit = () => {
    form.put(route('panel.settings.update'));
};
</script>

<template>
    <DashboardLayout>
        <Head :title="t('settings.title')" />
        <template #header>
            <h1 class="text-xl font-semibold text-gray-800">{{ t('settings.title') }}</h1>
        </template>

        <div class="mx-auto max-w-2xl px-4 pb-12 pt-8">
            <form class="space-y-6" @submit.prevent="submit">
                <!-- Section 1: Бронирования -->
                <div class="rounded-2xl bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-500">
                        {{ t('settings.section_booking') }}
                    </h2>
                    <div class="space-y-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                {{ t('settings.cleaning_buffer') }}
                            </label>
                            <div class="flex items-center gap-3">
                                <div class="flex-1">
                                    <label class="mb-1 block text-xs text-gray-500">{{ t('settings.hours') }}</label>
                                    <input
                                        v-model.number="bufferHours"
                                        type="number"
                                        min="0"
                                        max="5"
                                        class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-orange-300 focus:outline-none focus:ring-2 focus:ring-orange-400/20"
                                    />
                                </div>
                                <div class="flex-1">
                                    <label class="mb-1 block text-xs text-gray-500">{{ t('settings.minutes') }}</label>
                                    <input
                                        v-model.number="bufferMinutes"
                                        type="number"
                                        min="0"
                                        max="59"
                                        class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-orange-300 focus:outline-none focus:ring-2 focus:ring-orange-400/20"
                                    />
                                </div>
                                <div class="flex flex-col font-medium text-gray-500">
                                    <div><span class="invisible">1</span></div>
                                    <div>
                                        <span class="text-sm">= {{ totalBufferMinutes }} {{ t('settings.minutes_short') }}</span>
                                    </div>
                                </div>
                            </div>
                            <p class="mt-1 text-xs text-gray-500">{{ t('settings.cleaning_buffer_hint') }}</p>
                            <p v-if="form.errors.cleaning_buffer_minutes" class="mt-1 text-xs text-red-500">
                                {{ form.errors.cleaning_buffer_minutes }}
                            </p>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                {{ t('settings.min_hours') }}
                            </label>
                            <input
                                v-model.number="form.booking_min_hours"
                                type="number"
                                min="1"
                                max="5"
                                class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-orange-300 focus:outline-none focus:ring-2 focus:ring-orange-400/20"
                            />
                            <p v-if="form.errors.booking_min_hours" class="mt-1 text-xs text-red-500">
                                {{ form.errors.booking_min_hours }}
                            </p>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                {{ t('settings.max_hours') }}
                            </label>
                            <input
                                v-model.number="form.booking_max_hours"
                                type="number"
                                min="2"
                                max="24"
                                class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-orange-300 focus:outline-none focus:ring-2 focus:ring-orange-400/20"
                            />
                            <p v-if="form.errors.booking_max_hours" class="mt-1 text-xs text-red-500">
                                {{ form.errors.booking_max_hours }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Язык и локализация -->
                <div class="rounded-2xl bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-500">
                        {{ t('settings.section_locale') }}
                    </h2>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">
                            {{ t('settings.locale_default') }}
                        </label>
                        <div class="flex gap-3">
                            <button
                                type="button"
                                :class="[
                                    'rounded-lg px-4 py-2.5 text-sm font-medium transition',
                                    form.site_locale_default === 'ru'
                                        ? 'bg-orange-500 text-white'
                                        : 'bg-gray-100 text-gray-600 hover:bg-gray-200',
                                ]"
                                @click="form.site_locale_default = 'ru'"
                            >
                                RU — {{ t('settings.locale_ru') }}
                            </button>
                            <button
                                type="button"
                                :class="[
                                    'rounded-lg px-4 py-2.5 text-sm font-medium transition',
                                    form.site_locale_default === 'tk'
                                        ? 'bg-orange-500 text-white'
                                        : 'bg-gray-100 text-gray-600 hover:bg-gray-200',
                                ]"
                                @click="form.site_locale_default = 'tk'"
                            >
                                TK — {{ t('settings.locale_tk') }}
                            </button>
                        </div>
                        <p v-if="form.errors.site_locale_default" class="mt-1 text-xs text-red-500">
                            {{ form.errors.site_locale_default }}
                        </p>
                    </div>
                </div>

                <!-- Section 3: Касса и оплата -->
                <div class="rounded-2xl bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-500">
                        {{ t('settings.section_fiscal') }}
                    </h2>
                    <div class="space-y-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                {{ t('settings.fiscal_type') }}
                            </label>
                            <select
                                v-model="form.fiscal_device_type"
                                class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-orange-300 focus:outline-none focus:ring-2 focus:ring-orange-400/20"
                            >
                                <option value="atol">{{ t('settings.fiscal_atol') }}</option>
                                <option value="shtrikh">{{ t('settings.fiscal_shtrikh') }}</option>
                            </select>
                            <p v-if="form.errors.fiscal_device_type" class="mt-1 text-xs text-red-500">
                                {{ form.errors.fiscal_device_type }}
                            </p>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                {{ t('settings.fiscal_url') }}
                            </label>
                            <input
                                v-model="form.fiscal_device_url"
                                type="url"
                                class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-orange-300 focus:outline-none focus:ring-2 focus:ring-orange-400/20"
                                :placeholder="t('settings.fiscal_url_hint')"
                            />
                            <p class="mt-1 text-xs text-gray-500">{{ t('settings.fiscal_url_hint') }}</p>
                            <p v-if="form.errors.fiscal_device_url" class="mt-1 text-xs text-red-500">
                                {{ form.errors.fiscal_device_url }}
                            </p>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                {{ t('settings.online_payment') }}
                            </label>
                            <button
                                type="button"
                                role="switch"
                                :aria-checked="form.online_payment_enabled"
                                :class="[
                                    'relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2',
                                    form.online_payment_enabled ? 'bg-green-500' : 'bg-gray-200',
                                ]"
                                @click="form.online_payment_enabled = !form.online_payment_enabled"
                            >
                                <span
                                    :class="[
                                        'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition',
                                        form.online_payment_enabled ? 'translate-x-5' : 'translate-x-1',
                                    ]"
                                />
                            </button>
                            <p class="mt-1 text-xs text-orange-500">⚠️ {{ t('settings.online_payment_hint') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Уведомления -->
                <div class="rounded-2xl bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-500">
                        {{ t('settings.section_notifications') }}
                    </h2>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">
                            {{ t('settings.sms') }}
                        </label>
                        <button
                            type="button"
                            role="switch"
                            :aria-checked="form.sms_enabled"
                            :class="[
                                'relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2',
                                form.sms_enabled ? 'bg-green-500' : 'bg-gray-200',
                            ]"
                            @click="form.sms_enabled = !form.sms_enabled"
                        >
                            <span
                                :class="[
                                    'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition',
                                    form.sms_enabled ? 'translate-x-5' : 'translate-x-1',
                                ]"
                            />
                        </button>
                        <p class="mt-1 text-xs text-orange-500">⚠️ {{ t('settings.sms_hint') }}</p>
                    </div>
                </div>

                <div class="flex justify-end">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-lg bg-orange-500 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2 disabled:opacity-50"
                    >
                        {{ form.processing ? '...' : t('settings.save') }}
                    </button>
                </div>
            </form>
        </div>
    </DashboardLayout>
</template>
