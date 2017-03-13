<template>
  <div class="layout">
    <Menu ref="nav" mode="horizontal" theme="light" :activeName="activeNav" class="nav">
      <div class="layout-nav">
        <router-link class="logo" to="/">Laravel template</router-link>
        <div class="main-menu">
          <router-link v-for="nav in mainNav" :key="nav.name" :to="nav.link">
            <MenuItem v-if="can(nav.name)" :name="nav.name">
              <Icon :type="nav.icon"></Icon>
              {{ nav.text }}
            </MenuItem>
          </router-link>
          <MenuItem v-for="nav in extNav" :key="nav.name" v-if="can(nav.name)" :name="nav.name">
            <Icon :type="nav.icon"></Icon>
            {{ nav.text }}
          </MenuItem>
        </div>
        <MenuItem name="login" class="logout">
          <Icon type="ios-person"></Icon>
          <span class="user"> {{ logoutText }} </span>
          <span class="out"> 退出登录 </span>
        </MenuItem>
      </div>
    </Menu>
    <div class="main">
      <Menu class="left" :activeName="activeSide" theme="light" v-if="hasSide">
        <div class="layout-logo-left"></div>
        <Menu-item v-for="item in side" :name="item.name" :key="item.name">
          <Icon :type="item.icon" size="14"></Icon>
          <span class="layout-text">{{ item.text }}</span>
        </Menu-item>
      </Menu>
      <div class="right" :class="{'has-side': hasSide}" :style="{minHeight: minHeight + 'px'}">
        <div class="layout-content">
          <slot></slot>
        </div>
        <div class="layout-copy">
          2017 &copy; purocean@outlook.com
        </div>
      </iCol>
      </div>
    </div>
  </div>
</template>

<script>
import Auth from '@/auth/Auth'

export default {
  name: 'users',
  props: ['activeNav', 'side', 'extNav', 'activeSide'],
  components: { Auth },
  data () {
    return {
      mainNav: [
        {name: '/users', text: '用户管理', icon: 'person-stalker', link: '/users'},
        {name: '/departments', text: '部门管理', icon: 'person-stalker', link: '/departments'},
      ],
      minHeight: document.documentElement.clientHeight - 60
    }
  },
  mounted: function () {
    window.addEventListener('resize', this.handleResize)
  },
  beforeDestroy: function () {
    window.removeEventListener('resize', this.handleResize)
  },
  computed: {
    logoutText () {
      return Object.values(Auth.getRoles()).toString() + ' ' + (Auth.getUser().name || Auth.getUser().username)
    },
    hasSide () {
      return this.side !== undefined
    }
  },
  methods: {
    handleResize () {
      this.minHeight = document.documentElement.clientHeight - this.$refs.nav.$el.clientHeight
    },
    can (permission) {
      return Auth.can(permission)
    },
  }
}
</script>

<style scoped>
.layout-nav {
  width: 90%;
  margin: 0 auto;
}

.logo {
  float: left;
  font-size: 24px;
}

.logout.ivu-menu-item {
  float: right;
}

.main-menu {
  display: inline-block;
  float: left;
  margin-left: 6em;
}

.main {
  width: 100%;
  position: relative;
}

.left {
  position: absolute;
  top: 0;
  bottom: 0;
}

.right {
  position: relative;
  padding-bottom: 60px;
}

.right.has-side {
  margin-left: 240px;
}

.layout-content {
  padding: 10px;
}

.layout-copy {
  text-align: center;
  padding: 10px 0 20px;
  color: #9ea7b4;
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  background-color: #eee;
}

.logout span.out {
  display: none;
}

.logout:hover span.out {
  display: inline-block;
}

.logout:hover span.user {
  font-size: .5em;
}
</style>
