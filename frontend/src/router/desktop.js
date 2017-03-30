import Router from 'vue-router'
import Auth from '@/auth/Auth'
import Err from '@/desktop/Error'
import Login from '@/desktop/Login.vue'
import Users from '@/desktop/Users.vue'
import Departments from '@/desktop/Departments.vue'

const allowList = ['/', '/error', '/login']

const routes = [
  {path: '/', name: '/', redirect: '/users'},
  {path: '/users', name: 'users', component: Users},
  {path: '/departments', name: 'departments', component: Departments},
  {path: '/login', name: 'login', component: Login},
  {path: '/error', name: 'error', component: Err},
]

const router = new Router({ routes })

router.beforeEach((to, from, next) => {
  Auth.requireAuth(allowList, to, from, next)
})

export default router
