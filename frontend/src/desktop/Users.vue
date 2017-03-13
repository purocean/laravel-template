<template>
  <Layout class="users" activeNav="/users" :side="side" activeSide="/users">
    <Card>
      <iTable
        :content="self"
        :data="tableData"
        :columns="tableColumns"
        size="small"
        stripe
        border></iTable>
      <div style="margin: 10px;overflow: hidden">
          <div style="float: right;">
              <Page :total="100" :current="1" @on-change="changePage"></Page>
          </div>
      </div>
    </Card>
  </Layout>
</template>

<script>
import Layout from '@/layouts/Desktop'

export default {
  name: 'users',
  components: { Layout },
  data () {
    return {
      side: [{name: '/users', text: '用户管理', icon: 'person-stalker'}],
      self: this,
      tableColumns: [
        {
          title: 'ID',
          key: 'id'
        },
        {
          title: '名称',
          key: 'name',
        },
        {
          title: '更新时间',
          key: 'updated_at',
        }
      ]
    }
  },
}
</script>

<style scoped>

</style>
