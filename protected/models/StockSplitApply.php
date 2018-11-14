<?php

class StockSplitApply extends BaseActiveRecord{

    public static function model($className = __CLASS__){
        return parent::model($className);
    }

    public function tableName(){
        return 't_stock_split_apply';
    }

    public function relations() {
        return array(
            "details" => array(self::HAS_MANY, "StockSplitApplyDetail", array('bill_id'=>'bill_id')),
            'contract' => array(self::HAS_ONE, "Contract", array('contract_id'=>'contract_id')),
            'stockIn' => [self::BELONGS_TO,'StockIn',['bill_id'=>'stock_in_id']],
            'stockOut' => [self::BELONGS_TO,'StockOutOrder',['bill_id'=>'out_order_id']],
            "files"=>array(self::HAS_MANY, "StockSplitAttachment", array('base_id'=>'apply_id'),"on" => "files.status=1"),
        );
    }

    public function beforeSave(){
        if ($this->isNewRecord){
            $this->create_time = new CDbExpression("now()");
            $this->create_user_id= Utility::getNowUserId();
        }
        $this->update_time = new CDbExpression("now()");
        $this->update_user_id = Utility::getNowUserId();
        return parent::beforeSave();
    }
}