require('./bootstrap');
window.Vue = require('vue');

import App from './App.vue';
import VueAxios from 'vue-axios';
import axios from 'axios';
import Vuetify from "vuetify";
import VueJamIcons from 'vue-jam-icons';

Vue.use(VueAxios, axios);
Vue.use(Vuetify);
Vue.use(VueJamIcons);

const app = new Vue({
    el: '#app',
    vuetify : new Vuetify(),
    render: h => h(App),
});
