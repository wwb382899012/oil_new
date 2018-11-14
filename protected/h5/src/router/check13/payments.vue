<template>
  <div>
  <section class="container full">
    <div class="pannel-title" >
      <span>付款计划</span>
    </div>
    <payment-plan v-for="(item, index) in payDetails" :detailIndex="detailIndex" :key="item.detail_id" :period="item.period" :type="item.type" :amount="item.amount" :amount_paid="item.amount_paid" :amount_unpaid="item.amount_unpaid" :amount_to_pay="item.amount_to_pay"></payment-plan>
  </section>
  </div>
</template>

<script>
import PaymentPlan from '@/components/PaymentPlan'
import {mapBase, mapStatePlus} from '@/utils/mapVmodel'

export default {
  components: {
    PaymentPlan
  },
  data () {
    return {
    }
  },
  computed: {
    ...mapBase(),
    ...mapStatePlus('pageData', ['payDetails'])
  },
  created: () => {}
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->

<style scoped>
.container{
  &.fullbox{
    display: flex;
    flex-direction: column;
    position: absolute;
    top: 0;
    left: 0;
    bottom: 0;
    right: 0;
  }
  .pannel-title{
    display: flex;
    width:100%;
    font-size: 14px;
    padding: 15px 20px;
    background-color: rgba(242, 242, 242, 1);
    border-width: 1px;
    border-bottom: 0.5px solid rgba(228, 228, 228, 1);
    //border-top: none;
    align-items: center;
    overflow: hidden;

    span{
      flex: 1;
      padding-left: 10px;
      text-overflow: ellipsis;
      overflow: hidden;
    }
    &:before{
      content: " ";
      height: 10px;
      width: 3px;
      background-color: #666666;
    }
  }
  .pannel-content{
    padding: 0;
    transition: all cubic-bezier(0, 1, 0.5, 1) .3s;
    overflow: hidden;

    &.close{
      height: 0!important;
    }
    &.full {
      flex:1;
    }
  }
}
</style>
