<template>
<!----云盘主界面----Designed BY WZQ----->
  <div id="cloud-warpper">
    <div class="nav" id="c1">
      <div id="home">
        <div id="hlogo"></div>
        <p>Home</p>
      </div>
      <div id="searchicon"></div>
      <input id="search" v-model="search">
      <button id="user" @click="vshowdash()"></button>
    </div>
    <div id="side-panel">
      <!-- provide side control to operations page-->
      <div class="clom" style="padding-top:20px;"></div>
      <div class="clom" @click="vshowup()">
        <button id="up">
          <p>上传文件</p>
        </button>
      </div>
      <div class="clom" :style="diskcolor(0)" @click="fontview=0;chgview();">
        <button id="maindisk"></button>
        <p>我的网盘</p>
      </div>
      <div class="clom" :style="diskcolor(1)" @click="fontview=1;chgview();">
        <button id="bookmark"></button>
        <p>我的收藏</p>
      </div>
      <div class="clom" :style="diskcolor(2)" @click="fontview=2;chgview();">
        <button id="shared"></button>
        <p>我的分享</p>
      </div>
      <div class="clom"></div>
      <div class="usage-panel">
        <div id="usage-ind">
          <br>
          <p>已使用:{{fileSize(StorageUsed)}}</p>
          <p>总量:{{fileSize(StorageTotal)}}</p>
          <div id="usage-in" :style="'height:'+(StorageUsed)/(StorageTotal)+'%'"></div>
        </div>
      </div>
    </div>
    <transition name="fade">
      <div id="UploadePage" v-if="showup">
        <ul>
          <uploader
            end-point="/serverB/controller.php"
            :multiple="true"
            :multipart="true"
            :FPID="FNowID"
          ></uploader>
        </ul>
        <button
          style="background-color:lightcoral;width:40px;position:fixed;top:12%;left:68%;z-index:21"
          @click="vshowup()"
        >
          <p>X</p>
        </button>
      </div>
    </transition>
    <transition name="fade">
      <dash v-if="showdash"></dash>
    </transition>
    <transition name="bounce">
      <div id="detail" v-if="showdetail">
        <div class="dtline">文件名: {{detailinfo.FName}}</div>
        <div class="dtline">文件大小: {{fileSize(detailinfo.Fsize)}}</div>
        <div class="dtline">真实文件ID: {{detailinfo.FID}}</div>
        <div class="dtline">收藏状态: {{detailinfo.Fav}}</div>
        <div class="dtline">
          分享状态: {{detailinfo.FShare}}
          <button
            v-show="(detailinfo.FShare===0)"
            @click="sharethefile(detailinfo)"
          >分享文件</button>
          <button v-show="(detailinfo.FShare===1)" @click="unsharethefile(detailinfo)">取消分享</button>
        </div>
        <div class="dtline" v-show="getshare(detailinfo)">
          <p>分享地址:</p>

          <textarea id="sctxt" style="width:200px;height:40px;margin:3px 1px;float:left"></textarea>
          <button @click="shareurl()">复制链接</button>
        </div>
        <button
          style="background-color:lightcoral;width:40px;position:absolute;top:0%;right:0%;z-index:21"
          @click="showdetail=0"
        >
          <p>X</p>
        </button>
      </div>
    </transition>
    <transition name="fade">
    <div id="newfoler" v-if="shownf">
      <div
        style="position:fixed;left:40%;top:40%;border-radius:15px;background-color:#fff;width:320px;height:160px"
      >
        <button
          style="background-color:lightcoral;width:40px;float:right;margin-right:-10px;margin-top:-10px"
          @click="shownf=0"
        >
          <p>X</p>
        </button>
        <div style="margin:10px 10px;width:300px;height:140px">
          <div>请输入文件夹名</div>
          <div style="background-color:#fff">
            <input type="text" id="pf1" placeholder="新建文件夹" v-model="NFname">
          </div>
          <button @click="newFolder()">确定</button>
        </div>
      </div>
    </div>
    </transition>
    <div id="location">
      <p style="margin-left:250px;margin-top:5px;float:left">我的网盘:{{getLocation(FullPName)}}</p>
      <div class="fopt">
        <div id="del" @click="processqueue(2)"></div>
        <div id="down" @click="processqueue(1)"></div>
        <div style="width:280px;height:40px;margin-right:15px;"  v-show="progress!=0&&progress!=1" >
          <p style="font-size:20px;float:left;width:70px;height:28px;margin:3px 0px;">下载中</p>
          <div id="progress-bar">
            <div id="progress" :style="'width:'+progress*100+'%'"></div>
          </div>
        </div>
      </div>
    </div>
    <div class="cloud-container" v-if="fontview===0">
      <div style="min-width:200px;height:30px;">
        <p style="float:left">文件夹:</p><button id="newF" @click="vshownf()">新建文件夹</button>
      </div>
      <div style="float:none;width:100%;height:auto;margin:5px 5px;">
        <transition name="bounce">
          <div class="folder" v-show="FNowID!=0">
            <div class="folder-logo" @click="backtoFolder(FPID)"></div>
            <div class="options" @click="backtoFolder(FPID)">
              <div class="pname">返回:</div>
            </div>
          </div>
        </transition>
        <transition-group appear name="bounce" tag="div" mode="out-in">
          <div
            class="folder"
            v-for="fileInfo in isLocation(isdir(SearchFile(fileIndex)))"
            :key="fileInfo['FID']"
            :style="isfav(fileInfo)"
          >
            <div class="folder-logo" @click="enterFolder(fileInfo)"></div>
            <div class="options">
              <div class="pname" @click="enterFolder(fileInfo)">{{fileInfo['FName'] }}</div>
              <div style="float:right;width:30px;height:60px;">
                <div class="delete" @click="delteFolder(fileInfo)"></div>
                <div class="fav" v-show="(fileInfo.Fav===0)" @click="Fav(fileInfo)"></div>
                <div class="faved" v-show="(fileInfo.Fav===1)" @click="Fav(fileInfo)"></div>
              </div>
            </div>
          </div>
        </transition-group>
        <div style="height:3px;width:100%;clear:both"></div>
      </div>
      <div>
        <p>文件:</p>
      </div>
      <transition-group appear name="bounce" tag="div" mode="out-in">
        <div
          class="file"
          v-for="fileInfo in isLocation(isfile(SearchFile(fileIndex)))"
          :key="fileInfo['FID']"
        >
          <div class="file-logo" @click="vshowdetail(fileInfo)">
            <div class="share" v-show="(fileInfo.FShare===1)"></div>
            <div class="filewith"></div>
            <div class="name">
              <p class="pname">{{fileInfo['FName'] }}</p>

            </div>
          </div>
          <div class="options" :style="isfav(fileInfo)">
            <div class="checkbox">
              <input type="checkbox" :id="fileInfo['FID']" :value="fileInfo" v-model="Selected">
              <label :for="fileInfo['FID']"></label>
            </div>
            <div class="download" @click="download(fileInfo)"></div>
            <div class="delete" @click="deletefile(fileInfo['FID'],0)"></div>
            <p style="float:left;width:100px;height:20px;margin-top:8px;margin-left:5px;">{{fileInfo['Ftime'].substring(0,10) }}</p>
            <div style="height:20px;width:160px;cursor: auto">Size:{{fileSize(fileInfo['Fsize'])}}</div>
            <transition name="bounce-x">
              <div class="fav" v-show="(fileInfo.Fav===0)" @click="Fav(fileInfo)"></div>
            </transition>
            <transition name="bounce-x">
              <div class="faved" v-show="(fileInfo.Fav===1)" @click="Fav(fileInfo)"></div>
            </transition>
          </div>
        </div>
      </transition-group>
      <div style="height:180px;width:100%;clear:both"></div>
    </div>
    <div class="cloud-container" v-if="fontview===1">
      <div>
        <p>文件:</p>
      </div>
      <transition-group appear name="bounce" tag="div" mode="out-in">
        <div
          class="file"
          v-for="fileInfo in isfile(isFaved(SearchFile(fileIndex)))"
          :key="fileInfo['FID']"
        >
          <div class="file-logo" @click="vshowdetail(fileInfo)">
            <div class="share" v-show="(fileInfo.FShare===1)"></div>
            <div class="filewith"></div>
            <div class="name">
              <p class="pname">{{fileInfo['FName'] }}</p>
            </div>
          </div>
          <div class="options" :style="isfav(fileInfo)">
            <div class="checkbox">
              <input type="checkbox" :id="fileInfo['FID']" :value="fileInfo" v-model="Selected">
              <label :for="fileInfo['FID']"></label>
            </div>
            <div class="download" @click="download(fileInfo)"></div>
            <div class="delete" @click="deletefile(fileInfo['FID'],0)"></div>
            <p style="float:left;width:100px;height:20px;margin-top:8px;margin-left:5px;">{{fileInfo['Ftime'].substring(0,10) }}</p>
            <div style="height:20px;width:160px;cursor: auto">Size:{{fileSize(fileInfo['Fsize'])}}</div>
            <transition name="bounce-x">
              <div class="fav" v-show="(fileInfo.Fav===0)" @click="Fav(fileInfo)"></div>
            </transition>
            <transition name="bounce-x">
              <div class="faved" v-show="(fileInfo.Fav===1)" @click="Fav(fileInfo)"></div>
            </transition>
          </div>
        </div>
      </transition-group>
      <div style="height:180px;width:100%;clear:both"></div>
    </div>
    <div class="cloud-container" v-if="fontview===2">
      <div>
        <p>文件:</p>
      </div>
      <transition-group appear name="bounce" tag="div" mode="out-in">
        <div
          class="file"
          v-for="fileInfo in (isShare(SearchFile(fileIndex)))"
          :key="fileInfo['FID']"
        >
          <div class="file-logo" @click="vshowdetail(fileInfo)">
            <div class="share" v-show="(fileInfo.FShare===1)"></div>
            <div class="filewith"></div>
            <div class="name">
              <p class="pname">{{fileInfo['FName'] }}</p>
            </div>
          </div>
          <div class="options" :style="isfav(fileInfo)">
            <div class="checkbox">
              <input type="checkbox" :id="fileInfo['FID']" :value="fileInfo" v-model="Selected">
              <label :for="fileInfo['FID']"></label>
            </div>
            <div class="download" @click="download(fileInfo)"></div>
            <div class="delete" @click="deletefile(fileInfo['FID'],0)"></div>
            <p style="float:left;width:100px;height:20px;margin-top:8px;margin-left:5px;">{{fileInfo['Ftime'].substring(0,10) }}</p>
            <div style="height:20px;width:160px;cursor: auto">Size:{{fileSize(fileInfo['Fsize'])}}</div>
            <transition name="bounce-x">
              <div class="fav" v-show="(fileInfo.Fav===0)" @click="Fav(fileInfo)"></div>
            </transition>
            <transition name="bounce-x">
              <div class="faved" v-show="(fileInfo.Fav===1)" @click="Fav(fileInfo)"></div>
            </transition>
          </div>
        </div>
      </transition-group>
      <div style="height:180px;width:100%;clear:both"></div>
    </div>
    <!---.... something like this -->
  </div>
</template>
<script>
import axios from '../../http/axios'
import dash from './Dashboard.vue'
import Uploader from './uploader/Uploader.vue'
import FileDownloader from 'js-file-download'
import { exhttp } from '../../http/asyncAxios'
import forge from 'node-forge'
import util from './Cloudutil'
export default {
  name: 'cloud',
  components: {
    dash,
    Uploader
  },
  data () { // 提供了一些详细的变量
    return {
      username: '', // 用户名
      usex: '', // 性别
      uVIP: '', // VIP，目前未实现
      Vend: '', // VIP截至，未实现
      FName: '', // 文件详情视图的文件名
      NFname: '', // 新建文件夹的文件名
      FPID: null, // 当前视图下的父文件夹ID
      FNowID: 0, // 当前文件夹的ID
      FNowName: '', // 当前位置——————这一不错
      FullFPID: [0], // 完整的当前目录文件夹ID
      FullPName: [], // 完整的当前目录名
      showdash: false, // 下面的几个Boolean类型变量用来控制是否显示某些界面
      showup: false,
      shownf: false,
      showdetail: false,
      fileIndex: {
        // ID Name FinDID 之类的，接受服务器传回的文件列表
      },
      search: '', // 搜索框输入
      Selected: [], // 选择的文件
      PC: [], // 忘了啥了
      r: false, // 同上
      multipartChunkSize: 2 * 1024 * 1024, // 文件块的大小
      totalP: 0, // 总多选的任务量，v1版本遗留变量，可以删除
      NowP: 0, // 现在进行的任务
      progress: 0, // 进度条信息，能不能删我也忘了。。。
      StorageUsed: 0, // 用户空间信息
      StorageTotal: 0, // 用户空间信息
      detailinfo: {}, // 详细的
      FDIR: '', // 文件分享的目录
      fontview: 0 // 是网盘主视图？还是收藏视图？还是分享视图？由此变量决定
    }
  },
  mounted () {
    this.userinit() // 在用户网页加载完成后，执行数据更新函数
  },
  watch: { // 监控变量的改变，详情请看vue手册
    fileIndex: function (newVal) {
      this.fileIndex = newVal
    },
    FNowID: function (newVal) {
      this.FNowID = newVal
    }
  },
  methods: { // 可供调用的方法
    SearchFile: function () { // 文件搜索函数
      var search = this.search
      if (Array.isArray(this.fileIndex)) {
        return this.fileIndex.filter(function (fileInfo) {
          if (fileInfo['FName'].match(search) != null) {
            return fileInfo
          }
        })
      }
    },
    isdir: function (files) { // 判别是否为文件夹
      if (files) {
        return files.filter(function (file) {
          if (file.Ftype === 1) {
            return file
          }
        })
      }
    },
    isfile: function (files) { // 是否为文件
      if (files) {
        return files.filter(function (file) {
          if (file.Ftype === 0) {
            return file
          }
        })
      }
    },
    isLocation: function (files) { // 是否在当前文件夹下
      if (files) {
        var self = this
        return files.filter(function (file) {
          if (file.FPID === self.FNowID) {
            return file
          }
        })
      }
    },
    isShare: function (files) { // 是否被分享
      if (files) {
        return files.filter(function (file) {
          if (file.FShare === 1) {
            return file
          }
        })
      }
    },
    isFaved: function (files) { // 是否被收藏，返回文件
      if (files) {
        return files.filter(function (file) {
          if (file.Fav === 1) {
            return file
          }
        })
      }
    },

    // 下面的函数与上面无关！！！！
    isfav (fileInfo) { // 用来高亮被收藏文件的背景色的！！
      if (fileInfo.Fav === 1) {
        return 'background: #cfd8dc;'
      }
    },
    fileSize (a, b, c, d, e) { // 返回可阅读的文件大小
      const size =
        ((b = Math),
        (c = b.log),
        (d = 1e3),
        (e = (c(a) / c(d)) | 0),
        a / b.pow(d, e)).toFixed(2) +
        ' ' +
        (e ? 'kMGTPEZY'[--e] + 'B' : 'Bytes')
      return size
    },

    userinit () { // 数据更新函数，包括用户的信息，文件的目录
      axios
        .post('/serverA/controller.php', null, {
          headers: {
            APPFUNC: 3
          }
        })
        .then(function (res) {
          if (res.data) {
            localStorage.Sex = res.data['Sex']
            localStorage.Regdate = res.data['Regdate']
            localStorage.VIPend = res.data['VIPend']
            localStorage.mail = res.data['mail']
            localStorage.Phone = res.data['Phone']
          }
        })
      axios
        .post('/serverB/controller.php', null, {
          headers: {
            APPFUNC: 3
          }
        })
        .then(res => {
          this.$data.fileIndex = res.data
        })
      axios
        .post('/serverB/controller.php', null, {
          headers: {
            APPFUNC: 12
          }
        })
        .then(res => {
          this.$data.StorageUsed = res.data.StorageUsed
          this.$data.StorageTotal = res.data.StorageTotal
        })
    },

    diskcolor (input) { // 高亮被选中列表的颜色
      if (this.fontview === input) {
        return 'background-color:#eee;'
      }
    },

    async download (Fileinfo) { // 异步下载函数
      if (Fileinfo.FID != null) {
        try {
          let dec = await exhttp.post('/serverA/controller.php', Fileinfo, {
            headers: {
              APPFUNC: 5
            }
          })
          this.mulitpartsdownload(Fileinfo, dec)
          return new Promise((resolve, reject) => {
            if (dec !== null) {
              resolve(dec)
            } else {
              reject(dec)
            }
          })
        } catch (err) {
          alert('出现错误')
        }
      }
    },

    async ondec (decrypted, decipher, arrayBuffer) { // 文件解密，异步，但是还是会带来卡顿，建议新建webworker实现
      decrypted += decipher.output.getBytes()
      decipher.update(forge.util.createBuffer(arrayBuffer.data))
      var dec = {}
      dec.decrypted = decrypted
      dec.decipher = decipher
      return new Promise((resolve, reject) => {
        if (dec !== null) {
          resolve(dec)
        } else {
          reject(dec)
        }
      })
    },

    async mulitpartsdownload (Fileinfo, dec) { // 多块文件下载函数
      var Fkey = forge.util.hexToBytes(dec.data.Fkey)
      var FIV = forge.util.hexToBytes(dec.data.FIV)
      const totalParts = Math.ceil(Fileinfo.Fsize / this.multipartChunkSize)
      var i = 0
      var decrypted = ''
      var decipher = forge.cipher.createDecipher('AES-CFB', Fkey) // 初始化解密信息
      decipher.start({ iv: FIV })
      do {
        let obj = {}
        obj.FID = Fileinfo.FID
        obj.PID = i
        let finfo = JSON.stringify(obj) // 请求文件
        let arrayBuffer = await exhttp.post('/serverB/controller.php', finfo, {
          headers: {
            APPFUNC: 6
          },
          responseType: 'arraybuffer'
        })

        let ktmp = await this.ondec(decrypted, decipher, arrayBuffer) // 调用解密，
        decrypted = ktmp.decrypted
        decipher = ktmp.decipher
        /* decrypted += decipher.output.getBytes();
        decipher.update(forge.util.createBuffer(arrayBuffer.data)); */
        this.progress = i / totalParts
        // console.log(encrypted);
        i++
      } while (i < totalParts)
      decipher.finish()// 结束解密
      this.progress = i / totalParts
      decrypted += decipher.output.getBytes()// 如果最后的块大小小于最小解密块大小，就会在这里输出
      let filencd = new Blob([util.str2ab(decrypted)])// 生成blob
      FileDownloader(filencd, Fileinfo.FName)// 从内存中的blob下载，这个函数其实自己写也很好写，但是我懒。。。
    },

    deletefile (ID, flag) { // 删除文件函数
      if (ID != null) {
        var r
        if (flag === 0) {
          r = confirm('确定要删除这个文件吗?')
        } else {
          r = true
        }
        if (r === true) {
          var obj = {}
          obj.FID = ID
          var finfo = JSON.stringify(obj)
          axios
            .post('/serverB/controller.php', finfo, {
              headers: {
                APPFUNC: 7
              }
            })// 删除文件
            .then(res => {
              if (res.data === 1) {
                axios
                  .post('/serverA/controller.php', finfo, {
                    headers: {
                      APPFUNC: 7
                    }
                  })// 删除密钥
                  .then(res => {
                    if (res.data === 1) {
                      this.userinit()
                    }
                  })
              }
            })
        }
        r = false
      }
    },

    processqueue (m) { // 多选队列函数
      if (!this.Selected.length) {
        alert('请选择后再进行操作')
        return 0
      }
      var tmp = 1
      if (m === 1) {
        while (tmp) {
          tmp = this.Selected.shift()
          if (tmp) {
            this.download(tmp)
          }
        }
      }
      if (m === 2) {
        var r = confirm('确定要删除这些文件吗?')
        if (r === true) {
          while (tmp) {
            tmp = this.Selected.shift()
            if (tmp) {
              this.deletefile(tmp.FID, true)
            }
          }
        }
      }
      this.NowP++
    },

    vshowdash () { // 显示用户信息
      this.$data.showdash = !this.$data.showdash
    },

    vshowup () { // 显示上传器
      this.$data.showup = !this.$data.showup
      this.userinit()
    },
    vshownf () { // 显示新建文件夹界面
      this.$data.shownf = !this.$data.shownf
    },
    newFolder () { // 新建文件夹函数
      if (this.NFname === '') {
        alert('请输入文件夹名称！')
      } else {
        var fdinfo = {}
        fdinfo.FPID = this.FNowID
        fdinfo.NFname = this.NFname
        var finfo = JSON.stringify(fdinfo)
        axios
          .post('/serverB/controller.php', finfo, {
            headers: {
              APPFUNC: 8
            }
          })
          .then(res => {
            if (res.data !== 'error') {
              this.NFname = ''
              this.userinit()
            }
          })
        this.shownf = !this.shownf
      }
    },
    chgview () { // 改变视图时清空目录变量信息
      this.FullPName = []
      this.FullFPID = [0]
      this.FPID = null
      this.FNowID = 0
      this.FNowName = ''
    },
    enterFolder (fileInfo) { // 进入文件夹
      if (fileInfo != null) {
        this.FullPName.push(fileInfo.FName)
        this.FullFPID.push(fileInfo.FPID)
        this.FPID = fileInfo.FPID
        this.FNowID = fileInfo.FID
        this.FNowName = fileInfo.FName
        this.search = ''
      }
    },
    backtoFolder (FID) { // 返回文件夹
      if (FID != null) {
        this.FPID = FID
        this.FNowName = this.FullPName.pop()
        this.FNowID = this.FullFPID.pop()
        this.search = ''
      }
    },

    getLocation (locas) { // 文件指示器指向函数
      var l = ''
      locas.forEach(element => {
        l = l + '/' + element
      })
      return l
    },

    Fav (FileInfo) { // 分享文件和取消分享文件
      if (FileInfo.Fav === 0) {
        axios
          .post('/serverB/controller.php', FileInfo, {
            headers: {
              APPFUNC: 9
            }
          })
          .then(res => {
            if (res) {
              this.userinit()
            }
          })
      } else if (FileInfo.Fav === 1) {
        axios
          .post('/serverB/controller.php', FileInfo, {
            headers: {
              APPFUNC: 11
            }
          })
          .then(res => {
            if (res) {
              this.userinit()
            }
          })
      }
    },
    /**
     * AL Created BY WZQ
     * 判断是否时文件夹，如果是文件夹
     * 则进入
     * 如果是文件，则删除
     * 如果文件夹为空，则返回上一文件夹 然后删除该文件夹
     * 一直返回到文件夹ID等于记录下的文件夹ID为止
     * 这部分就不再另写注释
     */

    delteFolder (fileinfo) {
      this.FullFPID.push(fileinfo.FID)
      this.fileIndex.forEach(fileinfo1 => {
        if (fileinfo1.FPID === fileinfo.FID) {
          if (fileinfo1.Ftype === 0) {
            this.deletefile(fileinfo1.FID, 1)
          } else if (fileinfo1.Ftype === 1) {
            // console.log(fileinfo1.FID);
            this.delteFolder(fileinfo1)
            // console.log(123);
          }
        }
      })
      let FID = this.FullFPID.pop()
      // console.log("FID:" + FID);
      this.deletefile(FID, 1)
    },

    // 显示文件详情
    vshowdetail (fileinfo) {
      this.detailinfo = fileinfo
      this.showdetail = true
    },

    // 分享文件，由于服务器解密需要时间，所以写了异步
    async sharethefile (fileinfo) {
      if (fileinfo.FID) {
        var obj = {}
        obj.FID = fileinfo.FID
        var finfo = JSON.stringify(obj)
        let res = await exhttp.post('/serverA/controller.php', finfo, {
          headers: {
            APPFUNC: 5
          }
        })
        var t2 = {}
        t2.FID = res.data.FID
        t2.FIV = res.data.FIV
        t2.Fkey = res.data.Fkey
        t2.FName = fileinfo.FName
        var sinfo = JSON.stringify(t2)
        axios
          .post('/serverB/controller.php', sinfo, {
            headers: {
              APPFUNC: 5
            }
          })
          .then(res2 => {
            if (res2) {
              this.detailinfo.FShare = 1
              this.FDIR = res2.data
              var target = document.getElementById('sctxt')
              target.value =
                'http://192.168.139.128/serverB' +
                res2.data.substr(1, res2.data.length) +
                '/' +
                fileinfo.FName
            }
          })
      }
    },

    getshare (fileinfo) { // 得到分享的网址
      if (fileinfo.FShare === 0) {
        return 0
      } else if (fileinfo.FShare === 1) {
        axios
          .post('/serverB/controller.php', fileinfo, {
            headers: {
              APPFUNC: 13
            }
          })
          .then(res2 => {
            this.FDIR = res2.data
            var target = document.getElementById('sctxt')
            target.value =
              'http://192.168.139.128/serverB' +
              res2.data.substr(1, res2.data.length) +
              '/' +
              fileinfo.FName
          })
        return 1
      }
    },

    shareurl () { // 复制地址到剪贴板
      var target = document.getElementById('sctxt')
      target.select()
      document.execCommand('Copy')
      alert('文件地址已复制到剪贴板！')
    },

    unsharethefile (detailinfo) { // 取消分享
      var info = {}
      info.FName = detailinfo.FName
      info.FID = detailinfo.FID
      var pinfo = JSON.stringify(info)
      axios
        .post('/serverB/controller.php', pinfo, {
          headers: {
            APPFUNC: 14
          }
        })
        .then(res2 => {
          if (res2) {
            this.userinit()
            this.detailinfo.FShare = 0
          }
        })
    }
  }
}
</script>
<style src="../../css/main.css" scoped>
</style>
<style scoped>
.pname {
  display: inline-block;
  height: 40px;
  position: absolute;
}

#newfoler {
  position: fixed;
  width: 100%;
  height: 100%;
  margin: 0;
  background-color: #2b2929a2;
  z-index: 20;
}

#pf1 {
  margin: 5px 10px;
  border: none;
  border-radius: 0%;
  width: 280px;
  height: 40px;
  background-color: whitesmoke;
}

::-webkit-scrollbar {
  width: 5px;
  opacity: 0.8;
  background-color: #fefefe;
}

::-webkit-scrollbar-thumb {
  /*滚动条里面小方块*/
  -webkit-box-shadow: inset 0 0 5px rgba(0, 0, 0, 0.2);
  background: #eee;
}

#progress-bar {
  width: 200px;
  height: 30px;
  background-color: #eee;
}
#progress {
  width: 200px;
  height: 30px;
  background-color: limegreen;
  margin: 0;
  float: left;
}

#UploadePage {
  position: fixed;
  left: 30%;
  top: 15%;
  z-index: 20;
  background-color: #eee;
  border-radius: 2%;
  width: 40%;
  height: 60%;
}

#detail {
  position: fixed;
  left: 30%;
  top: 25%;
  z-index: 20;
  background-color: #eee;
  border-radius: 2%;
  width: 35%;
  height: 40%;
}

.dtline {
  margin: 5px 10px;
  width: 400px;
  height: 36px;
  font-size: 16px;
}

ul {
  list-style-type: none;
  padding: 0;
}
li {
  margin-bottom: 30px;
}
</style>
