<?php
/**
 * Created by youyi000.
 * DateTime: 2017/10/11 18:56
 * Describe：
 */

class StockInDetailSub extends BaseActiveRecord
{

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 't_stock_in_detail_sub';
    }

    public function beforeSave() {
        $this->update_time = new CDbExpression("now()");
        $this->update_user_id = Utility::getNowUserId();

        return parent::beforeSave();
    }
}