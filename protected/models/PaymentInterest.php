<?php

/**
 * @author  vector
 * @date    2018-06-11
 * @desc    付款占用利息表    
 */
class PaymentInterest extends BaseActiveRecord
{
    const STATUS_BACK   = -1; //驳回
    const STATUS_NEW    = 0; //新建
    const STATUS_SUBMIT = 1; //已提交(审核中)
    const STATUS_PASS   = 2; //审核通过
    const STATUS_DONE   = 3; //已停息

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_payment_interest';
    }

    public function relations()
    {
        return array(
            "change" => array(self::HAS_ONE, "PaymentInterestChange", "contract_id"),
            "detail" => array(self::HAS_MANY, "PaymentInterestDetail", "contract_id"),
        );
    }


}