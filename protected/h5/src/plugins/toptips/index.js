import Vue from 'vue'
import component from './component'

let Toptips = Vue.extend(component)

export default {
  install (Vue, options) {
    Vue.prototype.$toptips = (msg, closeTime, type) => {
      const div = document.createElement('div')
      document.body.appendChild(div)
      let instance = new Toptips({
        propsData: {
          msg,
          type,
          closeTime
        }
      }).$mount(div)
      console.log(instance)
      instance.show()
      return instance
    }
  }
}
