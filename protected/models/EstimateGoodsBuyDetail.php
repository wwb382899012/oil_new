<?php

/**
 * Created by vector.
 * DateTime: 2018/8/28 15:40
 * Describe：
 */
class EstimateGoodsBuyDetail extends BaseBusinessActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_estimate_goods_buy_detail';
    }

    public function relations()
    {
        return array(
            "contractProfit" => array(self::BELONGS_TO, "EstimateContractProfit", "contract_id"),
        );
    }

}