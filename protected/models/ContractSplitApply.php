<?php

/**
 * Desc: 合同拆分申请
 * User: susiehuang
 * Date: 2018/5/28 0028
 * Time: 15:39
 */

class ContractSplitApply extends BaseBusinessActiveRecord
{
    const STATUS_BACK = - 1; //驳回
    const STATUS_NEW = 0; //保存
    const STATUS_SUBMIT = 1; //提交待审核
    const STATUS_PASS = 10; //审核通过

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "t_contract_split_apply";
    }

    public function relations()
    {
        return array(
            'contract' => array(self::BELONGS_TO, 'Contract', 'contract_id'), //原合同
            'contractSplits' => array(self::HAS_MANY, 'ContractSplit', 'apply_id'), //合同拆分
            'stockSplits' => array(self::HAS_MANY, 'ContractStockSplit', 'apply_id'), //出入库拆分
            'files' => array(self::HAS_MANY, 'ContractSplitAttachment', array('base_id' => 'apply_id'),"on" => "files.status=1"),
            );
    }

    public function beforeSave() {
        if ($this->isNewRecord) {
            $this->create_time = new CDbExpression("now()");
            $this->create_user_id = Utility::getNowUserId();
        }
        $this->update_time = new CDbExpression("now()");
        $this->update_user_id = Utility::getNowUserId();

        return parent::beforeSave();
    }

}