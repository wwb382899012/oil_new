<?php
/**
 * Created by youyi000.
 * DateTime: 2018/2/5 18:16
 * Describe：
 */

namespace ddd\Common;


use ddd\Common\Domain\IEntity;

interface IAggregateRoot extends IEntity
{
    public function getId();

    public function setId($value);
}