<?php

/**
 * Created by vector.
 * DateTime: 2017/10/24 17:31
 * Describe：
 */
class Invoice extends BaseActiveRecord
{
    const STATUS_BACK = -1; //开票驳回
    const STATUS_SAVED = 1; //开票保存
    const STATUS_CHECKING = 2; //开票审核中
    const STATUS_PASS = 3; //开票审核通过

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_invoice';
    }

    public function relations()
    {
        return array(
            "application" => array(self::BELONGS_TO, "InvoiceApplication", "apply_id"),
            "contract" => array(self::BELONGS_TO, "Contract", "contract_id"),
            "project" => array(self::BELONGS_TO, "Project", "project_id"),
            "corporation" => array(self::BELONGS_TO, "Corporation", "corporation_id"),
            "user" => array(self::BELONGS_TO, "SystemUser", "user_id"),
            "invoiceDetail" => array(self::HAS_MANY, "InvoiceDetail", "apply_id"),
        );
    }


}