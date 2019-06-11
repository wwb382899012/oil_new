<?php
/**
 * Created by yu.li.
 * DateTime: 2018/6/7 15:30
 * Describe：
 */

class OriginalContractGoods extends BaseActiveRecord
{

    public static function model($className = __CLASS__) {
        return parent::model($className);
    }

    public function tableName() {
        return 't_original_contract_goods';
    }

    public function relations() {
        return array(
            "project" => array(self::BELONGS_TO, "Project", "project_id"),//项目信息
            "contract" => array(self::BELONGS_TO, "Contract", "contract_id"),
            "goods" => array(self::BELONGS_TO, "Goods", "goods_id"),
            "lockPriceDetail" => array(self::HAS_MANY, "LockPriceDetail", array('goods_id' => 'goods_id', 'contract_id' => 'contract_id')),
            "contractGoodsRollover" => array(self::HAS_MANY, "ContractGoodsRollover", array('goods_id' => 'goods_id', 'contract_id' => 'contract_id')),
        );
    }

    public function beforeSave() {
        if ($this->isNewRecord) {
            if (empty($this->create_time))
                $this->create_time = new CDbExpression("now()");
            if (empty($this->create_user_id))
                $this->create_user_id = Utility::getNowUserId();
        }
        if ($this->update_time == $this->getOldAttribute("update_time")) {
            $this->update_time = new CDbExpression("now()");
            $this->update_user_id = Utility::getNowUserId();
        }

        if ($this->type == ConstantMap::SALE_TYPE) {
            $this->unit_store = $this->unit;
            $this->unit_price = $this->unit;
        }
        return parent::beforeSave(); // TODO: Change the autogenerated stub
    }
}