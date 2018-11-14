import { fetchCheckData } from '@/service/check13'

export default {
  namespaced: true,
  state: {
    audit: [],
    auditError: [],
    auditCommentItemError: [],
    auditComment: '',
    auditCommentError: false,
    pageData: null
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
      let ret = await fetchCheckData(13, id, actionName, idName)
      if (ret.success) {
        commit('update', {field: 'pageData', value: ret.data})
        document.title = ret.data.pageTitle || '石油系统'
        if (ret.data.auditOptions) {
          commit('update', {field: 'audit', value: ret.data.auditOptions.map(i => ({value: '', comment: ''}))})
          commit('update', {field: 'auditError', value: ret.data.auditOptions.map(i => false)})
        }
      }
      return ret
    }
  }
}
