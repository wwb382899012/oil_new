<template>
     <panel :title="title" v-if="goods">
        <card v-for="good in goods" :key="good.id">
          <field-item label="品名" :content="good.name"/>
          <field-item label="计价标的" :content="good.refer_target"/>
          <field-item label="数量" :content="good.quantityName + '&nbsp;&nbsp;' +good.rateName"/>
          <field-item label="单位换算比" :content="good.unit_convert_rate"/>
          <inline>
            <field-item :label="name + '单价'" :content="good.price"/>
            <field-item label="汇率" :content="formatRate(good.exchangeRate)"/>
          </inline>
          <div class="split"></div>
          <field-item :label="name + '总价'" labelStyle="em" :content="good.amount"/>
          <field-item :label="name + '人民币总价'" v-if="good.exchangeRate" labelStyle="em" :content="good.amountCNY"/>
        </card>

        <card class="">
          <field-item label="合计人民币总价" labelStyle="orige" :content="totalAmountCNY"/>
        </card>
     </panel>

</template>

<script>
import Panel from '@/components/Panel'
import Card from '@/components/Card'
import FieldItem from '@/components/FieldItem'
import AuditItem from '@/components/AuditItem'
import AuditGoodsItem from '@/components/AuditGoodsItem'
import AuditTable from '@/components/AuditTable'
import SubmitBtn from '@/components/SubmitBtn'
import Inline from '@/components/Inline'
import {numberFormat} from '@/utils/format'
export default {
  components: {
    Panel,
    Card,
    AuditItem,
    FieldItem,
    AuditGoodsItem,
    AuditTable,
    SubmitBtn,
    Inline,
    numberFormat
  },
  props: {
    title: {},
    goods: {},
    name: {}
  },
  data () {
    return {
    }
  },
  computed: {
    totalAmountCNY: function () {
      let totalCNY = 0
      for (let i = 0; i < this.goods.length; i++) {
        totalCNY += Number(this.goods[i].amountCNYValue)
      }
      return '￥' + numberFormat(totalCNY / 100, 2)
    }
  },
  methods: {
    formatRate: function (rate) {
      return (rate === '1.000000') ? '1' : rate
    }
  }
}
</script>

<style scoped>

.split{
  height: 1px;
  border-top: 1px dotted #e2e2e2;
  width: 100%;
  margin: 5px auto;
}
</style>
