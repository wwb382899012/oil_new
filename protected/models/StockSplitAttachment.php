<?php

class StockSplitAttachment extends BaseActiveRecord{

    public static function model($className = __CLASS__){
        return parent::model($className);
    }

    public function tableName(){
        return 't_stock_split_attachment';
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