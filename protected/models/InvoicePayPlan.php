<?php

/**
 * Created by vector.
 * DateTime: 2017/10/24 17:31
 * Describe：
 */
class InvoicePayPlan extends BaseActiveRecord
{


    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_invoice_pay_plan';
    }

    public function relations()
    {
        return array(
            "user" => array(self::BELONGS_TO, "SystemUser", "user_id"),
        );
    }


}