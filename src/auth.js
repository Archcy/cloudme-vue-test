/* eslint-disable standard/no-callback-literal */
/* globals localStorage */
// 最初目的是管理登录相关，但是之后代码写的太烂，这边就没怎么调用了

import HTTP from './http/axios'

export default {
  login (username, pass, cb) {
    cb = arguments[arguments.length - 1]
    if (localStorage.token) {
      if (cb) cb(true)
      this.onChange(true)
      return
    }
    pretendRequest(username, pass, (res) => {
      if (res.authenticated) {
        localStorage.token = res.token
        if (cb) cb(true)
        this.onChange(true)
      } else {
        if (cb) cb(false)
        this.onChange(false)
      }
    })
  },

  getToken () {
    return localStorage.token
  }, // 获取token

  logout (cb) {
    delete localStorage.token
    if (cb) cb()
    this.onChange(false)
  }, // 登出

  loggedIn () {
    return !!localStorage.token
  }, // 登入状态

  onChange () { }
}

function pretendRequest (username, pass, cb) {
  setTimeout(() => { // 执行登入，但是这里应该是像两个服务器请求不同的token。之后验证时再分别发送
    var obj = {} // 但是为了编程方便，这里将两个服务器token生成设置一致，只向服务器A请求了token
    obj.username = username
    obj.passwd = pass
    var uinfo = JSON.stringify(obj)
    HTTP
      .post('/serverB/controller.php', uinfo, {
        headers: {
          'APPFUNC': 2
        }
      })
      .then(function (res) {
        if (res.data.status === 'success') {
          cb({
            authenticated: true,
            token: res.data.token
          })
        } else {
          cb({
            authenticated: false
          })
        }
      })
      .catch()
  }, 0)
}
