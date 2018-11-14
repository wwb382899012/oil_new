<?php

/**
 * @author  vector
 * @date    2018-06-11
 * @desc    付款占用利息明细表    
 */
class PaymentInterestDetail extends BaseActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_payment_interest_detail';
    }

    public function relations()
    {
        return array(
            "interest" => array(self::BELONGS_TO, "PaymentInterest", "contract_id"),
        );
    }


}