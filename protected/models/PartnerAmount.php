<?php

/**
 * Created by vector.
 * DateTime: 2017/08/31 18:30
 * Describe：
 */
class PartnerAmount extends BaseActiveRecord
{
    const TYPE_CONTRACT = 1;
    const TYPE_USED = 2;
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_partner_amount';
    }

    public function relations()
    {
        return array(
            "partner" => array(self::BELONGS_TO, "Partner", "partner_id"),
        );
    }
}