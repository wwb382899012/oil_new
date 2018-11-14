<?php

/**
 * Created by vector.
 * DateTime: 2018/8/28 15:40
 * Describe：
 */
class EstimateContractProfit extends BaseBusinessActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_estimate_contract_profit';
    }

    public function relations()
    {
        return array(
            "goodsItems" => array(self::HAS_MANY, "EstimateGoodsBuyDetail",array("contract_id"=>"contract_id")),
        );
    }

}