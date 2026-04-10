<script setup>
/**
 * Просмотр бронирования: данные, смена статуса (и итоговой суммы), история изменений (админ).
 */
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head, Link, router } from '@inertiajs/vue3';
import { usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    booking: { type: Object, required: true },
    auditLogs: { type: Array, default: () => [] },
});

const { t } = useI18n();
const page = usePage();
const isAdmin = page.props.auth?.user?.role === 'admin';

const currentLocale = computed(() => page.props.locale || 'ru');

const finalAmount = ref(
    props.booking.final_amount != null ? String(props.booking.final_amount) : ''
);
const cancelReason = ref(props.booking.cancel_reason ?? '');

const savingFinal = ref(false);
const changingStatus = ref(false);

const statusOrder = ['new', 'preliminary', 'confirmed', 'paid', 'completed'];
const statuses = statusOrder;

const isCancelled = computed(() => props.booking.status === 'cancelled');

/** Класс точки в таймлайне статусов: текущий — оранжевый, прошлые — зелёный, будущие — серый. */
function getStatusDotClass(status) {
    if (props.booking.status === status) return 'bg-orange-500';
    const idx = statusOrder.indexOf(status);
    const currentIdx = statusOrder.indexOf(props.booking.status);
    return idx < currentIdx ? 'bg-emerald-500' : 'bg-gray-300';
}

const saveFinalAmount = () => {
    savingFinal.value = true;
    router.patch(
        route('panel.bookings.setFinalAmount', props.booking.id),
        { final_amount: finalAmount.value || null },
        {
            preserveScroll: true,
            onFinish: () => {
                savingFinal.value = false;
            },
        },
    );
};

const changeStatus = (status) => {
    changingStatus.value = true;
    router.patch(
        route('panel.bookings.updateStatus', props.booking.id),
        {
            status,
            cancel_reason: status === 'cancelled' ? cancelReason.value : null,
        },
        {
            preserveScroll: true,
            onFinish: () => {
                changingStatus.value = false;
            },
        },
    );
};

const confirmDelete = () => {
    if (!confirm('Удалить бронирование? Это действие нельзя отменить.')) return;
    router.delete(route('panel.bookings.destroy', props.booking.id));
};

/** Локализованная подпись поля для аудит-лога (booking_date → «Дата», room_id → «Комната» и т.д.). */
function auditFieldLabel(field) {
    if (!field) return '—';
    const keyMap = { room_id: 'room', client_id: 'client' };
    const key = keyMap[field] ?? field;
    const out = t('bookings.' + key);
    return out && out !== 'bookings.' + key ? out : field;
}
</script>

<template>
    <DashboardLayout>
        <Head :title="t('bookings.show') + ' #' + booking.id" />
        <template #header>
            <div class="flex flex-1 items-center justify-between gap-4">
                <h1 class="text-xl font-semibold text-gray-800">
                    {{ t('bookings.show') }} #{{ booking.id }}
                </h1>
                <div class="flex gap-2">
                    <Link
                        :href="route('panel.bookings.edit', booking.id)"
                        class="inline-flex items-center rounded-lg bg-orange-500 px-4 py-2 text-sm font-medium text-white hover:bg-orange-600"
                    >
                        {{ t('bookings.edit_btn') }}
                    </Link>
                    <Link
                        :href="route('panel.bookings.index')"
                        class="inline-flex items-center rounded-lg border border-gray-300 px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-50"
                    >
                        {{ t('bookings.title') }}
                    </Link>
                </div>
            </div>
        </template>

        <div class="px-4 pb-12">
            <div class="grid gap-6 lg:grid-cols-3">
                <!-- LEFT COLUMN -->
                <div class="space-y-6 lg:col-span-2">
                    <!-- Booking Info Card -->
                    <section class="rounded-2xl bg-white p-6 shadow-sm">
                        <h2 class="mb-4 text-sm font-semibold text-gray-800">
                            {{ t('bookings.info') }}
                        </h2>
                        <dl class="grid gap-4 sm:grid-cols-2">
                            <div>
                                <dt class="text-xs font-medium uppercase text-gray-500">
                                    {{ t('bookings.room') }}
                                </dt>
                                <dd class="mt-1 text-sm font-medium text-gray-900">
                                    {{ booking.room?.name?.[currentLocale] ?? '—' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase text-gray-500">
                                    {{ t('bookings.booking_date') }}
                                </dt>
                                <dd class="mt-1 text-sm text-gray-700">
                                    {{ booking.booking_date }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase text-gray-500">
                                    {{ t('bookings.start_time') }} — {{ t('bookings.end_time') }}
                                </dt>
                                <dd class="mt-1 text-sm text-gray-700">
                                    {{ booking.start_time }} — {{ booking.end_time }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase text-gray-500">
                                    {{ t('bookings.cleaning_end_time') }}
                                </dt>
                                <dd class="mt-1 text-sm text-gray-700">
                                    {{ booking.cleaning_end_time || '—' }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase text-gray-500">
                                    {{ t('bookings.duration_hours') }}
                                </dt>
                                <dd class="mt-1 text-sm text-gray-700">
                                    {{ booking.duration_hours }} {{ t('bookings.hours') }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase text-gray-500">
                                    {{ t('bookings.guests_count') }}
                                </dt>
                                <dd class="mt-1 text-sm text-gray-700">
                                    {{ booking.guests_count }}
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase text-gray-500">
                                    {{ t('bookings.source') }}
                                </dt>
                                <dd class="mt-1 text-sm text-gray-700">
                                    <span
                                        v-if="booking.source === 'online'"
                                        class="inline-flex items-center gap-1 rounded-full bg-orange-50 px-2.5 py-0.5 text-xs font-medium text-orange-700"
                                    >
                                        🌐 {{ t('bookings.source_online') }}
                                    </span>
                                    <span
                                        v-else
                                        class="inline-flex items-center gap-1 rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-700"
                                    >
                                        🖥 {{ t('bookings.source_admin') }}
                                    </span>
                                </dd>
                            </div>
                            <div>
                                <dt class="text-xs font-medium uppercase text-gray-500">
                                    {{ t('bookings.status') }}
                                </dt>
                                <dd class="mt-1">
                                    <span
                                        class="inline-flex rounded-full bg-gray-100 px-2.5 py-0.5 text-xs font-medium text-gray-700"
                                    >
                                        {{ booking.status_label }}
                                    </span>
                                </dd>
                            </div>
                        </dl>

                        <div
                            v-if="booking.room?.capacity && booking.guests_count > booking.room.capacity"
                            class="mt-4 flex items-start gap-3 rounded-xl border border-amber-200 bg-amber-50 px-4 py-3"
                        >
                            <span class="flex-shrink-0 text-xl">⚠️</span>
                            <div>
                                <p class="text-sm font-semibold text-amber-800">
                                    {{ t('bookings.over_capacity_title') }}
                                </p>
                                <p class="mt-1 text-xs text-amber-700">
                                    {{ t('bookings.over_capacity_detail', {
                                        booked: booking.guests_count,
                                        max: booking.room.capacity,
                                        extra: booking.guests_count - booking.room.capacity,
                                    }) }}
                                </p>
                            </div>
                        </div>
                    </section>

                    <!-- Client Card -->
                    <section class="rounded-2xl bg-white p-6 shadow-sm">
                        <h2 class="mb-4 text-sm font-semibold text-gray-800">
                            {{ t('bookings.client') }}
                        </h2>
                        <div v-if="booking.client">
                            <p class="text-sm font-medium text-gray-900">
                                {{ booking.client.full_name }}
                            </p>
                            <p v-if="booking.client.phone" class="mt-1 text-sm text-gray-700">
                                {{ booking.client.phone }}
                            </p>
                            <p v-if="booking.client.birthday" class="mt-1 text-sm text-gray-700">
                                {{ booking.client.birthday }}
                                <span v-if="booking.client.is_birthday_today"> 🎂</span>
                            </p>
                        </div>
                        <div v-else>
                            <p class="text-xs font-semibold uppercase text-gray-500">
                                {{ t('bookings.guest') }}
                            </p>
                            <p class="mt-1 text-sm font-medium text-gray-900">
                                {{
                                    [booking.guest_name, booking.guest_last_name]
                                        .filter(Boolean)
                                        .join(' ') || '—'
                                }}
                            </p>
                            <p v-if="booking.guest_phone" class="mt-1 text-sm text-gray-700">
                                {{ booking.guest_phone }}
                            </p>
                            <p v-if="booking.guest_birthday" class="mt-1 text-sm text-gray-700">
                                {{ booking.guest_birthday }}
                            </p>
                        </div>
                    </section>

                    <!-- Extra Services Card -->
                    <section
                        v-if="booking.extra_services && booking.extra_services.length"
                        class="rounded-2xl bg-white p-6 shadow-sm"
                    >
                        <h2 class="mb-4 text-sm font-semibold text-gray-800">
                            {{ t('bookings.extra_services') }}
                        </h2>
                        <div class="overflow-x-auto rounded-xl border border-gray-100">
                            <table class="min-w-full divide-y divide-gray-100 text-sm">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th
                                            scope="col"
                                            class="px-3 py-2 text-left text-xs font-medium uppercase tracking-wide text-gray-500"
                                        >
                                            {{ t('bookings.service_name') }}
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-3 py-2 text-right text-xs font-medium uppercase tracking-wide text-gray-500"
                                        >
                                            {{ t('bookings.quantity') }}
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-3 py-2 text-right text-xs font-medium uppercase tracking-wide text-gray-500"
                                        >
                                            {{ t('bookings.price') }}
                                        </th>
                                        <th
                                            scope="col"
                                            class="px-3 py-2 text-right text-xs font-medium uppercase tracking-wide text-gray-500"
                                        >
                                            {{ t('bookings.subtotal') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 bg-white">
                                    <tr
                                        v-for="service in booking.extra_services"
                                        :key="service.id"
                                        class="hover:bg-gray-50"
                                    >
                                        <td class="px-3 py-2 text-gray-900">
                                            {{ service.name }}
                                        </td>
                                        <td class="px-3 py-2 text-right text-gray-700">
                                            {{ service.quantity }}
                                        </td>
                                        <td class="px-3 py-2 text-right text-gray-700">
                                            {{ service.price_at_booking }} TMT
                                        </td>
                                        <td class="px-3 py-2 text-right text-gray-900">
                                            {{ service.price_at_booking * service.quantity }} TMT
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>

                    <!-- Audit Log Card (admin only) -->
                    <section
                        v-if="isAdmin && auditLogs && auditLogs.length"
                        class="rounded-2xl bg-white p-6 shadow-sm"
                    >
                        <h2 class="mb-4 text-sm font-semibold text-gray-800">
                            {{ t('bookings.audit_log') }}
                        </h2>
                        <div class="overflow-x-auto rounded-xl border border-gray-100">
                            <table class="min-w-full divide-y divide-gray-100 text-xs">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500">
                                            {{ t('bookings.created_at') }}
                                        </th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500">
                                            {{ t('bookings.user') }}
                                        </th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500">
                                            {{ t('bookings.field') }}
                                        </th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500">
                                            {{ t('bookings.old_value') }}
                                        </th>
                                        <th class="px-3 py-2 text-left font-medium text-gray-500">
                                            {{ t('bookings.new_value') }}
                                        </th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50 bg-white">
                                    <tr
                                        v-for="log in auditLogs"
                                        :key="log.id"
                                        class="hover:bg-gray-50"
                                    >
                                        <td class="px-3 py-2 text-gray-700">
                                            {{ log.created_at }}
                                        </td>
                                        <td class="px-3 py-2 text-gray-700">
                                            {{ log.user_name || '—' }}
                                        </td>
                                        <td class="px-3 py-2 text-gray-700">
                                            {{ auditFieldLabel(log.field) }}
                                        </td>
                                        <td class="px-3 py-2 text-gray-500 line-through">
                                            {{ log.old_value ?? '—' }}
                                        </td>
                                        <td class="px-3 py-2 text-gray-900">
                                            {{ log.new_value ?? '—' }}
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </section>
                </div>

                <!-- RIGHT COLUMN -->
                <div class="space-y-6 lg:col-span-1">
                    <!-- Financial Card -->
                    <section class="rounded-2xl bg-white p-6 shadow-sm">
                        <h2 class="mb-4 text-sm font-semibold text-gray-800">
                            {{ t('bookings.financial') }}
                        </h2>
                        <dl class="space-y-3 text-sm">
                            <div class="flex items-center justify-between">
                                <dt class="text-gray-500">
                                    {{ t('bookings.base_amount') }}
                                </dt>
                                <dd class="font-medium text-gray-900">
                                    {{
                                        booking.base_amount != null
                                            ? booking.base_amount + ' TMT'
                                            : '—'
                                    }}
                                </dd>
                            </div>
                            <div>
                                <div class="flex items-center justify-between">
                                    <dt class="text-gray-500">
                                        {{ t('bookings.final_amount') }}
                                    </dt>
                                    <dd class="font-medium text-gray-900">
                                        <input
                                            v-model="finalAmount"
                                            type="number"
                                            step="0.01"
                                            min="0"
                                            class="w-24 rounded-md border border-gray-200 px-2 py-1 text-right text-sm focus:border-orange-300 focus:outline-none focus:ring-1 focus:ring-orange-400/20"
                                        />
                                        <span class="ml-1 text-xs text-gray-500">TMT</span>
                                    </dd>
                                </div>
                                <button
                                    type="button"
                                    class="mt-2 w-full rounded-lg bg-orange-500 px-3 py-1.5 text-xs font-medium text-white hover:bg-orange-600 disabled:cursor-not-allowed disabled:opacity-60"
                                    :disabled="savingFinal"
                                    @click="saveFinalAmount"
                                >
                                    {{ savingFinal ? t('common.saving') : t('common.save') }}
                                </button>
                            </div>
                            <div
                                v-if="
                                    booking.base_amount != null &&
                                    booking.final_amount != null &&
                                    booking.base_amount !== booking.final_amount
                                "
                                class="mt-2 rounded-lg bg-gray-50 px-3 py-2 text-xs text-gray-700"
                            >
                                <p>
                                    {{ t('bookings.difference') }}:
                                    <span class="font-semibold">
                                        {{
                                            (booking.final_amount - booking.base_amount).toFixed(2)
                                        }}
                                        TMT
                                    </span>
                                </p>
                            </div>
                        </dl>
                    </section>

                    <!-- Status Timeline Card -->
                    <section class="rounded-2xl bg-white p-6 shadow-sm">
                        <h2 class="mb-4 text-sm font-semibold text-gray-800">
                            {{ t('bookings.status_timeline') }}
                        </h2>
                        <ol class="relative space-y-3 border-l border-gray-200 pl-4 text-sm">
                            <li
                                v-for="status in statuses"
                                :key="status"
                                class="ml-2"
                            >
                                <div class="flex items-center gap-3">
                                    <span
                                        class="h-2 w-2 shrink-0 rounded-full"
                                        :class="getStatusDotClass(status)"
                                    />
                                    <span
                                        :class="[
                                            booking.status === status
                                                ? 'font-semibold text-gray-900'
                                                : 'text-gray-600',
                                        ]"
                                    >
                                        {{ t('bookings.statuses.' + status) }}
                                    </span>
                                </div>
                            </li>
                        </ol>
                        <div
                            v-if="isCancelled"
                            class="mt-4 rounded-lg bg-red-50 px-3 py-2 text-xs text-red-700"
                        >
                            <p class="font-semibold">
                                {{ t('bookings.statuses.cancelled') }}
                            </p>
                            <p v-if="booking.cancel_reason" class="mt-1">
                                {{ booking.cancel_reason }}
                            </p>
                        </div>
                    </section>

                    <!-- Actions Card -->
                    <section class="rounded-2xl bg-white p-6 shadow-sm">
                        <h2 class="mb-4 text-sm font-semibold text-gray-800">
                            {{ t('bookings.actions') }}
                        </h2>
                        <div class="space-y-3 text-sm">
                            <div class="space-y-2">
                                <button
                                    v-if="booking.status === 'new'"
                                    type="button"
                                    class="flex w-full items-center justify-center rounded-lg bg-indigo-500 px-3 py-2 text-sm font-medium text-white hover:bg-indigo-600 disabled:cursor-not-allowed disabled:opacity-60"
                                    :disabled="changingStatus"
                                    @click="changeStatus('preliminary')"
                                >
                                    {{ t('bookings.to_preliminary') }}
                                </button>
                                <button
                                    v-else-if="booking.status === 'preliminary'"
                                    type="button"
                                    class="flex w-full items-center justify-center rounded-lg bg-emerald-500 px-3 py-2 text-sm font-medium text-white hover:bg-emerald-600 disabled:cursor-not-allowed disabled:opacity-60"
                                    :disabled="changingStatus"
                                    @click="changeStatus('confirmed')"
                                >
                                    {{ t('bookings.to_confirmed') }}
                                </button>
                                <button
                                    v-else-if="booking.status === 'confirmed'"
                                    type="button"
                                    class="flex w-full items-center justify-center rounded-lg bg-sky-500 px-3 py-2 text-sm font-medium text-white hover:bg-sky-600 disabled:cursor-not-allowed disabled:opacity-60"
                                    :disabled="changingStatus"
                                    @click="changeStatus('paid')"
                                >
                                    {{ t('bookings.to_paid') }}
                                </button>
                                <!-- Для paid/completed — специальных кнопок не показываем -->
                            </div>

                            <!-- Cancel section -->
                            <div
                                v-if="
                                    booking.status !== 'cancelled' &&
                                    booking.status !== 'completed'
                                "
                                class="space-y-2"
                            >
                                <label class="block text-xs font-medium text-gray-700">
                                    {{ t('bookings.cancel_reason') }}
                                </label>
                                <textarea
                                    v-model="cancelReason"
                                    rows="2"
                                    class="w-full rounded-lg border border-gray-200 px-3 py-2 text-sm focus:border-red-300 focus:outline-none focus:ring-1 focus:ring-red-400/20"
                                    :placeholder="t('bookings.cancel_placeholder')"
                                />
                                <button
                                    type="button"
                                    class="flex w-full items-center justify-center rounded-lg bg-red-500 px-3 py-2 text-sm font-medium text-white hover:bg-red-600 disabled:cursor-not-allowed disabled:opacity-60"
                                    :disabled="changingStatus"
                                    @click="changeStatus('cancelled')"
                                >
                                    {{ t('bookings.cancel_btn') }}
                                </button>
                            </div>

                            <!-- Delete (admin only) -->
                            <button
                                v-if="isAdmin"
                                type="button"
                                class="mt-2 w-full text-xs font-medium text-red-600 hover:text-red-700"
                                @click="confirmDelete"
                            >
                                {{ t('bookings.delete_btn') }}
                            </button>
                        </div>
                    </section>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
