<template>
  <div>
     <panel title="上游合同条款" v-if="buyContent">
        <card>
          <field-item v-for="item in buyContent" :key="item.name" :label="item.name" :content="item.display_value" :force="true" />
        </card>
     </panel>
     <panel title="下游合同条款" v-if="sellContent">
        <card>
          <field-item v-for="item in sellContent" :key="item.name" :label="item.name" :content="item.display_value" :force="true" />
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
    ...mapStatePlus('pageData', ['buyContent', 'sellContent'])
  }
}
</script>

<style scoped>

</style>
