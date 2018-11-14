<template>
  <div>
     <panel title="代理手续费" v-if="agentDetails && agentDetails.length">
        <card v-for="item in agentDetails" :key="item.name">
          <field-item  label="品名" :content="item.name" />
          <field-item  label="计费方式" :content="item.payTypeName" />
          <field-item  label="计费单价" :content="item.price" />
          <field-item  label="计费单位" :content="item.unitName" />
          <field-item  label="代理手续费率" :content="item.feeRate" />
          <field-item  label="代理手续费" :content="item.fee" />
        </card>
     </panel>
  </div>
</template>

<script>
import Panel from '@/components/Panel'
import Card from '@/components/Card'
import FieldItem from '@/components/FieldItem'
import AuditItem from '@/components/AuditItem'
import AuditGoodsItem from '@/components/AuditGoodsItem'
import AuditTable from '@/components/AuditTable'
import SubmitBtn from '@/components/SubmitBtn'
import {mapBase, mapStatePlus} from '@/utils/mapVmodel'

export default {
  components: {
    Panel,
    Card,
    AuditItem,
    FieldItem,
    AuditGoodsItem,
    AuditTable,
    SubmitBtn
  },
  data () {
    return {
    }
  },
  computed: {
    ...mapBase(),
    ...mapStatePlus('pageData', ['agentDetails'])
  }
}
</script>

<style scoped>

</style>
