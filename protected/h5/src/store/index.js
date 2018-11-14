import Vue from 'vue'
import Vuex from 'vuex'
import check2 from './check2'
import check3 from './check3'
import check13 from './check13'
import {doLogin} from '@/service/common'

Vue.use(Vuex)
export default new Vuex.Store({
  modules: {
    check2,
    check3,
    check13
  },
  actions: {
    login (store, playload) {
      return doLogin(playload)
    }
  }
})
