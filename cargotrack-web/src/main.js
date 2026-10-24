import { createApp } from 'vue'
import { createPinia } from 'pinia'
import './style.css'
import App from './App.vue'
import router from './router'

const app = createApp(App)
//createApp(App).mount('#app')

app.use(createPinia())
app.use(router)

app.mount('#app')