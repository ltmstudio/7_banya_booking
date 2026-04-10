<script setup>
/**
 * Список комнат: фото, название, категория, цена, статус (toggle), действия.
 */
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import ConfirmDeleteModal from '@/Components/ConfirmDeleteModal.vue';
import { Head, Link, router, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    rooms: { type: Array, required: true },
    meta: { type: Object, required: true },
});

const { t } = useI18n();
const tRoom = computed(() => usePage().props.room || {});

/** Имя комнаты для текущей локали. */
const roomName = (room) => {
    const locale = usePage().props.locale || 'ru';
    return room.name?.[locale] || room.name?.ru || room.category_label || '—';
};

/** Бейджи категорий: standard=gray, family=blue, vip=purple */
const categoryBadgeClass = (cat) => {
    const map = {
        standard: 'bg-gray-100 text-gray-700',
        family: 'bg-blue-100 text-blue-700',
        vip: 'bg-purple-100 text-purple-700',
    };
    return map[cat] || 'bg-gray-100 text-gray-700';
};

/** Форматирование цены с промо. */
const priceDisplay = (room) => {
    const main = `${room.price_per_hour} TMT`;
    if (room.promo_price_per_hour) {
        return { main: `${room.promo_price_per_hour} TMT`, crossed: main };
    }
    return { main, crossed: null };
};

const showDeleteModal = ref(false);
const deleteTarget = ref(null);

const toggleStatus = (room) => {
    const count = room.active_bookings_count || 0;
    const toInactive = room.is_active;
    if (toInactive && count > 0) {
        const msg = tRoom.value.cancel_bookings_confirm?.replace(':count', count) || `Есть ${count} активных броней. Отменить их?`;
        if (!confirm(msg)) return;
        router.patch(route('panel.rooms.toggle-status', room.id), { cancel_bookings: true });
    } else {
        router.patch(route('panel.rooms.toggle-status', room.id));
    }
};

const openDeleteConfirm = (room) => {
    deleteTarget.value = room;
    showDeleteModal.value = true;
};

const doDelete = () => {
    if (deleteTarget.value) {
        router.delete(route('panel.rooms.destroy', deleteTarget.value.id));
        deleteTarget.value = null;
    }
};
</script>

<template>
    <DashboardLayout>
        <Head :title="t('rooms.title')" />
        <template #header>
            <div class="flex flex-1 items-center justify-between gap-4">
                <h1 class="text-xl font-semibold text-gray-800">{{ t('rooms.title') }}</h1>
                <Link
                    :href="route('panel.rooms.create')"
                    class="inline-flex items-center justify-center rounded-lg bg-orange-500 px-4 py-2 text-sm font-semibold text-white transition hover:bg-orange-600 focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2"
                >
                    {{ t('rooms.create') }}
                </Link>
            </div>
        </template>

        <div class="space-y-4">
            <div class="overflow-hidden rounded-lg bg-white shadow">
                <div class="hidden overflow-x-auto md:block">
                    <table class="min-w-full">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">Фото</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ t('rooms.name') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ t('rooms.category') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ t('rooms.capacity') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ t('rooms.price_per_hour') }}</th>
                                <th class="px-4 py-3 text-left text-xs font-medium uppercase tracking-wider text-gray-500">{{ t('rooms.is_active') }}</th>
                                <th class="px-4 py-3 text-right text-xs font-medium uppercase tracking-wider text-gray-500">{{ t('rooms.actions') }}</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white">
                            <tr
                                v-for="room in rooms"
                                :key="room.id"
                                class="border-b border-gray-100 transition-colors hover:bg-gray-50"
                            >
                                <td class="whitespace-nowrap px-4 py-3">
                                    <img
                                        v-if="room.photos?.length"
                                        :src="room.photos[0].url"
                                        :alt="roomName(room)"
                                        class="h-[60px] w-[60px] rounded-lg object-cover"
                                    />
                                    <div
                                        v-else
                                        class="flex h-[60px] w-[60px] items-center justify-center rounded-lg bg-gray-200 text-gray-400"
                                    >
                                        <svg class="h-6 w-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14" />
                                        </svg>
                                    </div>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm font-medium text-gray-900">{{ roomName(room) }}</td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    <span :class="['inline-flex rounded-full px-2.5 py-0.5 text-xs font-medium', categoryBadgeClass(room.category)]">
                                        {{ room.category_label }}
                                    </span>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">👥 {{ room.capacity ?? '—' }}</td>
                                <td class="whitespace-nowrap px-4 py-3 text-sm text-gray-600">
                                    <template v-if="priceDisplay(room).crossed">
                                        <span class="text-gray-400 line-through">{{ priceDisplay(room).crossed }}</span>
                                        <span class="ml-1">{{ priceDisplay(room).main }}/час</span>
                                    </template>
                                    <template v-else>
                                        {{ priceDisplay(room).main }}/час
                                    </template>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3">
                                    <button
                                        type="button"
                                        role="switch"
                                        :aria-checked="room.is_active"
                                        :class="[
                                            'relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors focus:outline-none focus:ring-2 focus:ring-orange-500 focus:ring-offset-2',
                                            room.is_active ? 'bg-green-500' : 'bg-gray-200',
                                        ]"
                                        @click="toggleStatus(room)"
                                    >
                                        <span
                                            :class="[
                                                'pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition',
                                                room.is_active ? 'translate-x-5' : 'translate-x-1',
                                            ]"
                                        />
                                    </button>
                                </td>
                                <td class="whitespace-nowrap px-4 py-3 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link
                                            :href="route('panel.rooms.edit', room.id)"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 hover:bg-gray-50"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" />
                                            </svg>
                                            {{ t('rooms.edit_btn') }}
                                        </Link>
                                        <button
                                            type="button"
                                            class="inline-flex items-center gap-1.5 rounded-lg border border-gray-200 bg-white px-2.5 py-1.5 text-xs font-medium text-gray-700 hover:bg-red-50 hover:border-red-200 hover:text-red-600"
                                            @click="openDeleteConfirm(room)"
                                        >
                                            <svg class="h-3.5 w-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" />
                                            </svg>
                                            {{ t('rooms.delete_btn') }}
                                        </button>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="rooms.length === 0">
                                <td colspan="7" class="px-4 py-12 text-center text-sm text-gray-500">
                                    {{ t('rooms.empty_state') }}
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>

                <!-- Mobile cards -->
                <div class="md:hidden">
                    <div
                        v-for="room in rooms"
                        :key="room.id"
                        class="flex flex-col gap-2 border-b border-gray-100 p-4 last:border-b-0"
                    >
                        <div class="flex items-start gap-3">
                            <img
                                v-if="room.photos?.length"
                                :src="room.photos[0].url"
                                :alt="roomName(room)"
                                class="h-14 w-14 shrink-0 rounded-lg object-cover"
                            />
                            <div v-else class="flex h-14 w-14 shrink-0 items-center justify-center rounded-lg bg-gray-200">
                                <svg class="h-6 w-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14" />
                                </svg>
                            </div>
                            <div class="min-w-0 flex-1">
                                <p class="font-medium text-gray-900">{{ roomName(room) }}</p>
                                <span :class="['inline-flex rounded-full px-2 py-0.5 text-xs font-medium', categoryBadgeClass(room.category)]">
                                    {{ room.category_label }}
                                </span>
                                <p class="mt-0.5 text-xs text-gray-500">👥 {{ room.capacity ?? '—' }}</p>
                                <p class="mt-1 text-sm text-gray-600">
                                    <span v-if="room.promo_price_per_hour" class="text-gray-400 line-through">{{ room.price_per_hour }} TMT</span>
                                    {{ room.promo_price_per_hour || room.price_per_hour }} TMT/час
                                </p>
                            </div>
                            <button
                                type="button"
                                :class="[
                                    'relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors',
                                    room.is_active ? 'bg-green-500' : 'bg-gray-200',
                                ]"
                                @click="toggleStatus(room)"
                            >
                                <span
                                    :class="[
                                        'inline-block h-5 w-5 transform rounded-full bg-white shadow transition',
                                        room.is_active ? 'translate-x-5' : 'translate-x-1',
                                    ]"
                                />
                            </button>
                        </div>
                        <div class="flex gap-2">
                            <Link
                                :href="route('panel.rooms.edit', room.id)"
                                class="rounded-lg bg-orange-500 px-3 py-1.5 text-sm font-medium text-white hover:bg-orange-600"
                            >
                                {{ t('rooms.edit_btn') }}
                            </Link>
                            <button
                                type="button"
                                class="rounded-lg border border-red-300 px-3 py-1.5 text-sm font-medium text-red-600 hover:bg-red-50"
                                @click="openDeleteConfirm(room)"
                            >
                                {{ t('rooms.delete_btn') }}
                            </button>
                        </div>
                    </div>
                    <div v-if="rooms.length === 0" class="py-12 text-center text-sm text-gray-500">
                        {{ t('rooms.empty_state') }}
                    </div>
                </div>

                <!-- Pagination -->
                <div v-if="meta.last_page > 1" class="flex items-center justify-between border-t border-gray-200 px-4 py-3">
                    <p class="text-sm text-gray-600">{{ meta.total }} {{ t('rooms.title').toLowerCase() }}</p>
                    <div class="flex gap-2">
                        <Link
                            v-if="meta.current_page > 1"
                            :href="route('panel.rooms.index', { page: meta.current_page - 1 })"
                            class="rounded-lg border border-gray-300 px-3 py-1.5 text-sm font-medium text-gray-700 hover:bg-gray-50"
                        >
                            ←
                        </Link>
                        <span class="py-1.5 text-sm text-gray-600">{{ meta.current_page }} / {{ meta.last_page }}</span>
                        <Link
                            v-if="meta.current_page < meta.last_page"
                            :href="route('panel.rooms.index', { page: meta.current_page + 1 })"
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
            :message="t('rooms.delete_confirm')"
            @confirm="doDelete"
        />
    </DashboardLayout>
</template>
