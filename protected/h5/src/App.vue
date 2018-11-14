<template>
  <div v-min-height id="app">
    <router-view/>
  </div>
</template>

<script>
import '@/assets/font/iconfont/iconfont.css'
export default {
  name: 'app'
}
</script>

<style>
body{
  background-color: #F5F6F8;
}
*{padding:0;margin:0;list-style:none;box-sizing: border-box;font-style: normal;}
input,textarea,select{
  outline: none;
  -webkit-tap-highlight-color: rgba(0,0,0,0);  
  font-size: inherit; 
}
#app {
  font-family: 'Avenir', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  color: #2c3e50;
}
.g-inline-checklist{
  display: flex;
  border: none;
  .mint-checklist-title, .mint-radiolist-title{
    display: none; 
  }
  .mint-cell{
    flex: 1; 
    background: none;
    border: none;
    &:last-child{
      background: none;
    }
    .mint-cell-left,.mint-cell-right{
     display: none; 
    }
  }
  .mint-cell-wrapper{
    padding: 0; 
    background: none;
  }
  .mint-radiolist-label{
    padding: 0; 
    display: flex;
    align-items: center;
  }
  .mint-radio-label{
    font-size: 14px;
    padding: 2px 0 0 0;
    vertical-align: inherit;
  }
}
.g-table{
    width: 100%;
    border: 1px solid #e2e2e2;
    border-collapse: collapse;
    text-align: center;
    td{
      padding: 5px; 
      border: 1px solid #e2e2e2;
    }
    tbody td{
      font-size: 13px;
    }
}
.g-rmb-label{
  .mint-cell-value{
    position: relative; 
  }
  .mint-cell-value:before{
     content: "￥"; 
     position: absolute;
     left: -20px;
     height: 100%;
     display: flex;
     justify-content: center;
     align-items: center;
  }
  &.g-unit-w-label{
    .mint-cell-value:after {
      content: '万元';
      position: absolute;
      right: 0;
      height: 100%;
      display: flex;
      justify-content: center;
      align-items: center;
    }
  }
}
.g-mt-cell{
  .mint-cell-title{
    flex: none; 
    min-width: 105px;
  }
  .mint-cell-value{
    flex: 1; 
    display: flex;
  }
  .g-input{
    flex:1; 
  }
}
.g-input{
  border: none;
}  
.g-picker-input{
  &:disabled{
   color: inherit; 
  }
}
.numeric-input-placeholder{
  color: #ccc!important;
}
::-webkit-input-placeholder { /* WebKit browsers */
    color: #ccc!important;
}
.g-dotted-split{
  margin: 10px 0 5px 0;
  border-top: 1px dotted #e2e2e2;
}
.mint-tab-item-label{
  font-size: 14px;
}
.picker-toolbar{
display: none;
}
</style>
