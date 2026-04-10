/**
 * Синхронизирует локаль vue-i18n с Inertia page.props.locale при смене языка (TK/RU).
 * Вызывать на лендинге, чтобы t('landing.*') обновлялся после переключения.
 */
import { watch } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { useI18n } from 'vue-i18n';

export function useSyncLocale() {
    const page = usePage();
    const { locale } = useI18n();

    watch(
        () => page.props.locale,
        (newLocale) => {
            if (newLocale) locale.value = newLocale;
        },
        { immediate: true }
    );
}
