<template>
  <Layout class="error" activeNav="/error" activeSide="/error" title="发生了一点错误">
    <div class="info">
      <div>
        <h1>错误 {{ $route.query.code }} </h1>
        <span> {{ $route.query.message }} </span>
      </div>
      <router-link :to="$route.query.from">返回上一页</router-link>
    </div>
  </Layout>
</template>

<script>
import Layout from '@/layouts/Desktop'

export default {
  name: 'error',
  components: { Layout },
  data () {
    return {
    }
  },
}
</script>

<style scoped>
.info {
  text-align: center;
}

h1 {
  font-size: 30px;
  display: inline-block;
}

span {
  font-size: 16px;
  margin-left: 10px;
}
</style>
