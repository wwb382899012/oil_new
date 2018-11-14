<?php
/**
 * Created by youyi000.
 * DateTime: 2017/11/23 16:18
 * Describe：
 */

class GoodsPrice extends BaseBusinessActiveRecord
{


    public static function model($className = __CLASS__)
    {
        return parent::model($className);
    }

    public function tableName()
    {
        return "t_goods_price";
    }

    public function relations()
    {
        return array(
            "goods" => array(self::BELONGS_TO, "Goods", "goods_id"),
        );
    }
}