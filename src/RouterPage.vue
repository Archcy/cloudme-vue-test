<template>
  <!---路由主控界面----->
  <div id="app">
    <template v-if="$route.matched.length">
    </template>
    <router-view></router-view>
  </div>
</template>

<script>
import auth from './auth'

export default {
  data () {
    return {
      loggedIn: auth.loggedIn() // 检查登录状态
    }
  },
  created () {
    auth.onChange = loggedIn => {
      this.loggedIn = loggedIn
    }
  }
}
</script>
<style>
#app{
  height: 100%;
  width: 100%;
}
/***
* 引入公共css文件
**/
@import "./css/public.css";

</style>
