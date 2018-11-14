<?php
/**
 * Created by youyi000.
 * DateTime: 2018/4/2 16:04
 * Describe：
 */

class ContractStat extends BaseActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "t_contract_stat";
    }
}