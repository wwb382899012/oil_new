<?php
/**
 * Created by vector.
 * DateTime: 2018/6/27 18:37
 * Describe：
 */

class BlockCommand extends AMQPCommand
{
    /**
     * 需要监听的队列信息
     * @var array
     */
    protected $queueConfig = array(
        "new.oil.contract.block"=>array(
            "fn"=>"contractBlock",
            "exchange"=>"new.oil.direct",
            "routingKey"=>"contract.block",
        ),
    );

    public function init()
    {
        $this->sleepTime = 10;
        $this->isAutoAck =true;
        parent::init();
    }

    /**
     * @param $msg
     * @throws Exception
     */
    public function contractBlock($msg)
    {
        $params=json_decode($msg,true); 
        BlockChainService::contractBlock($params["contract_id"]);
    }

}