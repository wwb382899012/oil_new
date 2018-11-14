<template>
<div class="panel-container">
  <panel type='accordion' title="项目信息">
    <card>
       <field-item label="项目编号" content="KY11ZN171122N01" />
       <field-item label="项目编号" content="：预付货款申请表进行审批，并授权进行网上汇款。出纳网上汇款：根据批准后汇款申请单付款并在汇完款后将网上汇款单发送给采购..." contentStyle="em2"/>
       <field-item label="项目编号" content="KY11ZN171122N01" contentStyle="important"/>
       <field-item label="项目编号" content="KY11ZN171122N01" contentStyle="normal" />
    </card>
  </panel>
  <panel>
    <card type="link" href="login">
       123123
    </card>
    <card type="link" href="login">
       123123
    </card>
  </panel>
  <panel type="link" href="login" title="登录"/>
  <panel title="审核">
    <card>
      <audit-item />
    </card>
    <card :topBorder="1" >
      <audit-item type="comment"/>
    </card>
  </panel>
  <panel title="商品信息">
    <card>
      <audit-goods-item />
    </card>
  </panel>
  <panel>
     <card>
        <audit-table >
          <template slot="header">
              <td>预计时间</td>
              <td>付款类别</td>
              <td>金额</td>
              <td>备注</td>
          </template>
          <template slot="body">
            <tr>
              <td>预计时间</td>
              <td>付款类别</td>
              <td>金额</td>
              <td>
                 <router-link to="">查看</router-link>
              </td>
            </tr>
          </template>
        </audit-table>
     </card>
  </panel>
  <panel>
    <audit-history-item :isLatest="true" ></audit-history-item>
    <audit-history-item></audit-history-item>
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
import AuditHistoryItem from '@/components/AuditHistoryItem'

export default {
  components: {
    Panel,
    Card,
    FieldItem,
    AuditItem,
    AuditGoodsItem,
    AuditTable,
    AuditHistoryItem
  }
}
</script>
<style scoped>
.panel-container{
  background-color: #f5f5f9;
  position: absolute;
  top:0;
  left:0;
  width: 100%;
  height: 100%;
}
</style>
