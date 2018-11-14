<?php
/**
 * Created by youyi000.
 * DateTime: 2018/2/26 14:49
 * Describe：
 */

namespace ddd\domain\entity;


use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;

class Partner extends BaseEntity implements IAggregateRoot
{
    function getId()
    {
        // TODO: Implement getId() method.
        return $this->partner_id;
    }

    function setId($value)
    {
        // TODO: Implement setId() method.
        $this->partner_id=$value;
    }

    function getIdName()
    {
        // TODO: Implement getIdName() method.
        return "partner_id";
    }

    public static function create()
    {
        return new Partner();
    }

    public function customAttributeNames()
    {
        return \Partner::model()->attributeNames();
    }
}