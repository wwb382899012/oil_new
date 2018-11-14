<?php

/**
 * Created by youyi000.
 * DateTime: 2017/1/19 10:49
 * Describe：
 */
class EmailCommand extends AMQPCommand
{
    /**
     * 需要监听的队列信息
     * @var array
     */
    protected $queueConfig = array(
        "new.oil.email"=>array(
            "fn"=>"sendEmail",
            "exchange"=>"new.oil.direct",
            "routingKey"=>"email",
        ),
    );

    public function init()
    {
        $this->sleepTime = 10;
        $this->isAutoAck=true;
        parent::init();
    }

    /**
     * @param $msg
     * @throws Exception
     */
    public function sendEmail($msg)
    {
        $params=json_decode($msg,true);
        $res=Email::sendToUser($params["userId"],$params["subject"],$params["content"],$params["attach"]);
        if(!$res)
            Mod::log("邮件发送失败，参数：".$msg,"error");
    }

}