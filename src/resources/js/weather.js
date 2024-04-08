// import { createApp, Vue } from 'vue'
import { createApp, h } from 'vue'
// import { createInertiaApp } from '@inertiajs/inertia-vue3';
// import { createInertiaApp } from '@inertiajs/'

import './bootstrap.js'
import '../scss/weather.scss'
import Index from './Pages/Weather/Index.vue'
// console.log('==== weather ====')

const appName = window.document.getElementsByTagName('title')[0]?.innerText || 'weather'

createInertiaApp({
    // title: (title) => `${title} - ${appName}`,
})

// var app = new Vue({
//     el: '#app',
//     data() {
//         return {
//             message: 'hello, VUe'
//         }
//     },
//     created() {
//         console.log('=========')
//     }
// })
