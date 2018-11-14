<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/6 15:06
 * Describe：
 */

namespace ddd\domain\event;


use ddd\Common\Domain\BaseEvent;

class DomainEvents
{

    private static $_instance;

    public $exchange="oil.ddd.direct";

    /**
     * 重试次数，主要是解决由于网络等特殊情况失败时自动重试
     * @var int
     */
    public $retryCount = 0;
    /**
     * @var float 重试间隔，单位是秒，默认为0.5秒(500毫秒)。
     */
    public $retryInterval = 0.5;

    /**
     * 获取实例的静态方法
     * @return DomainEvents
     */
    public static function Instance()
    {
        if(self::$_instance==null)
            self::$_instance=new DomainEvents();
        return self::$_instance;
    }


    /**
     * 发布消息
     * @param BaseEvent $event
     * @return bool
     */
    public function publish(BaseEvent $event)
    {
        $message=$event->serialize();
        if(is_array($message))
            $message=json_encode($message);
        $routeKey=$this->getRouteKey($event);
        for($retryCount=0; $retryCount<=$this->retryCount; $retryCount++)
        {
            $res=\AMQPService::publish($this->exchange, $routeKey, $message);
            if($res)
                return true;

            //判断是否需要sleep
            if($this->retryCount && $this->retryInterval && $retryCount<$this->retryCount)
                usleep($this->retryInterval);
        }
        return false;
    }

    /**
     * 获取AMQP消息的routeKey
     * @param BaseEvent $event
     * @return string
     */
    public function getRouteKey(BaseEvent $event)
    {
        //str_replace('\\','.',$event->eventName)
        return str_replace('\\','.',$event->eventName);
    }
}