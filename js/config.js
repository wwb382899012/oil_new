/**
 * Created by Susie on 2017/8/29 0029.
 */

//项目类型
var projectTypeSelfImport = 1; //进口自营
var projectTypeImportBuy = 2; //进口代采
var projectTypeImportChannel = 3; //进口渠道
var projectTypeSelfInternalTrade = 4; //内贸自营
var projectTypeInternalTradeBuy = 5; //内贸代采
var projectTypeInternalTradeChannel = 6; //内贸渠道
var projectTypeWarehouseReceipt = 7; //仓单质押

//购销顺序
var firstSaleLastBuy = 1; //先销后采
var firstBuyLastSale = 2; //先采后销

//币种
var currencyRMB = 1; //人民币
var currencyDollar = 2; //美元

var expenseTypeOther = 5; //付款类型为其他

//采购合同类型
var buySaleContractTypeDirectImport = 1; //直接进口合同
var buySaleContractTypeAgentImport = 2; //代理进口合同
var buySaleContractTypeInternal = 3; //国内采购合同

//代理手续费计费方式
var agentFeeCalculateByAmount = 1; //从量
var agentFeeCalculateByPrice = 2; //从价

//合同文本类型
var electronSignContractFile = 11; //电子双签合同

var config = new Object();
config.projectTypeChannelBuy = [projectTypeImportBuy, projectTypeImportChannel, projectTypeInternalTradeBuy, projectTypeInternalTradeChannel]; //进口渠道，内贸渠道，进口代采，内贸代采
config.projectTypeWarehouseReceipt = [projectTypeWarehouseReceipt]; //仓单质押
config.projectTypeSelfSupport = [projectTypeSelfImport, projectTypeSelfInternalTrade]; //进口自营，内贸自营

config.projectTypeImport = [projectTypeSelfImport, projectTypeImportBuy, projectTypeImportChannel]; //进口类型

config.firstSaleLastBuy = firstSaleLastBuy;
config.firstBuyLastSale = firstBuyLastSale;
config.projectTypeSelfImport = projectTypeSelfImport;
config.projectTypeSelfInternalTrade = projectTypeSelfInternalTrade;

config.currencyRMB = currencyRMB;
config.currencyDollar = currencyDollar;
config.expenseTypeOther = expenseTypeOther;

config.buySaleContractTypeDirectImport = buySaleContractTypeDirectImport;
config.buySaleContractTypeAgentImport = buySaleContractTypeAgentImport;
config.buySaleContractTypeInternal = buySaleContractTypeInternal;

config.agentFeeCalculateByAmount = agentFeeCalculateByAmount;
config.agentFeeCalculateByPrice = agentFeeCalculateByPrice;

config.buyContractSelectType = [projectTypeImportBuy, projectTypeImportChannel];
config.buyContractStaticType = [projectTypeInternalTradeBuy, projectTypeInternalTradeChannel, projectTypeWarehouseReceipt];

config.staticPrice = 1;  //死价
config.tempPrice = 2;    //活价（价格为暂估价）

config.stockNoticeTypeByWarehouse = 1;    //入库通知单发货方式：经仓
config.stockNoticeTypeDirectTransfer = 2;    //入库通知单发货方式：直调

config.electronSignContractFile = electronSignContractFile;

/**
 * 是否有代码模块
 * @param type
 * @returns {boolean}
 */
config.isHasAgentModule=function (type) {
    return type==config.buySaleContractTypeAgentImport;
}
/**
 * 付款申请类别
 * @type {{contract: number, sell_contract: number, multi_contract: number, project: number, corporation: number, claim: number}}
 */
config.payApplicationType={
    contract:11,
    sell_contract:12,
    multi_contract:13,
    project:14,
    corporation:15,
    claim:20
};
