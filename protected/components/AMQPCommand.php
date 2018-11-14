<?php

/**
 * Created by youyi000.
 * DateTime: 2017/1/19 10:48
 * Describe：
 */
class AMQPCommand extends CTaskDaemonCommand
{

    /**
     * 需要监听的队列信息
     * @var array
     */
    protected $queueConfig = array(

    );

    protected $queues=array();

    protected $isAutoAck=false;

    protected function getQueue($name)
    {
        $amqp=Mod::app()->amqp;
        if(empty($this->queues[$name]))
        {
            $queue=$amqp->bind($name,$this->queueConfig[$name]["exchange"],$this->queueConfig[$name]["routingKey"]);
            $this->queues[$name]=$queue;
        }
        return $this->queues[$name];
    }

    public function runTask($args=null)
    {
        // 遍历所有需要监听的渠道
        foreach ($this->queueConfig as $name => $v)
        {
            if(empty($v["fn"]) || empty($v["exchange"]) || empty($v["routingKey"]))
                continue;

            $queue=$this->getQueue($name);

            for($i=0;$i<5;$i++)
            {
                if($this->isAutoAck)
                    $data=$queue->get(AMQP_AUTOACK);
                else
                    $data=$queue->get();
                if(empty($data))
                    break;

                try{
                    Mod::log("AMQP channel is: ".$name.", and the parameters are: ".$data->getBody());
                    //执行消息处理函数
                    $fn=$v["fn"];
                    $this->$fn($data->getBody());

                    if(!$this->isAutoAck)
                        $queue->ack($data->getDeliveryTag());
                }
                catch(Exception $e)
                {
                    Mod::log("AMQP [".$name."] Error :".$e->getMessage().", message : ".$data->getBody(),"error");
                }
            }
        }
    }


}