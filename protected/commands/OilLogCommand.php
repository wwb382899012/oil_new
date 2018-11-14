<?php

/**
 * Created by youyi000.
 * DateTime: 2017/3/24 16:25
 * Describe：
 */
class OilLogCommand extends AMQPCommand
{
    /**
     * 需要监听的队列信息
     * @var array
     */
    protected $queueConfig = array(
        "new.oil.action.log"=>array(
            "fn"=>"addActionLog",
            "exchange"=>"new.oil.direct",
            "routingKey"=>"action.log",
        ),
    );

    public function init()
    {
        $this->sleepTime =10;
        $this->isAutoAck=true;
        parent::init();
    }

    /**
     * @param $msg
     * @throws Exception
     */
    public function addActionLog($msg)
    {
        $params=json_decode($msg,true);
        $model=new ActionLog();
        $model->setAttributes($params,false);
        $res=$model->save();
        if(!$res)
            Mod::log("Save action log error, the data is:".$msg,"error");
    }

}