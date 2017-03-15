import Storage from '@/utils/Storage'
import Http from '@/utils/Http'

const setUser = function (user) {
  return Storage.set('auth_user', user)
}

const getUser = function () {
  return Storage.get('auth_user', {})
}

const setAccessToken = function (token) {
  return Storage.set('auth_token', token)
}

const getAccessToken = function () {
  return Storage.get('auth_token', '')
}

const isLogin = function () {
  return !!getAccessToken()
}

const getPermissions = function () {
  return Storage.get('auth_permissions', {})
}

const setPermissions = function (permissions) {
  return Storage.set('auth_permissions', permissions)
}

const getRoles = function () {
  return Storage.get('auth_roles', {})
}

const setRoles = function (roles) {
  return Storage.set('auth_roles', roles)
}

const checkRole = function (role) {
  let roles = getRoles()
  return Object.keys(roles).indexOf(role) > -1
}

const checkPermission = function (permissions, permission) {
  if (!Array.isArray(permissions)) {
    permissions = Object.keys(permissions)
  }

  if (permissions.indexOf(permission) > -1 || permissions.indexOf(permission + '/*') > -1) {
    return true
  }

  let pos = permission.lastIndexOf('/')
  while (pos > -1) {
    pos = permission.lastIndexOf('/')
    permission = permission.substring(0, pos)
    if (permissions.indexOf(permission + '/*') > -1) {
      return true
    }
  }

  return false
}

/**
 * Check from server if callback provided.
 */
const can = function (item, callback) {
  if (callback) {
    Http.fetch('/api/limits', {}, result => {
      setRoles(result.data.roles)
      setPermissions(result.data.perms)
      callback(checkRole(item) || checkPermission(getPermissions(), item))
    }, error => {
      callback(error.status)
    })
  } else {
    return checkRole(item) || checkPermission(getPermissions(), item)
  }
}

const requireAuth = function (allowList, to, from, next) {
  if (checkPermission(allowList, to.path)) {
    next()
    return
  }

  if (isLogin()) {
    if (can(to.path)) {
      next()
    } else {
      can(to.path, allow => {
        if (allow) {
          next()
        } else {
          next({name: 'error', query: {code: 403, message: '无权访问资源', from: from.fullPath}})
        }
      })
    }
  } else {
    next({name: 'login', query: {next: to.fullPath}})
  }
}

export default {
  requireAuth,
  setUser,
  getUser,
  setAccessToken,
  getAccessToken,
  getPermissions,
  setPermissions,
  getRoles,
  setRoles,
  checkPermission,
  checkRole,
  isLogin,
  can
}
