<template>
   <transition name="fade">
   <div class="toptips" :class="type" v-if="showed">
      <div class="toptips-msg"  v-html="msg" :style="(inlineWidth)?'width:'+inlineWidth+'px':''"></div>
        <icon @click.native.stop="hide" class="toptips-close">&#xe63c;</icon>
      </div>
   </transition>
</template>
<script>
import Icon from '@/components/Icon'

export default {
  components: {
    Icon
  },
  props: {
    msg: {
      default: ''
    },
    type: {
      default: 'error'
    },
    closeTime: {
      default: 2000
    }
  },
  data () {
    return {
      showed: false,
      inlineWidth: 0
    }
  },
  methods: {
    show () {
      this.showed = true
    },
    hide () {
      this.showed = false
    }
  },
  mounted () {
    if (this.closeTime) {
      this.inlineWidth = window.innerWidth - 35
      this.timer = setTimeout(() => {
        this.hide()
      }, this.closeTime)
    }
  },
  beforeDestory () {
    clearTimeout(this.timer)
  }
}
</script>
<style scoped>
.fade-enter-active, .fade-leave-active {
  transition: opacity .5s;
}
.fade-enter, .fade-leave-to {
  opacity: 0;
}
.toptips{
  position: fixed;
  top: 0;
  left:0;
  right: 0;
  padding: 5px 10px;
  display: flex;
  align-items: center;
  color: #ffffff;
  font-size: 14px;
  span{
    flex:1;
  }
  &.error{
    background-color: rgba(#ff0000, 0.8);
  }
}
.toptips-msg {
  display:inline
}
.toptips-close {
  position:fixed;
  top:10px;
  right:10px;
}
</style>
