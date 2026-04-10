<script setup>
/**
 * Подвал лендинга: адреса и контакты.
 */
import { useI18n } from 'vue-i18n';
import { computed } from 'vue';

const { t } = useI18n();

/** Email хранится без символа '@' (vue-i18n воспринимает его как linked-format). */
const emailValue = computed(() => String(t('landing.footer.email_value')).replace(' at ', '@'));
</script>

<template>
    <footer class="border-t border-stone-200 py-8 dark:border-stone-700">
        <div class="flex flex-col gap-4 text-center sm:flex-row sm:gap-6">
            <div class="flex-1 min-w-0 space-y-1 text-sm text-stone-600 dark:text-stone-400">
                <p class="font-medium text-stone-800 dark:text-stone-200">{{ t('landing.footer.address') }}</p>
                <p>{{ t('landing.footer.address_value') }}</p>
            </div>
            <div class="flex-1 min-w-0 space-y-1 text-sm text-stone-600 dark:text-stone-400">
                <p class="font-medium text-stone-800 dark:text-stone-200">{{ t('landing.footer.phone') }}</p>
                <a :href="`tel:${t('landing.footer.phone_value').replace(/\\s/g, '')}`" class="hover:text-amber-600 dark:hover:text-amber-400 transition-colors">
                    {{ t('landing.footer.phone_value') }}
                </a>
            </div>
            <div class="flex-1 min-w-0 space-y-1 text-sm text-stone-600 dark:text-stone-400">
                <p class="font-medium text-stone-800 dark:text-stone-200">{{ t('landing.footer.email') }}</p>
                <a :href="`mailto:${emailValue}`" class="hover:text-amber-600 dark:hover:text-amber-400 transition-colors">
                    {{ emailValue }}
                </a>
            </div>
        </div>
        <p class="mt-6 text-center text-xs text-stone-500 dark:text-stone-500">
            © {{ new Date().getFullYear() }} {{ t('landing.footer.copyright') }}
        </p>
    </footer>
</template>
