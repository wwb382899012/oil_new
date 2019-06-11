<?php

/**
 * Desc: 配货明细
 * User: susiehuang
 * Date: 2017/10/9 0009
 * Time: 10:03
 */
class StockDeliveryDetail extends BaseActiveRecord {
    const STATUS_NOT_USE = -1;//删除
    const STATUS_SAVE = 0;//保存
    const STATUS_SUBMIT = 1;//提交

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 't_stock_delivery_detail';
    }

    public function relations() {
        return array(
            "deliveryOrder" => array(self::BELONGS_TO, "DeliveryOrder", "order_id"),
            'deliveryOrderDetail' => array(self::BELONGS_TO, 'DeliveryOrderDetail', 'detail_id'),
            "project"  => array(self::BELONGS_TO, "Project", "project_id"),
            "contract" => array(self::BELONGS_TO, "Contract", "contract_id"),
            "goods" => array(self::BELONGS_TO, "Goods", "goods_id"),
            "stock" => array(self::BELONGS_TO, "Stock", "stock_id"),
            "crossStock" => array(self::BELONGS_TO, "CrossDetail", "cross_detail_id"),
            "store" => array(self::BELONGS_TO, "Storehouse", "store_id"),
            "create_user" => array(self::BELONGS_TO, "SystemUser", array('create_user_id' => 'user_id')), // 创建人
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
        foreach ($this->details as $model) {
            $res = $model->delete();
            if (!$res) {
                return false;
            }
        }

        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }


}