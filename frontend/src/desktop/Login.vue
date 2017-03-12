<template>
  <div>
    <Menu mode="horizontal" theme="light" activeName="login" class="nav">
      <div class="layout-nav">
        <router-link class="logo" to="/">Laravel template</router-link>
        <Menu-item name="login" class="layout-menu">
          <Icon type="ios-person"></Icon>
          登录
        </Menu-item>
      </div>
    </Menu>
    <Card class="login-card">
      <h1>登录</h1>
      <a class="login-switcher" href="">二维码登录</a>
      <iForm ref="login-form" :model="formInline" :rules="ruleInline" class="login-form">
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
  </div>
</template>

<script>
import Http from '@/utils/Http'
import Auth from '@/auth/Auth'

export default {
  name: 'login',
  data () {
    return {
      loading: false,
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
              Auth.setUser(result.data.user)
              Auth.setAccessToken(result.data.token)
              this.$router.replace(this.$route.query.next || '/')

              this.$Message.success(result.message)
            } else {
              this.$Message.error(result.message)
            }

            this.loading = false
          })
        }
      })
    },
  }
}
</script>

<style scoped>
.logo {
  color: #444;
  font-size: 1.5em;
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
</style>
