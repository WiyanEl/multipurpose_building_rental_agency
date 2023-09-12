// Style
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap/dist/js/bootstrap.min.js'
import 'bootstrap-icons/font/bootstrap-icons.css'
import './assets/css/dashboard.css'
import './assets/main.css'
import Toast from "vue-toastification"

import 'vue-toastification/dist/index.css'
import '@jobinsjp/vue3-datatable/dist/style.css'
import '@vuepic/vue-datepicker/dist/main.css'

import axios from 'axios'
axios.defaults.baseURL = 'http://127.0.0.1:8000/'

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'


const app = createApp(App)

app.use(Toast)

app.use(router)

app.mount('#app')
