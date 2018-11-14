// The Vue build version to load with the `import` command
// (runtime-only or standalone) has been set in webpack.base.conf with an alias.
import Vue from 'vue'
import App from './App'
import router from './router'
import StorePlugin from '@/plugins/store'
import ToptipsPlugin from '@/plugins/toptips'
import { Cell, Checklist, Radio, Navbar, TabItem, TabContainer, TabContainerItem, Field, Popup, Picker } from 'mint-ui'

Vue.component(Cell.name, Cell)
Vue.component(Radio.name, Radio)
Vue.component(Checklist.name, Checklist)
Vue.component(TabContainer.name, TabContainer)
Vue.component(TabContainerItem.name, TabContainerItem)
Vue.component(Field.name, Field)
Vue.component(Popup.name, Popup)
Vue.component(Picker.name, Picker)
Vue.component(Navbar.name, Navbar)
Vue.component(TabItem.name, TabItem)
Vue.config.productionTip = false

Vue.use(StorePlugin)
Vue.use(ToptipsPlugin)

Vue.directive('min-height', {
  inserted (el) {
    el.style.minHeight = window.innerHeight + 'px'
  }
})
/* eslint-disable no-new */
new Vue({
  el: '#app',
  router,
  template: '<App/>',
  components: { App }
})
