<template>
  <div class="error-focus" :class="{error: value}">
    <slot></slot>
  </div>
</template>
<script>
export default {
  props: {
    value: {
      default: false
    }
  },
  data () {
    return {
    }
  },
  watch: {
    value (v) {
      if (!window.__errorFocus && v) {
        window.__errorFocus = true
        this.$el.scrollIntoView()
        clearTimeout(this.timer)
        this.timer = setTimeout(() => {
          this.$emit('input', false)
          window.__errorFocus = false
        }, 1000)
      }
    }
  }
}
</script>
<style scoped>
 @keyframes twinkling{
    0%{
      /* border: 2px solid transparent; */
      background-color: transparent;
    }
    100%{
      background-color: rgba(#ffc4c4, 0.1);
        /* border-color: rgba(#ff0000, 0.8); */
    }
}
.error-focus{
  position: relative;
}
.error{
  &:after{
    content: '';
    position: absolute;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    animation: twinkling .3s 3 ease-in-out;
  }
}
</style>
