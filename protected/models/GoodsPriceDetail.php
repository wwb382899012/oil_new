<?php

/**
 * Created by vector.
 * DateTime: 2018/8/28 15:40
 * Describe：
 */
class GoodsPriceDetail extends BaseActiveRecord
{

    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return 't_goods_price_detail';
    }

    public function relations()
    {
        return array(
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
        return parent::beforeSave();
    }

}