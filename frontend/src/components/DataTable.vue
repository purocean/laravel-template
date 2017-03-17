<template>
  <div>
    <div style="margin: 10px 0">
      <slot name="action"></slot>
    </div>
    <SpinWrapper :loading="loading">
      <Table
        :content="self"
        :data="tableData.data"
        :columns="columns"
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
  </div>
</template>

<script>
import Http from '@/utils/Http'
import SpinWrapper from '@/components/SpinWrapper'

export default {
  name: 'data-table',
  components: { SpinWrapper },
  props: ['resource', 'columns', 'context', 'search'],
  mounted () {
    this.loadData(1)
  },
  data () {
    return {
      self: this.context || this,
      tableData: {},
      loading: true,
    }
  },
  methods: {
    changePage (page) {
      this.loadData(page)
    },
    loadData (page) {
      this.loading = true
      Http.fetch(
        `/api/${this.resource}/list?page=${page}&search=` + encodeURIComponent(this.search || ''),
        {},
        result => {
          if (result.status === 'ok') {
            this.tableData = result.data
          } else {
            this.$Message.error(result.message)
          }
          this.loading = false
        }
      )
    },
  }
}
</script>

<style scoped>

</style>
