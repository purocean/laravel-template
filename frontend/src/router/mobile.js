import Vue from 'vue'
import Router from 'vue-router'
import Auth from '@/auth/Auth'
// import Err from '@/desktop/Error'
import Login from '@/mobile/Login.vue'
import Qrlogin from '@/mobile/Qrlogin.vue'

Vue.use(Router)

const allowList = ['/', '/error/*', '/login']

const routes = [
  {path: '/', name: '/', redirect: '/'},
  {path: '/login', name: 'login', component: Login},
  {path: '/qrlogin', name: 'qrlogin', component: Qrlogin},
  // {path: '/error', name: 'error', component: Err},
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
    next({name: 'login', query: {next: to.fullPath}})
  }
})

export default router
