<template>
  <div id="UploadePage">
    <ul>
      <uploader end-point="/serverB/controller.php"
      :multiple="true" :multipart="true">
      </uploader>
    </ul>
  </div>
</template>

<script>
import Uploader from './Uploader.vue'
export default {
  name: 'upView',
  components: {
    Uploader
  },
  data () {
    return {
      uploadst: null,
      gkey: null,
      encrypt: null,
      token: localStorage.token
    }
  },
  methods: {
    uploadStatus (response) {
      this.uploadst = response
    },
    gkeyStatus () {

    },
    resetLog (prop) {
      this[prop] = null
    }
  }
}
</script>

<style scoped>
#UploadePage{
  position: fixed;
  left: 30%;
  top: 15%;
  z-index: 20;
  background-color: #eee;
  border-radius: 2%;
  width: 40%;
  height: 60%;
}

ul {
  list-style-type: none;
  padding: 0;
}
li {
  margin-bottom: 30px;
}
</style>
