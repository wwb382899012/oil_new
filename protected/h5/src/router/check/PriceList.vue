<template>
  <div>
     <div class="toolbar">
        <router-link :to="{name:'AuditAddPrice'}" class="btn">+新增</router-link>
     </div>
     <panel title="采购合同占用">
        <card>
          <field-item label="占用对象" content="车有邦科技服务（深圳）有限公司"/>
          <field-item label="占用额度" content="￥11.00万元"/>
          <field-item label="备注" content="无"/>
        </card>
     </panel>
     <panel title="销售合同占用">
        <card>
          <field-item label="交货期限" content="20170829"/>
          <field-item label="交货方式" content="下游自提"/>
          <field-item label="保费承担方" content="下游"/>
          <field-item label="质量验收依据" content="测试"/>
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
  }
}
</script>

<style scoped>
.toolbar{
  padding: 15px;
}
.btn{
  margin: 5px;
  padding:5px 15px;
  border-radius: 8px;
  text-decoration: none!important;
  color: #fff;
  background-color: #19B491;
}
</style>
