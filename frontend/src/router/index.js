import Vue from 'vue'
import iView from 'iview'
import Router from 'vue-router'
import Login from '@/desktop/Login.vue'

import 'iview/dist/styles/iview.css'

Vue.use(Router)
Vue.use(iView)

const routes = [
  {path: '/', name: 'home', redirect: '/login'},
  {path: '/login', name: 'login', component: Login},
]

export default new Router({ routes })
