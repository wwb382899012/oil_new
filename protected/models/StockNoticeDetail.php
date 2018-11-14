<?php

/**
 * Desc: 入库通知单明细
 * User: susiehuang
 * Date: 2017/10/9 0009
 * Time: 10:03
 */
class StockNoticeDetail extends BaseHasSubActiveRecord {

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
        return 't_stock_in_batch_detail';
    }

    public function relations() {
        return array(
            "project" => array(self::BELONGS_TO, "Project", "project_id"),//项目信息
            "contract" => array(self::BELONGS_TO, "Contract", "contract_id"),
            "batch" => array(self::BELONGS_TO, "StockNotice", "batch_id"),
            "sub" => array(self::HAS_ONE, "StockNoticeDetailSub", "detail_id"),
            "subs" => array(self::HAS_MANY, "StockNoticeDetailSub", "detail_id"),
            "store" => array(self::BELONGS_TO, "Storehouse", "store_id"),
            "goods" => array(self::BELONGS_TO, "Goods", "goods_id"),
            "lock" => array(self::HAS_ONE, "LockPrice", array("batch_id"=>"batch_id", "goods_id"=>"goods_id"), "on"=>"lock.batch_id>0"),
            "contractGoods" => array(self::HAS_ONE, "ContractGoods", array("contract_id"=>"contract_id", "goods_id"=>"goods_id")),
            "create_user"=>array(self::BELONGS_TO, "SystemUser",array('create_user_id'=>'user_id')), // 创建人
            "contractSettlementGoods" => array(self::HAS_ONE, "ContractSettlementGoods", array("relation_id"=>"batch_id", "goods_id"=>"goods_id","contract_id"=>"contract_id")),
        );
    }

    public function beforeSave()
    {
        if ($this->isNewRecord){
            $this->create_time = new CDbExpression("now()");
            $this->create_user_id= Utility::getNowUserId();
        }
        $this->update_time = new CDbExpression("now()");
        $this->update_user_id = Utility::getNowUserId();

        return parent::beforeSave();
    }

    protected function beforeDelete(){
        foreach ($this->subs as $model){
            if(!$model->delete())
                return false;
        }

        return parent::beforeDelete();
    }
}