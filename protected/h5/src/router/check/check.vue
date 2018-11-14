<template>
  <div v-if="pageData">
     <panel title="项目信息">
       <card>
          <field-item label="项目编号" :content="pageData.projectCode" />
          <field-item label="项目类型" :content="pageData.projectTypeName" />
          <field-item label="购销信息" :content="pageData.buySellDesc" />
          <field-item label="合同类型" :content="pageData.contractTypeName" />
          <field-item label="代理模式" :content="pageData.agentTypeName" />
          <div class="g-dotted-split" v-if="pageData.agentName || pageData.prevParterName"></div>
          <field-item label="采购代理商" :content="pageData.agentName" />
          <field-item label="上游合作方" :content="pageData.prevPartnerName" />
          <field-item label="下游合作方" :content="pageData.nextPartnerName" />
          <div class="g-dotted-split" ></div>
          <field-item label="交易主体" :content="pageData.corporationName" />
       </card>
     </panel>
     <panel title="交易明细"  type="link" :href="{path:'/'+moduleName+'/'+actionName+'/goods', query: {id}}" >
       <card v-for="good in goods" v-if="good.name" :key="good.id">
          <audit-goods-item :name="good.name" :avatar="good.avatar" :blue="good.type=='2'" :quantity="good.quantityName" :price="good.price" />
       </card>
     </panel>
     <panel title="计价公式" v-if="pageData.buyFormula || pageData.sellFormula">
       <card class="simple-card" v-if="pageData.buyFormula && pageData.buyContract && pageData.buyContract.price_type == 2">
         <h1>采购计价公式</h1>
         <p>{{pageData.buyFormula}}</p>
       </card>
       <card class="simple-card" v-if="pageData.sellFormula && pageData.sellContract && pageData.sellContract.price_type == 2">
         <h1>销售计价公式</h1>
         <p>{{pageData.sellFormula}}</p>
       </card>
     </panel>
     </panel>
     <panel title="截止日期与期限">
        <card >
           <field-item label="最终交货日期" :content="pageData.up_delivery_term" />
           <field-item label="收票时间" :content="pageData.up_days" />
           <field-item label="最终发货日期" :content="pageData.down_delivery_term" />
           <field-item label="开票时间" :content="pageData.down_days" />
        </card>
     </panel>
     <panel type="link" title="合同条款" v-if="contractTitle" :href="{path:'/'+moduleName+'/'+actionName+'/contract', query: {id}}" />
     <panel type="link" title="代理手续费" v-if="pageData.agentDetails.length" :href="{path:'/'+moduleName+'/'+actionName+'/agentfee', query: {id}}" />
     <panel title="收付款计划" v-if="contractTitle">
        <card>
            <audit-table avatar="付" v-if="pageData.prevPayments">
              <template slot="header">
                  <td width="80">预计时间</td>
                  <td width="100">付款类别</td>
                  <td>金额</td>
                  <td width="50">备注</td>
              </template>
              <template slot="body">
                <tr v-for="item in pageData.prevPayments">
                  <td>{{item.date}}</td>
                  <td>{{item.typeName}}</td>
                  <td>{{item.price}}</td>
                  <td>
                    <action-modal :value="item.remark"></action-modal>
                  </td>
                </tr>
               </template>
            </audit-table>
            <audit-table class="table-split" avatar="收" :blue="true" v-if="pageData.nextPayments">
              <template slot="header">
                  <td width="80">预计时间</td>
                  <td width="100">收款类别</td>
                  <td>金额</td>
                  <td width="50">备注</td>
              </template>
              <template slot="body">
                <tr v-for="item in pageData.nextPayments">
                  <td>{{item.date}}</td>
                  <td>{{item.typeName}}</td>
                  <td>{{item.price}}</td>
                  <td>
                    <action-modal :value="item.remark"></action-modal>
                  </td>
                </tr>
               </template>
            </audit-table>
            <div class="g-dotted-split" ></div>
            <div v-if="pageData.attachments">
              <!--详情页面参数不一样, 审核页面用id, 详情用detail id-->
              <field-item v-if="actionName==='check'" type="link" :href="{path:'/'+moduleName+'/'+actionName+'/attachment', query: {id}}" label="项目预算表" content="查看" />
              <!--详情页面参数不一样, 审核页面用id, 详情用detail id-->
              <field-item v-if="actionName==='detail'" type="link" :href="{path:'/'+moduleName+'/'+actionName+'/attachment', query: {detail_id: pageData.detailId}}" label="项目预算表" content="查看" />
            </div>
        </card>
    </panel>
    <panel title="创建人信息">
       <card >
          <field-item label="创建人" :content="pageData.creator" />
          <field-item label="创建时间" :content="pageData.create_time" />
          <field-item label="修改人" :content="pageData.updater" />
          <field-item label="修改时间" :content="pageData.update_time" />
       </card>
    </panel>
    <panel title="额度管理" v-if="pageData.quotas.length">
       <card v-for="item in pageData.quotas" :key="item.id">
         <div class="quota-item">
          <text-avatar :blue="item.isBlue">{{item.icon}}</text-avatar>
          <div class="quota-info">
            <p>占用对象： {{item.name}}</p>
            <p>占用金额： {{item.price}}</p>
            <p>备注： {{item.comment || '无'}}</p>
          </div>
         </div>
       </card>
    </panel>
    <template v-if="moduleName=='check2' && actionName=='check'">
    <panel title="额度管理">
      <card>
         <error-focus v-model="audit1Error">
            <audit-item title="是否启用额度流程" v-model="audit1" />
         </error-focus>
      </card>
    </panel>
    </template>

    <panel title="额度占用情况" v-if="pageData && pageData.quotaData">
      <card>
        <audit-table>
          <template slot="header">
              <td width="70">占用对象</td>
              <td width="70">授信额度</td>
              <td>实际占用额度</td>
              <td>合同占用额度</td>
          </template>
          <template slot="body">
            <tr v-for="(item,index) in pageData.quotaData" :key="index">
              <td>{{item.partnerName}}</td>
              <td>{{item.credit_amount}}</td>
              <td>{{item.used_amount}}</td>
              <td>{{item.contract_used_amount}}</td>
            </tr>
           </template>
        </audit-table>
    	</card>
    </panel>
    <panel v-if="pageData.checkHistory.length" title="审核历史记录" type="link" :href="{path:'/'+moduleName+'/'+actionName+'/history', query: {detail_id: pageData.detailId}}"></panel>
    <panel v-if="actionName!='detail'" title="本次审核意见">
        <card v-for="(item, index) in pageData.auditOptions" :key="item.name">
            <error-focus v-model="auditError[index]" >
                <audit-item :title="(index+1) + '.' + item.name" :value="audit[index].value" @input="auditInput(index, 'value', $event)"></audit-item>
            </error-focus>
            <error-focus v-model="auditError[index]">
                <audit-item type="input" :value="audit[index].comment" @input="auditInput(index,'comment', $event)"></audit-item>
            </error-focus>
        </card>
      <card :topBorder="1" >
        <error-focus v-model="audit2Error">
          <audit-item type="comment" placeholder="请输入本次审核意见" v-model="audit2"/>
        </error-focus>
      </card>
    </panel>
    <submit-btn v-if="actionName!='detail'" okText="通过" cancelText="驳回" @okHandle="onSubmit('1')" @cancelHandle="onSubmit('-1')"/>
  </div>
  <div v-else>

  </div>
</template>

<script>
import { Indicator, MessageBox } from 'mint-ui'
import Panel from '@/components/Panel'
import Card from '@/components/Card'
import FieldItem from '@/components/FieldItem'
import AuditItem from '@/components/AuditItem'
import AuditGoodsItem from '@/components/AuditGoodsItem'
import AuditTable from '@/components/AuditTable'
import SubmitBtn from '@/components/SubmitBtn'
import ErrorFocus from '@/components/ErrorFocus'
import ActionModal from '@/components/ActionModal'
import Inline from '@/components/Inline'
import TextAvatar from '@/components/TextAvatar'
import mapVmodel, {mapBase, mapStatePlus} from '@/utils/mapVmodel'
import {post} from '@/utils/request'

export default {
  components: {
    Panel,
    Card,
    AuditItem,
    FieldItem,
    AuditGoodsItem,
    AuditTable,
    SubmitBtn,
    ErrorFocus,
    ActionModal,
    Inline,
    TextAvatar
  },
  data () {
    return {
      audit1Error: false,
      audit2Error: false
    }
  },
  computed: {
    ...mapBase(),
    ...mapVmodel({moduleName: true}, ['audit'], ['auditError'], ['audit1'], ['audit1comment'], ['audit2']),
    ...mapStatePlus('pageData'),
    id () {
      return this.pageData ? this.pageData.contractId : ''
    },
    goods () {
      let {pageData} = this
      let goods = []
      if (pageData) {
        if (pageData.buyGoods) {
          goods = goods.concat(pageData.buyGoods)
        }
        if (pageData.sellGoods) {
          goods = goods.concat(pageData.sellGoods)
        }
      }
      return goods
    },
    contractTitle () {
      let {pageData} = this
      let prefix = ''
      if (pageData.buyContent) {
        prefix += '上'
      }
      if (pageData.sellContent) {
        prefix += '下'
      }
      if (!prefix) {
        return ''
      }
      return prefix + '游'
    }
  },
  watch: {
    pageData () {
      document.title = this.pageData.pageTitle || '石油系统'
    }
  },
  methods: {
    auditInput (index, field, value) {
      let tmp = this.audit[index]
      tmp[field] = value
      this.audit.splice(index, 1, tmp)
      this.$store.commit(`${this.moduleName}/update`, {field: 'audit', value: this.audit})
    },
    async onSubmit (status) {
      // 风控审核
      let {moduleName, actionName} = this
      if (actionName === 'check') {
        let errorMsgs = []
        let failed = false
        let submitData = {
          items: [],
          checkStatus: status,
          check_id: this.pageData.checkId,
          detail_id: this.pageData.detailId
        }
        if (moduleName === 'check2' && !this.audit1 && status === '1') {
          this.audit1Error = Date.now()
          // this.$toptips('请选择是否启用额度流程')
          errorMsgs.push('请选择是否启用额度流程')
          failed = true
        } else if (!this.audit2) {
          this.audit2Error = Date.now()
          // this.$toptips('请输入本次审核意见')
          errorMsgs.push('请输入本次审核意见')
          failed = true
        }
        if (moduleName === 'check3') {
          this.pageData.auditOptions.find((opt, i) => {
            let v = this.audit[i]
            if (v.value === '') {
              failed = true
              errorMsgs.push('请选择' + opt.name)
            } else if (v.value === '0' && v.comment === '') {
              failed = true
              errorMsgs.push(opt.name + '：选择否，备注项必填；')
            } else {
              submitData.items.push({
                name: opt.name,
                displayValue: v.value === '1' ? '是' : '否',
                value: v.value,
                remark: v.comment
              })
            }
          })
        }
        if (failed) {
          for (let i = 0; i < errorMsgs.length; i++) {
            errorMsgs[i] = (i + 1) + '.' + errorMsgs[i]
          }
          let thisMsg = errorMsgs.join('<br>')
          this.$toptips(thisMsg)
          return true
        }
        if (!failed) {
          submitData.remark = this.audit2
          let confirmText = status === '1' ? '通过' : '驳回'
          let action = await MessageBox.confirm(`确定要${confirmText}本次审核吗？`, '').catch(() => (false))
          if (action) {
            Indicator.open()
            let {success, msg} = await post(`/${moduleName}/save`, {
              obj: submitData,
              items: submitData.items
            })

            if (success) {
              var res2 = {success: 1}
              if (status === '1' && this.audit1 === '0') {
                res2 = await post(`/quota/save`, {
                  contract_id: this.pageData.contractId,
                  project_id: this.pageData.projectId,
                  is_main: this.pageData.isMain
                })
              }

              Indicator.close()
              await MessageBox.alert('审核提交成功!', '')
              if (status === '1' && (this.audit1 === '1' || !res2.success)) {
                if (this.audit1 === '0' && !res2.success) { await MessageBox.alert(res2.msg, '') }

                this.$router.replace({path: `/${moduleName}/${actionName}/add-price`, query: {id: this.pageData.contractId, detail_id: this.pageData.detailId}})
              } else {
                this.$router.replace({path: `/${moduleName}/detail`, query: {detail_id: this.pageData.detailId, __force: true}})
              }
            } else {
              await MessageBox.alert(msg, '').then(action => {
                window.location.href = '/site/index'
              })
            }
          }
        }
      }
    }
  }
}
</script>

<style scoped>
.flex-1{
  flex: 1;
}
.table-split{
margin-top: 15px;
}
.simple-card{
  h1{
    font-weight: normal;
    font-size: 14px;
  }
  p{
    font-size: 12px;
    color: #949494;
  }
}
.quota-item{
  display: flex;
  font-size: 14px;
  .quota-info{
    padding: 0 10px;
    p{
      padding: 3px 0;
    }
  }
}
.simple-formula {
  font-size: 14px;
  overflow: hidden;
  font-family: Microsoft YaHei,'宋体' ,'Source Sans Pro', 'Helvetica Neue', Helvetica, Arial, sans-serif;
}
</style>
