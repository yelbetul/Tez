import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from "pinia";
import { FontAwesomeIcon } from '@fortawesome/vue-fontawesome'
import VueApexCharts from 'vue3-apexcharts'

import App from './App.vue'
import router from './router'

const app = createApp(App)

app.use(createPinia());
app.use(VueApexCharts)

app.component('font-awesome-icon', FontAwesomeIcon)

app.use(router)

app.mount('#app')
