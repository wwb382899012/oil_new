<template>
  <div v-min-height class="login-box">
    <header>
      <h1>能源产业科技集成平台</h1>
      <p class="version">V2.0</p>
    </header>
    <ul>
       <li>
         <span class="input-icon">&#xe641;</span><input type="text" placeholder="用户名" v-model="username">
       </li>
       <li>
         <span class="input-icon">&#xe633;</span><input type="password" placeholder="登录密码" v-model="password">
       </li>
        <button @click.stop="doLogin">登录</button>
    </ul>
    <footer>
      <p>
      <img :src="require('@/assets/img/icon/mini-logo.png')">
       <span>中优国聚</span>
      </p>
    </footer>
  </div>
</template>
<script>
import { Indicator } from 'mint-ui'
export default {
  components: {
  },
  data () {
    return {
      username: '',
      password: ''
    }
  },
  methods: {
    async doLogin () {
      let {username, password} = this
      if (!username) {
        return this.$toptips('请输入用户名')
      }
      if (!password) {
        return this.$toptips('请输入密码')
      }
      Indicator.open()
      let {success, msg, status} = await this.$store.dispatch('login', {username, password})
      Indicator.close()
      if (status !== 0 && !success) {
        this.$toptips(msg)
      } else if (this.$route.query.redirect) {
        location.href = this.$route.query.redirect
      } else if (document.referrer && !document.referrer.match(/login/)) {
        location.href = document.referrer
      }
    }
  }
}
</script>
<style scoped>
.login-box{
  display: flex;
  flex-direction: column;
  text-align: center;
  padding: 30px 40px;
  box-sizing: border-box;
  /* position: absolute; */
  /* top:0; */
  /* bottom:0; */
  /* width: 100%; */
  /* height: 100%; */
  h1{
    font-size: 24px;
    color: #1ABC9C;
  }
  .version{
    font-size:18px;
    color: #00CC00;
    margin: 40px auto;
  }
  input{
    border: none;
    background: transparent;
    font-size: 14px;
    padding: 15px;
    flex:1;
  }
  .input-icon{
    font-family: iconfont;
    color: #666666; 
  }
  button{
    border: none;
    background: rgba(26, 188, 156, 1);
    color: #ffffff;
    border-radius: 168px;
    height: 45px;
    display: flex;
    justify-content: center;
    align-items: center;
    width: 100%;
    font-size: 16px;
    margin-top: 80px;
  }
  ul{
    display: block;
    text-align: left;
    li{
      display: flex;
      align-items: center;
      border-bottom: 1px solid rgba(228, 228, 228, 1);
    }
  }
  footer{
    display: flex;
    justify-content: center;
    align-items: flex-end;
    padding-bottom: 50px;
    flex: 1;
    font-size: 18px;
    font-weight: 700;
    p{
      display:flex;
      justify-content: center;
      align-items:cetner;
    }
  }
}
</style>
