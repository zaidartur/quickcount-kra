import '../css/app.css';
import './bootstrap';
import 'primeicons/primeicons.css';
import router from '@/Router/index';

import { createInertiaApp, Head, Link } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { createApp, h } from 'vue';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

// PrimeVue template 
// import '@css/styles.scss';
// import '@css/tailwind.css';

import Aura from '@primevue/themes/aura';
import PrimeVue from 'primevue/config';

import InputText from 'primevue/inputtext';
import Password from 'primevue/password';
import Button from 'primevue/button';
import Checkbox from 'primevue/checkbox';
import Toast from 'primevue/toast';
import ToastService from 'primevue/toastservice';
import ConfirmationService from 'primevue/confirmationservice';
import DialogService from 'primevue/dialogservice';
import BadgeDirective from 'primevue/badgedirective';
import Message from 'primevue/message';
import StyleClass from 'primevue/styleclass';
import Select from 'primevue/select';
import SelectButton from 'primevue/selectbutton';
import Timeline from 'primevue/timeline';
import Chart from 'primevue/chart';
import Tooltip from 'primevue/tooltip';
import Ripple from 'primevue/ripple';
import ConfirmDialog from 'primevue/confirmdialog';
import ConfirmPopup from 'primevue/confirmpopup';
import Dialog from 'primevue/dialog';
import AnimateOnScroll from 'primevue/animateonscroll';

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob('./Pages/**/*.vue'),
        ),
    setup({ el, App, props, plugin }) {
        // set site theme (light/dark mode)
        // const { initSiteTheme } = useTheme();
        // initSiteTheme();

        // start the app
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue, Ziggy)
            .use(PrimeVue, {
                theme: {
                    preset: Aura,
                    options: {
                        darkModeSelector: '.app-dark'
                    }
                },
                ripple: true,
            })
            .use(DialogService)
            .use(ToastService)
            .use(ConfirmationService)
            // .use(router)
            .directive('tooltip', Tooltip)
            .directive('badge', BadgeDirective)
            .directive('ripple', Ripple)
            .directive('styleclass', StyleClass)
            .directive('animateonscroll', AnimateOnScroll)

            .component('Toast', Toast)
            .component('ConfirmDialog', ConfirmDialog)
            .component('ConfirmPopup', ConfirmPopup)
            .component('InputText', InputText)
            .component('Password', Password)
            .component('Button', Button)
            .component('Checkbox', Checkbox)
            .component('Message', Message)
            .component('Select', Select)
            .component('SelectButton', SelectButton)
            .component('Timeline', Timeline)
            .component('Chart', Chart)
            .component('Dialog', Dialog)

            .component('Head', Head)
            .component('Link', Link)
            .mount(el);
    },
    progress: {
        // color: '#4B5563',
        color: 'var(--p-primary-500)',
    },
});
