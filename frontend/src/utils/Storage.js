const perfix = 'laravel5-vue2'

let get = function (key, defaultValue) {
  return typeof window.localStorage[perfix + key] === 'undefined' ? defaultValue : JSON.parse(window.localStorage[perfix + key])
}

let set = function (key, value) {
  window.localStorage[perfix + key] = JSON.stringify(value)
}

export default {
  set,
  get
}
