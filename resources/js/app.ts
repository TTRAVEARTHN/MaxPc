import './bootstrap';

// это главный файл входа для вашего приложения Vue.js, подключающий основной компонент App.vue
import { createApp } from 'vue';
import App from './App.vue';

import "./ui/dropdown";
import "./ui/mobileMenu";
import "./validation/register";
import "./validation/login";
import "./catalog/categoryFilter";
import "./cart/cartAjax";
import "./cart/cartCounter";
import "./cart/cartAfterActions";
import "./compare/compareAjax"
import "./favorites/favoritesAjax";


// only mount App.vue if there's a #app element in the page
if (document.getElementById('app')) {
    createApp(App).mount('#app');
}


