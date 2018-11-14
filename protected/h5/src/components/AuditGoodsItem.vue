<template>
  <section class="goods-item">
    <text-avatar :blue="blue" :small="small">{{avatar}}</text-avatar>
    <div>
      <h1>{{name}}</h1>
      <p>
      <span>数量:{{quantity}}</span>
      <span>单价:{{price}}</span>
      </p>
    </div>
  </section>
</template>
<script>
import TextAvatar from './TextAvatar'
export default {
  components: {
    TextAvatar
  },
  props: {
    avatar: {default: '商'},
    name: {default: '商品名字'},
    quantity: {default: '0'},
    price: {default: '0'},
    blue: {default: false},
    small: {default: false}
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
.goods-item {
  display: flex;
  /* align-items: center; */
  div{
    padding: 0 15px; 
    flex: 1;
  }
  h1{
    font-weight: normal;
    color: #666;
    /* padding: 0 15px; */
    min-width: 100px;
    flex:1;
    font-size: 14px;
  }
  p{
    display: flex;
    padding-top: 3px;
  }
  span{
    flex: 1;
    font-size: 12px; 
    color: #949494;
    padding: 0 5px 0 0;
    flex:1;
  }
}
</style>
