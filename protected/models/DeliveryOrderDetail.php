<?php

/**
 * Desc: 发货单明细
 * User: susiehuang
 * Date: 2017/10/9 0009
 * Time: 10:03
 */
class DeliveryOrderDetail extends BaseActiveRecord {

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
        return 't_delivery_order_detail';
    }

    public function relations() {
        return array(
            "project"  => array(self::BELONGS_TO, "Project", "project_id"),//项目信息
            "contract" => array(self::BELONGS_TO, "Contract", "contract_id"), //合同信息
            "deliveryOrder" => array(self::BELONGS_TO, "DeliveryOrder", "order_id"), //发货单
            "goods" => array(self::BELONGS_TO, "Goods", "goods_id"), //商品信息
            "stockDeliveryDetail" => array(self::HAS_MANY, "StockDeliveryDetail", "detail_id"), //配货明细信息,建议弃用
            "stockDeliveryDetails" => [self::HAS_MANY, "StockDeliveryDetail", "detail_id"], //配货明细信息
            "stockOutDetail" => array(self::HAS_MANY, "StockOutDetail", "detail_id"), //出货明细信息
            "create_user" => array(self::BELONGS_TO, "SystemUser", array('create_user_id' => 'user_id')), // 创建人
            "contractGoods" => array(self::HAS_ONE, "ContractGoods", array("contract_id"=>"contract_id", "goods_id"=>"goods_id")),
            "stockDetail"=>array(self::HAS_MANY, "StockDeliveryDetail", 'detail_id')
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

    protected function beforeDelete() {
        foreach ($this->stockDeliveryDetails as & $model) {
            if (!$model->delete()) {
                return false;
            }
        }

        return parent::beforeDelete();
    }

}