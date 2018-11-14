<?php
/**
* 报表统计 命令行，提供常用的报表统计服务
*
*/
class ReportCommand extends CConsoleCommand
{
    /**
     * @desc:风控额度预警报表
     * @说明：该脚本部分数据依赖 RepairCommand的updatePartnerStat脚本
     *
     * */
    public function actionRiskAmountWarning()
    {
        ReportService::riskAmountWarning();
    }
    /**
     * @desc:上游供应商报表
     *
     * */
    public function actionPartnerBuyContract()
    {
        ReportService::partnerBuyContract();
    }
    /**
     * @desc:下游客户报表
     *
     * */
    public function actionPartnerSellContract()
    {
        ReportService::partnerSellContract();
    }

    /**
     * [actionPaymentInterest 生成收付款资金占用利息明细]
     */
    public function actionPaymentInterest()
    {
        echo "======开始生成利息明细相关数据======\n";
        InterestReportService::addInterestInfo();
        sleep(3);
        InterestReportService::addDayInterest();
        echo "======成功生成利息明细相关数据======\n";
    }
    /**
     * @desc:库存报表
     * @说明：交易主体对于的商品库存统计
     *
     * */
    public function actionCorporationStock()
    {
        CorporationStockService::corporationStock();
    }


}

