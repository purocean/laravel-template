import Vue from 'vue'
import iView from 'iview'
import Router from 'vue-router'
import Auth from '@/auth/Auth'
import Login from '@/desktop/Login.vue'

import 'iview/dist/styles/iview.css'

Vue.use(Router)
Vue.use(iView)

const allowList = ['/error', '/login']

const routes = [
  {path: '/', name: 'home', redirect: '/login'},
  {path: '/login', name: 'login', component: Login},
]

const router = new Router({ routes })

router.beforeEach((to, from, next) => {
  if (Auth.checkPermission(allowList, to.path)) {
    next()
    return
  }

  if (Auth.isLogin()) {
    Auth.can(to.path, allow => {
      if (allow) {
        next()
      } else {
        next({name: 'error'})
      }
    })
  } else {
    next({name: 'login'})
  }
})

export default router
