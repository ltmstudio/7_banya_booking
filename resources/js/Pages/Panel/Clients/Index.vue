<script setup>
/**
 * Список клиентов: таблица, поиск, пагинация, действия.
 * Поиск через GET ?search=.
 */
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ConfirmDeleteModal from '@/Components/ConfirmDeleteModal.vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    clients: { type: Array, required: true },
    meta: { type: Object, required: true },
    search: { type: String, default: '' },
});

const { t } = useI18n();
const showDeleteModal = ref(false);
const deleteTarget = ref(null);

const searchForm = useForm({ search: props.search });

const submitSearch = () => {
    searchForm.get(route('panel.clients.index'), { preserveState: true });
};

const openDeleteConfirm = (client) => {
    deleteTarget.value = client;
    showDeleteModal.value = true;
};

const doDelete = () => {
    if (deleteTarget.value) {
        router.delete(route('panel.clients.destroy', deleteTarget.value.id));
        deleteTarget.value = null;
    }
};
</script>

<template>
    <DashboardLayout>
        <Head :title="t('client.title')" />
        <template #header>
            <div class="flex flex-1 items-center justify-between gap-4">
                <h1 class="text-xl font-semibold text-gray-800">{{ t('client.title') }}</h1>
                <Link
                    :href="route('panel.clients.create')"
                    class="inline-flex items-center justify-center rounded-lg bg-orange-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2"
                >
                    {{ t('client.create') }}
                </Link>
            </div>
        </template>

        <div class="space-y-4">
            <!-- Поиск -->
            <form class="rounded-lg bg-white p-4 shadow" @submit.prevent="submitSearch">
                <div class="flex gap-2">
                    <input
                        v-model="searchForm.search"
                        type="search"
                        class="block flex-1 rounded-lg border border-gray-200 px-3 py-2 text-sm placeholder-gray-500 focus:border-orange-500 focus:outline-none focus:ring-1 focus:ring-orange-500"
                        :placeholder="t('client.search_placeholder')"
                    />
                    <button
                        type="submit"
                        class="rounded-lg bg-orange-500 px-4 py-2 text-sm font-medium text-white hover:bg-orange-600"
                    >
                        {{ t('client.search_btn') }}
                    </button>
                </div>
            </form>

            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="hidden overflow-x-auto md:block">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    {{ t('client.first_name') }} / {{ t('client.last_name') }}
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    {{ t('client.phone') }}
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    {{ t('client.birthday') }}
                                </th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">
                                    {{ t('client.bookings_count') }}
                                </th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">
                                    {{ t('client.actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            <tr
                                v-for="client in clients"
                                :key="client.id"
                                class="border-b border-gray-100 transition-colors hover:bg-gray-50"
                            >
                                <td class="whitespace-nowrap px-4 py-3">
                                    <span class="text-sm font-medium text-gray-900">{{ client.full_name }}</span>
                                    <span
                                        v-if="client.is_birthday_today"
                                        class="ml-2 inline-flex rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800"
                                    >
                                        {{ t('client.birthday_today') }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">{{ client.phone }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">{{ client.birthday || '—' }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">{{ client.bookings_count ?? 0 }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link
                                            :href="route('panel.clients.edit', client.id)"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                            {{ t('client.edit_btn') }}
                                        </Link>
                                        <button
                                            type="button"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 hover:bg-red-50 hover:border-red-200 hover:text-red-600"
                                            @click="openDeleteConfirm(client)"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            {{ t('client.delete_btn') }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="clients.length === 0">
                                <td colspan="5" class="px-4 py-12 text-center text-sm text-gray-500">
                                    {{ t('client.empty_state') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile cards -->
                <div class="md:hidden">
                    <div
                        v-for="client in clients"
                        :key="client.id"
                        class="flex flex-col gap-2 border-b border-gray-100 p-4 last:border-b-0"
                    >
                        <div class="flex items-start justify-between gap-2">
                            <p class="font-medium text-gray-900">{{ client.full_name }}</p>
                            <span
                                v-if="client.is_birthday_today"
                                class="shrink-0 rounded-full bg-amber-100 px-2 py-0.5 text-xs font-medium text-amber-800"
                            >
                                {{ t('client.birthday_today') }}
                            </span>
                        </div>
                        <p class="text-sm text-gray-600">{{ client.phone }}</p>
                        <p class="text-xs text-gray-500">{{ client.birthday || '—' }} · {{ client.bookings_count ?? 0 }} {{ t('client.bookings_count') }}</p>
                        <div class="flex gap-2">
                            <Link
                                :href="route('panel.clients.edit', client.id)"
                                class="rounded-lg bg-orange-500 px-3 py-1.5 text-sm font-medium text-white hover:bg-orange-600"
                            >
                                {{ t('client.edit_btn') }}
                            </Link>
                            <button
                                type="button"
                                class="rounded-lg border border-red-300 px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50"
                                @click="openDeleteConfirm(client)"
                            >
                                {{ t('client.delete_btn') }}
                            </button>
                        </div>
                    </div>
                    <div v-if="clients.length === 0" class="py-12 text-center text-sm text-gray-500">
                        {{ t('client.empty_state') }}
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="meta.last_page > 1" class="flex items-center justify-between border-t border-gray-200 px-4 py-3">
                    <p class="text-sm text-gray-600">{{ meta.total }} {{ t('client.title').toLowerCase() }}</p>
                    <div class="flex gap-2">
                        <Link
                            v-if="meta.current_page > 1"
                            :href="route('panel.clients.index', { page: meta.current_page - 1, search: searchForm.search })"
                            class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            ←
                        </Link>
                        <span class="py-1.5 text-sm text-gray-600">{{ meta.current_page }} / {{ meta.last_page }}</span>
                        <Link
                            v-if="meta.current_page < meta.last_page"
                            :href="route('panel.clients.index', { page: meta.current_page + 1, search: searchForm.search })"
                            class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            →
                        </Link>
                    </div>
                </div>
            </div>
        </div>

        <ConfirmDeleteModal
            v-model="showDeleteModal"
            :message="t('client.delete_confirm')"
            @confirm="doDelete"
        />
    </DashboardLayout>
</template>
