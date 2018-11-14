<?php

/*
 * Created By: yu.li
 * DateTime:2018-5-29 17:00:56.
 * Desc:合同终止
 */

class ContractTerminate extends BaseBusinessActiveRecord
{

    const STATUS_BACK = -1; //驳回
    const STATUS_NEW = 0; //保存
    const STATUS_SUBMIT = 1; //提交待审核
    const STATUS_PASS = 10; //审核通过

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "t_contract_terminate";
    }


    public function relations() {
        return [
            "contract" => array(self::BELONGS_TO, "Contract", 'contract_id'),
            "terminateAttachments" => array(self::HAS_MANY, "ContractTerminateAttachment", array("base_id" => "id"), "on" => "terminateAttachments.status=1"),
        ];
    }

}
