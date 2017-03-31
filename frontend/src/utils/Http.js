import 'whatwg-fetch'
import NProgress from 'nprogress'
import Auth from '../auth/Auth'

const fetchWithAuth = (url, params = {}, cbSuccess = (() => {}), cbError = (() => {}), ...other) => {
  if (params.showLoading !== false) NProgress.start()

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
    NProgress.done()

    let token = response.headers.get('Authorization') || ''
    if (token) {
      Auth.setAccessToken(token.replace(/^Bearer\s+?/i, ''))
    }

    if (response.ok) {
      if (response.headers.get('Content-Type') === 'application/json') {
        response.json().then(data => cbSuccess(data, response))
      } else {
        cbSuccess(response.body, response)
      }
    } else {
      if (response.status === 401) {
        Auth.setAccessToken('')
        Auth.setPermissions({})
        Auth.setRoles({})
        Auth.setUser('')
        window.location.href = `${window.location.href}__refresh__` + (new Date().getTime())
      } else if (response.status === 403) {
        Auth.setPermissions({})
        Auth.setRoles({})
        alert('无权限访问资源')
      } else {
        alert(`Error: ${response.status}`)
      }

      cbError(response)
      console.log('Network response was not ok.')
    }
  }).catch(error => {
    cbError(error)
    console.log('There has been a problem with your fetch operation: ' + error.message)
  })
}

const download = (url, params = {}, callback = (() => {})) => {
  fetchWithAuth(url, params, (body, response) => {
    response.blob().then(data => {
      let fileName = /filename[^;=\n]*=((['"]).*?\2|[^;\n]*)/
                      .exec(response.headers.get('Content-Disposition'))[1] || 'download'

      fileName = fileName.replace(/['"]/g, '')

      if (window.navigator.msSaveOrOpenBlob) {
        window.navigator.msSaveBlob(data, fileName)
      } else {
        let link = document.createElement('a')
        link.href = window.URL.createObjectURL(data, {type: response.headers.get('Content-Type')})
        link.download = fileName
        link.click()
        window.URL.revokeObjectURL(link.href)
      }

      callback(response)
    })
  })
}

export default { fetch: fetchWithAuth, download }
