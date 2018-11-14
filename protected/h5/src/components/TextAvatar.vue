<template>
  <section class="text-avatar" :class="{blue: blue!==false,small: small!==false}">
    <slot />
  </section>
</template>
<script>
export default {
  props: {
    blue: {default: false},
    small: {default: false}
  }
}
</script>
<style scoped>
.text-avatar{
  border-radius: 100%; 
  color: #ffffff;
  display: flex;
  font-size: 16px;
  width: 35px;
  height: 35px;
  justify-content: center;
  align-items: center;
  background-color: #06C504;
  border: 3px solid #bcffbc;

  &.blue{
    background-color: #005CFF;
    border-color: rgba(190,213,244,.8);
  }
  &.small{
    font-size: 14px;
    width: 30px;
    height: 30px;
  }
}
</style>
