<?php
/**
 * Desc:
 * User:  vector
 * Date: 2018/4/18
 * Time: 17:15
 */


class DeliverySettlementAttachment extends BaseActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_delivery_settlement_attachment';
    }

}