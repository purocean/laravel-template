import Vue from 'vue'
import Router from 'vue-router'
import Auth from '@/auth/Auth'
import Err from '@/mobile/Error'
import Login from '@/mobile/Login.vue'
import Qrlogin from '@/mobile/Qrlogin.vue'

Vue.use(Router)

const allowList = ['/', '/error/*', '/login']

const routes = [
  {path: '/', name: '/', redirect: '/'},
  {path: '/login', name: 'login', component: Login},
  {path: '/qrlogin', name: 'qrlogin', component: Qrlogin},
  {path: '/error', name: 'error', component: Err},
]

const router = new Router({ routes })

router.beforeEach((to, from, next) => {
  Auth.requireAuth(allowList, to, from, next)
})

export default router
