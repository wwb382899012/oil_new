import {post} from '@/utils/request'
import md5 from 'js-md5'

export const doLogin = async ({username, password}) => {
  let ret = await post('/site/login', {
    'obj[username]': username,
    'obj[password]': md5(password)
  })
  return ret
}
