import Vue from 'vue'
import Apps from './App.vue'
import router from './router'
import 'quasar/dist/quasar.min.css'
import 'quasar/dist/quasar.umd'
import infiniteScroll from "vue-infinite-scroll";
import infiniteloading from "vue-infinite-loading";
// import socketio from 'socket.io-client';
 
import VueSocketIO from 'vue-socket.io';
 
Vue.use(VueSocketIO, 'http://10.10.10.38:3000');

 
Vue.use(infiniteloading);
Vue.use(infiniteScroll);
Vue.config.productionTip = false;
new Vue(
    {
        el: '#app',router,render: h => h(Apps)
    }
    );
