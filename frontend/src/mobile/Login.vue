<template>
  <p> {{ message }} </p>
</template>

<script>
import Http from '@/utils/Http'
import Auth from '@/auth/Auth'

export default {
  name: 'login',
  data () {
    return {
      message: '请稍后……'
    }
  },
  mounted () {
    Auth.setAccessToken('')
    Auth.setPermissions({})
    Auth.setRoles({})
    Auth.setUser('')

    let next = this.$route.query.next || '/'

    if (!this.$route.query.code) {
      this.message = '获取微信授权……'
      location.href = '/api/wxcode?next=' + encodeURIComponent(next)
      return
    }

    this.message = '登录中……'

    Http.fetch(
      '/api/codelogin',
      {method: 'post', body: {code: this.$route.query.code}},
      result => {
        if (result.status === 'ok') {
          Auth.setUser(result.data.user)
          Auth.setAccessToken(result.data.token)

          this.message = '登录成功'

          setTimeout(() => {
            this.$router.replace(next.replace(/__refresh__\d{13}/g, ''))
          }, 500)
        } else {
          this.message = result.message
        }
      }
    )
  },
  methods: {
  }
}
</script>
