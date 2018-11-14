<?php

/**
 * 收款流水
 */
class BankFlowTemp extends BaseActiveRecord
{

    const STATUS_ABORTED=-1;//作废
    const STATUS_NEW=0;//待审核
    const STATUS_SUBMITED=1;//已审核

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_bank_flow_temp';
    }

    public function relations()
    {
        return array(
            "account" => array(self::BELONGS_TO, "Account", "account"),
            "corporation" => array(self::BELONGS_TO, "Corporation", "corporation_id"),
            "partner" => array(self::BELONGS_TO, "Partner", "pay_partner"),
        );
    }
}