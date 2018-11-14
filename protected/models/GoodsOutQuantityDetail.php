<?php

/**
 * Created by vector.
 * DateTime: 2018/8/28 15:40
 * Describe：
 */
class GoodsOutQuantityDetail extends BaseActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_goods_out_quantity_detail';
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
        return parent::beforeSave();
    }

}