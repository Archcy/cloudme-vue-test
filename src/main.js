import Vue from 'vue'
import VueRouter from 'vue-router'
import auth from './auth'
import RouterPage from './RouterPage.vue'
import Login from './components/UserAuth/Login.vue'
import Registme from './components/UserAuth/Reg.vue'
import Cloud from './components/CloudManager/Cloud.vue'

Vue.use(VueRouter)

function requireAuth (to, from, next) {
  if (!auth.loggedIn()) {
    next({
      path: '/login',
      query: { redirect: to.fullPath }
    })
  } else {
    next()
  }
}

const router = new VueRouter({ // 注册路由页面
  mode: 'history',
  base: './',
  routes: [
    { path: '/Cloud', component: Cloud, beforeEnter: requireAuth }, // 进入主界面会执行登录状态验证
    { path: '/login', component: Login },
    { path: '/reg', component: Registme },
    { path: '/', redirect: './Cloud' },
    { path: '/logout',
      beforeEnter (to, from, next) {
        auth.logout()
        next('/login') // logout页面并不存在，载入改url时会执行登出函数
      }
    }
  ]
})

new Vue({
  router,
  render: h => h(RouterPage) // RouterPage为渲染的实例
}).$mount('#app')
