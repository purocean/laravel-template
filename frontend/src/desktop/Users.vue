<template>
  <Layout class="users" activeNav="/users" :side="side" activeSide="/users">
    <Card>
      <DataTable ref="dataTable" resource="users" :columns="tableColumns">
        <div slot="action">
          <Button :loading="syncing" type="primary" @click.native="sync()">从企业号同步</Button>
        </div>
      </DataTable>
    </Card>
  </Layout>
</template>

<script>
import Http from '@/utils/Http'
import Layout from '@/layouts/Desktop'
import DataTable from '@/components/DataTable'

export default {
  name: 'users',
  components: { Layout, DataTable },
  data () {
    return {
      side: [{name: '/users', text: '用户管理', icon: 'person-stalker'}],
      tableColumns: [
        {
          title: 'ID',
          key: 'id',
          width: 80
        },
        {
          title: '用户名',
          key: 'username',
        },
        {
          title: '名字',
          key: 'name',
        },
        {
          title: '邮箱',
          key: 'email',
        },
        {
          title: '更新时间',
          key: 'updated_at',
        }
      ],
      syncing: false,
    }
  },
  methods: {
    sync () {
      this.syncing = true
      Http.fetch(`/api/users/sync`, {method: 'post'}, result => {
        if (result.status === 'ok') {
          this.$Message.success(result.message)
        } else {
          this.$Message.error(result.message)
        }

        this.syncing = false

        this.$refs.dataTable.loadData(1)
      })
    }
  }
}
</script>

<style scoped>

</style>
