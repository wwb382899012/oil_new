<?php
/**
 * Desc: 合同拆分申请附件
 * User: susiehuang
 * Date: 2018/5/28 0028
 * Time: 15:39
 */


class ContractSplitAttachment extends BaseBusinessActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_contract_split_attachment';
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