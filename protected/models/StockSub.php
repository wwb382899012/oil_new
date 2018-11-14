<?php

/**
 * Created by youyi000.
 * DateTime: 2017/10/11 18:56
 * Describe：
 */
class StockSub extends BaseActiveRecord {

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 't_stock_sub';
    }
}