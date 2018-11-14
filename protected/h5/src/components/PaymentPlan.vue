<template>
    <card>
      <field-item label="期数" :content="period" force="true"/>
      <field-item label="类别" :content="type " force="true"/>
      <field-item label="计划付款金额" :content="amount " force="true"/>
      <field-item label="已申请金额" :content="amount_paid " force="true"/>
      <field-item label="未申请金额" :content="amount_unpaid " force="true"/>
      <field-item label="本次付款金额" :content="amount_to_pay " force="true"/>
    </card>
</template>
<script>

import Card from '@/components/Card'
import FieldItem from '@/components/FieldItem'
export default {
  components: {
    Card,
    FieldItem
  },
  props: {
    period: {
      default: '1'
    },
    type: {
      default: ''
    },
    amount: {
      default: '0.00'
    },
    amount_paid: {
      default: '0.00'
    },
    amount_unpaid: {
      default: '0.00'
    },
    amount_to_pay: {
      default: '0.00'
    }
  },
  computed: {
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
input,textarea{
  border: none;
  background-color: transparent;
}
.payment-item {
  display: flex;
  font-size: 14px;
  flex-direction: column;
  position: relative;
  .title{
    /* font-weight: bold; */
    /* font-size: 14px; */
    color: #000;
    font-size: 16px;
  }
  .checklist{
   padding: 10px 0; 
  }
  .comment {
    display: flex;
    span{
      padding-right: 5px;
      line-height: normal;
    }
    input {
      border: none;
      background-color: transparent;
      flex: 1;
      font-size: 14px;
    }
  }
  textarea{
    padding: 0;
  }
  .comment-label{
    text-align: right;
    font-size: 12px;
    color: #ccc;
  }
}

</style>
