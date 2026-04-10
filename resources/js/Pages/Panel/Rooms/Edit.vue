<script setup>
/**
 * Форма редактирования комнаты. Существующие фото с кнопкой удаления, добавление новых.
 */
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const props = defineProps({
    editingRoom: { type: Object, required: true },
});

const { t: tI18n } = useI18n();
const t = computed(() => usePage().props.room || {});
const activeTab = ref('ru');

const form = useForm({
    name: props.editingRoom.name || { ru: '', tk: '' },
    description: props.editingRoom.description || { ru: '', tk: '' },
    category: props.editingRoom.category || 'standard',
    price_per_hour: props.editingRoom.price_per_hour ?? '',
    promo_price_per_hour: props.editingRoom.promo_price_per_hour ?? '',
    child_price_per_hour: props.editingRoom.child_price_per_hour ?? '',
    cleaning_buffer_minutes: props.editingRoom.cleaning_buffer_minutes ?? null,
    capacity: props.editingRoom.capacity ?? '',
    is_active: props.editingRoom.is_active ?? true,
    is_walk_in: props.editingRoom.is_walk_in ?? false,
    photos: [],
});

const categoryOptions = [
    { value: 'standard', labelKey: 'rooms.category_standard', icon: '🏠' },
    { value: 'family', labelKey: 'rooms.category_family', icon: '👨‍👩‍👧' },
    { value: 'vip', labelKey: 'rooms.category_vip', icon: '⭐' },
];

const photoPreviews = ref([]);
const photoInput = ref(null);

const addPhotos = (e) => {
    const input = e.target;
    const files = input?.files;
    if (!files?.length) return;
    for (let i = 0; i < files.length; i++) {
        const f = files[i];
        form.photos.push(f);
        const reader = new FileReader();
        reader.onload = (ev) => {
            photoPreviews.value.push(ev.target?.result);
        };
        reader.readAsDataURL(f);
    }
    input.value = '';
};

const removeNewPhoto = (index) => {
    form.photos.splice(index, 1);
    photoPreviews.value.splice(index, 1);
};

const deleteExistingPhoto = (photo) => {
    if (!confirm(tI18n('rooms.delete_photo_confirm'))) return;
    router.delete(route('panel.rooms.photos.destroy', { room: props.editingRoom.id, photo: photo.id }));
};

const onDrop = (e) => {
    e.preventDefault();
    const files = e.dataTransfer?.files;
    if (!files?.length) return;
    for (let i = 0; i < files.length; i++) {
        const f = files[i];
        if (!/^image\/(jpeg|jpg|png|webp)$/i.test(f.type)) continue;
        form.photos.push(f);
        const reader = new FileReader();
        reader.onload = (ev) => {
            photoPreviews.value.push(ev.target?.result);
        };
        reader.readAsDataURL(f);
    }
};

const onDragover = (e) => e.preventDefault();

const submit = () => {
    form
        .transform((data) => ({ ...data, _method: 'PUT' }))
        .post(route('panel.rooms.update', props.editingRoom.id), {
            forceFormData: true,
        });
};
</script>

<template>
    <DashboardLayout>
        <Head :title="t.edit" />
        <template #header>
            <div class="flex items-center justify-between gap-4">
                <h1 class="text-xl font-semibold text-gray-800">{{ t.edit }}</h1>
                <Link :href="route('panel.rooms.index')" class="text-sm text-gray-500 hover:text-orange-600">← {{ tI18n('common.cancel') }}</Link>
            </div>
        </template>

        <div class="max-w-2xl mx-auto mt-8 px-4 pb-12">
            <form class="rounded-2xl border border-gray-200 bg-white p-8 shadow-md" @submit.prevent="submit">
                <!-- Section 1: Basic info -->
                <div class="mb-8">
                    <p class="mb-4 text-xs font-medium uppercase tracking-wider text-gray-500">{{ t.name }} / {{ t.description }}</p>
                    <div class="mb-4 flex gap-2">
                        <button
                            type="button"
                            :class="['rounded-lg px-4 py-2 text-sm font-medium', activeTab === 'ru' ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200']"
                            @click="activeTab = 'ru'"
                        >
                            RU
                        </button>
                        <button
                            type="button"
                            :class="['rounded-lg px-4 py-2 text-sm font-medium', activeTab === 'tk' ? 'bg-orange-500 text-white' : 'bg-gray-100 text-gray-600 hover:bg-gray-200']"
                            @click="activeTab = 'tk'"
                        >
                            TK
                        </button>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">{{ t.name }}</label>
                            <input
                                v-model="form.name[activeTab]"
                                type="text"
                                maxlength="150"
                                :placeholder="tI18n('rooms.name_placeholder')"
                                class="input-field"
                            />
                            <p v-if="form.errors[`name.${activeTab}`]" class="mt-1 text-xs text-red-500">{{ form.errors[`name.${activeTab}`] }}</p>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">{{ t.description }}</label>
                            <textarea
                                v-model="form.description[activeTab]"
                                rows="3"
                                :placeholder="tI18n('rooms.description_placeholder')"
                                class="input-field"
                            />
                            <p v-if="form.errors[`description.${activeTab}`]" class="mt-1 text-xs text-red-500">{{ form.errors[`description.${activeTab}`] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Category & prices -->
                <div class="mb-8">
                    <p class="mb-3 text-xs font-medium uppercase tracking-wider text-gray-500">{{ t.category }}</p>
                    <div class="mb-6 flex gap-3">
                        <button
                            v-for="opt in categoryOptions"
                            :key="opt.value"
                            type="button"
                            :class="[
                                'flex flex-1 flex-col items-center gap-2 rounded-xl border-2 px-4 py-3 text-sm transition',
                                form.category === opt.value ? 'border-orange-500 bg-orange-50 text-orange-700' : 'border-gray-200 hover:border-gray-300',
                            ]"
                            @click="form.category = opt.value"
                        >
                            <span class="text-2xl">{{ opt.icon }}</span>
                            <span>{{ tI18n(opt.labelKey) }}</span>
                        </button>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">
                            {{ tI18n('rooms.capacity') }}
                        </label>
                        <input
                            v-model="form.capacity"
                            type="number"
                            min="1"
                            max="50"
                            :placeholder="tI18n('rooms.capacity_placeholder')"
                            class="input-field"
                        />
                        <p class="mt-1 text-xs text-gray-500">
                            {{ tI18n('rooms.capacity_hint') }}
                        </p>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                {{ t.price_per_hour }} ({{ tI18n('rooms.tmt_per_hour') }}) <span class="text-orange-500">*</span>
                            </label>
                            <input
                                v-model="form.price_per_hour"
                                type="number"
                                step="0.01"
                                min="0"
                                required
                                :placeholder="tI18n('rooms.price_placeholder')"
                                class="input-field"
                            />
                            <p v-if="form.errors.price_per_hour" class="mt-1 text-xs text-red-500">
                                {{ form.errors.price_per_hour }}
                            </p>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                {{ t.promo_price_per_hour }} ({{ tI18n('rooms.tmt_per_hour') }})
                            </label>
                            <input
                                v-model="form.promo_price_per_hour"
                                type="number"
                                step="0.01"
                                min="0"
                                :placeholder="tI18n('rooms.price_placeholder')"
                                class="input-field"
                            />
                            <p class="mt-1 text-xs text-gray-500">
                                {{ t.promo_hint }}
                            </p>
                            <p v-if="form.errors.promo_price_per_hour" class="mt-1 text-xs text-red-500">
                                {{ form.errors.promo_price_per_hour }}
                            </p>
                        </div>
                        <!-- Child price -->
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                {{ t.child_price_per_hour }} ({{ tI18n('rooms.tmt_per_hour') }})
                            </label>
                            <input
                                v-model="form.child_price_per_hour"
                                type="number"
                                step="0.01"
                                min="0"
                                :placeholder="tI18n('rooms.price_placeholder')"
                                class="input-field"
                            />
                            <p class="mt-1 text-xs text-gray-500">
                                {{ t.child_price_hint }}
                            </p>
                        </div>
                        <!-- Cleaning buffer -->
                        <div>
                            <label for="cleaning_buffer_minutes" class="mb-2 block text-sm font-medium text-gray-700">
                                {{ tI18n('rooms.cleaning_buffer_minutes') }}
                            </label>
                            <select
                                id="cleaning_buffer_minutes"
                                v-model="form.cleaning_buffer_minutes"
                                class="input-field"
                            >
                                <option :value="null">
                                    {{ tI18n('rooms.buffer_global') }}
                                </option>
                                <option :value="15">{{ tI18n('rooms.buffer_15') }}</option>
                                <option :value="30">{{ tI18n('rooms.buffer_30') }}</option>
                                <option :value="60">{{ tI18n('rooms.buffer_60') }}</option>
                                <option :value="120">{{ tI18n('rooms.buffer_120') }}</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">
                                {{ tI18n('rooms.buffer_hint') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Photos -->
                <div class="mb-8">
                    <p class="mb-3 text-xs font-medium uppercase tracking-wider text-gray-500">{{ t.photos }}</p>
                    <!-- Existing photos -->
                    <div v-if="editingRoom.photos?.length" class="mb-4 grid grid-cols-3 gap-3">
                        <div
                            v-for="photo in editingRoom.photos"
                            :key="photo.id"
                            class="group relative aspect-square overflow-hidden rounded-lg bg-gray-100"
                        >
                            <img :src="photo.url" alt="" class="h-full w-full object-cover" />
                            <button
                                type="button"
                                class="absolute right-2 top-2 rounded-full bg-red-500 p-1 text-white opacity-0 transition group-hover:opacity-100"
                                @click="deleteExistingPhoto(photo)"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                    <!-- Upload zone -->
                    <div
                        class="rounded-xl border-2 border-dashed border-gray-200 bg-gray-50/50 p-8 text-center transition hover:border-orange-300"
                        @drop="onDrop"
                        @dragover="onDragover"
                        @click="photoInput?.click()"
                    >
                        <input
                            ref="photoInput"
                            type="file"
                            accept="image/jpeg,image/jpg,image/png,image/webp"
                            multiple
                            class="hidden"
                            @change="addPhotos"
                        />
                        <p class="text-sm text-gray-500">{{ t.photos_drop }}</p>
                    </div>
                    <!-- New photo previews -->
                    <div v-if="photoPreviews.length" class="mt-4 grid grid-cols-3 gap-3">
                        <div
                            v-for="(url, i) in photoPreviews"
                            :key="'new-' + i"
                            class="group relative aspect-square overflow-hidden rounded-lg bg-gray-100"
                        >
                            <img :src="url" alt="" class="h-full w-full object-cover" />
                            <button
                                type="button"
                                class="absolute right-2 top-2 rounded-full bg-red-500 p-1 text-white opacity-0 transition group-hover:opacity-100"
                                @click.stop="removeNewPhoto(i)"
                            >
                                <svg class="h-4 w-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                                </svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Section 4: Статус и режим «без бронирования». -->
                <div class="mb-8 space-y-4">
                    <label class="flex items-center gap-3">
                        <button
                            type="button"
                            role="switch"
                            :aria-checked="form.is_active"
                            :class="[
                                'relative inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors',
                                form.is_active ? 'bg-green-500' : 'bg-gray-200',
                            ]"
                            @click="form.is_active = !form.is_active"
                        >
                            <span
                                :class="[
                                    'inline-block h-5 w-5 transform rounded-full bg-white shadow transition',
                                    form.is_active ? 'translate-x-5' : 'translate-x-1',
                                ]"
                            />
                        </button>
                        <span class="text-sm font-medium text-gray-700">{{ t.is_active }}</span>
                    </label>

                    <label class="flex cursor-pointer items-start gap-3">
                        <button
                            type="button"
                            role="switch"
                            :aria-checked="form.is_walk_in"
                            :class="[
                                'relative mt-0.5 inline-flex h-6 w-11 shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors',
                                form.is_walk_in ? 'bg-blue-500' : 'bg-gray-200',
                            ]"
                            @click="form.is_walk_in = !form.is_walk_in"
                        >
                            <span
                                :class="[
                                    'inline-block h-5 w-5 transform rounded-full bg-white shadow transition',
                                    form.is_walk_in ? 'translate-x-5' : 'translate-x-1',
                                ]"
                            />
                        </button>
                        <div>
                            <p class="text-sm font-medium text-gray-700">{{ tI18n('rooms.is_walk_in') }}</p>
                            <p class="mt-0.5 text-xs text-gray-500">{{ tI18n('rooms.is_walk_in_hint') }}</p>
                        </div>
                    </label>

                    <div
                        v-if="form.is_walk_in"
                        class="flex items-start gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 dark:border-blue-800 dark:bg-blue-900/20"
                    >
                        <span class="flex-shrink-0 text-blue-500">ℹ️</span>
                        <p class="text-xs text-blue-700 dark:text-blue-300">{{ tI18n('rooms.is_walk_in_warning') }}</p>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex flex-wrap items-center justify-between gap-4 border-t border-gray-200 pt-8">
                    <Link :href="route('panel.rooms.index')" class="text-sm text-gray-600 hover:text-orange-600">{{ tI18n('common.cancel') }}</Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-lg bg-orange-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-orange-600 disabled:opacity-50"
                    >
                        {{ form.processing ? tI18n('common.saving') : t.save }}
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
