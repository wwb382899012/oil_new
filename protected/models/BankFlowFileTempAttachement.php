<?php

/**
 * 收款流水
 */
class BankFlowFileTempAttachement extends BaseActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_bank_flow_file_temp_attachment';
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