<template>
  <div class="qrlogin">
      <img src="~@/assets/img/info.png" />
      <h1>扫码登录</h1>
      <p class="name"> {{ name }} </p>
      <button id="login" :disabled="submit" @click="login"> {{ message }} </button>
  </div>
</template>

<script>
import Http from '@/utils/Http'
import Auth from '@/auth/Auth'

export default {
  name: 'qrlogin',
  data () {
    return {
      submit: false,
      message: '确认登录',
      name: Auth.getUser().name || Auth.getUser().username,
    }
  },
  mounted () {
    this.login(false)
  },
  methods: {
    login (confirmed = true) {
      if (confirmed) {
        this.message = '请稍后……'
        this.submit = true
      }

      Http.fetch(
        '/api/confirmqrlogin',
        {
          method: 'post',
          body: {
            nonce: this.$route.query.nonce,
            login: confirmed
          }
        },
        result => {
          if (result.status !== 'ok') {
            alert(result.message)
            window.WeixinJSBridge.invoke('closeWindow')
          }

          confirmed && window.WeixinJSBridge.invoke('closeWindow')
        }
      )
    }
  }
}
</script>

<style scoped>
  body {
    background-color: #f5f5f9;
  }

  .qrlogin {
    background-color:  #fff;
    margin-top: 30px;
    text-align: center;
    padding: 16px;
  }

  h1 {
    font-size: 1.5em;
  }

  img {
    width: 80px;
    height: 80px;
    display: inline-block;
  }

  .name {
    font-size: 14px;
    color: #888;
  }

  button {
    color: #fff;
    background-color: #108ee9;
    border: 1px solid #108ee9;
    width: 100%;
    display: inline-block;
    outline: 0 none;
    -webkit-appearance: none;
    -webkit-box-sizing: border-box;
    padding: 0;
    text-align: center;
    font-size: 20px;
    height: 50px;
    line-height: 50px;
    border-top-left-radius: 6px;
    border-top-right-radius: 6px;
    border-bottom-left-radius: 6px;
    border-bottom-right-radius: 6px;
    -webkit-background-clip: padding-box;
  }
</style>
