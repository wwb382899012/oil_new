<template>
  <section class="audit-table">
    <text-avatar class="avatar" v-if="avatar" :blue="blue" :small="small">{{avatar}}</text-avatar>
    <table class="g-table">
        <thead>
          <tr>
            <slot name="header"/>
          </tr>
        </thead>
        <tbody>
            <slot name="body"/>
        </tbody>
    </table>
  </section>
</template>
<script>
import TextAvatar from './TextAvatar'
export default {
  components: {
    TextAvatar
  },
  props: {
    avatar: {default: ''},
    blue: {default: false},
    small: {default: true}
  },
  watch: {
  },
  data () {
    return {
    }
  },
  methods: {
  }
}
</script>
<style scoped>
.audit-table {
  position: relative;
  font-size: 14px;
  margin-top: 15px; 
  .avatar{
     position: absolute; 
     left:-10px;
     top:-20px;
  } 
}
</style>
