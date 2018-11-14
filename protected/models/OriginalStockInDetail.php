<?php

/**
 * Desc: 入库单明细备份
 */
class OriginalStockInDetail extends BaseHasSubActiveRecord{

    public static function model($className = __CLASS__){
        return parent::model($className);
    }

    public function tableName(){
        return 't_original_stock_in_detail';
    }

    public function relations(){
        return [];
    }

    public function beforeSave(){
        if($this->isNewRecord){
            if(empty($this->create_time)){
                $this->create_time = new CDbExpression("now()");
            }
            if(empty($this->create_user_id)){
                $this->create_user_id = Utility::getNowUserId();
            }
        }
        if($this->update_time == $this->getOldAttribute("update_time")){
            $this->update_time = new CDbExpression("now()");
            $this->update_user_id = Utility::getNowUserId();
        }

        return parent::beforeSave();
    }

}