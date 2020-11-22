const { times } = require('lodash')

require('./bootstrap')

window.Vue = require('vue')

import VueRouter from 'vue-router'
Vue.use(VueRouter)

import AppComponent from './App.vue'
import HomeComponent from './pages/Home.vue'
import ProductsComponent from './pages/Products.vue'

import SideBarComponent from "./components/SideBar.vue"
import TopBarComponent from "./components/TopBar.vue"
import FooterComponent from "./components/Footer.vue"

Vue.component('sidebar', SideBarComponent)
Vue.component('topbar', TopBarComponent)
Vue.component('foot', FooterComponent)

const routes = [
    {path:'/', component:HomeComponent},
    {path:'/products', component:ProductsComponent},
    {path:'/example', component:AppComponent},
]

const router = new VueRouter({
    routes
})

const app = new Vue({
    el: '#app',
    router
})
