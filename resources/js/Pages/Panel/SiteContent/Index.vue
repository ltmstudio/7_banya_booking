<script setup>
/**
 * Страница управления двуязычным контентом секции «О нас».
 */
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { useForm } from '@inertiajs/vue3';
import { computed, ref } from 'vue';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const props = defineProps({
    contents: { type: Object, default: () => ({}) },
});

const activeTab = ref('ru');

const form = useForm({
    about_title: {
        ru: props.contents.about_title?.ru ?? '',
        tk: props.contents.about_title?.tk ?? '',
    },
    about: {
        ru: props.contents.about?.ru ?? '',
        tk: props.contents.about?.tk ?? '',
    },
});

/**
 * Текущая длина текста для активного языка.
 */
const currentAboutLength = computed(() => form.about[activeTab.value]?.length ?? 0);

/**
 * Сохраняет контент в БД.
 */
function submit() {
    form.put(route('panel.site-content.update'));
}
</script>

<template>
    <DashboardLayout>
        <template #header>
            <h1 class="text-xl font-semibold text-gray-800">
                {{ t('site_content.title') }}
            </h1>
        </template>

        <div class="mx-auto max-w-3xl px-4 pb-12 pt-8">
            <form class="space-y-6" @submit.prevent="submit">
                <div class="flex gap-2">
                    <button
                        v-for="lang in ['ru', 'tk']"
                        :key="lang"
                        type="button"
                        :class="[
                            'rounded-lg px-5 py-2 text-sm font-medium transition',
                            activeTab === lang
                                ? 'bg-orange-500 text-white'
                                : 'bg-gray-100 text-gray-600 hover:bg-gray-200',
                        ]"
                        @click="activeTab = lang"
                    >
                        {{ lang.toUpperCase() }}
                    </button>
                </div>

                <div class="rounded-2xl border border-gray-100 bg-white p-6 shadow-sm">
                    <h2 class="mb-4 text-sm font-semibold uppercase tracking-wider text-gray-500">
                        {{ t('site_content.about_section') }}
                    </h2>

                    <div class="mb-4">
                        <label class="mb-2 block text-sm font-medium text-gray-700">
                            {{ t('site_content.about_title') }}
                        </label>
                        <input
                            v-model="form.about_title[activeTab]"
                            type="text"
                            maxlength="255"
                            class="w-full rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-orange-300 focus:outline-none focus:ring-2 focus:ring-orange-400/20"
                        >
                        <p v-if="form.errors[`about_title.${activeTab}`]" class="mt-1 text-xs text-red-500">
                            {{ form.errors[`about_title.${activeTab}`] }}
                        </p>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-medium text-gray-700">
                            {{ t('site_content.about_text') }}
                        </label>
                        <textarea
                            v-model="form.about[activeTab]"
                            rows="6"
                            maxlength="2000"
                            class="w-full resize-none rounded-xl border border-gray-200 px-4 py-2.5 text-sm focus:border-orange-300 focus:outline-none focus:ring-2 focus:ring-orange-400/20"
                        />
                        <div class="mt-1 flex justify-between">
                            <p v-if="form.errors[`about.${activeTab}`]" class="text-xs text-red-500">
                                {{ form.errors[`about.${activeTab}`] }}
                            </p>
                            <p class="ml-auto text-xs text-gray-400">
                                {{ currentAboutLength }} / 2000
                            </p>
                        </div>
                    </div>
                </div>

                <div class="rounded-2xl border border-gray-100 bg-gray-50 p-6">
                    <p class="mb-3 text-xs font-medium uppercase tracking-wider text-gray-400">
                        {{ t('site_content.preview') }}
                    </p>
                    <h3 class="mb-2 text-xl font-bold text-gray-900">
                        {{ form.about_title[activeTab] || '...' }}
                    </h3>
                    <p class="whitespace-pre-line text-sm leading-relaxed text-gray-600">
                        {{ form.about[activeTab] || '...' }}
                    </p>
                </div>

                <div class="flex justify-end">
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="rounded-xl bg-orange-500 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-orange-600 disabled:opacity-50"
                    >
                        {{ form.processing ? '...' : t('settings.save') }}
                    </button>
                </div>
            </form>
        </div>
    </DashboardLayout>
</template>
