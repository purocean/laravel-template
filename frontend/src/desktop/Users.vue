<template>
  <div>
    <Layout class="users" activeNav="/users" :side="side" activeSide="/users" title="用户管理">
      <Card>
        <DataTable :search="searchStr" :context="self" ref="dataTable" resource="users" :columns="tableColumns">
          <div slot="action" style="display: flex; justify-content:space-between;">
            <Button :loading="syncing" type="primary" @click.native="sync()">从企业号同步</Button>
            <div style="display:inline-block; width: 20em;">
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
    Http.fetch('/api/users/allroles', {}, result => {
      if (result.status === 'ok') {
        this.$Message.success(result.message)
      } else {
        this.$Message.error(result.message)
      }

      this.allroles = result.data
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

      Http.fetch(`/api/users/roles?username=${user}`, {}, result => {
        if (result.status === 'ok') {
          this.checkRoles = result.data.map(function (elem) {
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
        `/api/users/attachroles`,
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
      console.log(this.searchStr)
      this.$refs.dataTable.loadData(1)
    }
  }
}
</script>

<style scoped>

</style>
