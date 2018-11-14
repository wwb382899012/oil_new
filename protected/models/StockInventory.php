<?php

/**
 * Desc: 库存盘点
 * User: susiehuang
 * Date: 2017/11/14 0009
 * Time: 16:33
 */
class StockInventory extends BaseBusinessActiveRecord {
    const STATUS_BACK = - 1;//审核驳回
    const STATUS_NEW = 0;//新增加
    const STATUS_SUBMIT = 10;//提交(待审核)
    const STATUS_PASS = 20;//审核通过

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 't_stock_inventory';
    }

    public function relations() {
        return array(
            "corporation" => array(self::BELONGS_TO, "Corporation", "corporation_id"),
            "store" => array(self::BELONGS_TO, "Storehouse", "store_id"),
            "stockInventoryGoodsDetail" => array(self::HAS_MANY, "StockInventoryGoodsDetail", "inventory_id"),
            "stockInventoryDetail" => array(self::HAS_MANY, "StockInventoryDetail", "inventory_id"),
            "create_user" => array(self::BELONGS_TO, "SystemUser", array('create_user_id' => 'user_id')),
        );
    }
}