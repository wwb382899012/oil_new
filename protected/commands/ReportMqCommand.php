<?php

/**
 * Created by youyi000.
 * DateTime: 2017/3/24 16:25
 * Describe：
 */
class ReportMqCommand extends AMQPCommand
{
    /**
     * 需要监听的队列信息
     * @var array
     */
    protected $queueConfig = array(
        "new.oil.delivery.profit"=>array(
            "fn"=>"addContractProfit",
            "exchange"=>"new.oil.direct",
            "routingKey"=>"delivery.profit",
        ),
        "new.oil.buy.goods.cost"=>array(
            "fn"=>"addBuyGoodsCost",
            "exchange"=>"new.oil.direct",
            "routingKey"=>"buy.goods.cost",
        ),
        "new.oil.profit"=>array(
            "fn"=>"addProfit",
            "exchange"=>"new.oil.direct",
            "routingKey"=>"profit",
        ),
        "new.oil.add.estimate.contract.profit"=>array(
            "fn"=>"addEstimateContractProfit",
            "exchange"=>"new.oil.direct",
            "routingKey"=>"buy.contract.business.check.pass",
        ),
        "new.oil.add.estimate.project.profit"=>array(
            "fn"=>"addEstimateProjectProfit",
            "exchange"=>"new.oil.direct",
            "routingKey"=>"estimate.contract.profit",
        ),
        "new.oil.add.estimate.corporation.profit"=>array(
            "fn"=>"addEstimateCorporationProfit",
            "exchange"=>"new.oil.direct",
            "routingKey"=>"estimate.project.profit",
        ),
        "new.oil.estimate.profit.receive.confirm"=>array(
            "fn"=>"addEstimateProjectProfit",
            "exchange"=>"new.oil.direct",
            "routingKey"=>"receive.confirm",
        ),
        "new.oil.estimate.profit.pay.confirm"=>array(
            "fn"=>"addEstimateProjectProfitTwo",
            "exchange"=>"new.oil.direct",
            "routingKey"=>"pay.confirm",
        ),
    );

    public function init()
    {
        $this->sleepTime =10;
        $this->isAutoAck=true;
        parent::init();
    }

    /**
     * @name:addContractProfit
     * @desc: 生成合同、项目、交易主体利润
     * @param:* @param $msg
     * @throw:
     * @return:void
     */
    public function addContractProfit($msg)
    {
        $params=json_decode($msg,true);
        if(empty($params))
            Mod::log("add contract profit error, the data is:".$msg,"error");
        $ProfitService = new \ddd\Profit\Application\ProfitService();
        if(!empty($params['order_id']))
            $ProfitService->addContractProfit($params['order_id']);
        if(!empty($params['contract_id']))
            $ProfitService->addProjectProfit($params['contract_id']);
        if(!empty($params['project_id']))
            $ProfitService->addCorporationProfit($params['project_id']);

    }
    /**
     * @name:addBuyGoodsCost
     * @desc: 生成采购商品成本
     * @param:* @param $msg
     * @throw:
     * @return:void
     */
    public function addBuyGoodsCost($msg)
    {
        $params=json_decode($msg,true);
        if(empty($params))
            Mod::log("add buyGoodsCost error, the data is:".$msg,"error");
        $ProfitService = new \ddd\Profit\Application\ProfitService();
        if(!empty($params['batch_id']))
            $ProfitService->addBuyGoodsCostByBatchId($params['batch_id']);
        if(!empty($params['contract_id']))
            $ProfitService->addBuyGoodsCostByContractId($params['contract_id']);

    }
    /**
     * @name:addProfit
     * @desc: 生成发货单利润
     * @param:* @param $msg
     * @throw:
     * @return:void
     */
    public function addProfit($msg)
    {
        $params=json_decode($msg,true);
        if(empty($params))
            Mod::log("add profit error, the data is:".$msg,"error");
        $ProfitService = new \ddd\Profit\Application\ProfitService();
        if(!empty($params['batch_id']))
            $ProfitService->addProfitByBatchId($params['batch_id']);
        if(!empty($params['contract_id']))
            $ProfitService->addProfitByContractId($params['contract_id']);

    }


    /**
     * @param $msg
     * @throws Exception
     */
    public function addEstimateContractProfit($msg)
    {
        $params=json_decode($msg,true);

        try
        {
            \ddd\Profit\Application\Estimate\EstimateProfitService::service()->createEstimateContractProfit($params["contract_id"]);
        }
        catch (Exception $e)
        {
            Mod::log("addEstimateContractProfit error: ".$e->getMessage(),"error");
        }
    }

    /**
     * @param $msg
     * @throws Exception
     */
    public function addEstimateProjectProfit($msg)
    {
        $params=json_decode($msg,true);
        Mod::log("收款触发项目预估利润","error");
        try
        {
            \ddd\Profit\Application\Estimate\EstimateProfitService::service()->createEstimateProjectProfit($params["project_id"]);
        }
        catch (Exception $e)
        {
            Mod::log("addEstimateContractProfit error: ".$e->getMessage(),"error");
        }
    }

    /**
     * @param $msg
     * @throws Exception
     */
    public function addEstimateProjectProfitTwo($msg)
    {
        $params=json_decode($msg,true);
        Mod::log("付款触发项目预估利润","error");
        try
        {
            \ddd\Profit\Application\Estimate\EstimateProfitService::service()->createEstimateProjectProfit($params["project_id"]);
        }
        catch (Exception $e)
        {
            Mod::log("addEstimateContractProfit error: ".$e->getMessage(),"error");
        }
    }

    /**
     * @param $msg
     * @throws Exception
     */
    public function addEstimateCorporationProfit($msg)
    {
        $params=json_decode($msg,true);

        try
        {
            \ddd\Profit\Application\Estimate\EstimateProfitService::service()->createEstimateCorporationProfit($params["corporation_id"]);
        }
        catch (Exception $e)
        {
            Mod::log("addEstimateContractProfit error: ".$e->getMessage(),"error");
        }
    }



}