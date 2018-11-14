<?php

/**
 * Desc: 入库单明细
 * User: susiehuang
 * Date: 2017/10/9 0009
 * Time: 10:03
 */
class StockInDetail extends BaseHasSubActiveRecord {

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
        return 't_stock_in_detail';
    }

    public function relations() {
        return array(
            "project"  => array(self::BELONGS_TO, "Project", "project_id"),//项目信息
            "contract" => array(self::BELONGS_TO, "Contract", "contract_id"),
            "stockIn" => array(self::BELONGS_TO, "StockIn", "stock_in_id"),
            "store" => array(self::BELONGS_TO, "Storehouse", "store_id"),
            "goods" => array(self::BELONGS_TO, "Goods", "goods_id"),
            "stockNoticeDetail" => array(self::BELONGS_TO, "StockNoticeDetail", "detail_id"),
            "sub" => array(self::HAS_ONE, "StockInDetailSub", "stock_id"),
            "subs" => array(self::HAS_MANY, "StockInDetailSub", "stock_id"),
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
        foreach ($this->subs as $model) {
            if (!$model->delete()) {
                return false;
            }
        }
        return parent::beforeDelete();
    }
}