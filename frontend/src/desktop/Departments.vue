<template>
  <Layout class="departments" activeNav="/departments" :side="side" activeSide="/departments">
    <Card>
      <SpinWrapper :loading="!tableData.data">
        <Table
          :content="self"
          :data="tableData.data"
          :columns="tableColumns"
          size="small"
          loading="true"
          stripe
          border></Table>
        <div style="margin: 10px;overflow: hidden">
          <div style="float: right;">
            <Page
              size="small"
              :total="tableData.total"
              :page-size="tableData.per_page"
              :current="tableData.current"
              @on-change="changePage"
              show-total
              show-elevator></Page>
          </div>
        </div>
      </SpinWrapper>
    </Card>
  </Layout>
</template>

<script>
import Http from '@/utils/Http'
import Layout from '@/layouts/Desktop'
import SpinWrapper from '@/components/SpinWrapper'

export default {
  name: 'departments',
  components: { Layout, SpinWrapper },
  mounted () {
    Http.fetch('/api/departments/list', {}, result => {
      if (result.status === 'ok') {
        this.tableData = result.data
      } else {
        this.$Message.error(result.message)
      }
    })
  },
  data () {
    return {
      side: [{name: '/departments', text: '部门管理', icon: 'person-stalker'}],
      self: this,
      tableData: {},
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
      ]
    }
  },
  methods: {
    changePage (page) {

    }
  }
}
</script>

<style scoped>

</style>
