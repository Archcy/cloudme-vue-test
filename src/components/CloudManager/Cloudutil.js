export default {
  str2ab (str) { // 字符串转arraybuffer
    let buf = new ArrayBuffer(str.length) // 2 bytes for each char
    let bufView = new Uint8Array(buf)
    for (let i = 0, strLen = str.length; i < strLen; i++) {
      bufView[i] = str.charCodeAt(i)
    }
    return buf
  },
  fileSize (a, b, c, d, e) {
    const size = (b = Math, c = b.log, d = 1e3, e = c(a) / c(d) | 0, a / b.pow(d, e)).toFixed(2) +
      ' ' + (e ? 'kMGTPEZY'[--e] + 'B' : 'Bytes')
    return size
  },

  /**
   * Extract the extension from a filename
   *
   * @param  {String} filename
   * @return {String}
   */
  fileExtension (filename) {
    return filename ? filename.slice((filename.lastIndexOf('.') - 1 >>> 0) + 2) : ''
  }

}
