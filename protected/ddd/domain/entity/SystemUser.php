<?php
/**
 * Created by youyi000.
 * DateTime: 2018/2/26 15:22
 * Describe：
 */

namespace ddd\domain\entity;


use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;

class SystemUser extends BaseEntity implements IAggregateRoot
{
    function getId()
    {
        // TODO: Implement getId() method.
        return $this->user_id;
    }

    function setId($value)
    {
        // TODO: Implement setId() method.
        $this->user_id=$value;
    }

    function getIdName()
    {
        // TODO: Implement getIdName() method.
        return "user_id";
    }

    public static function create()
    {
        return new SystemUser();
    }

    public function customAttributeNames()
    {
        return \SystemUser::model()->attributeNames();
    }

}