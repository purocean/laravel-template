<template>
  <Layout class="users" activeNav="/users" :side="side" activeSide="/users">34567890</Layout>
</template>

<script>
import Layout from '@/layouts/Desktop'

export default {
  name: 'users',
  components: { Layout },
  data () {
    return {
      side: [{name: '/users', text: '用户管理', icon: 'person-stalker'}]
    }
  },
}
</script>

<style scoped>

</style>
