<template>
<span class="action-modal" :class="{disabled: !value}" @click.stop="doTap">
  {{text}}
</span>
</template>
<script>
import { MessageBox } from 'mint-ui'

export default {
  props: {
    value: {default: ''},
    text: {default: '查看'}
  },
  methods: {
    doTap () {
      if (this.value) {
        MessageBox.alert(this.value, '备注')
      }
    }
  }
}
</script>
<style scoped>
.action-modal{
  color: #0066FF;
  text-decoration: underline;
  &.disabled{
    text-decoration: none;
    color: #cccccc;
  }
}
</style>
