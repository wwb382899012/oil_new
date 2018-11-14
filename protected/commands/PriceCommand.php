<?php
/**
 * Created by vector.
 * DateTime: 2018/8/30 19:19
 * Describe：价格监听服务
 */

class PriceCommand extends AMQPCommand
{
    /**
     * 需要监听的队列信息
     * @var array
     */
    protected $queueConfig = array(
        "new.oil.buy.contract.business.check.pass"=>array(
            "fn"=>"buyContractPass",
            "exchange"=>"new.oil.direct",
            "routingKey"=>"buy.contract.business.check.pass",
        ),
        "new.oil.sell.contract.business.check.pass"=>array(
            "fn"=>"sellContractPass",
            "exchange"=>"new.oil.direct",
            "routingKey"=>"sell.contract.business.check.pass",
        ),
        "new.oil.lading.bill.settlement.pass"=>array(
            "fn"=>"ladingBillSettled",
            "exchange"=>"new.oil.direct",
            "routingKey"=>"lading.bill.settlement.pass",
        ),
        "new.oil.delivery.order.settlement.pass"=>array(
            "fn"=>"deliveryOrderSettled",
            "exchange"=>"new.oil.direct",
            "routingKey"=>"delivery.order.settlement.pass",
        ),

        "new.oil.sell.contract.price"=>array(
            "fn"=>"sellContractPrice",
            "exchange"=>"new.oil.direct",
            "routingKey"=>"sell.contract.price",
        ),

        "new.oil.buy.contract.price"=>array(
            "fn"=>"buyContractPrice",
            "exchange"=>"new.oil.direct",
            "routingKey"=>"buy.contract.price",
        ),

        "new.oil.sell.settled.price"=>array(
            "fn"=>"sellSettledPrice",
            "exchange"=>"new.oil.direct",
            "routingKey"=>"sell.settled.price",
        ),

        "new.oil.buy.settled.price"=>array(
            "fn"=>"buySettledPrice",
            "exchange"=>"new.oil.direct",
            "routingKey"=>"buy.settled.price",
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
    public function buyContractPass($msg)
    {
        $params=json_decode($msg,true);

        try
        {
            \ddd\Profit\Application\Price\BuyPriceService::service()->onBuyContractConfirmed($params["contract_id"]);
        }
        catch (Exception $e)
        {
            Mod::log("buyContract error: ".$e->getMessage(),"error");
        }
    }

    /**
     * @param $msg
     * @throws Exception
     */
    public function sellContractPass($msg)
    {
        $params=json_decode($msg,true);

        try
        {
            \ddd\Profit\Application\Price\SellPriceService::service()->onSellContractConfirmed($params['contract_id']);
        }
        catch (Exception $e)
        {
            Mod::log("sellContract error: ".$e->getMessage(),"error");
        }
    }

    /**
     * @param $msg
     * @throws Exception
     */
    public function ladingBillSettled($msg)
    {
        $params=json_decode($msg,true);

        try
        {
            \ddd\Profit\Application\Price\BuyPriceService::service()->onLadingSettledConfirmed($params['bill_id']);
        }
        catch (Exception $e)
        {
            Mod::log("ladingBillSettled error: ".$e->getMessage(),"error");
        }
    }

    public function deliveryOrderSettled($msg)
    {
        $params=json_decode($msg,true);

        try
        {
            \ddd\Profit\Application\Price\SellPriceService::service()->onDeliverySettledConfirmed($params['bill_id']);
        }
        catch (Exception $e)
        {
            Mod::log("deliveryOrderSettled error: ".$e->getMessage(),"error");
        }
    }


    /**
     * [sellContractPrice 销售合同单价]
     * @param
     * @param  [type] $msg [description]
     * @return [type]
     */
    public function sellContractPrice($msg)
    {
        $params=json_decode($msg,true);

        try
        {
            \ddd\Profit\Application\Price\SellPriceService::service()->onSellContractPriceChange($params['contract_id']);
        }
        catch (Exception $e)
        {
            Mod::log("sellContractPrice error: ".$e->getMessage(),"error");
        }
    }


    /**
     * [buyContractPrice 采购合同单价]
     * @param
     * @param  [type] $msg [description]
     * @return [type]
     */
    public function buyContractPrice($msg)
    {
        $params=json_decode($msg,true);

        try
        {
            \ddd\Profit\Application\Price\BuyPriceService::service()->onBuyContractPriceChange($params['contract_id']);
        }
        catch (Exception $e)
        {
            Mod::log("buyContractPrice error: ".$e->getMessage(),"error");
        }
    }

    /**
     * [sellSettledPrice 销售结算单价]
     * @param
     * @param  [type] $msg [description]
     * @return [type]
     */
    public function sellSettledPrice($msg)
    {
        $params=json_decode($msg,true);

        try
        {
            \ddd\Profit\Application\Price\SellPriceService::service()->onSellSettledPriceChange($params['bill_id']);
        }
        catch (Exception $e)
        {
            Mod::log("sellSettledPrice error: ".$e->getMessage(),"error");
        }
    }

    /**
     * [buySettledPrice 采购结算单价]
     * @param
     * @param  [type] $msg [description]
     * @return [type]
     */
    public function buySettledPrice($msg)
    {
        $params=json_decode($msg,true);

        try
        {
            \ddd\Profit\Application\Price\BuyPriceService::service()->onBuySettledPriceChange($params['bill_id']);
        }
        catch (Exception $e)
        {
            Mod::log("buySettledPrice error: ".$e->getMessage(),"error");
        }
    }

}