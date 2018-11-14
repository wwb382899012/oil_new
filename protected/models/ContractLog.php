<?php
/**
 * Created by youyi000.
 * DateTime: 2017/11/2 16:22
 * Describe：
 */

class ContractLog extends BaseActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "t_contract_log";
    }

}