require('./bootstrap');

window.Vue = require('vue')

import AppComponent from './App.vue'

Vue.component('app', AppComponent)

const app = new Vue({
    el: '#app'
})
