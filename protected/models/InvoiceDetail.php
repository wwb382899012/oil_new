<?php

/**
 * Created by vector.
 * DateTime: 2017/10/24 17:31
 * Describe：
 */
class InvoiceDetail extends BaseActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_invoice_detail';
    }

}