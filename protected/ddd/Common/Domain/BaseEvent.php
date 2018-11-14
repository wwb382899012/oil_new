<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/6 14:45
 * Describe：
 */

namespace ddd\Common\Domain;


abstract class BaseEvent extends \CEvent
{
    /**
     * 事件Key
     * @var
     */
    public $eventKey;

    /**
     * 事件名称
     * @var string
     */
    public $eventName;
    /**
     * 事件时间
     * @var string
     */
    public $eventTime;

    /**
     * Returns the fully qualified name of this class.
     * @return string the fully qualified name of this class.
     */
    public static function className()
    {
        return get_called_class();
    }

    public function __construct($sender=null,$params=null)
    {
        parent::__construct($sender,$params);
        $this->eventKey=static::className();
        $this->eventTime=date('Y-m-d H:i:s');
        $this->init();
        $this->initEventName();
    }

    public function init()
    {

    }

    /**
     * 初始化事件名称，如果事件名称为空时，取类名
     */
    public function initEventName()
    {
        if(empty($this->eventName))
            $this->eventName=get_class($this);
    }


    /**
     * 获取事件消息体
     * @return mixed
     */
    /*public function getEventBody()
    {
        $data=array("event_name"=>$this->eventName,
                    "event_time"=>$this->eventTime,
                    "source"=>$this->source->getAttributes());

        return $data;

    }*/

    /**
     * 把当前对象序列化
     * @return array
     */
    public function serialize()
    {
        $data=array(
            "class"=>get_class($this),
            "eventName"=>$this->eventName,
            "eventTime"=>$this->eventTime,
            "senderId"=>$this->sender->getId(),
            "sender"=>$this->sender->getAttributes());

        return $data;
    }

    /**
     * 子类需要重写该方法，由序列化的数组恢复原事件对象
     * @param $eventSerializedData
     */
    public function unSerialize($eventSerializedData)
    {
        $this->eventName=$eventSerializedData["eventName"];
        $this->eventTime=$eventSerializedData["eventTime"];
        $this->sender=$eventSerializedData["sender"];
    }

    /**
     * @param $eventSerializedData
     * @throws \Exception
     */
    public static function crateEvent($eventSerializedData)
    {
        if(empty($eventSerializedData["class"]))
            throw new \Exception("");
        $event=new $eventSerializedData["class"];
        $event->unSerialize($eventSerializedData);
    }


}