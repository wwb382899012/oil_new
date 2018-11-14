<?php

/**
 * Created by vector.
 * DateTime: 2018/8/28 15:40
 * Describe：
 */
class Event extends BaseActiveRecord
{
    const ESTIMATE_CONTRACT_PROFIT_EVENT = 1;//预估合同利润变更事件
    const EstimateContractProfitEvent    = "EstimateContractProfit";//预估合同利润变更事件名
    const Sell_OUT_QUANTITY_EVENT        = 2;//销售结算出库数量事件
    const SellOutQuantityEvent           = "SellOutQuantity";//销售结算出库数量事件名
    const Lading_BILL_SETTLEMENT_EVENT = 3; //入库通知单结算事件
    const LadingBillSettlementEvent = "LadingBillSettlement";
    const DELIVERY_ORDER_SETTLEMENT_EVENT = 4; //发货单结算事件
    const DeliveryOrderSettlementEvent = "DeliveryOrderSettlement";
    const SELL_CONTRACT_PRICE_EVENT=5;//销售合同单价变更事件
    const SellContractPriceEvent='SellContractPrice';
    const BUY_CONTRACT_PRICE_EVENT=6;//采购合同单价变更事件
    const BuyContractPriceEvent='BuyContractPrice';
    const SELL_SETTLED_PRICE_EVENT=7;//销售结算单价变更事件
    const SellSettledPriceEvent='SellSettledPrice';
    const BUY_SETTLED_PRICE_EVENT=8;//采购结算单价变更事件
    const BuySettledPriceEvent='BuySettledPrice';
    const BUY_CONTRACT_BUSINESS_CHECK_PASS_EVENT=9;//采购合同业务审核通过事件
    const BuyContractBusinessCheckPassEvent='BuyContractBusinessCheckPass';
    const SELL_CONTRACT_BUSINESS_CHECK_PASS_EVENT=10;//销售合同业务审核通过事件
    const SellContractBusinessCheckPassEvent='SellContractBusinessCheckPass';
    const ESTIMATE_CONTRACT_PROFIT_EVENT_BY_PRICE = 11;//通过单价变更预估合同利润事件
    const EstimateContractProfitEventByPrice    = "EstimateContractProfitByPrice";
    const ESTIMATE_CONTRACT_PROFIT_EVENT_BY_QUANTITY = 12;//通过销售数量变更预估合同利润事件
    const EstimateContractProfitEventByQuantity    = "EstimateContractProfitByQuantity";
    const ESTIMATE_CONTRACT_PROFIT_EVENT_BY_SELL_PRICE = 13;//通过单价变更预估合同利润事件  解决baitch_id和order_id值相同的情况下，冲突
    const EstimateContractProfitEventBySellPrice    = "EstimateContractProfitBySellPrice";

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_event';
    }

    public function relations()
    {
        return array(
        );
    }

    public function beforeSave()
    {
        if ($this->isNewRecord)
        {
            if (empty($this->create_time))
                $this->create_time = new CDbExpression("now()");
            if (empty($this->create_user_id))
                $this->create_user_id= Utility::getNowUserId();
        }
        return parent::beforeSave();
    }

}