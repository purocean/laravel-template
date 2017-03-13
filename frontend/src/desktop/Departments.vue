<template>
  <Layout class="departments" activeNav="/departments" :side="side" activeSide="/departments">
    <Card>
      <Crud ref="crud" resource="departments" :columns="tableColumns">
        <div slot="action">
          <Button :loading="syncing" type="primary" @click.native="sync()">从企业号同步</Button>
        </div>
      </Crud>
    </Card>
  </Layout>
</template>

<script>
import Http from '@/utils/Http'
import Layout from '@/layouts/Desktop'
import Crud from '@/components/Crud'

export default {
  name: 'departments',
  components: { Layout, Crud },
  data () {
    return {
      side: [{name: '/departments', text: '部门管理', icon: 'person-stalker'}],
      tableColumns: [
        {
          title: 'ID',
          key: 'id',
          width: 80
        },
        {
          title: '名称',
          key: 'name',
        },
        {
          title: '更新时间',
          key: 'created_at',
        }
      ],
      syncing: false,
    }
  },
  methods: {
    sync () {
      this.syncing = true
      Http.fetch(`/api/departments/sync`, {method: 'post'}, result => {
        if (result.status === 'ok') {
          this.$Message.success(result.message)
        } else {
          this.$Message.error(result.message)
        }

        this.syncing = false

        this.$refs.crud.loadData(1)
      })
    }
  }
}
</script>

<style scoped>

</style>
