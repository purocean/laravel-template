<template>
  <div class="layout">
    <Menu ref="nav" mode="horizontal" theme="light" :activeName="activeNav" class="nav">
      <div class="layout-nav">
        <router-link class="logo" to="/">Laravel template</router-link>
        <div class="main-menu">
          <router-link
            v-for="nav in mainNav"
            v-if="can(nav.name)"
            :key="nav.name"
            :to="nav.link">
            <MenuItem :name="nav.name">
              <Icon :type="nav.icon"></Icon>
              {{ nav.text }}
            </MenuItem>
          </router-link>
          <router-link
            v-for="nav in extNav"
            v-if="can(nav.name)"
            :key="nav.name"
            :to="nav.link">
            <MenuItem :name="nav.name">
              <Icon :type="nav.icon"></Icon>
              {{ nav.text }}
            </MenuItem>
          </router-link>
        </div>
        <router-link to="/login"> <!-- 暂时先这样吧 -->
          <MenuItem
            name="login"
            class="logout"
            style="width: 180px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
            <Icon type="ios-person"></Icon>
            <span class="user"> {{ logoutText }} </span>
            <span class="out"> 退出登录 </span>
          </MenuItem>
        </router-link>
      </div>
    </Menu>
    <div class="main">
      <Menu
        class="left"
        theme="light"
        :activeName="activeSide"
        :openNames="openSide"
        :style="{overflow: 'auto', minHeight: minHeight + 'px'}"
        v-if="hasSide">
        <div class="layout-logo-left"></div>
         <template v-for="item in side">
          <router-link
            v-if="!item.children"
            :key="item.name"
            :to="item.link || ''"
            class="link">
            <MenuItem :name="item.name" >
              <Icon :type="item.icon" size="14"></Icon>
              <span class="layout-text">{{ item.text }}</span>
            </MenuItem>
          </router-link>
          <Submenu v-else :name="item.name" :key="item.name">
            <template slot="title">
              <Icon :type="item.icon"></Icon>
              {{ item.text }}
            </template>
            <router-link
              v-for="sub in item.children"
              :to="sub.link || ''"
              class="sublink"
              :key="sub.name">
              <MenuItem :name="sub.name" >
                <span class="layout-text">{{ sub.text }}</span>
              </MenuItem>
            </router-link>
          </Submenu>
        </template>
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
import 'nprogress/nprogress.css'

export default {
  name: 'desktop-layout',
  props: ['activeNav', 'side', 'extNav', 'activeSide', 'openSide', 'title'],
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
    this.updateTitle()
    window.addEventListener('resize', this.handleResize)
  },
  beforeDestroy: function () {
    window.removeEventListener('resize', this.handleResize)
  },
  computed: {
    logoutText () {
      return (Auth.getUser().name || Auth.getUser().username) +
              ' ' +
              Object.values(Auth.getRoles()).filter(x => x !== '普通用户').toString()
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
    updateTitle () {
      window.document.title = this.title || 'Laravel template'
    }
  },
  watch: {
    title () {
      this.updateTitle()
    }
  }
}
</script>

<style>
  body > #nprogress > .spinner > .spinner-icon {
    border-top-color: #5cadff;
    border-left-color: #5cadff;
  }

  body > #nprogress > .bar {
    background: #5cadff;
  }

  body > #nprogress > .bar > .peg {
    box-shadow: 0 0 10px #5cadff, 0 0 5px #5cadff;
  }
</style>

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
  background: #f0f0f0;
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
  background-color: #fff;
}

.logout span.out {
  display: none;
}

.logout:hover span.out {
  display: inline-block;
}

.logout:hover span.user {
  font-size: .5em;
  display: none;
}

a.sublink {
  color: #757575;
}

a.link {
  color: #444;
}

.ivu-menu-horizontal .ivu-menu-item {
  font-size: 14px;
  padding: 0 12px;
}
</style>
