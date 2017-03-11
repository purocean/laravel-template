import NProgress from 'nprogress';
import Config from 'config';

import Storage from '../utils/Storage';
import Http from '../utils/Http';

const setUser = function (user) {
  return Storage.set('auth_user', user);
}

const getUser = function () {
  let user = Storage.get('auth_user', {});
  return (user && user.expires > Date.parse(new Date()) / 1000) ? user : {};
}

const getAccessToken = function () {
  return getUser().access_token;
}

const isLogin = function () {
  return !!getAccessToken();
}

const getPermissions = function () {
  return Storage.get('auth_permissions', {});
}

const setPermissions = function (permissions) {
  return Storage.set('auth_permissions', permissions);
}

const getRoles = function () {
  return Storage.get('auth_roles', {});
}

const setRoles = function (roles) {
  return Storage.set('auth_roles', roles);
}

const checkRole = function (role) {
  let roles = getRoles();
  return Object.keys(roles).indexOf(role) > -1;
}

const checkPermission = function (permissions, permission) {
  if (!Array.isArray(permissions)) {
    permissions = Object.keys(permissions);
  }

  if (permissions.indexOf(permission) > -1 || permissions.indexOf(permission + '/*') > -1) {
    return true;
  }

  let pos = permission.lastIndexOf('/');
  while (pos > -1) {
    pos = permission.lastIndexOf('/');
    permission = permission.substring(0, pos);
    if (permissions.indexOf(permission + '/*') > -1) {
      return true;
    }
  }

  return false;
}

/**
 * Check from server if callback provided.
 */
const can = function (item, callback) {
  if (callback) {
    Http.fetch('/user/items', {}, data => {
        setRoles(data.roles);
        setPermissions(data.permissions);
        callback(checkRole(item) || checkPermission(getPermissions(), item));
    }, error => {
        callback(error.status);
    });
  } else {
    return checkRole(item) || checkPermission(getPermissions(), item);
  }
}

const requireAuth = (prevState, nextState, replace, callback) => {
  NProgress.start();
  const path = nextState.location.pathname;
  const search = nextState.location.search;

  if (checkPermission(Config.allowRoutes, path)) {
    callback();
    NProgress.done();
    return;
  }

  if (isLogin()) {
    if (can(path)) {
      callback();
      NProgress.done();
    } else {
      can(path, allow => {
        if (allow === 401) {
          replace({pathname: '/login', query: {redirect: path + search}});
          callback();
        } else if(allow) {
          callback();
        } else {
          replace({pathname: '/error'});
          callback();
        }

        NProgress.done();
      });
    }
  } else {
    replace({pathname: '/login', query: {redirect: path + search}});
    callback();
    NProgress.done();
  }
};

export default {
  requireAuth,
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
};
