<?php
/**
 * Created by youyi000.
 * DateTime: 2017/12/5 10:55
 * Describe：
 */

class PartnerTypeRelation extends BaseActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "t_partner_type_relation";
    }

}