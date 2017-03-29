const serialize = function (data) {
  return Object.keys(data).map(keyName => {
    return encodeURIComponent(keyName) + '=' + encodeURIComponent(data[keyName])
  }).join('&')
}

const fixFull = url => {
  if (/^\//.test(url)) {
    url = location.protocol + '//' + location.host + url
  } else if (!/^http/.test(url)) {
    url = location.protocol + '//' + location.host + location.pathname.substring(0, location.pathname.lastIndexOf('/')) + '/' + url
  }

  return url
}

export default { serialize, fixFull }
