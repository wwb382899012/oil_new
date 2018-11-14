<?php

/**
 * Desc: 合同出入库拆分
 * User: susiehuang
 * Date: 2018/5/28 0028
 * Time: 15:39
 */

class ContractStockSplit extends BaseBusinessActiveRecord{

    const TYPE_STOCK_IN = 1; //入库拆分
    const TYPE_STOCK_OUT = 2; //出库拆分

    /**
     * 未勾选平移
     */
    const STATUS_UN_SPLIT = 0;

    /**
     * 已勾选平移
     */
    const STATUS_SPLIT = 1;

    public static function model($className = __CLASS__){
        return parent::model($className);
    }

    public function tableName(){
        return "t_contract_stock_split";
    }

    public function relations(){
        return array('contractSplitApply' => array(self::BELONGS_TO, 'ContractSplitApply', 'apply_id'), //合同平移申请
            'splitDetails' => array(self::HAS_MANY, 'ContractStockSplitDetail', 'stock_split_id'), //出入库拆分明细
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