const { times } = require('lodash')

require('./bootstrap')

window.Vue = require('vue')

import VueRouter from 'vue-router'
import Vuex from 'vuex'
import VueSocketIO from "vue-socket.io"

Vue.use(VueRouter);
Vue.use(Vuex);

Vue.use(
    new VueSocketIO({
        debug: true,
        connection: "http://homestead.test:8080"
    })
)

import AppComponent from './App.vue'
import WelcomeComponent from './pages/Welcome.vue'

import DashboardComponent from './pages/Dashboard.vue'

import ProductsComponent from './pages/Products.vue'

import UsersComponent from './pages/Users.vue'

import LoginComponent from './pages/login.vue'
import SignUpComponent from './pages/signup.vue'

import EditUserComponent from './pages/editUser.vue'

const store = new Vuex.Store({
    state: {
        authenticated: false,
        user: null,
    },
    mutations: {
        signIn(state, user) {
            state.user = user;
            state.authenticated = true;

            this._vm.$socket.emit('user_logged', state.user);
        },
        signOut(state) {
            this._vm.$socket.emit('user_logged_out', state.user);
            state.user = null;
            state.authenticated = false;
        },
        update(state, user){
            state.user = user;
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
            { path: 'edit-profile', component: EditUserComponent },
            { path: 'users', component: UsersComponent },
        ]
    },
]

const router = new VueRouter({
    routes
})

const app = new Vue({
    el: '#app',
    router,
    store,
})
