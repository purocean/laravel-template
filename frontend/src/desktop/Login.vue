<template>
  <div class="login">
    <div id="particle"></div>
    <Menu mode="horizontal" theme="light" activeName="login" class="nav">
      <div class="layout-nav">
        <router-link class="logo" to="/">Laravel template</router-link>
        <MenuItem name="login" class="layout-menu">
          <Icon type="ios-person"></Icon>
          登录
        </MenuItem>
      </div>
    </Menu>
    <Card class="login-card">
      <h1>登录</h1>
      <a class="login-switcher" @click="switchLogin()">
        <span v-show="useQr">账号密码登录</span>
        <span v-show="!useQr">二维码登录</span>
      </a>
      <div v-show="useQr" class="qrlogin">
        <h4>{{ qrStatus }}</h4>
        <img :src="qrImg" />
      </div>
      <iForm
        v-show="!useQr"
        ref="login-form"
        :model="formInline"
        :rules="ruleInline"
        class="login-form">
        <FormItem prop="username">
          <Input type="text" v-model="formInline.username" placeholder="Username" @keyup.enter.native="handleSubmit()">
            <Icon type="ios-person-outline" slot="prepend"></Icon>
          </Input>
        </FormItem>
        <FormItem prop="password">
          <Input type="password" v-model="formInline.password" placeholder="Password" @keyup.enter.native="handleSubmit()">
            <Icon type="ios-locked-outline" slot="prepend"></Icon>
          </Input>
        </FormItem>
        <FormItem>
          <Button type="primary" @click.native="handleSubmit()" :loading="loading" long>登录</Button>
        </FormItem>
      </iForm>
    </Card>
    <div id="demo" style="width: 400px; height: 250px;"></div>
  </div>
</template>

<script>
import Http from '@/utils/Http'
import Auth from '@/auth/Auth'
import QRCode from 'jr-qrcode'
import Particleground from 'Particleground.js'

const Particle = Particleground.particle

export default {
  name: 'login',
  data () {
    return {
      loading: false,
      useQr: true,
      qrImg: '',
      qrStatus: '请稍后……',
      qrNonce: '',
      loginTimer: null,
      codeTimer: null,
      formInline: {
        username: '',
        password: ''
      },
      ruleInline: {
        username: [
          { required: true, message: '请填写用户名', trigger: 'blur' }
        ],
        password: [
          { required: true, message: '请填写密码', trigger: 'blur' },
        ]
      }
    }
  },
  mounted () {
    Auth.setAccessToken('')
    Auth.setPermissions({})
    Auth.setRoles({})
    Auth.setUser('')
    let x = new Particle('#particle', {
      color: ['#6eb2f7'],
      maxSpeed: 0.5,
      minSpeed: 0.2,
    })
    console.log(x)

    this.updateQr()
    window.document.title = '登录'
  },
  beforeDestroy: function () {
    window.removeEventListener('resize', this.handleResize)
  },
  methods: {
    handleSubmit () {
      this.$refs['login-form'].validate((valid) => {
        if (valid) {
          this.loading = true

          Http.fetch('/api/login', {
            method: 'post',
            body: this.formInline
          }, result => {
            if (result.status === 'ok') {
              this.success(result.data)
              this.$Message.success(result.message)
            } else {
              this.$Message.error(result.message)
            }

            this.loading = false
          })
        }
      })
    },
    switchLogin () {
      this.useQr = !this.useQr
    },
    success (data) {
      Auth.setUser(data.user)
      Auth.setAccessToken(data.token)
      let next = this.$route.query.next || '/'
      this.$router.replace(next.replace(/__refresh__\d*$/, ''))
      this.loginTimer && window.clearInterval(this.loginTimer)
      this.codeTimer && window.clearInterval(this.codeTimer)
    },
    updateQr () {
      Http.fetch('/api/qrcode', {}, result => {
        if (result.status === 'ok') {
          this.qrNonce = result.data.nonce
          this.qrImg = QRCode.getQrBase64(result.data.url)
          this.qrStatus = '请扫码'

          this.loginTimer = setInterval(() => {
            if (this.useQr) {
              Http.fetch(
                '/api/qrlogin',
                {showLoading: false, method: 'post', body: {nonce: this.qrNonce}},
                result => {
                  if (result.status === 'ok') {
                    this.success(result.data)
                    this.$Message.success(result.message)
                  } else {
                    this.qrStatus = result.message
                  }
                }
              )
            }
          }, 3000)

          this.codeTimer = window.setTimeout(() => {
            this.loginTimer && window.clearInterval(this.loginTimer)
            this.updateQr()
          }, result.data.expires * 1000)
        } else {
          this.$Message.error(result.message)
        }
      })
    }
  }
}
</script>

<style scoped>
.logo {
  color: #444;
  font-size: 1.5em;
}

.login {
  position: relative;
}

#particle {
  position: fixed;
  top: 0;
  right: 0;
  bottom: 0;
  left: 0;
}

.layout-nav {
  width: 90%;
  margin: 0 auto;
}

.layout-menu.ivu-menu-item {
  float: right;
}

.login-card {
  width: 350px;
  margin: 80px auto;
}

.login-card h1 {
  font-size: 1.3em;
  display: inline-block;
}

.login-switcher {
  vertical-align: top;
  float: right;
}

.login-form {
  margin-top: 20px;
}

.qrlogin {
  text-align: center;
}
</style>
