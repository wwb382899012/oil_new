<?php
/**
 * Created by youyi000.
 * DateTime: 2018/2/26 15:22
 * Describe：
 */

namespace ddd\domain\entity;


use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;

class Goods extends BaseEntity implements IAggregateRoot
{
    function getId()
    {
        // TODO: Implement getId() method.
        return $this->goods_id;
    }

    function setId($value)
    {
        // TODO: Implement setId() method.
        $this->goods_id=$value;
    }

    function getIdName()
    {
        // TODO: Implement getIdName() method.
        return "goods_id";
    }

    public static function create()
    {
        return new Goods();
    }

    public function customAttributeNames()
    {
        return \Goods::model()->attributeNames();
    }

}