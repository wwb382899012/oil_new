<?php
/**
 * Created by youyi000.
 * DateTime: 2018/2/26 15:22
 * Describe：
 */

namespace ddd\domain\entity;


use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;

class Attachment extends BaseEntity implements IAggregateRoot
{
    public $id;
    
    public $name;
    
    public $file_url;
    
    function getId()
    {
        // TODO: Implement getId() method.
        return $this->id;
    }

    function setId($value)
    {
        // TODO: Implement setId() method.
        $this->id=$value;
    }

    function getIdName()
    {
        // TODO: Implement getIdName() method.
        return "id";
    }

    public static function create()
    {
        return new Attachment();
    }

    /* public function customAttributeNames()
    {
        return \Goods::model()->attributeNames();
    } */

}