
<template>
<!----登录界面----->
  <div id="login-warpper">
    <div>
        <button><router-link to="/Reg" style="color:#eee;">注册</router-link></button>
    </div>
    <div id="logo"></div>
    <div id="login-d">
      <h2 style="margin:5px 10px;"> 请输入登录信息:</h2>
      <div class="lg">
        用户名:
        <div class="lginpt">
          <div class="ipt">
            <input type="text" id="username" placeholder="用户名" v-model="username">
          </div>
        </div>
      </div>
      <div class="lg">
        密码:
        <div class="lginpt">
          <div class="ipt">
            <input type="password" id="pass" placeholder="密码" v-model="pass">
          </div>
        </div>
      </div>
      <div style="clear:both"></div>
      <div class="lginpt" style="background-color:#fff;">
        <button
          v-on:click="login"
          type="submit"
          style="float:right;"
        >登录</button>
      </div>
      <p class="error">{{error}}</p>
    </div>
  </div>
</template>
<script>
import auth from '../../auth'
export default {
  name: 'login',
  data () {
    return {
      username: '',
      pass: '',
      code: '',
      error: ''
    }
  },
  methods: { // 调用了Auth.js 用来注册
    login () {
      localStorage.CIC = 0
      auth.login(this.$data.username, this.$data.pass, loggedIn => {
        if (!loggedIn) {
          this.error = '登陆失败'
        } else {
          this.error = ''
          localStorage.username = this.$data.username
          this.$router.replace(this.$route.query.redirect || '/')
        }
      })
    }
  }
}
</script>
<style src="../../css/login.css" scoped>

</style>

<style scoped>
.error{
    color: red;
}
</style>
