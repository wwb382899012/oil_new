<template>
 <div>
    <div ref="ghost"></div>
    <transition name="fade">
    <div ref="box" v-show="show" class="box fixed" >
      <button @click.stop="$emit('cancelHandle')">{{cancelText}}</button>
      <button @click.stop="$emit('okHandle')" class="ok">{{okText}}</button>
    </div>
    </transition>
 </div>
</template>
<script>
export default {
  // name: 'submit-btn',
  props: {
    cancelText: {
      default: '清除'
    },
    okText: {
      default: '提交'
    }
  },
  data () {
    return {
      show: true
    }
  },
  mounted () {
    window.addEventListener('scroll', this.handleScroll)
    let ghost = this.$refs.ghost
    let box = this.$refs.box
    ghost.style.height = box.offsetHeight + 'px'
  },
  beforeDestory () {
    window.removeEventListener('scroll', this.handleScroll)
    clearTimeout(this.timer)
  },
  methods: {
    handleScroll (e) {
      // let {height: winHeight} = document.documentElement.getBoundingClientRect()
      // console.log(window.screen.height - top, height)
      // console.log(e)
      // this.fixed = false
      clearTimeout(this.timer)
      this.show = false
      this.timer = setTimeout(() => {
        this.show = true
      }, 100)
    }
  }
}
</script>
<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity .1s
}
.fade-enter, .fade-leave-to /* .fade-leave-active in below version 2.1.8 */ {
  opacity: 0
}
.box{
  display: flex;
  width:  100%;
  background: #ffffff;
  border-top: 1px solid #f0f0f0;
  transition: .3s;
  &.fixed{
    position: fixed;
    bottom: 0;
    left: 0;
    right: 0;
  }
  button{
    border-radius: 5px;
    flex: 1;
    margin: 15px 10px;
    padding: 7px;
    color: #19B491;
    border: 1px solid #19B491;
    background: #ffffff;
    font-size: 16px;
    &.ok{
      color: #ffffff; 
      background-color: #19B491;
    }
  }
}
</style>
