import './styles/app.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';

import Vue from 'vue'
import VueRouter from 'vue-router'

const routes = [
    { path: '/', component: require('./components/Games.vue').default },
    { path: '/games/:gameId', component: require('./components/Army.vue').default, name: 'add-army' },
]

const router = new VueRouter({
    routes
})

// VueRouter init
Vue.use(VueRouter)

const app = new Vue({
    router
}).$mount('#app')