<?php

/**
 * Created by vector.
 * DateTime: 2017/10/24 17:31
 * Describe：
 */
class InvoiceApplication extends BaseActiveRecord
{
    const TYPE_BUY=1;//进项票
    const TYPE_SELL=2;//销项票

    const SUB_TYPE_GOODS=1;//货款类
    const SUB_TYPE_OTHER=2;//非货款类

    const STATUS_BACK = -1; //发票申请驳回
    const STATUS_SAVED = 1; //发票申请保存
    const STATUS_CHECKING = 2; //发票申请审核中
    const STATUS_PASS = 3; //发票申请审核通过

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_invoice_application';
    }

    public function relations()
    {
        return array(
            "contract" => array(self::BELONGS_TO, "Contract", "contract_id"),
            "project" => array(self::BELONGS_TO, "Project", "project_id"),
            "corporation" => array(self::BELONGS_TO, "Corporation", "corporation_id"),
            "user" => array(self::BELONGS_TO, "SystemUser", "user_id"),
            "applyDetail" => array(self::HAS_MANY, "InvoiceApplicationDetail", "apply_id"),
            "plan" => array(self::HAS_MANY, "InvoicePayPlan", "apply_id"),
        );
    }


}