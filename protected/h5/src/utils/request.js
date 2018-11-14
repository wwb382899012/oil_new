import axios from 'axios'
import qs from 'qs'
// import { MessageBox } from 'mint-ui'

const API_HOST = process.env.NODE_ENV !== 'production' ? '/api' : location.origin
const isSuccess = (data) => {
  return typeof data === 'object' && data.state === 0
}
const fetchErrorMsg = (data) => {
  if (!data) {
    return ''
  }
  return typeof data === 'string' ? data : data.data
}
const reduceResponse = (data) => {
  let err = !isSuccess(data)
  let ret = {success: true, msg: '操作成功'}
  if (err) {
    ret.success = false
    ret.msg = fetchErrorMsg(data)
  } else {
    ret = {...ret, data: data.data}
  }
  return ret
}
const reduceError = (status, data) => {
  let ret = {success: false, msg: fetchErrorMsg(data) || '服务异常，请稍后重试', status}
  return ret
}
const requestMiddleware = async (data) => {
  if (!data.success && data.msg === '请先登录') {
    // await MessageBox.alert(data.msg)
    location.href = '/site/login?redirect=' + encodeURIComponent(location.href)
  }
}
export function request (method, url, params = {}, opts) {
  // if (process.browser) {
    // let isSameOrigin = url.match(new RegExp(location.origin))
  method = method.toLowerCase()

  opts = {
    method: method,
    //   withCredentials: !!isSameOrigin,
    headers: {
        // 'Accept': 'application/json',
      'Content-Type': 'application/x-www-form-urlencoded',
        // 'Content-Type': 'application/json'
      'X-Requested-With': 'XMLHttpRequest'
    },
    params: {},
    data: {},
    ...opts
  }
  if (method === 'get' || method === 'delete') {
    params['__ajax'] = 1
    opts.params = params
  } else {
    opts.data = qs.stringify(params)
  }
  return axios(API_HOST + url, opts).then(async res => {
    let ret = res.status === 200 ? reduceResponse(res.data) : reduceError(res.status, res.data)
    await requestMiddleware(ret)
    return ret
  }).catch((err) => {
    let data = err.response ? err.response.data : {}
    let status = err.response ? err.response.status : 0
    return reduceError(status, data)
  })
}

export const get = (...args) => request('GET', ...args)
export const post = (...args) => request('POST', ...args)
