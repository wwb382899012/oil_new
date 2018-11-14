import {get} from '@/utils/request'
import {numberFormat} from '@/utils/format'
import { Indicator, MessageBox } from 'mint-ui'

const formatMoney = (num, currency, map) => (num !== 0 && num !== '0') ? map.currency[currency].ico + numberFormat(num / 100, 2) : map.currency[currency].ico + '0.00'

// const trueOrFalse = (i) => i ? '是' : '否'

const trueOrFalseFactoring = (i) => i !== '0' ? '是' : '否'

const mapContractInfo = (contract, map, project) => {
  if (!contract) {
    return null
  }
  if (!Array.isArray(contract)) {
    return {
      projectCode: project.project_code,
      projectTypeName: map.project_type[project.type],
      contractCode: contract.contract_code,
      contractTypeName: map.contract_config[contract.type][contract.category].name,
      partnerName: contract.partner ? contract.partner.name : ''
    }
  }
  let tmpcb = (i) => ({
    projectCode: i.project.project_code,
    contractCode: i.contract.contract_code,
    amount: formatMoney(i.amount, 1, map),
    contractTypeName: map.contract_config[i.contract.type][i.contract.category].name
  })
  return contract.length ? contract.map(tmpcb) : []
}

const mapContractType = (contractType, map) => {
  return map.contract_category[contractType]
}

const mapProjectInfo = (project, map) => {
  return {
    projectCode: project.project_code,
    projectTypeName: map.project_type[project.type],
    partnerName: project.partner ? project.partner.name : ''

  }
}

const mapAttachments = (attachments) => {
  let ret = []
  attachments = attachments || {}
  for (let key in attachments) {
    attachments[key].forEach(i => {
      ret.push({
        id: i.id,
        name: i.name,
        url: i.file_url,
        isImg: isImg(i.file_url),
        download_path: i.download_path
      })
    })
  }
  return ret
}

const mapPayments = (payments, map, apply) => {
  let ret = []
  payments = payments || []
  payments.forEach(i => {
    ret.push({
      period: i.payment.period + '期',
      type: map.pay_type[i.payment.expense_type].name + ((i.payment.expense_type === 5 || i.payment.expense_type === '5') ? '(' + i.payment.expense_name + ')' : ''),
      amount: formatMoney(i.payment.amount, i.currency, map), // 计划付款金额
      amount_paid: (apply.status >= 10) ? formatMoney(i.payment.amount_paid - i.amount, i.currency, map)
      : formatMoney(i.payment.amount_paid, i.currency, map), // 已申请金额
      amount_unpaid: formatMoney(i.payment.amount - i.payment.amount_paid, i.currency, map), // 未申请金额
      amount_to_pay: formatMoney(i.amount, i.currency, map)// 本次付款金额
    })
  })
  return ret
}

const mapContractFiles = (contractFiles, map) => {
  contractFiles = contractFiles || []
  return contractFiles.map(i => {
    let typeNameObj = map.contract_file_attachment_type[i.type]
    return {
      id: i.file_id,
      name: i.name,
      url: i.file_url,
      typeName: typeNameObj ? typeNameObj.name : '',
      isImg: isImg(i.file_url),
      download_path: i.download_path
    }
  })
}
const isImg = (url) => {
  return !!url.match(/\.(jpg|png|gif|jpeg|bmp)$/)
}
// 审核页面通用数据获取和处理
export const fetchCheckData = async (checkid, id, tp = 'check', idName) => {
  Indicator.open()
  let ret = await get(`/check${checkid}/${tp}`, {[idName]: id})
  Indicator.close()
  if (ret.success && ret.data) {
    let {pageTitle, apply, map, items = [], data: extra, checkHistory = [], checkLogs = [], payDetails = [], contractPaiedAmount = ''} = ret.data
    if (checkLogs.length) {
      checkHistory = checkLogs
    }
    let factor = apply.factor
    if (!factor || Array.isArray(factor)) {
      factor = {}
    }

    let hasSingleContract = apply.contract && !Array.isArray(apply.contract)
    let data = {
      pageTitle,
      applyId: apply.apply_id,
      detailId: extra.detail_id,
      checkId: extra.check_id,
      amount: formatMoney(apply.amount, apply.currency, map),
      subContractCode: apply.sub_contract_code,
      subContractType: mapContractType(apply.sub_contract_type, map),
      payRemark: apply.remark || '',
      subjectName: apply.subject.name,
      // 是否对接保理
      isFactoring: (Number(apply.type) !== 13) ? trueOrFalseFactoring(apply.is_factoring) : null,
      // 本次保理金额
      payee: apply.payee || '',
      factoringAmount: formatMoney(apply.amount_factoring, apply.currency, map),
      // 资金对接编号
      applyType: apply.type,
      factoringCodeFund: factor.contract_code_fund,
      factoringCode: factor.contract_code,
      contractCode: hasSingleContract ? apply.contract.contract_code : '',
      accountName: apply.account_name,
      bank: apply.bank,
      account: apply.account,
      payExtra: apply.extra ? Object.values(map.pay_application_extra).map(i => ({name: i.name, value: map.isNor[apply.extra.items[i.id]]})) : [],
      multipleContract: (Number(apply.type) === 13) ? mapContractInfo(apply.details, map) : null, // 多合同合并付款
      corporationName: apply.corporation.name,
      singleContract: (Number(apply.type) === 11 || Number(apply.type) === 12) ? mapContractInfo(apply.contract, map, apply.project) : null, // 合同下付款
      auditOptions: items.map(i => ({name: i.name})),
      attachments: mapAttachments(apply.attachments),
      payDetails: (Number(apply.type) === 11 || Number(apply.type) === 12) ? mapPayments(payDetails, map, apply) : [],
      projectInfo: (Number(apply.type) === 14) ? mapProjectInfo(apply.project, map) : null,
      contractFiles: mapContractFiles(apply.contract_files, map),
      checkHistory: checkHistory.map(i => (
        {
          id: i.id,
          nodeName: i.node_name,
          time: i.check_time,
          name: i.name,
          comment: i.remark,
          status: i.check_status,
          checkChoices: i.checkChoices
        })),
      contractPaiedAmount: contractPaiedAmount
    }
    if (apply.extra && apply.extra.remark) { data.payRemark += apply.extra.remark }
    ret.data = data
  } else {
    MessageBox({
      title: '',
      message: ret.msg,
      confirmButtonText: '返回首页'
    }).then(action => {
      if (action === 'confirm') {
        window.location.href = '/site/index'
      }
    })
  }
  return ret
}
