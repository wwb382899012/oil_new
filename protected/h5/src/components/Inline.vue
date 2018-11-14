<template>
<div class="g-inline-field-item"><slot /></div>
</template>
<script>
export default {
}
</script>
<style>
.g-inline-field-item{
  display: flex;
  section {
    flex: 1;
    padding-right: 10px!important;
  }
}
</style>
