<?php

/**
 * Created by youyi000.
 * DateTime: 2016/11/7 15:51
 * Describe：企业资料库
 */
class PartnerInfo extends BaseActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_partner_info';
    }

}