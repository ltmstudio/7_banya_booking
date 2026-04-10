<script setup>
/**
 * Список пользователей: таблица, поиск, пагинация, действия.
 * Фильтрация по имени/email на клиенте.
 */
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ConfirmDeleteModal from '@/Components/ConfirmDeleteModal.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    users: { type: Array, required: true },
    meta: { type: Object, required: true },
});

const { t } = useI18n();
const tUser = computed(() => usePage().props.user || {});
const page = usePage();
const searchQuery = ref('');
const showDeleteModal = ref(false);
const deleteTarget = ref(null);

/** Фильтрация по имени или email */
const filteredUsers = computed(() => {
    const q = searchQuery.value.trim().toLowerCase();
    if (!q) return props.users;
    return props.users.filter(
        (u) =>
            (u.name || '').toLowerCase().includes(q) ||
            (u.email || '').toLowerCase().includes(q)
    );
});

/** Бейджи ролей: admin=red, manager=blue, cashier=green. Всегда badge. */
const roleBadgeClass = (role) => {
    const map = {
        admin: 'bg-red-100 text-red-700',
        manager: 'bg-blue-100 text-blue-700',
        cashier: 'bg-green-100 text-green-700',
    };
    return map[role] || 'bg-gray-100 text-gray-700';
};

/** Лейбл роли */
const roleLabel = (role) => {
    const map = { admin: tUser.value.role_admin, manager: tUser.value.role_manager, cashier: tUser.value.role_cashier };
    return map[role] || role;
};

const openDeleteConfirm = (user) => {
    deleteTarget.value = user;
    showDeleteModal.value = true;
};

const doDelete = () => {
    if (deleteTarget.value) {
        router.delete(route('panel.users.destroy', deleteTarget.value.id));
        deleteTarget.value = null;
    }
};

</script>

<template>
    <DashboardLayout>
        <Head :title="tUser.title" />
        <template #header>
            <div class="flex flex-1 items-center justify-between gap-4">
                <h1 class="text-xl font-semibold text-gray-800">{{ tUser.title }}</h1>
                <Link
                    :href="route('panel.users.create')"
                    class="inline-flex items-center justify-center rounded-lg bg-amber-600 px-4 py-2 text-sm font-semibold text-white shadow-sm transition-colors hover:bg-amber-700 focus:outline-none focus:ring-2 focus:ring-amber-500 focus:ring-offset-2"
                >
                    {{ tUser.add || 'Добавить' }}
                </Link>
            </div>
        </template>

        <div class="space-y-4">
            <!-- Поиск -->
            <div class="rounded-lg bg-white p-4 shadow">
                <input
                    v-model="searchQuery"
                    type="search"
                    class="block w-full rounded-lg border border-gray-200 px-3 py-2 text-sm placeholder-gray-500 focus:border-amber-500 focus:outline-none focus:ring-1 focus:ring-amber-500"
                    :placeholder="tUser.search_placeholder || 'Поиск по имени или email'"
                />
            </div>

            <div class="overflow-hidden rounded-lg bg-white shadow">
                <!-- Desktop table -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ tUser.name }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ tUser.email }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ tUser.role }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ tUser.created_at }}</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">{{ tUser.actions }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            <tr
                                v-for="user in filteredUsers"
                                :key="user.id"
                                class="border-b border-gray-100 transition-colors hover:bg-gray-50"
                            >
                                <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">{{ user.name }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">{{ user.email }}</td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    <span :class="['inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium', roleBadgeClass(user.role)]">
                                        {{ roleLabel(user.role) }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">{{ user.created_at }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link
                                            :href="route('panel.users.edit', user.id)"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                            {{ tUser.edit_btn }}
                                        </Link>
                                        <button
                                            type="button"
                                            :disabled="user.id === page.props.auth?.user?.id"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 hover:bg-red-50 hover:border-red-200 hover:text-red-600 disabled:cursor-not-allowed disabled:opacity-50"
                                            @click="openDeleteConfirm(user)"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            {{ tUser.delete_btn }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="filteredUsers.length === 0">
                                <td colspan="5" class="px-4 py-12 text-center text-sm text-gray-500">
                                    {{ tUser.empty_state || 'Пользователи не найдены' }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile cards -->
                <div class="md:hidden">
                    <div
                        v-for="user in filteredUsers"
                        :key="user.id"
                        class="flex flex-col gap-2 border-b border-gray-100 p-4 last:border-b-0"
                    >
                        <div class="flex items-start justify-between gap-2">
                            <div>
                                <p class="font-medium text-gray-900">{{ user.name }}</p>
                                <p class="text-sm text-gray-600">{{ user.email }}</p>
                            </div>
                            <span :class="['shrink-0 rounded-full px-2.5 py-0.5 text-xs font-medium', roleBadgeClass(user.role)]">
                                {{ roleLabel(user.role) }}
                            </span>
                        </div>
                        <p class="text-xs text-gray-500">{{ tUser.created_at }}: {{ user.created_at }}</p>
                        <div class="flex gap-2">
                            <Link
                                :href="route('panel.users.edit', user.id)"
                                class="rounded-lg bg-amber-600 px-3 py-1.5 text-sm font-medium text-white hover:bg-amber-700"
                            >
                                {{ tUser.edit_btn }}
                            </Link>
                            <button
                                type="button"
                                class="rounded-lg border border-red-300 px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50 disabled:opacity-50"
                                :disabled="user.id === page.props.auth?.user?.id"
                                @click="openDeleteConfirm(user)"
                            >
                                {{ tUser.delete_btn }}
                            </button>
                        </div>
                    </div>
                    <div v-if="filteredUsers.length === 0" class="py-12 text-center text-sm text-gray-500">
                        {{ tUser.empty_state || 'Пользователи не найдены' }}
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="meta.last_page > 1" class="flex items-center justify-between border-t border-gray-200 px-4 py-3">
                    <p class="text-sm text-gray-600">
                        {{ meta.total }} {{ tUser.title.toLowerCase() }}
                    </p>
                    <div class="flex gap-2">
                        <Link
                            v-if="meta.current_page > 1"
                            :href="route('panel.users.index', { page: meta.current_page - 1 })"
                            class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            ←
                        </Link>
                        <span class="py-1.5 text-sm text-gray-600">
                            {{ meta.current_page }} / {{ meta.last_page }}
                        </span>
                        <Link
                            v-if="meta.current_page < meta.last_page"
                            :href="route('panel.users.index', { page: meta.current_page + 1 })"
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
            :message="t('user.delete_confirm')"
            @confirm="doDelete"
        />
    </DashboardLayout>
</template>
