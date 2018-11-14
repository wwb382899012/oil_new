<?php

/**
 * Created by youyi000.
 * DateTime: 2017/3/29 16:37
 * Describe：
 */
class PartnerCheckInfo extends BaseActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_partner_check_info';
    }


}