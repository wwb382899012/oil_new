<?php

/**
 * Created by youyi000.
 * DateTime: 2017/4/14 9:58
 * Describe：
 */
class PartnerFieldLog extends BaseActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_partner_field_log';
    }


}