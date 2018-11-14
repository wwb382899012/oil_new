<?php
/**
 * Created by youyi000.
 * DateTime: 2018/2/5 18:01
 * Describe：
 */

namespace ddd\domain\entity\factoring;


use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;

class Factoring extends BaseEntity implements IAggregateRoot
{
    function getId()
    {
        // TODO: Implement getId() method.
    }

    function setId($value)
    {
    }

    function getIdName()
    {
        // TODO: Implement getIdName() method.
    }


    public static function create()
    {
        return new Factoring();
    }
}