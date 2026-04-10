<script setup>
/**
 * Форма редактирования клиента.
 */
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import PhoneInput from '@/Components/PhoneInput.vue';
import BirthdayInput from '@/Components/BirthdayInput.vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { computed } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    editingClient: { type: Object, required: true },
});

const { t } = useI18n();
const tClient = computed(() => usePage().props.client || {});

/** birthday из d.m.Y в Y-m-d для input[type=date]. */
const birthdayForInput = (val) => {
    if (!val) return '';
    const parts = String(val).split('.');
    if (parts.length !== 3) return val;
    return `${parts[2]}-${parts[1]}-${parts[0]}`;
};

const form = useForm({
    first_name: props.editingClient.first_name ?? '',
    last_name: props.editingClient.last_name ?? '',
    phone: props.editingClient.phone ?? '',
    birthday: birthdayForInput(props.editingClient.birthday),
});

const submit = () => {
    form.put(route('panel.clients.update', props.editingClient.id));
};
</script>

<template>
    <DashboardLayout>
        <Head :title="tClient.edit" />
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h1 class="text-xl font-semibold text-gray-800">{{ tClient.edit }}</h1>
                <Link :href="route('panel.clients.index')" class="text-sm text-gray-500 hover:text-orange-600">
                    ← {{ tClient.cancel }}
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
                        <label class="mb-2 block text-sm font-medium text-gray-700">{{ t('client.first_name') }} *</label>
                        <input
                            v-model="form.first_name"
                            type="text"
                            maxlength="100"
                            required
                            class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-orange-300 focus:outline-none focus:ring-2 focus:ring-orange-400/20"
                        />
                        <p v-if="form.errors.first_name" class="mt-1 text-xs text-red-500">{{ form.errors.first_name }}</p>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">{{ t('client.last_name') }}</label>
                        <input
                            v-model="form.last_name"
                            type="text"
                            maxlength="100"
                            class="w-full rounded-lg border border-gray-200 px-4 py-2.5 text-sm focus:border-orange-300 focus:outline-none focus:ring-2 focus:ring-orange-400/20"
                        />
                        <p v-if="form.errors.last_name" class="mt-1 text-xs text-red-500">{{ form.errors.last_name }}</p>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">{{ t('client.phone') }} * (+993 XX XXXXXXX)</label>
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

                <div v-if="editingClient.bookings_count > 0" class="mt-4 rounded-lg bg-blue-50 px-4 py-2 text-sm text-blue-700">
                    {{ t('client.bookings_count') }}: {{ editingClient.bookings_count }}
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
                        {{ form.processing ? 'Сохранение...' : t('client.save') }}
                    </button>
                </div>
            </form>
        </div>
    </DashboardLayout>
</template>
