import "./bootstrap";
import { createApp } from "vue";
import App from "./App.vue";
import vuetify from "../plugins/vuetify";
import router from "../router";
import VueApexCharts from "vue3-apexcharts";
import { QuillEditor } from "@vueup/vue-quill";

const app = createApp(App).use(router).use(vuetify);
app.use(VueApexCharts);
// app.component("QuillEditor", QuillEditor);
app.mount("#app");
