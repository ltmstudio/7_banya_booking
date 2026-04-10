<script setup>
/**
 * Форма создания клиента: имя, фамилия, телефон (+993 + 8 цифр), дата рождения.
 */
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import PhoneInput from '@/Components/PhoneInput.vue';
import BirthdayInput from '@/Components/BirthdayInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const form = useForm({
    first_name: '',
    last_name: '',
    phone: '',
    birthday: '',
});

const submit = () => {
    form.post(route('panel.clients.store'));
};
</script>

<template>
    <DashboardLayout>
        <Head :title="t('client.create')" />
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h1 class="text-xl font-semibold text-gray-800">{{ t('client.create') }}</h1>
                <Link :href="route('panel.clients.index')" class="text-sm text-gray-500 hover:text-orange-600">
                    ← {{ t('client.cancel') }}
                </Link>
            </div>
        </template>

        <div class="mx-auto mt-8 max-w-2xl px-4 pb-12">
            <form
                class="rounded-2xl border border-gray-200 bg-white p-8 shadow-md"
                @submit.prevent="submit"
            >
                <div class="space-y-4">
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">{{ t('client.first_name') }} <span class="text-orange-500">*</span></label>
                        <input
                            v-model="form.first_name"
                            type="text"
                            maxlength="100"
                            required
                            :placeholder="t('client.first_name_placeholder')"
                            class="input-field"
                        />
                        <p v-if="form.errors.first_name" class="mt-1 text-xs text-red-500">{{ form.errors.first_name }}</p>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">{{ t('client.last_name') }}</label>
                        <input
                            v-model="form.last_name"
                            type="text"
                            maxlength="100"
                            :placeholder="t('client.last_name_placeholder')"
                            class="input-field"
                        />
                        <p v-if="form.errors.last_name" class="mt-1 text-xs text-red-500">{{ form.errors.last_name }}</p>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">{{ t('client.phone') }} <span class="text-orange-500">*</span> (+993 XX XXXXXXX)</label>
                        <PhoneInput
                            v-model="form.phone"
                            :error="form.errors.phone"
                        />
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">{{ t('client.birthday') }}</label>
                        <BirthdayInput
                            v-model="form.birthday"
                            :error="form.errors.birthday"
                        />
                    </div>
                </div>

                <div class="mt-8 flex flex-wrap items-center justify-between gap-4 border-t border-gray-200 pt-8">
                    <Link :href="route('panel.clients.index')" class="text-sm text-gray-600 hover:text-orange-600">
                        {{ t('client.cancel') }}
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-lg bg-orange-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-orange-600 disabled:opacity-50"
                    >
                        {{ form.processing ? t('common.saving') : t('client.save') }}
                    </button>
                </div>
            </form>
        </div>
    </DashboardLayout>
</template>

<style scoped>
.input-field {
    @apply w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-orange-300 focus:outline-none focus:ring-2 focus:ring-orange-400/20;
}
</style>
