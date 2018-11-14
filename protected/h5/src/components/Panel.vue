<template>
  <section class="container" :class="{fullbox: type == 'full'}">
    <div class="pannel-title" :class="{close, link: type == 'link'}"  @click.stop="switchState">
      <span>{{ title }}</span>
    </div>
    <div class="pannel-content" :class="{close,full: type=='full'}" >
      <slot />
    </div>
  </section>
</template>
<script>
const autoSetHeight = (el) => {
  if (el.offsetHeight) {
    el.style.height = el.offsetHeight + 'px'
  }
}
export default {
  props: {
    title: {
      default: ''
    },
    type: {
      default: 'accordion'
    },
    href: {
      default: ''
    }
  },
  data () {
    return {
      close: 0
    }
  },
  methods: {
    switchState (...args) {
      if (this.type === 'link') {
        this.href ? this.$router.push(typeof this.href === 'string' ? {name: this.href} : this.href) : this.$emit('goto', args)
      }
      if (this.type === 'accordion') {
        this.close = !this.close
      }
    }
  },
  directives: {
    autoSetHeight: {
      inserted: autoSetHeight,
      componentUpdated: autoSetHeight
    }
  }
}
</script>
<style scoped>
.container{
  &.fullbox{
    display: flex;
    flex-direction: column;
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
  }
  .pannel-title{
    display: flex;
    width:100%;
    font-size: 14px;
    padding: 15px 20px;
    background-color: rgba(242, 242, 242, 1);
    border-width: 1px;
    border-bottom: 0.5px solid rgba(228, 228, 228, 1);
    //border-top: none;
    align-items: center;
    overflow: hidden;

    span{
      flex: 1;
      padding-left: 10px;
      text-overflow: ellipsis;
      overflow: hidden;
    }
    &:before{
      content: " ";
      height: 10px;
      width: 3px;
      background-color: #666666;
    }

    &:after{
      content: '\e600';
      font-family: 'iconfont';
      transition: 0.3s;
    }
    &.link:after{
      content: '\e602'; 
    }
    &.close:after{
      transform: rotate(180deg);
    }
    &.close{
    }
  }
  .pannel-content{
    padding: 0;
    transition: all cubic-bezier(0, 1, 0.5, 1) .3s;
    overflow: hidden;

    &.close{
      height: 0!important;
    }
    &.full {
      flex:1;
    }
  }
}
</style>
