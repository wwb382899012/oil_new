<?php

/**
 * Desc: 合同拆分
 * User: susiehuang
 * Date: 2018/5/28 0028
 * Time: 15:39
 */

class ContractSplit extends BaseBusinessActiveRecord{
    public static function model($className = __CLASS__){
        return parent::model($className);
    }

    public function tableName(){
        return "t_contract_split";
    }

    public function relations(){
        return array('contractSplitApply' => array(self::BELONGS_TO, 'ContractSplitApply', 'apply_id'), //合同拆分申请
            'goodsItems' => array(self::HAS_MANY, 'ContractSplitGoods', 'split_id'), //合同拆分商品明细
            'stockSplitDetails' => array(self::HAS_MANY, 'ContractStockSplitDetail', 'split_id'), //出入库拆分明细
            'contract' => array(self::BELONGS_TO, 'Contract', 'contract_id'), //合同拆分明细对应的旧合同
            'new_contract' => array(self::BELONGS_TO, 'Contract', 'new_contract_id'), //合同拆分明细对应的新合同
            'partner' => array(self::BELONGS_TO, 'Partner', 'partner_id'), //合同拆分明细对应的新合同的合作方
        );
    }

    public function beforeSave(){
        if($this->isNewRecord){
            $this->create_time = new CDbExpression("now()");
            $this->create_user_id = Utility::getNowUserId();
        }
        $this->update_time = new CDbExpression("now()");
        $this->update_user_id = Utility::getNowUserId();

        return parent::beforeSave();
    }
}