<?php

/**
 * Desc: 入库通知单
 * User: susiehuang
 * Date: 2017/10/9 0009
 * Time: 10:03
 */
class StockNotice extends BaseActiveRecord {
//    const STATUS_SETTLE_BACK = -1; // 打回入库结算
    const STATUS_NEW = 0;//新增加
    const STATUS_SUBMIT = 10;//提交

    const STATUS_SETTLE_INVALIDITY = 13;//提单结算作废
    const STATUS_SETTLE_REVOCATION = 14; //提单结算撤回
    const STATUS_SETTLE_BACK = 15; // 打回入库结算
    const STATUS_SETTLE_SUBMIT = 20; // 入库通知结算审核
    const STATUS_SETTLED = 30; // 入库通知已结算


    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 't_stock_in_batch';
    }

    public function relations() {
        return array(
            'contract' => array(self::BELONGS_TO, "Contract", "contract_id"),//合同信息
            'project' => array(self::BELONGS_TO, "Project", "project_id"),//项目信息
            "corporation" => array(self::BELONGS_TO, "Corporation", "corporation_id"),//交易主体
            'partner' => array(self::BELONGS_TO, 'Partner', 'partner_id'),//上游合作方
            "details" => array(self::HAS_MANY, "StockNoticeDetail", "batch_id"),//合同商品交易信息
            "create_user"=>array(self::BELONGS_TO, "SystemUser",array('create_user_id'=>'user_id')), // 创建人
            "stockIn"=>array(self::HAS_MANY, "StockIn", "batch_id"),
            "contractSettlement" => array(self::HAS_ONE, "ContractSettlement",array('contract_id' => 'contract_id')),
            "stockBatchSettlement"=>array(self::HAS_MANY, "StockBatchSettlement", "batch_id"),
            'originalOrder' => [self::BELONGS_TO, "StockNotice", ['original_id'=>'batch_id']], //拆分的源入库通知单
            "ladingSettlement"=>array(self::HAS_MANY, "LadingSettlement", array("lading_id"=>"batch_id")),
            "attachments"=>array(self::HAS_MANY, "StockBatchAttachment", array('base_id'=>'batch_id'),"on" => "attachments.status=1"),
            "priceDetails"=>array(self::HAS_MANY, "GoodsPriceDetail", array('bill_id'=>'batch_id'), "on"=>"priceDetails.is_settled=1 and priceDetails.type=1"),
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
        return parent::beforeSave(); // TODO: Change the autogenerated stub
    }

    protected function beforeDelete()
    {

        foreach ($this->details as $model)
        {
            $res=$model->delete();
            if(!$res)
                return false;
        }

        return parent::beforeDelete(); // TODO: Change the autogenerated stub
    }
}