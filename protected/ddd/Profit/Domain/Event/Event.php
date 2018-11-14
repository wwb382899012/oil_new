<?php
/**
 * @Name            事件
 * @DateTime        2018年8月29日 10:50:10
 * @Author          Administrator
 */


namespace ddd\Profit\Domain\Event;

use ddd\Common\Domain\BaseEntity;
use ddd\Common\IAggregateRoot;


class Event extends BaseEntity implements IAggregateRoot
{
     #region property
    
    /**
     * 标识id 
     * @var   bigint
     */
    public $id;
    
    /**
     * 事件id 
     * @var   bigint
     */
    public $event_id;

    /**
     * 事件类型 
     * @var   int
     */
    public $event_type;
    
    /**
     * 事件名称 
     * @var   string
     */
    public $event_name;

    #endregion   
    
     public function getId()
    {
        // TODO: Implement getId() method.
        return $this->id;
    }

    public function setId($id)
    {
        // TODO: Implement setId() method.
        $this->id=$id;
    }
    
    
    /**
     * 创建工厂方法
     */
    public static function create($eventId, $type, $name)
    {
        $entity =  new static();
        $entity->event_id   = $eventId;
        $entity->event_type = $type;
        $entity->event_name = $name;

        return $entity;
    }
    
    
}

?>