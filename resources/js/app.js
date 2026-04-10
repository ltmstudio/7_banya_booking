import '../css/app.css';
import './bootstrap';


import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
import { createI18n } from 'vue-i18n';
import { createPinia } from 'pinia';
import ru from '../../lang/ru.json';
import tk from '../../lang/tk.json';
import './echo';

const savedLocale = typeof window !== 'undefined' ? localStorage.getItem('locale') || 'ru' : 'ru';

const i18n = createI18n({
    legacy: false,
    locale: savedLocale,
    fallbackLocale: 'ru',
    messages: { ru, tk },
});

const pinia = createPinia();

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(pinia)
            .use(i18n)
            .use(Echo)
            .mount(el);
    },
    progress: {
        color: '#4B5563',
    },
});
