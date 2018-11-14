<?php
/**
 * Created by youyi000.
 * DateTime: 2018/2/26 14:47
 * Describe：
 *  公司主体
 */

namespace ddd\domain\entity;


use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;

class Corporation extends BaseEntity implements IAggregateRoot
{
    function getId()
    {
        // TODO: Implement getId() method.
        return $this->corporation_id;
    }

    function setId($value)
    {
        // TODO: Implement setId() method.
        $this->corporation_id=$value;
    }

    function getIdName()
    {
        // TODO: Implement getIdName() method.
        return "corporation_id";
    }

    public static function create()
    {
        return new Corporation();
    }

    public function customAttributeNames()
    {
        return \Corporation::model()->attributeNames();
    }
}