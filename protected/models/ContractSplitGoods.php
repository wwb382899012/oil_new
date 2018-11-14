<?php

/**
 * Desc: 合同拆分商品明细
 * User: susiehuang
 * Date: 2018/5/28 0028
 * Time: 15:39
 */

class ContractSplitGoods extends BaseBusinessActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "t_contract_split_goods";
    }

    public function relations()
    {
        return array(
            'contractSplit' => array(self::BELONGS_TO, 'ContractSplit', 'split_id'), //合同拆分
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