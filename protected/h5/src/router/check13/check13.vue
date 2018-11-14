<template>
  <div v-if="pageData">
    <panel title="付款信息">
      <card>
        <field-item label="付款申请编号" :content="pageData.applyId"/>
        <field-item label="付 款  金   额" :content="pageData.amount "/>
        <field-item label="合同已实付金额" :content="pageData.contractPaiedAmount "/>
        <field-item label="用            途" :content="pageData.subjectName "/>
        <field-item label="付  款 原 因" :content="pageData.payRemark "/>
      </card>
      <card type="link" :href="{path:'/'+moduleName+'/'+actionName+'/payments', query: {id: pageData.applyId}}" v-if="pageData.payDetails.length && (pageData.applyType === 11 || pageData.applyType === '11')">付款计划 ( {{pageData.payDetails.length}} )</card>
      <card v-if="pageData.payDetails.length === 0 && (pageData.applyType === 11 || pageData.applyType === '11')">付款计划 ( 0 )</card>

      <!--详情页面参数不一样, 审核页面用id, 详情用detail id-->
      <card type="link" :href="{path:'/'+moduleName+'/'+actionName+'/contract-files', query: {id: pageData.applyId}}" v-if="pageData.contractFiles.length&&actionName==='check'">合同附件 ( {{pageData.contractFiles.length}} )</card>
      <!--详情页面参数不一样, 审核页面用id, 详情用detail id-->
      <card type="link" :href="{path:'/'+moduleName+'/'+actionName+'/contract-files', query: {detail_id:pageData.detailId}}" v-if="pageData.contractFiles.length&&actionName==='detail'">合同附件 ( {{pageData.contractFiles.length}} )</card>
      <card v-if="pageData.contractFiles.length === 0 && (pageData.applyType === 11 || pageData.applyType === '11')">合同附件 ( 0 )</card>
      <!--详情页面参数不一样, 审核页面用id, 详情用detail id-->
      <card type="link" :href="{path:'/'+moduleName+'/'+actionName+'/attachments', query: {id: pageData.applyId}}" v-if="pageData.attachments.length&&actionName==='check'">付款附件 ( {{pageData.attachments.length}} )</card>
      <!--详情页面参数不一样, 审核页面用id, 详情用detail id-->
      <card type="link" :href="{path:'/'+moduleName+'/'+actionName+'/attachments', query: {detail_id:pageData.detailId}}" v-if="pageData.attachments.length&&actionName==='detail'">付款附件 ( {{pageData.attachments.length}} )</card>
      <card v-if="pageData.attachments.length===0">付款附件 ( 0 )</card>
      <card>
        <field-item  v-if="pageData.applyType === 11 || pageData.applyType === '11'" label="是否对接保理" :content="pageData.isFactoring" force="1"/>
        <field-item  v-if="pageData.applyType === 11 || pageData.applyType === '11'" label="本次保理金额" :content="pageData.factoringAmount" force="1"/>
        <field-item  v-if="pageData.applyType === 11 || pageData.applyType === '11'" label="资金对接编号" :content="pageData.factoringCodeFund" />
        <field-item  v-if="pageData.applyType === 11 || pageData.applyType === '11'" label="保理对接编号" :content="pageData.factoringCode" />
        <field-item label="付款合同类别" :content="pageData.subContractType" force="1"/>
        <field-item label="付款合同编号" :content="pageData.subContractCode" force="1"/>
      </card>
      <card>
        <field-item label="收款单位" :content="pageData.payee"/>
        <field-item label="收款账户名" :content="pageData.accountName"/>
        <field-item label="开 户 银 行" :content="pageData.bank "/>
        <field-item label="银 行 账 号" :content="pageData.account "/>
      </card>
    </panel>
    <panel v-if="pageData.multipleContract" title="合同信息" type="link" :href="{path:'/'+moduleName+'/'+actionName+'/contract', query: {id: pageData.applyId}}">
      <card>
        <field-item label="交易主体" :content="pageData.corporationName"/>
        <div class="g-dotted-split"></div>
        <field-item v-for="item in pageData.multipleContract" :key="item.contractCode + item.projectCode" label="合同编号" :content="item.contractCode"/>
      </card>
    </panel>
    <panel v-else-if="pageData.singleContract" title="合同信息">
      <card>
        <field-item label="交易主体" :content="pageData.corporationName"/>
        <field-item label="合作方" :content="pageData.singleContract.partnerName"/>
        <field-item label="项目编号" :content="pageData.singleContract.projectCode"/>
        <field-item label="项目类型" :content="pageData.singleContract.projectTypeName "/>
        <field-item label="合同编号" :content="pageData.singleContract.contractCode"/>
        <field-item label="合同类型" :content="pageData.singleContract.contractTypeName"/>
      </card>
    </panel>
    <panel v-else-if="pageData.projectInfo" title="项目信息">
      <card>
        <field-item label="交易主体" :content="pageData.corporationName"/>
        <field-item label="合作方" :content="pageData.projectInfo.partnerName"/>
        <field-item label="项目编号" :content="pageData.projectInfo.projectCode"/>
        <field-item label="项目类型" :content="pageData.projectInfo.projectTypeName "/>
      </card>
    </panel>
    <card v-else>
      <field-item label="交易主体" :content="pageData.corporationName"/>
    </card>
    <panel title="风险提示">
      <card>
        <field-item v-for="item in pageData.payExtra" :key="item.name" :label="item.name" :content="item.value"/>
      </card>
    </panel>
    <panel title="审核历史记录" v-if="pageData.checkHistory.length > 0" type="link" :href="{path:'/'+moduleName+'/'+actionName+'/history', query: {detail_id: pageData.detailId}}"></panel>
    <template v-if="actionName!='detail'">
    <panel title="本次审核意见" v-if="pageData.auditOptions">
       <card v-for="(item, index) in pageData.auditOptions" :key="item.name">
        <error-focus v-model="auditError[index]" >
            <audit-item :title="(index+1) + '.' + item.name" :value="audit[index].value" @input="auditInput(index, 'value', $event)"></audit-item>
        </error-focus>
        <error-focus v-model="auditError[index]">
            <audit-item type="input" :value="audit[index].comment" @input="auditInput(index,'comment', $event)"></audit-item>
        </error-focus>
       </card>
      <card :topBorder="1" >
        <error-focus v-model="auditCommentError">
          <audit-item type="comment" placeholder="请输入本次审核意见" v-model="auditComment"/>
        </error-focus>
      </card>
    </panel>
    <submit-btn okText="通过" cancelText="驳回" @okHandle="onSubmit('1')" @cancelHandle="onSubmit('-1')"/>
    </template>
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
    Inline
  },
  data () {
    return {
    }
  },
  computed: {
    ...mapBase(),
    ...mapVmodel({moduleName: true}, ['audit'], ['auditError'], ['auditComment'], ['auditCommentError']),
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
        if (pageData.shellGoods) {
          goods = goods.concat(pageData.shellGoods)
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
      if (pageData.shellContent) {
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
      if (moduleName === 'check13' && actionName === 'check') {
        let errorMsgs = []
        let failed = false
        let submitData = {
          items: [],
          checkStatus: status,
          check_id: this.pageData.checkId,
          detail_id: this.pageData.detailId
        }
        this.pageData.auditOptions.find((opt, i) => {
          let v = this.audit[i]
          if (v.value === '') {
            failed = true
            // this.$set(this.auditError, i, new Date().getTime())
            errorMsgs.push('请选择' + opt.name)
            // this.$toptips('请选择' + opt.name)
            // return true
          } else if (v.value === '0' && v.comment === '') {
            failed = true
            // this.$set(this.auditError, i, new Date().getTime())
            errorMsgs.push(opt.name + '：选择否，备注项必填；')
            // this.$toptips('请填写备注')
            // return true
          } else {
            submitData.items.push({
              name: opt.name,
              displayValue: v.value === '1' ? '是' : '否',
              value: v.value,
              remark: v.comment
            })
          }
        })
        if (this.auditComment === '') {
          this.$store.commit(`${moduleName}/update`, {field: 'auditCommentError', value: new Date().getTime()})
          // this.$toptips('请输入本次审核意见')
          errorMsgs.push('请输入本次审核意见')
          failed = true
        } else {
          submitData.remark = this.auditComment
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
          let action = await MessageBox.confirm(`确定要${status === '-1' ? '驳回' : '通过'}本次审核吗?`, '').catch(() => false)
          if (action) {
            Indicator.open()
            let {success, msg} = await post(`/check13/save`, {data: submitData})
            Indicator.close()
            if (!success) {
              this.$toptips(msg)
            } else {
              await MessageBox.alert('提交成功!', '')
              this.$router.replace({ path: `/${this.moduleName}/detail`, query: {detail_id: this.pageData.detailId, __force: true} })
            }
          }
        }
      }
    }
  }
}
</script>

<style scoped>
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
</style>
