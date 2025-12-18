import './bootstrap';
import '../css/app.css';
import '../css/custom.css';
import './styles/global.css';
import 'remixicon/fonts/remixicon.css';

import { createApp } from 'vue';
import router from './router';
import App from './App.vue';

createApp(App)
    .use(router)
    .mount('#app');

