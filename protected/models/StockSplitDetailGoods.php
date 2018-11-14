<?php

class StockSplitDetailGoods extends BaseActiveRecord{

    public static function model($className = __CLASS__){
        return parent::model($className);
    }

    public function tableName(){
        return 't_stock_split_detail_goods';
    }

    public function beforeSave(){
        if ($this->isNewRecord){
            if (empty($this->create_time))
                $this->create_time = new CDbExpression("now()");
            if (empty($this->create_user_id))
                $this->create_user_id= Utility::getNowUserId();
        }

        $this->update_time = new CDbExpression("now()");
        $this->update_user_id = Utility::getNowUserId();

        return parent::beforeSave();
    }
}