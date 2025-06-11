import "../css/app.css";
import "./bootstrap";

import { createInertiaApp } from "@inertiajs/vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createApp, h } from "vue";
import { ZiggyVue } from "../../vendor/tightenco/ziggy";

import ToastService from "primevue/toastservice";
import ConfirmationService from "primevue/confirmationservice";
import FocusTrap from "primevue/focustrap";
import Tooltip from "primevue/tooltip";

import PrimeVue from "primevue/config";
import Aura from "@primeuix/themes/aura";

import Vue3ColorPicker from "vue3-colorpicker";
import "vue3-colorpicker/style.css";

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue"),
        ),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .use(PrimeVue, {
                theme: {
                    preset: Aura,
                    options: {
                        darkModeSelector: "none",
                    },
                    cssLayer: {
                        name: "primevue",
                        order: "theme, base, primevue",
                    },
                },
            })
            .use(ToastService)
            .use(ConfirmationService)
            .use(Vue3ColorPicker)
            .directive("tooltip", Tooltip)
            .directive("focustrap", FocusTrap)
            .mount(el);
    },
    progress: {
        color: "#4B5563",
    },
});
