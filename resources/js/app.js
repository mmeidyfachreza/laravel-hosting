/**
 * First we will load all of this project's JavaScript dependencies which
 * includes Vue and other libraries. It is a great starting point when
 * building robust, powerful web applications using Vue and Laravel.
 */

require('./bootstrap');

window.Vue = require('vue');

/**
 * The following block of code may be used to automatically register your
 * Vue components. It will recursively scan this directory for the Vue
 * components and automatically register them with their "basename".
 *
 * Eg. ./components/ExampleComponent.vue -> <example-component></example-component>
 */

// const files = require.context('./', true, /\.vue$/i);
// files.keys().map(key => Vue.component(key.split('/').pop().split('.')[0], files(key).default));

// Vue.component('example-component', require('./components/ExampleComponent.vue').default);

/**
 * Next, we will create a fresh Vue application instance and attach it to
 * the page. Then, you may begin adding components to this application
 * or customize the JavaScript scaffolding to fit your unique needs.
 */

import VueRouter from 'vue-router'
import VueSweetalert2 from 'vue-sweetalert2'
import 'sweetalert2/dist/sweetalert2.min.css'

Vue.use(VueRouter)
Vue.use(VueSweetalert2)

// import App from './views/App'
// import Hello from './views/Hello'
// import Home from './views/Home'
// import UsersEdit from './views/UsersEdit';
// import UsersIndex from './views/UsersIndex'
// import NotFound from './views/NotFound'
// import UsersCreate from './views/UsersCreate';
//import App from './views/presence-valid';

import showqr from './views/showqr';
import scanqr from './views/scanner';
import presensi from './views/presensi'

// const router = new VueRouter({
//     mode: 'history',
//     routes: [
//         {
//             path: '/spa',
//             name: 'home',
//             component: Home
//         },
//         {
//             path: '/spa/hello',
//             name: 'hello',
//             component: Hello,
//         },
//         {
//             path: '/spa/users',
//             name: 'users.index',
//             component: UsersIndex,
//         },
//         {
//             path: '/spa/users/:id/edit',
//             name: 'users.edit',
//             component: UsersEdit,
//         },
//         {
//             path: '/spa/users/create',
//             name: 'users.create',
//             component: UsersCreate,
//         },
//         // { path: '/spa/404', name: '404', component: NotFound },
//         // { path: '*', redirect: '/404' },
//     ],
// });

const app = new Vue({
    el: '#app',
    components: { showqr,scanqr,presensi },
    // router,
});
