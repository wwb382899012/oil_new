<?php

/**
 * @author  vector
 * @date    2018-06-11
 * @desc    付款占用利息变动表    
 */
class PaymentInterestChange extends BaseActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_payment_interest_change';
    }


}