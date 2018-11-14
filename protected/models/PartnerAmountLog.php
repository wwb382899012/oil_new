<?php

/**
 * Created by vector.
 * DateTime: 2017/08/31 18:30
 * Describe：
 */
class PartnerAmountLog extends BaseActiveRecord
{
    /**
     * 合同
     */
    const CATEGORY_CONTRACT_SUBMIT = 10;
    const CATEGORY_CONTRACT_REJECT = 11;
    const CATEGORY_CONTRACT_DONE = 19;
    /**
     * 收款
     */
    const CATEGORY_RECEIPTS = 21;
    /**
     * 付款
     */
    const CATEGORY_PAYMENT = 22;
    const CATEGORY_REFUND_IN = 23;
    const CATEGORY_REFUND_OUT = 24;
    const CATEGORY_PAY_CLAIM = 25;
    /**
     * 出入库
     */
    const CATEGORY_STOCK_IN = 31;
    const CATEGORY_STOCK_OUT = 32;
    const CATEGORY_STOCK_REFUND_IN = 33;
    const CATEGORY_STOCK_REFUND_OUT = 34;

    /**
     * 额度增减方式
     */
    const METHOD_ADD = 1; //增
    const METHOD_SUBTRACT = - 1; //减

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_partner_amount_log';
    }

    public function relations()
    {
        return array();
    }
}