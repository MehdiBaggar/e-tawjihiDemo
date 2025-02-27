

import { createApp } from 'vue';
import App from './vue/views/index.vue';
import router from './vue/router';
import './scss/config/interactive/app.scss'
import './scss/mermaid.min.css'



// Create the Vue app
const app = createApp(App);
app.use(router);


// Mount the app
app.mount('#vue');