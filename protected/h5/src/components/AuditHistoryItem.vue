<template>
  <section class="audit-history" :class="{'is-latest': isLatest!=false}">
    <div class="split"></div>
    <div class="content">
      <p class="core-content">
      <span class="name" >{{name}}</span>
        <span>审核人: {{member}}</span>
        <span :class="'status-'+status"></span>
      </p>
      <p v-if="checkChoices">
        {{checkChoices}}
      </p>
      <p>
        <span>审核意见:</span>  
        <span class="desc">{{desc}}</span>
      </p>
      <p class="time">{{time}}</p>
    </div>
  </section>
</template>
<script>
export default {
  components: {
  },
  props: {
    isLatest: {default: false},
    name: {default: '风控审核'},
    member: {default: '李坤'},
    status: {default: 1},
    desc: {default: '说明'},
    time: {default: '2017-04-01 12:00:00'},
    checkChoices: {default: null}
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
.audit-history {
  display:flex;
  font-size: 14px;
  color: #999999;
  .split{
     width: 1px; 
     height: 100%;
     min-height: 100px;
     margin: 0 10px;
     background: #F0F0F0;
     position: relative;
     &:after{
      content: ' '; 
      display: block;
      width:10px;
      height:10px;
      background-color: #C5C5C5;
      position: absolute;
      border-radius: 100%;
      left: -5px;
      top: 5px;
     }
  }
  .name{
    color: #FF9900;
    
  }
  .content{
    flex:1;
    padding: 0 10px; 
    p{
      margin-bottom: 5px; 
    }
  }
  .core-content{
    display: flex;
    justify-content: space-between;
    width: 100%;
  }
  .status-0{
    &:after{
      content: '待审核'; 
    } 
  }
  .status-1{
    color: #00CC00;
    &:after{
      content: '结果: 通过'; 
    } 
  }
  .status--1{
    color: red;
    &:after{
      content: '结果: 驳回'; 
    } 
  }
  .time{
   font-size: 13px; 
   color: #CCCCCC;
  }
  &.is-latest{
    padding-top: 20px;
    .name{
      color: red;
    }
    .split:after{
      background-color: #19B491;
      border: 3px solid  #87D5C5;
      height: 8px;
      width:8px;
      left: -6px;
      top: 0;
    }
  }
}
</style>
