<?php

/**
 * Desc: 实际出库明细信息
 * User: susiehuang
 * Date: 2017/10/9 0009
 * Time: 10:03
 */
class StockOutDetail extends BaseHasSubActiveRecord {

    /**
     * 删除
     */
    const STATUS_NOT_USE = -1;

    /**
     * 保存
     */
    const STATUS_SAVE = 0;

    /**
     * 明细有效，并提交审核
     */
    const STATUS_SUBMIT = 1;

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 't_stock_out_detail';
    }

    public function relations() {
        return array(
            "project"  => array(self::BELONGS_TO, "Project", "project_id"),//项目信息
            "contract" => array(self::BELONGS_TO, "Contract", "contract_id"),
            "deliveryOrder" => array(self::BELONGS_TO, "DeliveryOrder", "order_id"),
            "goods" => array(self::BELONGS_TO, "Goods", "goods_id"),
            "create_user" => array(self::BELONGS_TO, "SystemUser", array('create_user_id' => 'user_id')), // 创建人
            "stockDetail"=>array(self::BELONGS_TO, "StockDeliveryDetail", 'detail_id'),
            "stockDeliveryDetail" => array(self::BELONGS_TO, "StockDeliveryDetail", 'stock_detail_id'),
            "stock" => array(self::BELONGS_TO, "Stock", 'stock_id'),
            "store" => array(self::BELONGS_TO, "Storehouse", 'store_id'),
            'contract' => array(self::BELONGS_TO, 'Contract', 'contract_id'),
            "contractGoods" => array(self::HAS_ONE, "ContractGoods", array("contract_id"=>"contract_id", "goods_id"=>"goods_id")),
            "stockOutOrder"=>array(self::BELONGS_TO, "StockOutOrder", 'out_order_id')
        );
    }

    public function beforeSave() {
        if ($this->isNewRecord) {
            $this->create_time = new CDbExpression("now()");
            $this->create_user_id = Utility::getNowUserId();
        }
        $this->update_time = new CDbExpression("now()");
        $this->update_user_id = Utility::getNowUserId();

        return parent::beforeSave();
    }


}