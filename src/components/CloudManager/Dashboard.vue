<template>
  <!---用户简要信息显示界面--Designed BY WZQ --->
  <div id="dash">
    <div class="bc">
      <div id="head"></div>
      <div id="name">用户名:{{username}}</div>
      <div id="Sex">性别:{{sex}}</div>
    </div>
    <div id="VIP" class="bc">
      <div id="VIPimg" v-bind:class="{'VIP':isvip}"></div>
      <div v-bind:class="{'VIP':isvip}">您是VIP会员</div>
      <div id="VIPEND" v-bind:class="{'VIP':isvip}">{{VIPend}}</div>
      <button v-bind:class="{'VIP':isvip}">续费会员</button>
      <button style="background-color:lightcoral;width:40px;"><router-link to="/Logout"><p>X</p></router-link></button>
    </div>
    <!--用来显示用户的信息等，但是会员这里没写-->
  </div>
</template>
<script>
export default {
  data () {
    return {
      username: localStorage.username,
      sex: localStorage.Sex,
      VIPend: localStorage.VIPend,
      isvip: false
    }
  },
  methods: {

  }
}
</script>
<style scopped>
#dash {
  position: fixed;
  top: 30px;
  right: 60px;
  z-index: 10;
  background-color:#fff;
  border-radius: 5;
  width: 340px;
  height: 180px;
  font-family: "Lucida Sans", "Lucida Sans Regular", "Lucida Grande",
    "Lucida Sans Unicode", Geneva, Verdana, sans-serif;
  font-size: 18px;
}

#dash .bc {
  float: left;
  width: 120px;
  height: 160px;
  margin: 10px 20px;
}

#head {
  width: 80px;
  height: 80px;
  background-image: url("../../css/img/user/product_logo_avatar_circle_blue_color_48.png");
  background-repeat: no-repeat;
  background-size: 100%;
}

#VIPimg {
  width: 30px;
  height: 20px;
  background-image: url("../../css/img/user/VIP.png");
  background-repeat: no-repeat;
  background-origin: content-box;
  background-size: 100%;
}

</style>
