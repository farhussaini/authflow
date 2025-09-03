import './bootstrap';
import { createApp } from "vue";
import { createVuetify } from "vuetify";
import "vuetify/styles"; // Vuetify core styles
import "@mdi/font/css/materialdesignicons.css"; // Material icons

import App from "./components/App.vue";
import router from "./router";

const vuetify = createVuetify();

createApp(App)
    .use(router)
    .use(vuetify)
    .mount("#app");