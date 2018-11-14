<?php

/**
 * Desc: 合同出入库拆分明细商品信息
 * User: susiehuang
 * Date: 2018/5/28 0028
 * Time: 15:39
 */

class ContractStockSplitGoods extends BaseBusinessActiveRecord
{
    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "t_contract_stock_split_goods";
    }

    public function relations()
    {
        return array(
            'stockSplit' => array(self::BELONGS_TO, 'ContractStockSplitDetail', 'split_detail_id'),
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