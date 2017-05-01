<template>
  <div>
    <div style="margin: 10px 0">
      <slot name="action"></slot>
    </div>
    <SpinWrapper :loading="loading">
      <Table
        :context="self"
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
            :current="tableData.current_page"
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
  props: ['url', 'columns', 'context'],
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
    refresh () {
      this.loadData(this.tableData.current_page)
    },
    loadData (page) {
      this.loading = true
      Http.fetch(this.url.replace('{page}', page), {}, result => {
        if (result.status === 'ok') {
          this.tableData = result.data
          this.tableData.data = this.tableData.data.map(elem => {
            return Object.assign({
              __meta_current_page: result.data.current_page,
              __meta_from: result.data.from,
              __meta_last_page: result.data.last_page,
              __meta_next_page_url: result.data.next_page_url,
              __meta_per_page: result.data.per_page,
              __meta_prev_page_url: result.data.prev_page_url,
              __meta_to: result.data.to,
              __meta_total: result.data.total,
            }, elem)
          })
        } else {
          this.$Message.error(result.message)
        }
        this.loading = false
      })
    },
  },
  watch: {
    url () {
      this.loadData(1)
    }
  }
}
</script>

<style scoped>

</style>
