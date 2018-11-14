<?php

/**
 * Created by vector.
 * DateTime: 2018/8/28 15:40
 * Describe：
 */
class EstimateCorporationProfit extends BaseActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_estimate_corporation_profit';
    }

    public function relations()
    {
        return array(
            "projectProfit" => array(self::HAS_MANY, "EstimateProjectProfit", "corporation_id"),
        );
    }

}