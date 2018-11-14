<template>
  <section class="card" :class="{[type]:true,'top-border':topBorder!=false}" @click.stop="goto">
  <div class="item"><slot /></div>
</section>
</template>
<script>
export default {
  props: {
    type: {
      default: 'blank'
    },
    href: {
      default: ''
    },
    topBorder: { default: false }
  },
  methods: {
    goto (...args) {
      if (this.type === 'link') {
        this.href ? this.$router.push(typeof this.href === 'string' ? {name: this.href} : this.href) : this.$emit('goto', args)
      }
    }
  }
}
</script>
<style scoped>
.card{
  padding:15px 15px;
  margin-bottom: 5px;
  background-color: #ffffff;
  &.top-border{
   border-top:1px solid #e2e2e2; 
  }
  &.link{
    display: flex; 
    padding-top: 10px;
    padding-bottom: 10px;
    .item{
      flex: 1; 
    }
  }
  &.link:after{
    font-family: 'iconfont';
    content: '\e602';
  }
}
</style>
