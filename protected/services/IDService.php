<?php

/**
 * Created by youyi000.
 * DateTime: 2016/7/4 11:44
 * Describe：
 *      获取统一编号的服务类，日期字符+顺序号：201611020001
 */
class IDService
{
    /**
     * 获取保理对接的统一Id编号
     * @return int|string
     */
    public static function getFactorDetailId() {
        return self::getId("oil.factor.detail.id.",4);
    }

    /**
     * 获取资金对接编号序列号
     * @param string $month
     * @return int|string
     */
    public static function getFactoringFundCodeSerial($month='') {
        return self::getId('factor.fund.code.serial.', 3, 'Ym', 86400 * 31,$month);
    }

    /**
     * 获取保理对接编号序列号
     * @param string $month
     * @param string $corpCode 交易主体编码
     * @return int|string
     */
    public static function getFactoringCodeSerial($month='', $corpCode) {
        return self::getId('factor.code.serial.'.$corpCode, 3, 'Ym', 86400 * 31,$month);
    }

    /**
     * 获取库存盘点的统一Id编号
     * @return int|string
     */
    public static function getStockInventoryId()
    {
        return self::getId("oil.stock.inventory.id.",4);
    }

    /**
     * 获取付款申请的统一Id编号
     * @return int|string
     */
    public static function getPayApplicationId()
    {
        return self::getId("oil.pay.application.id.",5);
    }

    /**
     * 获取入库单的统一Id编号
     * @return int|string
     */
    public static function getStockInId()
    {
        return self::getId("oil.store.in.id.",4);
    }
    /**
     * 获取出库单的统一Id编号
     * @return int|string
     */
    public static function getStoreOutOrderId()
    {
        return self::getId("oil.store.out.order.id.",4);
    }
    /**
     * 获取入库通知单的统一Id编号
     * @return int|string
     */
    public static function getStockBatchId()
    {
        return self::getId("oil.store.batch.id.",4);
    }

    /**
     * 获取发货单Id编号
     * @return int|string
     */
    public static function getDeliveryOrderId() {
        return self::getId("oil.delivery.order.id.",4);
    }

    /**
     * 获取合同编码序列的统一编码
     * @return int|string
     */
    public static function getContractCodeId()
    {
        $len = 2;
        $id = self::getId("oil.contract.code.id.",$len);
        return substr($id,strlen($id)-$len);
    }


    /**
     * 获取项目编码最后日期+数字的统一编码
     * @return int|string
     */
    public static function getProjectCodeId()
    {
        return self::getId("oil.project.code.id.",2,"ymd");
    }

    /**
     * 获取项目的统一Id编号
     * @return int|string
     */
    public static function getProjectId()
    {
        return self::getId("oil.project.id.",3);
    }

    /**
     * 获取项目的统一Id编号
     * @return int|string
     */
    public static function getContractFileId()
    {
        return self::getId("oil.contract.file.id.", 4);
    }


    /**
     * 获取收入单号的统一Id编号
     * @return int|string
     */
    public static function getCodeId()
    {
        return self::getId("oil.income.code.id.",4);
    }

    /**
     * 获取收入成本确认单的统一Id编号
     * @return int|string
     */
    public static function getStatementId()
    {
        return self::getId("oil.income.statement.id.",3);
    }

    /**
     * 获取合作方的统一Id编号
     * @return int|string
     */
    public static function getPartnerId()
    {
        return self::getId("oil.apply.partner.id.",4);
    }

    /**
     * 获取合作方评审的统一Id编号
     * @return int|string
     */
    public static function getPartnerReviewId()
    {
        return self::getId("oil.review.partner.id.",3);
    }

	/**
	 * @desc 获取现场风控统一Id编号
	 * @return int|string
	 */
	public static function getPartnerRiskId() {
		return self::getId("oil.risk.partner.id", 3);
    }

    /**
     * 获取项目风控计划的统一Id编号
     * @return int|string
     */
    public static function getRiskPlanId()
    {
        return self::getId("oil.risk.plan.id.");
    }

    /**
     * 获取项目评审排期的统一Id编号
     * @return int|string
     */
    public static function getReviewId()
    {
        return self::getId("oil.review.id.");
    }

    /**
     * 获取项目风控考察的统一Id编号
     * @return int|string
     */
    public static function getRiskId()
    {
        return self::getId("oil.risk.id.");
    }


    /**
     * 获取流水编号
     * @return int|string
     */
    public static function getBankFlowId()
    {
        return self::getId("bank.flow.id.", 5);
    }

    /**
     * 获取导入流水编号
     * @return int|string
     */ 
    public static function getBankFlowImportId()
    {
        return self::getId("bank.flow.import.id.", 5);
    }

    /**
     * 获取流水编号
     * @return int|string
     */
    public static function getReceiveConfirmId()
    {
        return self::getId("receive.confirm.id.", 6);
    }

    /**
     * 获取申请发编号
     * @return int|string
     */ 
    public static function getInoviceApplicationId()
    {
        return self::getId("oil.invoice.application.id.",5);
    }

    /**
     * 获取付款实付编号
     * @return int|string
     */ 
    public static function getPayConfirmId()
    {
        return self::getId("oil.payment.confirm.id.",5);
    }

    /**
     * 获取付款止付编号
     * @return int|string
     */ 
    public static function getPayStopId()
    {
        return self::getId("oil.payment.stop.id.",3);
    }

    /**
     * 获取合同结算单编号
     * @return int|string
     */
    public static function getContractSettlementId()
    {
        return self::getId("oil.contract.settlement.id.",3);
    }

    /**
     * 获取合同结算单编码
     * @return int|string
     */ 
    public static function getContractSettlementCode()
    {
        return self::getId("oil.contract.settlement.code.",3);
    }

    /**
     * 获取提单结算单编号
     * @return int|string
     */
    public static function getLadingSettlementId()
    {
        return self::getId("oil.lading.settlement.id.",4);
    }

    /**
     * 获取提单结算单编码
     * @return int|string
     */ 
    public static function getLadingSettlementCode()
    {
        return self::getId("oil.lading.settlement.code.",3);
    }

    /**
     * 获取发货单结算单编码
     * @return int|string
     */
    public static function getDeliverySettlementId()
    {
        return self::getId("oil.delivery.settlement.id.",4);
    }

    /**
     * 获取发货单结算单编码
     * @return int|string
     */ 
    public static function getDeliverySettlementCode()
    {
        return self::getId("oil.delivery.settlement.code.",3);
    }

    /**
     * 获取货款结算明细编号
     * @return int|string
     */ 
    public static function getGoodsExpenseSettlementId()
    {
        return self::getId("oil.goods.expense.settlement.id.",3);
    }

    public static function getGoodsSettlementId()
    {
        return self::getId("oil.goods.settlement.id.",3);
    }

    /**
     * 获取非货款结算明细编号
     * @return int|string
     */ 
    public static function getOtherExpenseSettlementId()
    {
        return self::getId("oil.other.expense.settlement.id.",3);
    }

    public static function getOtherSettlementId()
    {
        return self::getId("oil.other.settlement.id.",3);
    }

    /**
     * 获取指定Key的当天的日期及顺序号的组合ID
     * @param $key
     * @param int $len  顺序号的长度，默认为6
     * @param string $dateFormat    获取时间的格式化字符串
     * @param int $expire 过期时间
     * @param string $date 日期
     * @return int|string
     */
    public static function getId($key,$len=6,$dateFormat="Ymd",$expire=86400,$date = '')
    {
        if(empty($date)) {
            $date=date($dateFormat);
        }
        $keyName=$key.$date;

        $redis = Mod::app()->redis;

        $id=$redis->incr($keyName);
        if($id<1)
        {
            Mod::log("获取Key为：".$key."的id出错");
            return 0;
        }
        if($id==1)
        {
            $redis->expire($keyName, $expire);
        }

        $id="000000000000000000".$id;

        $id=substr($id,strlen($id)-$len);

        return $date.$id;
    }

    /**
     * @desc 获取自然序列号
     * @param string $key
     * @return string
     */
    public static function getSerialNum($key) {
        $redis = Mod::app()->redis;
        $serial = $redis->incr($key);
        if ($serial < 1) {
            Mod::log("获取Key为：" . $key . "的序列号出错", CLogger::LEVEL_ERROR);
            $serial = 0;
        }

        return $serial;
    }
}