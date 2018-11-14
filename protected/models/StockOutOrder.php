<?php
class StockOutOrder extends BaseHasSubActiveRecord {

    const STATUS_INVALIDITY = -5;//作废
    const STATUS_REVOCATION = -3; //撤回
    const STATUS_BACK = -1;//审核驳回
    const STATUS_SAVED = 0; //出库单保存
    const STATUS_SUBMIT = 10;//提交(待审核)
    const STATUS_SUBMITED = 1; //出库单审核通过
    const STATUS_SETTLED = 30; //已结算
    
    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 't_stock_out_order';
    }

    public function relations() {
        return array(
            "deliveryOrder" => array(self::BELONGS_TO, "DeliveryOrder", "order_id"),
            "goods" => array(self::BELONGS_TO, "Goods", "goods_id"),
            "details" => array(self::HAS_MANY, "StockOutDetail", 'out_order_id'),
            "store" => array(self::BELONGS_TO, "Storehouse", 'store_id'),
            "originalOrder" => [self::BELONGS_TO, "StockOutOrder", ['original_id'=>'out_order_id']],
            "attachments"=>array(self::HAS_MANY, "DeliveryAttachment", array('base_id'=>'out_order_id'),"on" => "attachments.status=1 and attachments.type=".ConstantMap::STOCK_OUT_ATTACH_TYPE),
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
            if (!$model->delete()) {
                return false;
            }
        }

        return parent::beforeDelete();
    }

    public function isCanEdit(){
        return $this->status<self::STATUS_SUBMITED;
    }

}