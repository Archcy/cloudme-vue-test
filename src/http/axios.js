// 对axios进行设置
import axios from 'axios'
axios.defaults.baseURL = 'http://192.168.139.128' // 这个IP地址是你后端服务器的IP地址
if (localStorage.token) {
  axios.defaults.headers.common['Authorization'] = localStorage.token // 设置验证为token
}
axios.defaults.headers.post['Content-Type'] = 'application/x-www-form-urlencoded'
// set new axios instance
export default axios.create()
