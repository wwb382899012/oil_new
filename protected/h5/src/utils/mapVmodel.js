function fetchModuleName (moduleName) {
  if (moduleName === true) {
    let [moduleNameFromRoute] = this.$route.path.replace(/^\//, '').split('/')
    return moduleNameFromRoute
  } else {
    return moduleName
  }
}
export default (options = {}, ...args) => {
  let opts = Object.assign({
    mutationName: 'update',
    moduleName: '',
    getFn (moduleName, state, mapName) {
      if (moduleName) {
        return state[moduleName][mapName]
      } else {
        return state[mapName]
      }
    }
  }, options)
  let mapStateRet = {}
  args.map((item) => {
    let [fieldName, mapName, getFn] = item
    mapName = mapName || fieldName
    let ret = {
      get () {
        try {
          let moduleName = fetchModuleName.call(this, opts.moduleName)
          const state = this.$store.state
          // return [getFn ? 'getFn' : 'opts.getFn']()
          let args = [moduleName, state, mapName]
          return getFn ? getFn(...args) : opts.getFn(...args)
        } catch (err) {
          return null
        }
      },
      set (value) {
        try {
          let moduleName = fetchModuleName.call(this, opts.moduleName)
          this.$store.commit([moduleName, opts.mutationName].join('/'), {field: mapName, value})
        } catch (err) {
        }
      }
    }
    mapStateRet[fieldName] = ret
  })
  return mapStateRet
}

export const mapBase = () => {
  return {
    moduleName () {
      let p = this.$route.path.replace(/^\//, '').split('/')
      return p[0]
    },
    actionName () {
      let p = this.$route.path.replace(/^\//, '').split('/')
      return p[1] || ''
    }
  }
}

export const mapStatePlus = (group, args = [], module = true) => {
  let mapRet = {}
  if (args.length <= 0) {
    mapRet = {
      [group] () {
        let {state} = this.$store
        let moduleName = fetchModuleName.call(this, module)
        return state[moduleName][group]
      }
    }
  } else {
    args.map(i => {
      mapRet[i] = function () {
        let {state} = this.$store
        let moduleName = fetchModuleName.call(this, module)
        let data = state[moduleName][group]
        if (data) {
          return data[i]
        } else {
          return null
        }
      }
    })
  }
  return mapRet
}
