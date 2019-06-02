<template>
<!---注册界面，采用了和登录界面相同的设计
 在注册的布局上，不是很人性化，添加两列div即可正常使用tab切换输入
 但是我懒得再去改了---->
  <div id="login-warpper">
    <div>
      <button>
        <router-link to="/Login" style="color:#eee;">返回</router-link>
      </button>
    </div>
    <div id="login-d">
      <h2 style="margin:5px 10px;">请输入您的账户信息:</h2>
      <div class="lg">
        用户名:
        <div class="lginpt">
          <div class="ipt">
            <input type="text" id="username" placeholder="用户名" v-model="username">
          </div>
        </div>
      </div>

      <div class="lg">
        电话:
        <div class="lginpt">
          <div class="ipt">
            <input type="text" id="phone" placeholder="电话" v-model="phone">
          </div>
        </div>
      </div>
      <div class="lg">
        密码:
        <div class="lginpt">
          <div class="ipt">
            <input type="password" id="password" placeholder="密码" v-model="password">
          </div>
        </div>
      </div>
      <div class="lg">
        邮箱:
        <div class="lginpt">
          <div class="ipt">
            <input type="mail" id="mail" placeholder="邮箱" v-model="mail">
          </div>
        </div>
      </div>
      <div class="lg">
        请重新输入您的密码:
        <div class="lginpt">
          <div class="ipt">
            <input
              type="password"
              id="password2"
              placeholder="请重新输入您的密码"
              v-model="password2"
            >
          </div>
        </div>
      </div>
      <div class="lg">
        性别:
        <div class="lginpt" style="background-color: #fff;">
          <div class="ipt">
            <div style="float:left;width:100px;">
              <div class="fl">
                <input type="radio" id="male" name="sex" v-on:change="male">
                <label for="male"></label>
              </div>
              <div style="float:left">男性</div>
            </div>
            <div style="float:left;width:100px;">
              <div class="fl">
                <input type="radio" id="female" name="sex" v-on:change="female">
                <label for="female"></label>
              </div>
              <div class="fl">女性</div>
            </div>
          </div>
        </div>
      </div>
      <div class="lginpt" style="background-color:#fff;width: 700px;margin: 0px;">
        <button
          v-on:click="dealdata"
          type="submit"
          style="background-color:blueviolet;float: right;"
        >注册</button>
        <div style="color:red;float: right;">{{perror}}</div>
      </div>
      <div style="clear:both"></div>
    </div>
  </div>
</template>
<script>
import axios from '../../http/axios'
export default {
  data () {
    return {
      UID: '',
      username: '',
      password: '',
      password2: '',
      code: '',
      phone: '',
      mail: '',
      sex: '',
      error: 1
    }
  },
  computed: {
    perror: function () {
      if (this.$data.password !== this.$data.password2) {
        return '密码输入不一致'
      }
      return ''
    }
  },
  methods: {
    male: function () {
      this.$data.sex = 'm'
    },
    female: function () {
      this.$data.sex = 'f'
    },
    dealdata: function () { // 封装和发送数据
      try {
        if (this.$data.username) {
          if (this.$data.password === this.$data.password2) {
            let obj = {}
            obj.username = this.$data.username
            obj.passwd = this.$data.password
            obj.mail = this.$data.mail
            obj.phone = this.$data.phone
            obj.sex = this.$data.sex
            var uinfo = JSON.stringify(obj)
            axios
              .post('/serverA/controller.php', uinfo, {
                headers: {
                  'APPFUNC': 1
                }
              })
              .then(res => {
                // console.log(res.data)
                switch (res.data.status) {
                  case '0':
                    alert('用户名已经被占用')
                    break
                  case '3':
                    alert('用户名非法')
                    break
                  case '1':
                    let obj2 = {}
                    obj2.username = this.$data.username
                    obj2.passwd = this.$data.password
                    obj2.mail = this.$data.mail
                    obj2.phone = this.$data.phone
                    obj2.UID = res.data.UID
                    this.$data.error = 0
                    var uinfo2 = JSON.stringify(obj2)
                    axios
                      .post('/serverB/controller.php', uinfo2, {
                        headers: {
                          'APPFUNC': 1
                        }
                      })
                      .then(function (res2) {
                        // console.log(res2.data)
                        switch (res2.data) {
                          case 1:
                            alert('注册成功')
                            break
                        }
                      })
                    break
                }
              })
          } else {
            alert('两次密码输入不一致!')
          }
        } else {
          alert('请输入用户名！')
        }
      } catch (error) {
        alert('注册错误，请联系管理员')
      }
    }
  }
}
</script>
<style src="../../css/login.css" scoped>
</style>
<style scoped>
#login-d {
  width: 800px;
  height: 440px;
  border-radius: 1%;
}

#login-d div {
  float: left;
}
</style>
