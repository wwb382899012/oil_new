<?php

/**
 * Created by vector.
 * DateTime: 2018/8/28 15:40
 * Describe：
 */
class EstimateProjectProfit extends BaseBusinessActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_estimate_project_profit';
    }

    public function relations()
    {
        return array(
            "contractProfit" => array(self::HAS_MANY, "EstimateContractProfit", "project_id"),
        );
    }

}