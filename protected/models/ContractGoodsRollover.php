<?php

/**
 * Created by vector.
 * DateTime: 2017/08/31 18:30
 * Describe：
 */
class ContractGoodsRollover extends BaseActiveRecord
{
    const STATUS_TRASH = -9;//转月作废
    const STATUS_SAVED = 1; //转月保存

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_contract_goods_rollover';
    }

    public function relations()
    {
        return array(
            "oldTarget" => array(self::BELONGS_TO, "ContractGoodsTarget", "old_target_id"),//旧计价标的
            "target" => array(self::BELONGS_TO, "ContractGoodsTarget", "target_id"),//新计价标的
            "goods" => array(self::HAS_ONE, "Goods", "goods_id"),//商品信息
            'lockPrice' => array(self::BELONGS_TO, "LockPrice", 'lock_id'),
        );
    }

    public function beforeSave()
    {
        if ($this->isNewRecord)
        {
            if (empty($this->create_time))
                $this->create_time = new CDbExpression("now()");
            if (empty($this->create_user_id))
                $this->create_user_id= Utility::getNowUserId();
        }
        if ($this->update_time == $this->getOldAttribute("update_time"))
        {
            $this->update_time = new CDbExpression("now()");
            $this->update_user_id = Utility::getNowUserId();
        }
        return parent::beforeSave(); // TODO: Change the autogenerated stub
    }

}