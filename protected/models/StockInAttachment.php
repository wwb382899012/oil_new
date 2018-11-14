<?php

/**
 * Desc: 入库单附件
 * User: susiehuang
 * Date: 2017/10/9 0009
 * Time: 10:03
 */
class StockInAttachment extends BaseBusinessActiveRecord {
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 't_stock_in_attachment';
    }

    public function relations() {
        return array(

        );
    }
}