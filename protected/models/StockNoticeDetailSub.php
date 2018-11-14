<?php
/**
 * Created by youyi000.
 * DateTime: 2017/10/11 20:10
 * Describe：
 */

class StockNoticeDetailSub extends BaseActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 't_stock_in_batch_detail_sub';
    }

    public function beforeSave(){
        $this->update_time = new CDbExpression("now()");
        $this->update_user_id = Utility::getNowUserId();

        return parent::beforeSave();
    }
}