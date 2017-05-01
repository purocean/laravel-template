<template>
  <div>
    <Layout class="users" activeNav="/users" :side="side" activeSide="/users" title="用户管理">
      <Card>
        <DataTable :search="searchStr" :context="self" ref="dataTable" :url="url" :columns="tableColumns">
          <div slot="action" style="display: flex; justify-content:space-between;">
            <Button :loading="syncing" type="primary" @click.native="sync()">从企业号同步</Button>
            <div v-show="!$route.params.rolename" style="display:inline-block; width: 20em;">
              <Input v-model="searchStr" placeholder="搜索用户..." @on-enter="search">
                <Button slot="append" icon="ios-search" @click="search" style="cursor: pointer;"></Button>
              </Input>
            </div>
          </div>
        </DataTable>
      </Card>
    </Layout>
    <Modal
      v-model="showRoles"
      title="分配角色"
      @on-ok="attachRoles">
      <Checkbox-group v-model="checkRoles">
        <Checkbox v-for="role in allroles" :key="role.id" :label="role.name">{{ role.display_name }}</Checkbox>
      </Checkbox-group>
    </Modal>
    <Modal
      v-model="showMessage"
      title="发送消息"
      @on-ok="sendMessage">
      <Input type="textarea" :rows="4" placeholder="请输入..." v-model="message"></Input>
    </Modal>
  </div>
</template>

<script>
import Http from '@/utils/Http'
import Layout from '@/layouts/Desktop'
import DataTable from '@/components/DataTable'

export default {
  name: 'users',
  components: { Layout, DataTable },
  mounted () {
    Http.fetch('/api/rbac/roles', {}, result => {
      if (result.status !== 'ok') {
        this.$Message.error(result.message)
      }

      this.allroles = result.data
      this.side = this.side.concat(result.data.map(elem => {
        return {name: `/users/${elem.name}`, text: elem.display_name, link: `/users/${elem.name}`, icon: 'person-stalker'}
      }))
    })
  },
  data () {
    return {
      self: this,
      checkRoles: [],
      message: '',
      searchStr: '',
      showRoles: false,
      showMessage: false,
      allroles: null,
      username: null,
      url: 'api/users?page={page}',
      side: [{name: '/users', text: '全部用户', link: '/users', icon: 'person-stalker'}],
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
        },
        {
          title: '操作',
          key: 'control',
          width: 110,
          render (row) {
            return `<i-button
              type="ghost"
              shape="circle"
              icon="chatbubble-working"
              @click="showMessageModal('${row.username}')"></i-button>
            <i-button
              type="ghost"
              shape="circle"
              icon="edit"
              @click="showRolesModal('${row.username}')"></i-button>`
          }
        }
      ],
      syncing: false,
    }
  },
  methods: {
    showRolesModal (user) {
      this.checkRoles = []
      this.showRoles = true
      this.username = user

      Http.fetch(`/api/rbac/roles/${user}`, {}, result => {
        if (result.status === 'ok') {
          this.checkRoles = result.data.map(elem => {
            return elem.name
          })
        } else {
          this.$Message.error(result.message)
        }
      })
    },
    showMessageModal (user) {
      this.message = ''
      this.showMessage = true
      this.username = user
    },
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
    },
    attachRoles () {
      Http.fetch(
        `/api/rbac/roles/attach`,
        {
          method: 'post',
          body: {username: this.username, rolenames: this.checkRoles}
        },
        result => {
          if (result.status === 'ok') {
            this.$Message.success(result.message)
          } else {
            this.$Message.error(result.message)
          }
        }
      )
    },
    sendMessage () {
      Http.fetch(
        `/api/users/sendmessage`,
        {
          method: 'post',
          body: {username: this.username, message: this.message}
        },
        result => {
          if (result.status === 'ok') {
            this.$Message.success(result.message)
          } else {
            this.$Message.error(result.message)
          }
        }
      )
    },
    search () {
      this.url = `api/users?search=${this.searchStr}&page={page}&_t=${new Date().getTime()}`
    },
  },
  watch: {
    $route () {
      if (this.$route.params.rolename) {
        this.searchStr = ''
        this.url = 'api/rbac/roles/users/' + encodeURIComponent(this.$route.params.rolename) + '?page={page}'
      } else {
        this.url = 'api/users?page={page}'
      }
    }
  }
}
</script>

<style scoped>

</style>
