import { get } from '@/utils/request'
import { numberFormat } from '@/utils/format'
import { Indicator, MessageBox } from 'mint-ui'

const formatMoney = (num) => numberFormat(num / 100, 2)
const formatWanMoney = (num) => numberFormat(num / 1000000, 2)
const mapGoods = (goods, map, exchangeRate, unit) => {
  if (!goods) return {}
  console.log(goods)
  return goods.map(i => ({
    id: i.detail_id,
    name: i.goods.name,
    quantity: i.quantity,
    quantityName: (i.quantity * 1).toFixed(2) + (!Array.isArray(i.goods) && i.unit !== '0' ? map.goods_unit[i.unit].name : ''),
    type: i.type,
    avatar: i.type === '1' ? '采' : '销',
    price: map.currency[i.currency].ico + formatMoney(i.price),
    rateName: i.more_or_less_rate * 1 !== 0 ? '±' + numberFormat(i.more_or_less_rate * 100, 2) + '%' : '',
    amount: map.currency[i.currency].ico + formatMoney(i.amount),
    amountCNY: '￥' + formatMoney(i.amount_cny),
    amountCNYValue: i.amount_cny,
    exchangeRate: Number(exchangeRate) ? numberFormat(exchangeRate, 2) : '',
    refer_target: i.refer_target,
    unit_convert_rate: i.unit === '2' ? '' : (map.goods_unit[i.unit].name + '/' + unit + ' = ' + i.unit_convert_rate)

  }))
}
const mapPayments = (type, payments, map) => {
  if (!payments) return {}
  return payments.map(i => ({
    date: i.pay_date,
    typeName: map[type][i.expense_type].name + (Number(i.expense_type) !== 5 ? '' : '(' + i.expense_name + ')'),
    price: map.currency[i.currency].ico + formatMoney(i.amount),
    remark: i.remark
  }))
}
const mapAttachments = (attachments, map) => {
  if (!attachments) return {}
  // console.log(map.project_launch_attachment_type)
  return attachments.map(i => {
    // console.log(map.project_launch_attachment_type[i.type], i.type)
    let mapType = map.project_launch_attachment_type[i.type]
    return {
      id: i.id,
      name: i.name,
      status: i.status,
      url: i.file_url,
      typeName: mapType ? mapType.name : '',
      isImg: !!i.file_url.match(/\.(jpg|png|bmp|gif)$/),
      download_path: i.download_path
    }
  })
}
const mapProjectTypeName = (project, map) => {
  let typeName = map.project_type[project.type]
  if (project.base && project.base.buy_sell_type !== '0') {
    typeName += '-' + map.purchase_sale_order[project.base.buy_sell_type]
  }
  return typeName
}
const mapAgentDetail = (details, map) => {
  if (!details) {
    return []
  }
  return details.map(i => ({
    name: i.agentGoods.name,
    unitName: map.goods_unit[i.agentGoods.unit].name,
    // 记价方式
    payTypeName: map.agent_fee_pay_type[i.type],
    price: '￥' + formatMoney(i.price),
    feeRate: numberFormat(i.fee_rate * 100, 2) + '%',
    fee: '￥' + formatMoney(i.amount)
  }))
}
const mapContractTypeName = (buy, sell, map) => {
  let ret = []
  if (buy && typeof buy === 'object' && buy.type && buy.category) {
    ret.push(map.contract_config[buy.type][buy.category].name)
  }
  if (sell && typeof sell === 'object' && sell.type && sell.category) {
    ret.push(map.contract_config[sell.type][sell.category].name)
  }
  return ret.join('/')
}
const mapQuotas = (quotas, map, type) => {
  quotas = quotas || []
  return quotas.map(i => {
    let info = Array.isArray(i.quotaManager) ? i.quotaPartner : i.quotaManager
    return {
      id: i.detail_id,
      name: info.name || info.user_name,
      price: '￥' + formatWanMoney(i.amount, 2) + '万元',
      comment: i.remark,
      icon: type === 1 ? '采' : '销',
      isBlue: type === 2
    }
  })
}

// 审核页面通用数据获取和处理
export const fetchCheckData = async (checkid, id, tp = 'check', idName = 'id') => {
  Indicator.open()
  let ret = await get(`/check${checkid}/${tp}`, { [idName]: id })
  Indicator.close()
  if (ret.success && ret.data) {
    let { pageTitle, contract, relative, map, data: extra, checkHistory = [], checkLogs = [], goods_unit_convert, creator, updater, create_time, update_time, extraItems } = ret.data
    if (checkLogs.length) {
      checkHistory = checkLogs
    }
    let { project } = contract || {}
    let buyContract = contract.type === '1' ? contract : relative
    let sellContract = contract.type === '2' ? contract : relative
    let quotaData = [
      {
        partnerName: contract.partner && contract.partner.name,
        credit_amount: contract.partner && ('￥' + formatMoney(contract.partner.credit_amount)),
        used_amount: contract.partner && contract.partner.usedAmount && ('￥' + formatMoney(contract.partner.usedAmount.used_amount)),
        contract_used_amount: contract.partner && contract.partner.contractAmount && ('￥' + formatMoney(contract.partner.contractAmount.used_amount))
      }
    ]
    if (contract.relation_contract_id && relative && relative.partner) {
      quotaData.push({
        partnerName: relative.partner && relative.partner.name,
        credit_amount: relative.partner && ('￥' + formatMoney(relative.partner.credit_amount)),
        used_amount: relative.partner && relative.partner.usedAmount && ('￥' + formatMoney(relative.partner.usedAmount.used_amount)),
        contract_used_amount: relative.partner && relative.partner.contractAmount && ('￥' + formatMoney(relative.partner.contractAmount.used_amount))
      })
    }
    // console.log(goods_unit_convert)
    let data = {
      quotaData: quotaData,
      pageTitle,
      detailId: extra.detail_id,
      contractId: contract.contract_id,
      isMain: contract.is_main,
      projectId: contract.project_id,
      projectCode: project.project_code,
      projectType: project.type * 1,
      projectTypeName: mapProjectTypeName(project, map),
      contractTypeName: mapContractTypeName(buyContract, sellContract, map),
      agentTypeName: map.buy_agent_type[contract.agent_type],
      agentName: contract.agent.name,
      buySellDesc: contract.buy_sell_desc,
      corporationName: contract.corporation.name,
      prevPartnerName: (buyContract && buyContract.partner) ? buyContract.partner.name : '',
      nextPartnerName: (sellContract && sellContract.partner) ? sellContract.partner.name : '',
      buyGoods: buyContract ? mapGoods(buyContract.contractGoods, map, buyContract.exchange_rate, goods_unit_convert) : '',
      sellGoods: sellContract ? mapGoods(sellContract.contractGoods, map, sellContract.exchange_rate, goods_unit_convert) : '',
      buyContent: (buyContract && buyContract.extra) ? JSON.parse(buyContract.extra.content) : '',
      sellContent: (sellContract && sellContract.extra) ? JSON.parse(sellContract.extra.content) : '',
      prevPayments: (buyContract && buyContract.payments) ? mapPayments('pay_type', buyContract.payments, map) : '',
      nextPayments: (sellContract && sellContract.payments) ? mapPayments('proceed_type', sellContract.payments, map) : '',
      attachments: mapAttachments(project.attachments, map),
      agentDetails: mapAgentDetail(contract.agentDetail, map),
      buyFormula: buyContract ? buyContract.formula || '' : '',
      sellFormula: sellContract ? sellContract.formula || '' : '',
      checkId: extra.check_id,
      quotas: mapQuotas((buyContract && buyContract.quotas) ? buyContract.quotas : null, map, 1).concat(mapQuotas((sellContract && sellContract.quotas) ? sellContract.quotas : null, map, 2)),
      checkHistory: checkHistory.map(i => (
        {
          id: i.id,
          nodeName: i.node_name,
          time: i.check_time,
          name: i.name,
          comment: i.remark,
          status: i.check_status,
          checkChoices: i.checkChoices ? i.checkChoices : ''
        }

          )),
      buyContract: buyContract,
      sellContract: sellContract,
      creator: creator,
      updater: updater,
      create_time: create_time,
      update_time: update_time,
      up_delivery_term: (buyContract && buyContract.delivery_term) ? buyContract.delivery_term : '',
      up_delivery_mode: (buyContract && buyContract.delivery_mode) ? buyContract.delivery_mode : '',
      up_days: (buyContract && buyContract.days) ? buyContract.days + '（根据入库单日期倒推）' : '',
      down_delivery_term: (sellContract && sellContract.delivery_term) ? sellContract.delivery_term : '',
      down_delivery_mode: (buyContract && sellContract.delivery_mode) ? sellContract.delivery_mode : '',
      down_days: (sellContract && sellContract.days) ? sellContract.days + '（根据出库单日期倒推）' : ''
    }
    if (extraItems) {
      data.auditOptions = extraItems.map(i => ({name: i.name}))
    }
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
    // if (tp === 'check') {
    //   MessageBox({
    //     title: '',
    //     message: ret.msg,
    //     showCancelButton: true,
    //     confirmButtonText: '返回首页',
    //     cancelButtonText: '查看详情'
    //   }).then(action => {
    //     if (action === 'confirm') {
    //       window.location.href = '/site/index'
    //     } else {
    //       // fetchCheckData(checkid, id, 'detailById', 'id')
    //       router.push('/check' + checkid + '/detailById?b=' + checkid + '&id=' + id)
    //     }
    //   })
    // } else {
    //   MessageBox({
    //     title: '',
    //     message: ret.msg,
    //     confirmButtonText: '返回首页'
    //   }).then(action => {
    //     if (action === 'confirm') {
    //       window.location.href = '/site/index'
    //     }
    //   })
    // }
  }
  return ret
}
export async function getQuotaInfo (contractId, isMain) {
  Indicator.open()
  let ret = await get(`/quota/ajaxEdit`, { contract_id: contractId, is_main: isMain })
  Indicator.close()
  if (ret.success && ret.data) {
    let { contract, relative } = ret.data
    ret.data.detailId = contractId
    ret.data.buyContract = contract.type === '1' ? true : !!relative
    ret.data.sellContract = contract.type === '2' ? true : !!relative
  } else {
    MessageBox.alert(ret.msg, '').then(action => {
      window.location.href = '/site/index'
    })
  }
  return ret
}
