<?php
/**
 * Created by vector.
 * DateTime: 2018/9/3 10:30
 * Describe：数量监听服务
 */

class QuantityCommand extends AMQPCommand
{
	/**
     * 需要监听的队列信息
     * @var array
     */
    protected $queueConfig = array(
        "new.oil.delivery.order.settled"=>array(
            "fn"=>"deliveryOrderSettled",
            "exchange"=>"new.oil.direct",
            "routingKey"=>"delivery.order.settlement.pass",
        ),
        "new.oil.sell.out.quantity"=>array(
            "fn"=>"sellOutQuantity",
            "exchange"=>"new.oil.direct",
            "routingKey"=>"sell.out.quantity",
        ),
    );

    public function init()
    {
        $this->sleepTime = 1;
        $this->isAutoAck=true;
        parent::init();
    }

    /**
     * @param $msg
     * @throws Exception
     */
    public function deliveryOrderSettled($msg)
    {
        $params=json_decode($msg,true);

        try
        {
            \ddd\Profit\Application\QuantityService::service()->onDeliveryOrderSettled($params["bill_id"]);
        }
        catch (Exception $e)
        {
            Mod::log("deliveryOrderSettled error: ".$e->getMessage(),"error");
        }
    }


    public function sellOutQuantity($msg)
    {
        $params=json_decode($msg,true);

        try
        {
            \ddd\Profit\Application\QuantityService::service()->onSellQuantityChange($params["bill_id"]);
        }
        catch (Exception $e)
        {
            Mod::log("buyContract error: ".$e->getMessage(),"error");
        }
    }

}