import { fetchCheckData, getQuotaInfo } from '@/service/check'

const defaultFormData = () => {
  return {
    audit1: '',
    audit1comment: '',
    audit2: '',
    audit: [],
    auditError: [],
    auditCommentItemError: []
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
      let ret = await fetchCheckData(3, id, actionName, idName)
      if (ret.success) {
        commit('update', {field: 'pageData', value: ret.data})
        document.title = ret.data.pageTitle || '石油系统'
        if (ret.data.auditOptions) {
          commit('update', {field: 'audit', value: ret.data.auditOptions.map(i => ({value: '', comment: ''}))})
          commit('update', {field: 'auditError', value: ret.data.auditOptions.map(i => false)})
        }
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
