<template>
  <div>
     <panel title="合同信息" v-if="multipleContract">
        <card v-for="item in multipleContract" :key="item.contractCode + item.projectCode">
          <field-item label="合同类型" :content="item.contractTypeName" />
          <field-item label="合同编码" :content="item.contractCode" />
          <field-item label="项目编号" :content="item.projectCode" />
          <field-item label="本次付款金额" :content="item.amount" />
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
    ...mapStatePlus('pageData', ['multipleContract'])
  }
}
</script>

<style scoped>

</style>
