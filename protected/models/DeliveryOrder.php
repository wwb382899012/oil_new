<?php

/**
 * Desc: 发货单
 * User: susiehuang
 * Date: 2017/10/9 0009
 * Time: 10:03
 */
class DeliveryOrder extends BaseActiveRecord {

    const STATUS_TRASH = -3;//作废
    const STATUS_BACK = -1;//审核驳回
    const STATUS_NEW = 0;//新增加
    const STATUS_SUBMIT = 10;//提交(待审核)
    const STATUS_PASS = 20;//审核通过

    const STATUS_SETTLE_INVALIDITY = 25;//发货单结算作废
    const STATUS_SETTLE_REVOCATION = 26; //发货单结算撤回
    const STATUS_SETTLE_SUBMIT = 30; // 提交发货单结算
    const STATUS_SETTLE_BACK = 40; // 结算打回
    const STATUS_SETTLE_PASS = 50; // 结算审核通过

    const DELIVERY_ORDER_CODE_FIXED_STR = 'FH';
    public static $deliveryOrderCodeKey = 'delivery.order.code.serial';

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 't_delivery_order';
    }

    public function relations() {
        return array(
            'contract' => array(self::BELONGS_TO, "Contract", "contract_id"),//合同信息
            "corporation" => array(self::BELONGS_TO, "Corporation", "corporation_id"),
            'partner' => array(self::BELONGS_TO, 'Partner', 'partner_id'),
            "details" => array(self::HAS_MANY, "DeliveryOrderDetail", "order_id"),
            "contractSettlement" => array(self::HAS_ONE, "ContractSettlement",array('contract_id' => 'contract_id')),
            "settlementDetails" => array(self::HAS_MANY, "DeliverySettlementDetail", "order_id"),
            "stockIn" => array(self::BELONGS_TO, "StockIn", "stock_in_id"),
            "stockOuts" => array(self::HAS_MANY, "StockOutOrder", "order_id",'order'=> 'stockOuts.out_order_id desc'),
            "stockOutDetails" => array(self::HAS_MANY, "StockOutDetail", array('order_id' => 'order_id')), // 出库明细
            "stockDetails" => array(self::HAS_MANY, "StockDeliveryDetail", "order_id"), //配货明细信息,建议弃用
            "stockDeliveryDetails" => [self::HAS_MANY, "StockDeliveryDetail", "order_id"], //配货明细信息
            "create_user" => array(self::BELONGS_TO, "SystemUser", array('create_user_id' => 'user_id')), // 创建人
            'originalOrder' => [self::BELONGS_TO, "DeliveryOrder",['original_id'=>'order_id']], //拆分的源发货单
            "attachments"=>array(self::HAS_MANY, "DeliveryAttachment", array('base_id'=>'order_id'),"on" => "attachments.status=1 and attachments.type=".ConstantMap::STOCK_DELIVERY_ATTACH_TYPE),
            "outDetails"=>array(self::HAS_MANY, "GoodsOutQuantityDetail", array('bill_id'=>'order_id')),
            "priceDetails"=>array(self::HAS_MANY, "GoodsPriceDetail", array('bill_id'=>'order_id'), "on"=>"priceDetails.is_settled=1 and priceDetails.type=2"),
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
        foreach ($this->details as & $model) {
            $res = $model->delete();
            if (!$res) {
                return false;
            }
        }

        return parent::beforeDelete();
    }


}