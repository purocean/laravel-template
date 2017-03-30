// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import iView from 'iview'
import Router from 'vue-router'
import Desktop from './CDesktop'
import router from './router/desktop'
import '@/theme/default.less'

Vue.config.productionTip = false

Vue.use(Router)
Vue.use(iView)

/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  template: '<Desktop/>',
  components: { Desktop }
})
