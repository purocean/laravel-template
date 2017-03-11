import 'whatwg-fetch'

import Auth from '../auth/Auth'

const fixFullUrl = url => {
  if (/^\//.test(url)) {
    url = location.protocol + '//' + location.host + url
  } else if (!/^http/.test(url)) {
    url = location.protocol + '//' + location.host + location.pathname.substring(0, location.pathname.lastIndexOf('/')) + '/' + url
  }

  return url
}

export default {
  fixFullUrl,

  fetch: (url, params = {}, cbSuccess = (() => {}), cbError = (() => {}), ...other) => {
    params = Object.assign({
      headers: Object.assign({
        'Accept': 'application/json',
        'Content-Type': 'application/json',
        'Authorization': 'Bearer ' + Auth.getAccessToken()
      }, params.headers),
    }, params, {
      body: typeof params.body === 'object' ? JSON.stringify(params.body) : params.body,
    })

    return fetch(url, params, ...other).then(response => {
      if (response.ok) {
        response.json().then((data) => cbSuccess(data, response))
      } else {
        if (response.status === 401) {
          Auth.setPermissions({})
          Auth.setRoles({})
          Auth.setUser('')
          location.reload()
        } else if (response.status === 404) {
          alert('404 Not Found!')
        } else if (401 < response.status && response.status < 500) {
          Auth.setPermissions({})
          Auth.setRoles({})
          alert('Forbidden!')
          location.reload()
        } else if (500 <= response.status && response.status < 600) {
          alert('Server error.')
        }

        cbError(response)
        console.log('Network response was not ok.')
      }
    }).catch(error => {
      cbError(error)
      console.log('There has been a problem with your fetch operation: ' + error.message)
    })
  }
}
