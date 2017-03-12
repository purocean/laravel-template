import Vue from 'vue'
import iView from 'iview'
import Router from 'vue-router'
import Auth from '@/auth/Auth'
import Err from '@/desktop/Error'
import Login from '@/desktop/Login.vue'
import Home from '@/desktop/Home.vue'
import Users from '@/desktop/Users.vue'

import 'iview/dist/styles/iview.css'

Vue.use(Router)
Vue.use(iView)

const allowList = ['/', '/error', '/login']

const routes = [
  {path: '/', name: '/', redirect: '/users'},
  {path: '/home', name: 'home', component: Home},
  {path: '/users', name: 'users', component: Users},
  {path: '/login', name: 'login', component: Login},
  {path: '/error', name: 'error', component: Err},
]

const router = new Router({ routes })

router.beforeEach((to, from, next) => {
  if (Auth.checkPermission(allowList, to.path)) {
    next()
    return
  }

  if (Auth.isLogin()) {
    if (Auth.can(to.path)) {
      next()
    } else {
      Auth.can(to.path, allow => {
        if (allow) {
          next()
        } else {
          next({name: 'error'})
        }
      })
    }
  } else {
    next({name: 'login', query: {next: from.path}})
  }
})

export default router
