<template>
<section class="field-item" v-if="force || content">
  <span v-html="label" class="label" :class="labelStyle" />
  <span class="split">：</span>
  <span v-html="content" v-if="!href" class="content" :class="contentStyle" ></span>
  <template v-if="href">
    <router-link :to="href" class="content link" tag="span">{{content}}</router-link>
  </template>
</section>
</template>
<script>
export default {
  props: {
    label: {
    },
    labelStyle: {
      default: 'normal'
    },
    content: {
    },
    contentStyle: {
      default: 'em'
    },
    href: {
    },
    force: {
      default: false
    }
  },
  mounted () {
    // console.log(JSON.stringify(this.attachment))
  }
}
</script>
<style scoped>
.field-item{
//   display: flex;
//   align-items: center;
   padding: 5px 0;
   font-size: 0;
   span{
     font-size: 14px;
     color: #666666;

     &.split{
        padding-right: 10px;
     }
     &.content{
       word-break: break-all;
        &.em {
            color: #0066ff;
        }
        &.em2 {
            color: #00CC00;
        }
        &.important {
            color: #FF3300;
        }
        &.normal {
            color: #666;
        }
     }
     &.label{
       &.em{
         color: #00CC00;
       }
       &.orige {
         color: #E9967A;
       }
     }
   }
   .link{
    color: #0066FF;
    text-decoration: underline;
   }
}
</style>
