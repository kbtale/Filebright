import { createApp } from 'vue'
import PrimeVue from 'primevue/config'
import App from './App.vue'
import router from './router'
import './assets/main.scss'

const app = createApp(App)
app.use(router)
app.use(PrimeVue, { unstyled: true })
app.mount('#app')
