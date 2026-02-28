import { createApp } from 'vue'
import PrimeVue from 'primevue/config'
import App from './App.vue'
import router from './router'
import './assets/main.scss'

import { authStore } from './stores/authStore'

const app = createApp(App)
app.use(router)
app.use(PrimeVue, { unstyled: true })

// Initialize auth before mounting
authStore.initialize().then(() => {
  app.mount('#app')
})
