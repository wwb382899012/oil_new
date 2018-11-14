import Vue from 'vue'
import Router from 'vue-router'

const _404 = () => import(/* webpackChunkName: '_404' */'./_404.vue')
const Test = () => import(/* webpackChunkName: 'Test' */'./Test.vue')
const CheckAttachment = () => import(/* webpackChunkName: 'ViewAttachment' */'./check/attachment.vue')
const Login = () => import(/* webpackChunkName: 'Login' */'./login.vue')
const AuditContract = () => import(/* webpackChunkName: 'AuditContract' */'./audit/contract.vue')
const AgentFee = () => import(/* webpackChunkName: 'AgentFee' */'./check/agentfee.vue')
const AuditAddPrice = () => import(/* webpackChunkName: 'AuditAddPrice' */'./check/addPrice.vue')
const CheckCheck = () => import(/* webpackChunkName: 'CheckCheck' */'./check/check.vue')
const Check13Check = () => import(/* webpackChunkName: 'CheckCheck' */'./check13/check13.vue')
const Check13Contract = () => import(/* webpackChunkName: 'Check13Contract' */'./check13/contract.vue')
const Check13Attachments = () => import(/* webpackChunkName: 'Check13Attachments' */'./check13/attachments.vue')
const Check13contractFiles = () => import(/* webpackChunkName: 'Check13contractFiles' */'./check13/contractFiles.vue')
const Check13History = () => import(/* webpackChunkName: 'Check13History' */'./check13/history.vue')
const CheckContract = () => import(/* webpackChunkName: 'CheckContract' */'./check/contract.vue')
const CheckGoods = () => import(/* webpackChunkName: 'CheckGoods' */'./check/goods.vue')
const CheckHistory = () => import(/* webpackChunkName: 'CheckHistory' */'./check/history.vue')
const Check13Payments = () => import(/* webpackChunkName: 'CheckHistory' */'./check13/payments.vue')
Vue.use(Router)
const router = new Router({
  mode: 'history',
  routes: [
    {
      path: '/site/login',
      name: 'login',
      component: Login
    },
    {
      path: '/audit/contract',
      name: 'AuditContract',
      component: AuditContract
    },
    {
      path: '/check13/(check|detail)',
      name: 'Check13Check',
      component: Check13Check
    },
    {
      path: '/(check2|check3)/(check|detail)',
      name: 'CheckCheck',
      component: CheckCheck
    },
    {
      path: '/(check2|check3)/history',
      name: 'CheckHistory',
      component: CheckHistory
    },
    {
      path: '/(check2|check3)/(check|detail)/history',
      name: 'CheckHistory',
      component: CheckHistory
    },
    {
      path: '/check13/history',
      name: 'Check13History',
      component: Check13History
    },
    {
      path: '/check13/(check|detail)/history',
      name: 'Check13History',
      component: Check13History
    },
    {
      path: '/(check2|check3)/contract',
      name: 'CheckContract',
      component: CheckContract
    },
    {
      path: '/(check2|check3)/(check|detail)/contract',
      name: 'CheckContract',
      component: CheckContract
    },
    {
      path: '/check13/(check|detail)/contract',
      name: 'Check13Contract',
      component: Check13Contract
    },
    {
      path: '/check13/(check|detail)/attachments',
      name: 'Check13Attachments',
      component: Check13Attachments
    },
    {
      path: '/check13/(check|detail)/contract-files',
      name: 'Check13contractFiles',
      component: Check13contractFiles
    },
    {
      path: '/check13/(check|detail)/payments',
      name: 'Check13Payments',
      component: Check13Payments
    },
    {
      path: '/check*/agentfee',
      name: 'AgentFee',
      component: AgentFee
    },
    {
      path: '/check*/(check|detail)/agentfee',
      name: 'AgentFee',
      component: AgentFee
    },
    {
      path: '/check*/(check|detail)/goods',
      name: 'CheckGoods',
      component: CheckGoods
    },
    {
      path: '/check*/(check|detail)/add-price',
      name: 'AuditAddPrice',
      component: AuditAddPrice
    },
    {
      path: '/(check2|check3)/(check|detail)/attachment',
      name: 'CheckAttachment',
      component: CheckAttachment
    },
    {
      path: '/test',
      name: 'test',
      component: Test
    },
    {
      path: '/',
      name: '404',
      component: _404
    }
  ],
  scrollBehavior (to, from, savedPosition) {
    // if ((to.name === 'CheckCheck' || to.name === 'Check13Check') && savedPosition) {
    if (savedPosition) {
      // 点击返回或前进才会有savedPosition
      setTimeout(() => {
        window.scrollTo(savedPosition.x, savedPosition.y)
      }, 10)
    } else {
      setTimeout(() => {
        window.scrollTo(0, 0)
      }, 10)
    }
  }
})
router.beforeEach((to, from, next) => {
  let store = router.app.$store
  let [moduleName, actionName] = to.path.replace(/^\//, '').split('/')
  if (moduleName.match(/^(check)/) && actionName.match(/^(check|detail)$/)) {
    if (actionName === 'history') {
      actionName = 'detail'
    } else if (actionName !== 'detail') {
      actionName = 'check'
    }
    let idName = actionName === 'detail' ? 'detail_id' : 'id'
    store.dispatch(`${moduleName}/fetchPageData`, {id: to.query[idName], actionName, idName, force: to.query.__force})
  }
  next()
})

export default router
