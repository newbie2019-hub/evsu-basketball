import { createApp } from "vue";
import { createPinia } from "pinia";

import App from "./App.vue";
import router from "./router";
import "./assets/main.css";

const app = createApp(App);

app.use(createPinia());
app.use(router);

/**
 *  MDI Icons
 */
import mdiVue from "mdi-vue/v3";
import * as mdijs from "@mdi/js";

app.use(mdiVue, {
  icons: mdijs,
});

/** Vue Toastification */
import Toast from "vue-toastification";
import "vue-toastification/dist/index.css";
import { toast } from "./config/toast";

app.use(Toast, toast);
app.mount("#app");
