const { times } = require('lodash')

require('./bootstrap')

window.Vue = require('vue')

import VueRouter from 'vue-router'
import Vuex from 'vuex'

Vue.use(VueRouter)
Vue.use(Vuex)

import AppComponent from './App.vue'
import WelcomeComponent from './pages/Welcome.vue'

import DashboardComponent from './pages/Dashboard.vue'

import ProductsComponent from './pages/Products.vue'

import UsersComponent from './developer/Users.vue'

import LoginComponent from './pages/login.vue'
import SignUpComponent from './pages/signup.vue'

const store = new Vuex.Store({
    state: {
        authenticated: false,
        user: null,
    },
    mutations: {
        signIn(state, user) {
            state.user = user;
            state.authenticated = true;
        },
        signOut(state) {
            state.user = null;
            state.authenticated = false;
        },
    },
})

const routes = [
    {
        path: '/', component: AppComponent,
        children: [
            { path: 'welcome', component: WelcomeComponent },
            { path: 'dashboard', component: DashboardComponent },
            { path: 'products', component: ProductsComponent },
            { path: 'login', component: LoginComponent },
            { path: 'signup', component: SignUpComponent },
        ]
    },

    { path: '/dev/users', component: UsersComponent },
]

const router = new VueRouter({
    routes
})

const app = new Vue({
    el: '#app',
    router,
    store,
})
