import './styles/app.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap';

import Vue from 'vue'
import VueRouter from 'vue-router'

const routes = [
    { path: '/', component: require('./components/Games.vue').default, name: 'home' },
    { path: '/games/:gameId', component: require('./components/Army.vue').default, name: 'add-army' },
    { path: '/games/:gameId/battle', component: require('./components/Battle.vue').default, name: 'battle' },
]

const router = new VueRouter({
    routes
})

// VueRouter init
Vue.use(VueRouter)

const app = new Vue({
    router
}).$mount('#app')