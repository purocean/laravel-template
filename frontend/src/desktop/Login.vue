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
      <i-form ref="login-form" :model="formInline" :rules="ruleInline" class="login-form">
        <Form-item prop="user">
        <Input type="text" v-model="formInline.user" placeholder="Username">
            <Icon type="ios-person-outline" slot="prepend"></Icon>
          </Input>
        </Form-item>
        <Form-item prop="password">
        <Input type="password" v-model="formInline.password" placeholder="Password">
            <Icon type="ios-locked-outline" slot="prepend"></Icon>
          </Input>
        </Form-item>
        <Form-item>
          <i-button type="primary" @click.native="handleSubmit('formInline')" long>登录</i-button>
        </Form-item>
      </i-form>
    </Card>
  </div>
</template>

<script>
export default {
  name: 'login',
  data () {
    return {
      formInline: {
        user: '',
        password: ''
      },
      ruleInline: {
        user: [
          { required: true, message: '请填写用户名', trigger: 'blur' }
        ],
        password: [
        { required: true, message: '请填写密码', trigger: 'blur' },
          { type: 'string', min: 6, message: '密码长度不能小于6位', trigger: 'blur' }
        ]
      }
    }
  },
  methods: {
    handleSubmit () {
      this.$refs['login-form'].validate((valid) => {
        if (valid) {
          this.$Message.success('提交成功!')
        } else {
          this.$Message.error('表单验证失败!')
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
