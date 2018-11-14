<?php

/**
 * Desc: 库存盘点明细
 * User: susiehuang
 * Date: 2017/11/14 0009
 * Time: 16:33
 */
class StockInventoryDetail extends BaseBusinessActiveRecord {
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 't_stock_inventory_detail';
    }

    public function relations() {
        return array(
            "stockInventory" => array(self::BELONGS_TO, "StockInventory", 'inventory_id'),
            "stockInventoryGoodsDetail" => array(self::BELONGS_TO, "StockInventoryGoodsDetail", 'goods_detail_id'),
            "corporation" => array(self::BELONGS_TO, "Corporation", "corporation_id"),
            "stockIn" => array(self::BELONGS_TO, "StockIn", "stock_in_id"),
            "store" => array(self::BELONGS_TO, "Storehouse", "store_id"),
            "goods" => array(self::BELONGS_TO, "Goods", 'goods_id'),
            "create_user" => array(self::BELONGS_TO, "SystemUser", array('create_user_id' => 'user_id')),
        );
    }
}