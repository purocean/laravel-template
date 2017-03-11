import Storage from '@/utils/Storage'
import Http from '@/utils/Http'

const setUser = function (user) {
  return Storage.set('auth_user', user)
}

const getUser = function () {
  let user = Storage.get('auth_user', {})
  return (user && user.expires > Date.parse(new Date()) / 1000) ? user : {}
}

const getAccessToken = function () {
  return getUser().access_token
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
    Http.fetch('/user/items', {}, data => {
      setRoles(data.roles)
      setPermissions(data.permissions)
      callback(checkRole(item) || checkPermission(getPermissions(), item))
    }, error => {
      callback(error.status)
    })
  } else {
    return checkRole(item) || checkPermission(getPermissions(), item)
  }
}

export default {
  setUser,
  getUser,
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
