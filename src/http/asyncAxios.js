// 用async封装的axios，便于传参。
import axios from 'axios'
const exhttp = {
  async post (url, data, config) {
    try {
      let res = await axios.post(url, data, config)
      return new Promise((resolve, reject) => {
        if (res !== null) {
          resolve(res)
        } else {
          reject(res)
        }
      })
    } catch (err) {
      alert('系统运行错误')
    }
  }
}
export { exhttp }
