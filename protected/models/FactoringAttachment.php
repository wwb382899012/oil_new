<?php

/**
 * Desc: 保理附件
 * User: susiehuang
 * Date: 2017/10/9 0009
 * Time: 10:03
 */
class FactoringAttachment extends BaseActiveRecord {
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 't_factoring_attachment';
    }
}