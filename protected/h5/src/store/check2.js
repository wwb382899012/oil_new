import { fetchCheckData, getQuotaInfo } from '@/service/check'

const defaultFormData = () => {
  return {
    audit1: '',
    audit1comment: '',
    audit2: ''
  }
}

export default {
  namespaced: true,
  state: {
    ...defaultFormData(),
    audit1Error: '',
    pageData: null,
    quotaInfo: null
  },
  mutations: {
    update (state, {field, value}) {
      state[field] = value
    }
  },
  actions: {
    async fetchPageData ({commit, state}, {id, actionName, idName, force}) {
      if (!force && state.pageData) {
        return {success: true, data: state.pageData}
      }
      let ret = await fetchCheckData(2, id, actionName, idName)
      if (ret.success) {
        commit('update', {field: 'pageData', value: ret.data})
        document.title = ret.data.pageTitle || '石油系统'
      }
      return ret
    },
    async getQuotaInfo ({commit}, {contractId, isMain}) {
      let ret = await getQuotaInfo(contractId, isMain)
      if (ret.success) {
        commit('update', {field: 'quotaInfo', value: ret.data})
      }
    }
  }
}
