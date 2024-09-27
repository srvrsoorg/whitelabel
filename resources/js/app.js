import "./bootstrap";
import { createApp, defineAsyncComponent } from "vue";
import App from "@/App.vue";
import VTooltip from "v-tooltip";
import Router from "@/router";
import Toast, { POSITION } from "vue-toastification";
import { ColorPicker } from "vue3-colorpicker";
import { createPinia } from "pinia";
import piniaPersist from "pinia-plugin-persist";
import PerfectScrollbar from "vue3-perfect-scrollbar";
import vueCountryRegionSelect from "vue3-country-region-select";
import VueGtag from "vue-gtag";

import { registerSW } from "virtual:pwa-register";

const updateSW = registerSW({
    immediate: true,
    onRegistered(r) {
        r &&
            setInterval(() => {
                r.update();
            }, 60000);
    },
});

import $ from "jquery";
window.$ = window.jQuery = $;

import axios from "@/plugins/axios";
import VueApexCharts from "vue3-apexcharts";

// Import mixins globally
import GlobalMixin from "@/mixins/global";
import color from "@/mixins/color";
import ErrorMixin from "@/mixins/validationError";

const Button = defineAsyncComponent(() =>
    import("@/components/uiElements/Button.vue")
);
const PasswordVisibility = defineAsyncComponent(() =>
    import("@/components/uiElements/PasswordVisibility.vue")
);
const Badge = defineAsyncComponent(() =>
    import("@/components/uiElements/Badge.vue")
);
const Skeleton = defineAsyncComponent(() =>
    import("@/components/uiElements/Skeleton.vue")
);
const Confirmation = defineAsyncComponent(() =>
    import("@/components/uiElements/ConfirmationModal.vue")
);
const Modal = defineAsyncComponent(() =>
    import("@/components/uiElements/Modal.vue")
);
const Table = defineAsyncComponent(() =>
    import("@/components/uiElements/Table.vue")
);
const TableSkeleton = defineAsyncComponent(() =>
    import("@/components/uiElements/TableSkeleton.vue")
);
const Pagination = defineAsyncComponent(() =>
    import("@/components/uiElements/Pagination.vue")
);

const Perpage = defineAsyncComponent(() =>
    import("@/components/uiElements/Perpage.vue")
);
const Breadcrumb = defineAsyncComponent(() =>
    import("@/components/uiElements/Breadcrumb.vue")
);

import { createI18n } from 'vue-i18n'
const i18n = createI18n({
    legacy: false,
    globalInjection: true,
    locale: window.siteSettings?.locale ?? 'en-US',
    fallbackLocale: 'en-US',
    numberFormats: {
        [window.siteSettings?.locale ?? 'en-US']: {
            currency: {
                style: 'currency',
                currency: window.siteSettings?.currency ?? 'USD',
                currencyDisplay: 'code',
                minimumFractionDigits: 3,
                maximumFractionDigits: 3
            },
        }
  },
})

const app = createApp(App);

app.use(i18n)

app.mixin(GlobalMixin);
app.mixin(ErrorMixin);
app.mixin(color);

import debounce from "lodash/debounce";
app.config.globalProperties.$debounce = debounce;

import VueProgressBar from "@aacassandra/vue3-progressbar";

app.component("Button", Button);
app.component("PasswordVisibility", PasswordVisibility);
app.component("Badge", Badge);
app.component("Skeleton", Skeleton);
app.component("Breadcrumb", Breadcrumb);
app.component("Modal", Modal);
app.component("Confirmation", Confirmation);
app.component("Table", Table);
app.component("TableSkeleton", TableSkeleton);
app.component("Pagination", Pagination);
app.component("Perpage", Perpage);

// Create Pinia store
const pinia = createPinia();
pinia.use(piniaPersist);

// Use Pinia for state management
app.use(pinia);

app.use(Router);
app.use(vueCountryRegionSelect);
app.use(PerfectScrollbar);

// Configure axios
app.config.globalProperties.$axios = axios;

// Use Apexcharts for charts
app.use(VueApexCharts);

if (
    window.siteSettings &&
    (window.siteSettings.analytics || window.siteSettings.analytics != "null")
) {
    app.use(VueGtag, {
        config: {
            id: window.siteSettings.analytics,
            params: {
                send_page_view: false, // disable automatic page view tracking
            },
        },
    });
}

app.use(VueProgressBar, {
    thickness: "3px",
});

app.use(ColorPicker);

// Use Toastification for toast messages
import toast from "@/plugins/toast-notification";
app.config.globalProperties.$toast = toast;
app.use(Toast, {
    position: POSITION.TOP_RIGHT,
});

import { LoadingPlugin } from "vue-loading-overlay";
import "vue-loading-overlay/dist/css/index.css";
app.use(LoadingPlugin, {
    color: "#ffffff",
    zIndex: 99999,
    opacity: 0.6,
    backgroundColor: "#000000",
});

//Use VTooltip for tooltips
app.use(VTooltip, {
    defaultPlacement: "auto",
    disposeTimeout: 5000,
});

axios.interceptors.request.use((config) => {
    app.config.globalProperties.$Progress.start();
    return config;
});

axios.interceptors.response.use(
    (response) => {
        app.config.globalProperties.$Progress.finish();
        return response;
    },
    (error) => {
        app.config.globalProperties.$Progress.fail();
        return Promise.reject(error);
    }
);

app.mount("#app");
