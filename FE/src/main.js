import './assets/main.css'

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import PrimeVue from 'primevue/config';
import 'primevue/resources/themes/aura-light-purple/theme.css'
import 'primevue/resources/primevue.min.css'
import 'primeicons/primeicons.css'
import ToastService from 'primevue/toastservice';
import Tooltip from 'primevue/tooltip';
import i18n from "./i18n.js";
import ConfirmationService from 'primevue/confirmationservice';

const app = createApp(App)

app.config.devtools = false
app.config.debug = false
app.config.silent = true
app.config.warnHandler = (msg, instance, trace) => {}

app.use(PrimeVue);
app.directive('tooltip', Tooltip);
app.use(router);
app.use(ToastService);
app.use(i18n);
app.use(ConfirmationService);
app.mount('#app')
