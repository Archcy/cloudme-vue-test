<template>
<!---上传界面，原型来自vuejs-uploader-master项目，但是更改量很大----->
  <div
    class="vuejs-uploader"
    @drop.stop.prevent="dropFiles"
    @dragover.stop.prevent="dragover"
    @dragleave="dragleave"
    :class="{'vuejs-uploader--dragged' : isDraggedOver && !isBrowseDisabled}"
  >
    <label :class="{'disabled': isBrowseDisabled}">
      <span v-if="isSingleFileUpload">
        <!-- Customisable slot for single file uploads -->
        <slot name="browse-btn">
          <span class="vuejs-uploader__btn">选择</span>
        </slot>

        <p
          class="vuejs-uploader__error"
          v-if="files[0] && hasError(files[0]) && showErrors"
        >{{ handleError(files[0].error) }}</p>

        <div v-if="showProgressBar && files[0]" class="vuejs-uploader__progress">
          <div class="vuejs-uploader__progress-bar" :style="progressBarStyle(files[0])"></div>
        </div>
      </span>

      <span v-if="isMultipleFileUpload">
        <slot name="browse-btn">
          <span class="vuejs-uploader__btn">选择</span>
        </slot>
      </span>
      <!-- File Input -->
      <input
        type="file"
        :disabled="isBrowseDisabled"
        :multiple="multiple"
        :accept="accept"
        @change="selectFiles"
      >
    </label>

    <span v-if="isMultipleFileUpload">
      <button
        type="button"
        class="vuejs-uploader__btn vuejs-uploader__btn--clear"
        @click="clear"
        :disabled="noFiles"
      >
        <slot name="clear-btn">清空</slot>
      </button>
      <button
        type="button"
        class="vuejs-uploader__btn vuejs-uploader__btn--upload"
        @click="upload"
        :disabled="isUploadDisabled"
        :class="{'vuejs-uploader__btn--ready' : hasFiles}"
      >
        <slot name="upload-btn">上传</slot>
      </button>
    </span>

    <!-- Errors -->
    <div v-if="errorMessage" class="vuejs-uploader__error">{{ errorMessage }}</div>

    <!-- File list -->
    <ul class="vuejs-uploader__queue" v-if="isMultipleFileUpload">
      <li v-for="(fileObj, i) in this.files" class="vuejs-uploader__file" :key="i">
        <div class="vuejs-uploader__file--preview">
          <div class="loading" v-if="isImageUpload(fileObj) && !fileObj.image"></div>
          <img :src="fileObj.image" v-if="fileObj.image">

          <span
            v-if="!isImageUpload(fileObj)"
            class="vuejs-uploader__file-icon"
            :class="fileObj.extension"
          >{{ fileObj.extension }}</span>
        </div>
        <div class="vuejs-uploader__file--meta">
          <p class="vuejs-uploader__file--filename">{{ fileObj.file.name }}</p>
          <p class="vuejs-uploader__file--filesize">{{ fileObj.formattedFilesize }}</p>
          <p v-if="fileObj.error && showErrors">{{ handleError(fileObj.error) }}</p>

          <slot name="extra" :fileObj="fileObj"></slot>

          <div class="vuejs-uploader__progress">
            <div class="vuejs-uploader__progress-bar" :style="progressBarStyle(fileObj)"></div>
          </div>
        </div>
        <div>
          <button
            type="button"
            class="vuejs-uploader__btn vuejs-uploader__btn--delete"
            @click="removeFile(fileObj)"
          >
            <slot name="remove-btn">移除</slot>
          </button>
        </div>
      </li>
    </ul>
  </div>
</template>
<script>
/**
 * @TODO
 * Investigate sending files with fetch instead of axios
 * Listen for 429 header then setTimeout to Retry-after header
 * Debug cleanQueue method
 */
import axios from 'axios'
import { exhttp } from '../../../http/asyncAxios'
import FileUpload from './FileUpload'
import ImageUpload from './ImageUpload'
import forge from 'node-forge'
import util from '../Cloudutil'
export default {
  props: {
    /**
     * Server end point to send files to
     */
    endPoint: {
      type: String,
      required: true
    },

    /**
     * The ID of this Page
     */
    FPID: {
      type: Number,
      required: true
    },

    /**
     * Error handler
     */
    showErrors: {
      type: Boolean,
      default: true
    },

    /**
     * Accept list of mimes
     */
    accept: String,

    /**
     * Upload multiple files at once
     */
    multiple: Boolean,

    /**
     * Upload larger files as multipart uploads?
     */
    multipart: Boolean,

    /**
     * Multipart upload chunk size
     */
    multipartChunkSize: {
      type: Number,
      default: 1024 * 1024 * 2 // 2mb
    },

    /**
     * Number of files that can be added to the queue
     */
    maxUploads: {
      type: Number,
      default: 5
    },

    /**
     * Maximun preview image width
     */
    maxThumbWidth: {
      type: Number,
      default: 80
    },

    /**
     * Maximun preview image height
     */
    maxThumbHeight: {
      type: Number,
      default: 80
    },

    /**
     * Array of additional data properties to add to the FileObj
     */
    userDefinedProperties: Array,

    /**
     * Show progress bar for single file uploads (shows by default for multiple file uploads)
     */
    showProgressBar: {
      type: Boolean,
      default: false
    },

    /**
     * Disable uploading
     */
    disabled: Boolean
  },
  data () {
    return {
      axios: null,
      files: [],
      errorMessage: null,
      isDraggedOver: null,
      headers: {
        APPFUNC: '4',
        Authorization: localStorage.token
      }
    }
  },
  computed: {
    getFiles () {
      return this.files
    },
    hasFiles () {
      return !this.noFiles
    },
    isSingleFileUpload () {
      return !this.multiple
    },
    isMultipleFileUpload () {
      return this.multiple
    },
    isMultipartUpload () {
      return this.multipart
    },
    isBrowseDisabled () {
      return this.disabled
    },
    isUploadDisabled () {
      let completeRequired = true
      if (this.userDefinedProperties) {
        this.userDefinedProperties.forEach(prop => {
          this.files.forEach(file => {
            if (prop.required && !file[prop.property]) {
              completeRequired = false
            }
          })
        })
      }
      return !this.files.length || !completeRequired
    },
    noFiles () {
      return this.files.length === 0
    }
  },
  methods: {
    dragover () {
      this.isDraggedOver = true
    },
    dragleave () {
      this.isDraggedOver = false
    },
    /**
     * Initiate the upload
     */
    upload () {
      if (this.isUploadDisabled) {
        return false
      }
      this.resetError()
      this.files.forEach(file => this.uploadFile(file))
    },

    /**
     * Empty the file upload queue
     */
    clear () {
      this.resetError()
      this.files = []
    },

    /**
     * Is the file to be uploaded an image?
     * @param {FileUpload}
     * @return {Boolean}
     */
    isImageUpload (fileObj) {
      return fileObj.hasOwnProperty('image')
    },

    /**
     * Is the file an Image?
     *
     * @param {File} file
     * @return {Boolean}
     */
    isImage (file) {
      return ['image/jpeg', 'image/png', 'image/gif'].includes(file.type)
    },

    /**
     * Get File ID & File Key From Different Server
     *
     * @return {Promise}
     */

    async getFilekey () {
      try {
        let res = await exhttp.post('/serverB/controller.php', null, {
          headers: {
            APPFUNC: 10 // 需要先向文件服务器请求生成文件ID，ID是int，目前不是最优，详见后端注释
            // 这样写是因为初版系统的缘故，懒得改了
          } // 可以考虑将生成文件ID这一步放到服务器A
        })
        let enc = await exhttp.post('/serverA/controller.php', res.data, {
          headers: {
            APPFUNC: 4 // 生成密钥文件
          }
        })
        return new Promise((resolve, reject) => {
          if (res !== null) {
            resolve(enc)
          } else {
            reject(enc)
          }
        })
      } catch (err) {
        alert('文件密钥生成失败，请联系管理员')
      }
    },

    /**
     * Upload a single file
     *
     * @param  {FileUpload} fileObj
     * @return {Promise}
     */
    uploadFile (fileObj) {
      if (fileObj.isUploading) {
        return false
      }
      // adds a flag to prevent attempting to
      // upload the same file multiple times.
      fileObj.isUploading = true
      this.multipartUploadFile(fileObj)
      return true
      // Modfied
    },

    /**
     * Upload a file in chunks
     * This creates an array of parts to be uploaded
     *
     * @param  {FileUpload} fileObj
     */
    async multipartUploadFile (fileObj) {
      let enc = await this.getFilekey()
      let Fkey = forge.util.hexToBytes(enc.data.Fkey)
      let FIV = forge.util.hexToBytes(enc.data.FIV)
      let FID = enc.data.FID
      let FPID = this.FPID
      let totalParts = Math.ceil(fileObj.file.size / this.multipartChunkSize)
      let cipher = forge.cipher.createCipher('AES-CFB', Fkey)
      cipher.start({ iv: FIV })
      let currentPart = 1
      // const queue = [];
      while (currentPart <= totalParts) {
        let arrayBuffer = await this.getFileChunk(fileObj, currentPart) // 得到切片文件块(以arraybuffer形式存储，便于操作)
        cipher.update(forge.util.createBuffer(arrayBuffer))
        if (cipher.output.length() > 0) { // 如果最后加密的块太小，就不会在这里输出
          let enct = util.str2ab(cipher.output.getBytes()) // 这里我用string直接作为blob发送会有问题，所以转换为了arraybuffer
          // 如果有好的解决方案，请务必告知
          let encrypted = new Blob([enct]) // 把加密后文件转化为blob
          await this.perthreadupload(FID, FPID, encrypted, fileObj, currentPart, totalParts)
        }
        currentPart++
      }
      cipher.finish()
      // 在finish之后，会输出小于块大小的那一部分
      let enct = util.str2ab(cipher.output.getBytes())
      if (cipher.output.length() > 0) {
        let encrypted = new Blob([enct])
        await this.perthreadupload(FID, FPID, encrypted, fileObj, currentPart, totalParts)
      }
      this.Encprogress = 0
      this.$emit('fileUploaded', {
        file: fileObj
      })
    },

    /**
     * upload per flie block
     * @param  {FileUpload} fileObj
     * @return {Promise}
     */
    async perthreadupload (FID, FPID, encrypted, fileObj, currentPart, totalParts) { // 每个文件块上传
      let data = new FormData()
      let config = {
        onUploadProgress: progressEvent => {
          fileObj.setMultipartProgress(
            progressEvent,
            totalParts,
            currentPart
          )
        }
      } // 用来存储上传过程的一些详细信息
      data.append('FID', FID)
      data.append('FPID', FPID)
      data.append('multipart', true)
      data.append('file', encrypted)
      data.append('filename', fileObj.file.name)
      data.append('mime', fileObj.file.type)
      data.append('totalSize', fileObj.file.size)
      data.append('partSize', this.multipartChunkSize)
      data.append('totalParts', totalParts)
      data.append('currentPart', currentPart)
      data = this.appendUserData(fileObj, data)
      /***
           * 上面的变量用来存储要上传的信息
           */
      this.$http
        .post(this.endPoint, data, config)
        .then(response => {
          if (response) {
            this.$emit('chunkUploaded', fileObj, currentPart)
          }
        })
        .catch(error => {
          this.$emit('error', error)
        })
    },

    /**
     * Removes from the queue any parts that have already been uploaded
     * This requires the server response to contain a `remainingParts` property
     * with an array of all remain parts to be uploaded.
     * This is for resumable uploads.
     *
     * @param  {Array} queue
     * @param  {FileUpload} fileObj
     * @param  {Object} response
     * @return {Array}
     */
    cleanQueue (queue, fileObj, response) {
      if (!response) {
        return queue
      }
      if (response.data.meta && response.data.meta.remainingParts) {
        return queue.filter(item => {
          const uploaded =
            response.data.meta.remainingParts.includes(item.currentPart) ===
            false
          if (uploaded) {
            fileObj.uploadedParts.push({
              part: item.currentPart,
              loaded: 100
            })
          }
          return !uploaded
        })
      }
      return queue
    },

    /**
     * Slice a File object into chunks
     *
     * @param  {FileUpload} fileObj
     * @param  {Number} part
     * @return {Arraybuffer}
     */
    async getFileChunk (fileObj, part) {
      const start = (part - 1) * this.multipartChunkSize
      const end = Math.min(start + this.multipartChunkSize, fileObj.file.size)
      return new Promise(function (resolve, reject) {
        let reader = new FileReader()
        reader.readAsArrayBuffer(fileObj.file.slice(start, end))
        reader.onload = function () {
          resolve(reader.result)
        }
        reader.onerror = reject
      })
    },

    /**
     * Add files to the FileList
     */
    selectFiles (event) {
      this.addFiles(event.target.files)
    },

    /**
     * Add files by dropping
     */
    dropFiles (event) {
      this.isDraggedOver = false
      this.addFiles(event.dataTransfer.files)
    },

    /**
     * Add file(s) to the file list
     *
     * @param {FileList}
     */
    addFiles (files) {
      Array.from(files).forEach(file => {
        if (this.files.length === this.maxUploads) {
          this.setError(
            'Only ' + this.maxUploads + ' files can be uploaded at one time'
          )
          return false
        }
        if (
          this.files.findIndex(
            existingFile => existingFile.file.name === file.name
          ) === -1
        ) {
          const fileObj = this.uploadFactory(file)
          this.files.push(fileObj)
        }
      })
      // start upload if queue is not being used i.e not multiple
      if (!this.multiple) {
        this.resetError()
        this.upload()
        this.$emit('startUpload')
      }

      this.browse = null
    },

    /**
     * Set an error message
     *
     * @param {String} error
     */
    setError (error) {
      this.errorMessage = error
    },

    /**
     * Reset the error message
     */
    resetError () {
      this.errorMessage = null
    },

    /**
     * Returns a FileUpload object
     *
     * @param  {File} file
     * @return {FileUpload|ImageUpload}
     */
    uploadFactory (file) {
      let fileObj
      if (this.isImage(file)) {
        fileObj = new ImageUpload(file)
        if (this.isMultipleFileUpload) {
          this.getPreviewImage(fileObj)
        }
      } else {
        fileObj = new FileUpload(file)
      }

      return this.appendUserProperties(fileObj)
    },

    /**
     * Append user properties to the file obj - useful when adding form elements in the slot
     *
     * @param  {FileUpload} fileObj
     * @return {FileUpload}
     */
    appendUserProperties (fileObj) {
      if (this.userDefinedProperties) {
        this.userDefinedProperties.forEach(obj => {
          fileObj[obj.property] = null
        })
      }
      return fileObj
    },

    /**
     * Add the user defined properties to the FormData object
     *
     * @param  {FileUpload} fileObj
     * @param  {FormData} formData
     * @return {FormData}
     */
    appendUserData (fileObj, formData) {
      if (this.userDefinedProperties) {
        this.userDefinedProperties.forEach(obj => {
          formData.append(obj.property, fileObj[obj.property])
        })
      }
      return formData
    },

    /**
     * Remove file from the file list
     *
     * @param  {File} file
     */
    removeFile (file) {
      this.$el.querySelector('input[type=file]').value = null
      this.resetError()
      const index = this.files.indexOf(file)
      this.files.splice(index, 1)
    },

    /**
     * Get a preview image
     *
     * @param {Object} fileObj
     */
    getPreviewImage (fileObj) {
      const reader = new FileReader()
      reader.onload = e => {
        this.resizeImage(
          e.target.result,
          this.maxThumbWidth,
          this.maxThumbHeight,
          fileObj
        )
      }
      reader.readAsDataURL(fileObj.file)
    },

    /**
     * Create resized image.
     * Draw reader result onto resized canvas element then set the dataUri to the ImageUpload.image property
     *
     * @param {String} src
     * @param {Number} maxWidth
     * @param {Number} maxHeight
     * @param {ImageUpload} fileObj
     */
    resizeImage (src, maxWidth, maxHeight, fileObj) {
      const canvas = document.createElement('canvas')
      const img = new Image()
      const ctx = canvas.getContext('2d')
      img.src = src
      img.onload = () => {
        const resize = this.calculateAspectRatioFit(
          img.width,
          img.height,
          maxWidth,
          maxHeight
        )
        canvas.width = resize.width
        canvas.height = resize.height
        ctx.drawImage(
          img,
          0,
          0,
          img.width,
          img.height,
          0,
          0,
          canvas.width,
          canvas.height
        )
        fileObj.image = canvas.toDataURL(fileObj.file.type, 0.8)
      }
    },

    /**
     * Conserve aspect ratio of the orignal region. Useful when shrinking/enlarging
     * images to fit into a certain area.
     *
     * @param {Number} srcWidth Source area width
     * @param {Number} srcHeight Source area height
     * @param {Number} maxWidth Fittable area maximum available width
     * @param {Number} maxHeight Fittable area maximum available height
     * @return {Object} { width, heigth }
     */
    calculateAspectRatioFit (srcWidth, srcHeight, maxWidth, maxHeight) {
      const ratio = Math.min(maxWidth / srcWidth, maxHeight / srcHeight)
      return {
        width: srcWidth * ratio,
        height: srcHeight * ratio
      }
    },

    /**
     * Style object for the progress bar
     *
     * @param {FileObj}
     * @return {Object}
     */
    progressBarStyle (fileObj) {
      return this.isMultipartUpload &&
        fileObj.filesize > this.multipartChunkSize
        ? { width: fileObj.multipartUploadPercent + '%' }
        : { width: fileObj.singlepartUploadPercent + '%' }
    },

    /**
     * Configure axios
     */
    configureHttpClient () {
      const config = {}
      if (this.headers) {
        config.headers = this.headers
      }
      axios.defaults.baseURL = 'http://192.168.139.128'
      this.$http = axios.create(config)
    },

    /**
     * @param  {File}
     * @return {Boolean}
     */
    hasError (file) {
      return Boolean(file.error)
    },

    /**
     * Defer to external error handler if configured else return the error message
     * @param  {Object} error
     * @return {Object}
     */
    handleError (error) {
      return error
    }
  },
  mounted () {
    this.configureHttpClient()
    this.$on('fileUploaded', file => this.removeFile(file))
  }
}
</script>
<style lang="stylus">
.vuejs-uploader {
  label {
    cursor: pointer;

    &.disabled {
      cursor: not-allowed;
    }
  }

  [type=file] {
    display: none;
  }
}

.vuejs-uploader__error {
  color: red;
  padding: 10px;
  margin: 10px 0;
}

.vuejs-uploader__queue {
  list-style-type: none;
  padding: 0;
  border-top: 1px solid #eee;
  padding-top: 10px;
}

.vuejs-uploader__btn {
  overflow: hidden;
  position: relative;
  padding: 6px 10px;
  border: 1px solid #ccc;
  border-radius: 3px;
  font-size: 100%;
  background: inherit;
  font-family: inherit;
  margin: 2px 5px;
}

.vuejs-uploader__btn--clear {
  background-color: #fff;
  border: inherit;
  border-radius: 50px;
}

.vuejs-uploader__btn--upload {
  background-color: #aaa;
  border: inherit;
  border-radius: 50px;
}

.vuejs-uploader__btn--delete {
  padding: 3px 6px;
  margin: 1px 10px;
}

.vuejs-uploader__file {
  border-bottom: 1px solid #eee;
  padding-bottom: 10px;
  margin-bottom: 10px;
  display: flex;
}

.vuejs-uploader__file--preview {
  display: inline-block;
  width: 100px;
  display: flex;
  align-items: center;
  justify-content: center;
}

.vuejs-uploader__file--meta {
  display: inline-block;
  flex-grow: 1;
}

.vuejs-uploader__file--filename, .vuejs-uploader__file--filesize {
  vertical-align: top;
  margin: 0;
}

.vuejs-uploader__progress {
  position: relative;
  height: 6px;
  border-radius: 3px;
  background: #eee;
}

.vuejs-uploader__progress-bar {
  display: inline-block;
  height: 6px;
  background: limegreen;
  position: absolute;
  border-radius: 3px;
}

.loading:after {
  content: '';
  display: block;
  font-size: 10px;
  width: 1em;
  height: 1em;
  margin-top: -0.5em;
  animation: spinner 1500ms infinite linear;
  border-radius: 0.5em;
  box-shadow: rgba(0, 0, 0, 0.25) 1.5em 0 0 0, rgba(0, 0, 0, 0.25) 1.1em 1.1em 0 0, rgba(0, 0, 0, 0.25) 0 1.5em 0 0, rgba(0, 0, 0, 0.25) -1.1em 1.1em 0 0, rgba(0, 0, 0, 0.25) -1.5em 0 0 0, rgba(0, 0, 0, 0.25) -1.1em -1.1em 0 0, rgba(0, 0, 0, 0.25) 0 -1.5em 0 0, rgba(0, 0, 0, 0.25) 1.1em -1.1em 0 0;
}

.encbar {
  width: 100px;
  height: 20px;
  margin: 1px 5px;
}

@keyframes spinner {
  0% {
    transform: rotate(0deg);
  }

  100% {
    transform: rotate(360deg);
  }
}

.vuejs-uploader__dir-icon {
  height: 60px;
  width: 60px;
  background: rgb(141, 202, 247);
  border-top: 3px solid $borderColour;
  line-height: 35px;
  text-align: center;
  position: relative;
  display: inline-block;
  color: #fff;
  font-weight: bold;
  border-radius: 0 3px 3px 3px;
  font-size: 16px;

  &.open {
    padding-left: 6px;

    .dir-icon-open {
      display: block;
    }
  }

  .dir-icon-open {
    display: none;
    position: absolute;
    left: 0;
    top: 0;

    &:before, &:after {
      content: '';
      position: absolute;
      height: 0;
      width: 0;
      top: 0;
    }

    &:before {
      left: 0;
      border-left: 3px solid #acdcfd;
      border-bottom: 17px solid #8dcaf7;
      border-right: 3px solid #8dcaf7;
      border-top: 17px solid #acdcfd;
    }

    &:after {
      left: 60px;
      border-left: 3px solid #8dcaf7;
      border-bottom: 17px solid #fff;
      border-right: 3px solid #fff;
      border-top: 17px solid #8dcaf7;
    }
  }

  &:before, &:after {
    content: '';
    position: absolute;
    background: $borderColour;
    display: inline-block;
  }

  &:before {
    top: -8px;
    left: 0;
    height: 6px;
    width: 20px;
    border-radius: 3px 0 0 0;
  }

  &:after {
    top: -8px;
    left: 20px;
    border: 3px solid #fff;
    border-left: 3px solid $borderColour;
    border-bottom: 3px solid $borderColour;
  }
}

/**
 * File mixin
 */
file($colour) {
  background: $colour;

  &:after {
    border-bottom: 6px solid lighten($colour, 10%);
    border-left: 6px solid lighten($colour, 10%);
  }
}

.vuejs-uploader__file-icon {
  font-weight: bold;
  color: #fff;
  border-radius: 3px;
  line-height: 60px;
  height: 60px;
  width: 50px;
  text-align: center;
  display: inline-block;
  text-transform: uppercase;
  position: relative;

  &:after {
    content: '';
    position: absolute;
    top: 0;
    right: 0;
    border-top: 6px solid #fff;
    border-right: 6px solid #fff;
  }

  file(rgb(120, 120, 120));

  /**
   * App specific branded colours
   */
  &.doc, &.docx {
    file(rgb(75, 99, 158));
  }

  &.key {
    file(rgb(110, 180, 250));
  }

  &.numbers {
    file(rgb(215, 116, 39));
  }

  &.pdf {
    file(rgb(210, 120, 12));
  }

  &.pptx {
    file(rgb(185, 89, 62));
  }

  &.psd {
    file(rgb(125, 210, 249));
  }

  &.csv, &.xls, &.xlsx {
    file(rgb(78, 120, 83));
  }

  &.swf { // flash
  }

  /**
   * Generic image
   */
  &.arw, &.bmp, &.cr2, &.dng, &.tif, &.tiff, &.raf, &.tga {
    file(rgb(98, 168, 145));
  }

  /**
   * Generic video
   */
  &.webm, &.m4v, &.mov, &.mp4, &.ogv, &.vtt {
    file(rgb(98, 168, 245));
  }

  /**
   * Generic video caption
   */
  &.mvtt, &.sbv, &.srt {
    file(rgb(100, 80, 145));
  }

  /**
   * Generic project
   */
  &.fcp, &.prproj {
  }

  /**
   * Generic text
   */
  &.eml, &.html, &.rtf, &.txt, &.xml {
    file(rgb(220, 200, 190));
    color: #333;
  }

  /**
   * Generic compression
   */
  &.zip {
  }

  &.webarchive {
  }
}
</style>
