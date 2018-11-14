<?php

/**
 * 付款流水认领
 */
class PayClaim extends BaseBusinessActiveRecord
{

    const STATUS_ABORTED=-1;//作废
    const STATUS_NEW=0;//新建
    const STATUS_SUBMITED=1;//已提交

    const STATUS_DONE=2; // 认领完成

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_pay_claim';
    }

    public function relations()
    {
        return array(
            "apply" => array(self::BELONGS_TO, "PayApplication", "apply_id"),
            "contract" => array(self::BELONGS_TO, "Contract", "contract_id"),
            "creator" => array(self::BELONGS_TO, "SystemUser", "create_user_id"),
            "project" => array(self::BELONGS_TO, "Project", "project_id"),
            "payClaimDetail" => array(self::HAS_MANY, "PayClaimDetail", "claim_id"),
        );
    }
}