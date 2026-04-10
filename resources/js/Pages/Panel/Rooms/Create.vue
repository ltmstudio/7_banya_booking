<script setup>
/**
 * Форма создания комнаты: название/описание по языкам, категория, цены, фото.
 */
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();


const activeTab = ref('ru');

const form = useForm({
    name: { ru: '', tk: '' },
    description: { ru: '', tk: '' },
    category: 'standard',
    price_per_hour: '',
    promo_price_per_hour: '',
    child_price_per_hour: '',
    cleaning_buffer_minutes: null,
    capacity: '',
    is_active: true,
    is_walk_in: false,
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
            const url = ev.target?.result;
            photoPreviews.value.push(url);
        };
        reader.readAsDataURL(f);
    }
    input.value = '';
};

const removePhoto = (index) => {
    form.photos.splice(index, 1);
    photoPreviews.value.splice(index, 1);
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
    form.post(route('panel.rooms.store'), { forceFormData: true });
};
</script>

<template>
    <DashboardLayout>
        <Head :title="t('rooms.create')" />
        <template #header>
            <h1 class="text-xl font-semibold text-gray-800">{{ t('rooms.create') }}</h1>
        </template>

        <div class="max-w-2xl mx-auto mt-8 px-4 pb-12">
            <form
                class="rounded-2xl border border-gray-200 bg-white p-8 shadow-md"
                @submit.prevent="submit"
            >
                <!-- Section 1: Basic info -->
                <div class="mb-8">
                    <p class="mb-4 text-xs font-medium uppercase tracking-wider text-gray-500">{{ t('rooms.name') }} / {{ t('rooms.description') }}</p>
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
                            <label class="mb-2 block text-sm font-medium text-gray-700">{{ t('rooms.name') }} <span class="text-orange-500">*</span></label>
                            <input
                                v-model="form.name[activeTab]"
                                type="text"
                                maxlength="150"
                                :placeholder="t('rooms.name_placeholder')"
                                class="input-field"
                            />
                            <p v-if="form.errors[`name.${activeTab}`]" class="mt-1 text-xs text-red-500">{{ form.errors[`name.${activeTab}`] }}</p>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">{{ t('rooms.description') }}</label>
                            <textarea
                                v-model="form.description[activeTab]"
                                rows="3"
                                :placeholder="t('rooms.description_placeholder')"
                                class="input-field"
                            />
                            <p v-if="form.errors[`description.${activeTab}`]" class="mt-1 text-xs text-red-500">{{ form.errors[`description.${activeTab}`] }}</p>
                        </div>
                    </div>
                </div>

                <!-- Section 2: Category & prices -->
                <div class="mb-8">
                    <p class="mb-3 text-xs font-medium uppercase tracking-wider text-gray-500">{{ t('rooms.category') }}</p>
                    <div class="mb-6 flex gap-3">
                        <button
                            v-for="opt in categoryOptions"
                            :key="opt.value"
                            type="button"
                            :class="[
                                'flex flex-1 flex-col items-center gap-2 rounded-xl border-2 px-4 py-3 text-sm transition',
                                form.category === opt.value
                                    ? 'border-orange-500 bg-orange-50 text-orange-700'
                                    : 'border-gray-200 hover:border-gray-300',
                            ]"
                            @click="form.category = opt.value"
                        >
                            <span class="text-2xl">{{ opt.icon }}</span>
                            <span>{{ t(opt.labelKey) }}</span>
                        </button>
                    </div>
                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">
                            {{ t('rooms.capacity') }}
                        </label>
                        <input
                            v-model="form.capacity"
                            type="number"
                            min="1"
                            max="50"
                            :placeholder="t('rooms.capacity_placeholder')"
                            class="input-field"
                        />
                        <p class="mt-1 text-xs text-gray-500">
                            {{ t('rooms.capacity_hint') }}
                        </p>
                    </div>
                    <div class="grid gap-4 sm:grid-cols-2">
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                {{ t('rooms.price_per_hour') }} ({{ t('rooms.tmt_per_hour') }}) <span class="text-orange-500">*</span>
                            </label>
                            <input
                                v-model="form.price_per_hour"
                                type="number"
                                step="0.01"
                                min="0"
                                required
                                :placeholder="t('rooms.price_placeholder')"
                                class="input-field"
                            />
                            <p v-if="form.errors.price_per_hour" class="mt-1 text-xs text-red-500">
                                {{ form.errors.price_per_hour }}
                            </p>
                        </div>
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                {{ t('rooms.promo_price_per_hour') }} ({{ t('rooms.tmt_per_hour') }})
                            </label>
                            <input
                                v-model="form.promo_price_per_hour"
                                type="number"
                                step="0.01"
                                min="0"
                                :placeholder="t('rooms.price_placeholder')"
                                class="input-field"
                            />
                            <p class="mt-1 text-xs text-gray-500">
                                {{ t('rooms.promo_hint') }}
                            </p>
                            <p v-if="form.errors.promo_price_per_hour" class="mt-1 text-xs text-red-500">
                                {{ form.errors.promo_price_per_hour }}
                            </p>
                        </div>
                        <!-- Child price -->
                        <div>
                            <label class="mb-2 block text-sm font-medium text-gray-700">
                                {{ t('rooms.child_price_per_hour') }} ({{ t('rooms.tmt_per_hour') }})
                            </label>
                            <input
                                v-model="form.child_price_per_hour"
                                type="number"
                                step="0.01"
                                min="0"
                                :placeholder="t('rooms.price_placeholder')"
                                class="input-field"
                            />
                            <p class="mt-1 text-xs text-gray-500">
                                {{ t('rooms.child_price_hint') }}
                            </p>
                        </div>
                        <!-- Cleaning buffer -->
                        <div>
                            <label for="cleaning_buffer_minutes" class="mb-2 block text-sm font-medium text-gray-700">
                                {{ t('rooms.cleaning_buffer_minutes') }}
                            </label>
                            <select
                                id="cleaning_buffer_minutes"
                                v-model="form.cleaning_buffer_minutes"
                                class="input-field"
                            >
                                <option :value="null">
                                    {{ t('rooms.buffer_global') }}
                                </option>
                                <option :value="15">{{ t('rooms.buffer_15') }}</option>
                                <option :value="30">{{ t('rooms.buffer_30') }}</option>
                                <option :value="60">{{ t('rooms.buffer_60') }}</option>
                                <option :value="120">{{ t('rooms.buffer_120') }}</option>
                            </select>
                            <p class="mt-1 text-xs text-gray-500">
                                {{ t('rooms.buffer_hint') }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Section 3: Photos -->
                <div class="mb-8">
                    <p class="mb-3 text-xs font-medium uppercase tracking-wider text-gray-500">{{ t('rooms.photos') }}</p>
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
                        <p class="text-sm text-gray-500">{{ t('rooms.photos_drop') }}</p>
                    </div>
                    <div v-if="photoPreviews.length" class="mt-4 grid grid-cols-3 gap-3">
                        <div
                            v-for="(url, i) in photoPreviews"
                            :key="i"
                            class="group relative aspect-square overflow-hidden rounded-lg bg-gray-100"
                        >
                            <img :src="url" alt="" class="h-full w-full object-cover" />
                            <button
                                type="button"
                                class="absolute right-2 top-2 rounded-full bg-red-500 p-1 text-white opacity-0 transition group-hover:opacity-100"
                                @click.stop="removePhoto(i)"
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
                        <span class="text-sm font-medium text-gray-700">{{ t('rooms.is_active') }}</span>
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
                            <p class="text-sm font-medium text-gray-700">{{ t('rooms.is_walk_in') }}</p>
                            <p class="mt-0.5 text-xs text-gray-500">{{ t('rooms.is_walk_in_hint') }}</p>
                        </div>
                    </label>

                    <div
                        v-if="form.is_walk_in"
                        class="flex items-start gap-2 rounded-xl border border-blue-200 bg-blue-50 px-4 py-3 dark:border-blue-800 dark:bg-blue-900/20"
                    >
                        <span class="flex-shrink-0 text-blue-500">ℹ️</span>
                        <p class="text-xs text-blue-700 dark:text-blue-300">{{ t('rooms.is_walk_in_warning') }}</p>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="flex flex-wrap items-center justify-between gap-4 border-t border-gray-200 pt-8">
                    <Link :href="route('panel.rooms.index')" class="text-sm text-gray-600 hover:text-orange-600">
                        {{ t('rooms.cancel') }}
                    </Link>
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-lg bg-orange-500 px-6 py-2.5 text-sm font-medium text-white shadow-sm transition hover:bg-orange-600 disabled:opacity-50"
                    >
                        {{ form.processing ? t('common.saving') : t('rooms.save') }}
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
