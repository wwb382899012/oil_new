<?php

/**
 * Desc: 入库单
 * User: susiehuang
 * Date: 2017/10/9 0009
 * Time: 10:03
 */
class StockIn extends BaseActiveRecord {

    const STATUS_INVALIDITY = -5;//作废
    const STATUS_REVOCATION = -3; //撤回
    const STATUS_BACK = -1;//审核驳回
    const STATUS_NEW = 0;//新增加
    const STATUS_SUBMIT = 10;//提交(待审核)
    const STATUS_PASS = 20;//审核通过
    const STATUS_SETTLED = 30; //已结算

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 't_stock_in';
    }

    public function relations() {
        return array(
            "project"  => array(self::BELONGS_TO, "Project", "project_id"),//项目信息
            "contract" => array(self::BELONGS_TO, "Contract", "contract_id"),
            "notice" => array(self::BELONGS_TO, "StockNotice", "batch_id"),
            "details" => array(self::HAS_MANY, "StockInDetail", "stock_in_id"),
            "stocks" => array(self::HAS_MANY, "Stock", "stock_in_id"),
            "store" => array(self::BELONGS_TO, "Storehouse", "store_id"),
            "originalOrder" => [self::BELONGS_TO, "StockIn", ['original_id'=>'stock_in_id']],
            "create_user" => array(self::BELONGS_TO, "SystemUser", array('create_user_id' => 'user_id')), // 创建人
            "files" => array(self::HAS_MANY, "StockInAttachment", array("base_id" => "stock_in_id"), "on" => "files.status=1"),
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

        return parent::beforeDelete();
    }


}