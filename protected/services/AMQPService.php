<?php

/**
 * Created by PhpStorm.
 * User: youyi000
 * Date: 2016/3/9
 * Time: 15:23
 * Describe：
 */
class AMQPService
{
    private static $exchanges=array();


    /**
     * 发布队列消息
     * @param $exchange
     * @param $routeKey
     * @param $message
     * @return bool
     */
    public static function publish($exchange,$routeKey,$message)
    {
        try {
            $obj = self::getExchange($exchange);
            return $obj->publish($message, $routeKey);
        }
        catch(Exception $e)
        {
            Mod::log("AQMP Publish Message Error: ".$e->getMessage(),"error");
            return false;
        }
    }

    /**
     * 发布到延迟队列
     * @param $queueName
     * @param $message
     * @param $seconds
     * @param $routeKey
     * @return null
     */
    public static function publishToDelayQueue($queueName,$message,$seconds,$routeKey="")
    {
        $params=array(
            "queueName"=>$queueName,
            "seconds"=>$seconds,
            "message"=>$message,
            "routingKey"=>$routeKey,
        );
        return self::addToDelayQueue($params);
    }

    /**
     * 调用接口命令
     * @param $params
     * @return mixed
     */
    public static function cmd($params)
    {
        $url=Mod::app()->params["delay_amqp_url"];
        return Utility::cmd($params,$url);
    }

    /**
     * 增加到延时队列
     * @param $params
     * @return null
     */
    public static function addToDelayQueue($params)
    {
        $data=array(
            "cmd"=>"14010000",
            "tag"=>1,
            "queue_name"=>$params["queueName"],
            "wait_seconds"=>$params["seconds"],
            "message"=>$params["message"],
            "routing_key"=>$params["routingKey"],
        );
        $res=self::cmd($data);
        if(!empty($res) && $res["code"]==0)
        {
            return true;
        }
        else
        {
            Mod::log("发布消息到延时队列[14010000]接口出错，参数：".json_encode($data)."，错误信息：".$res["msg"],"error");
            return false;
        }
    }

    /**
     * 获取exchange
     * @param $exchangeName
     * @return mixed
     */
    protected static function getExchange($exchangeName)
    {
        if(empty(self::$exchanges[$exchangeName]))
        {
            $obj=Mod::app()->amqp->exchange($exchangeName);
            self::$exchanges[$exchangeName]=$obj;
        }
        return self::$exchanges[$exchangeName];
    }

    /**
     * 发送邮件
     * @param $userId
     * @param $subject
     * @param $content
     * @param array $attachArray
     */
    public static function publishEmail($userId,$subject,$content,$attachArray=array())
    {
        $data=array("userId"=>$userId,"subject"=>$subject,"content"=>$content,"attach"=>$attachArray);

        return self::publish("new.oil.direct","email",json_encode($data));
    }

    /**
     * 发送微信提醒
     * @param $userIds
     * @param $content
     * @return bool
     */
    public static function publishWinxinReminder($userIds,$content)
    {
        $data=array("userIds"=>$userIds,"content"=>$content);

        return self::publish("new.oil.direct","new.oil.weinxin.remind",json_encode($data));
    }

    /**
     * 发送微信提醒
     * @param $userIds
     * @param $title
     * @param $msg
     * @param $link
     * @return bool
     */
    public static function publishWinxinSingleNewsReminder($userIds,$title, $msg, $link)
    {
        $data=array("userIds"=>$userIds,"title"=>$title,"msg"=>$msg,"link"=>$link);

        return self::publish("new.oil.direct","new.oil.weinxin.singlenews.remind",json_encode($data));
    }

    /**
     * 操作日志消息发布
     * @param $data
     */
    public static function publishActionLog($data)
    {
        return self::publish("new.oil.direct","action.log",json_encode($data));
    }

    /**
     * 发布提醒的队列
     * @param $data
     */
    public static function publishReminder($data)
    {
        return self::publish("new.oil.direct","task.reminder",json_encode($data));
    }

    /**
     * 发布文件转换成PDF的事件
     * @param $filePath
     * @return bool
     */
    public static function publishFileWordToPDF($filePath)
    {
        $data=array("filePath"=>$filePath);
        return self::publish("new.oil.direct","file.word.to.pdf",json_encode($data));
    }

    /**
     * 发布合同信息上链事件
     * @param $contractId
     * @return bool
     */
    public static function publishContractBlock($contractId)
    {
        $data=array("contract_id"=>$contractId);
        return self::publish("new.oil.direct","contract.block",json_encode($data));
    }

    /**
     * @desc 发布自动实付消息
     * @param $params
     * @return bool
     */
    public static function publishForAutoPayment($params)
    {
        return self::publish("new.oil.direct", "new.oil.auto.payment", json_encode($params));
    }

    /**
     * @desc 发布查询自动实付状态到延时队列
     * @param array $params
     * @param int $seconds
     * @return bool
     */
    public static function publishQueryAutoPayStatusToDelayQueue($params, $seconds)
    {
        return self::publishToDelayQueue("new.oil.query.auto.pay.status", json_encode($params), $seconds, 'new.oil.query.auto.pay.status.delay');
    }

    /**
     * @desc 发布查询自动实付状态消息
     * @param array $params
     * @return bool
     */
    public static function publishForQueryAutoPayStatus($params)
    {
        return self::publish("new.oil.direct", "new.oil.query.auto.pay.status", json_encode($params));
    }

    /**
     * @desc 发布自动实付请求消息
     * @param array $params
     * @param int $seconds
     * @return bool
     */
    public static function publishForAutoPaymentToDelayQueue($params, $seconds)
    {
        return self::publishToDelayQueue("new.oil.for.auto.payment", json_encode($params), $seconds, 'new.oil.for.auto.payment.delay');
    }

    public static function publishForPayment($applyId)
    {
        return self::publish('new.oil.direct', 'new.oil.do.payment', $applyId);
    }


    /**
    * 发布发货单利润变化事件
    * @param $orderId
    * @return bool
    */
    public static function publishDeliveryProfit($orderId)
    {
        $data=array("order_id"=>$orderId);
        return self::publish("new.oil.direct","delivery.profit",json_encode($data));
    }
    /**
     * 发布合同利润变化事件
     * @param $contractId
     * @return bool
     */
    public static function publishContractProfit($contractId)
    {
        $data=array("contract_id"=>$contractId);
        return self::publish("new.oil.direct","delivery.profit",json_encode($data));
    }
    /**
     * 发布项目利润变化事件
     * @param $projectId
     * @return bool
     */
    public static function publishProjectProfit($projectId)
    {
        $data=array("project_id"=>$projectId);
        return self::publish("new.oil.direct","delivery.profit",json_encode($data));
    }

    /**
     * @name:publishBuyGoodsCost
     * @desc:入库通知单变化，发布采购商品成本变化事件
     * @param: null $batchId
     * @param null $contractId
       @throw:
     * @return:bool
     */
    public static function publishBuyGoodsCost($batchId=null,$contractId=null)
    {
        if(empty($batchId))
            $data=array("contract_id"=>$contractId);
        else
            $data=array("batch_id"=>$batchId);
        return self::publish("new.oil.direct","buy.goods.cost",json_encode($data));
    }

    /**
     * @name:publishBuyGoodsCost
     * @desc:采购商品成本变化，发布发货单利润变化事件
     * @param: null $batchId
     * @param null $contractId
    @throw:
     * @return:bool
     */
    public static function publishProfit($batchId=null,$contractId=null)
    {
        if(empty($batchId))
            $data=array("contract_id"=>$contractId);
        else
            $data=array("batch_id"=>$batchId);
        return self::publish("new.oil.direct","profit",json_encode($data));
    }

    /**
     * [publishEstimateContractProfit 预估合同利润变更事件]
     * @param
     * @param  [bigint] $projectId    [项目id]
     * @return [bool]
     */
    public static function publishEstimateContractProfit($projectId)
    {
        $data=array("project_id"=>$projectId);
        return self::publish("new.oil.direct","estimate.contract.profit",json_encode($data));
    }

    /**
     * [publishEstimateProjectProfit 预估项目利润变更事件]
     * @param
     * @param  [bigint] $corporation_id    [交易主体id]
     * @return [bool]
     */
    public static function publishEstimateProjectProfit($corporation_id)
    {
        $data=array("corporation_id"=>$corporation_id);
        return self::publish("new.oil.direct","estimate.project.profit",json_encode($data));
    }

    /**
     * [publishSellOutQuantity 销售出库数量变更事件]
     * @param
     * @param  [bigint] $billId [发货单id]
     * @return [bool]
     */
    public static function publishSellOutQuantity($billId)
    {
        $data=array("bill_id"=>$billId);
        return self::publish("new.oil.direct","sell.out.quantity",json_encode($data));
    }

    /**
     * [publishSellPrice 发布销售合同价格变更事件]
     * @param
     * @param  [bigint] $contractId [合同id]
     * @return [bool]
     */
    public static function publishSellContractPrice($contractId)
    {
        $data=array("contract_id"=>$contractId);
        return self::publish("new.oil.direct","sell.contract.price",json_encode($data));
    }

    /**
     * [publishSellSettledPrice 发布销售结算价格变更事件]
     * @param
     * @param  [bigint] $billId [发货单id]
     * @return [bool]
     */
    public static function publishSellSettledPrice($billId)
    {
        $data=array("bill_id"=>$billId);
        return self::publish("new.oil.direct","sell.settled.price",json_encode($data));
    }

    /**
     * [publishBuyPrice 发布采购合同价格变更事件]
     * @param
     * @param  [bigint] $contractId [合同id]
     * @return [bool]
     */
    public static function publishBuyContractPrice($contractId)
    {
        $data=array("contract_id"=>$contractId);
        return self::publish("new.oil.direct","buy.contract.price",json_encode($data));
    }

    /**
     * [publishBuySettledPrice 发布采购结算价格变更事件]
     * @param
     * @param  [bigint] $billId [入库通知单id]
     * @return [bool]
     */
    public static function publishBuySettledPrice($billId)
    {
        $data=array("bill_id"=>$billId);
        return self::publish("new.oil.direct","buy.settled.price",json_encode($data));
    }

    /**
     * [publishBuyContractBusinessCheckPass 发布采购合同业务审核通过事件]
     * @param
     * @param  [bigint] $contractId [采购合同id]
     * @return [bool]
     */
    public static function publishBuyContractBusinessCheckPass($contractId)
    {
        $data=array("contract_id"=>$contractId);
        return self::publish("new.oil.direct","buy.contract.business.check.pass",json_encode($data));
    }

    /**
     * [publishSellContractBusinessCheckPass 发布销售合同业务审核通过事件]
     * @param
     * @param  [bigint] $contractId [销售合同id]
     * @return [bool]
     */
    public static function publishSellContractBusinessCheckPass($contractId)
    {
        $data=array("contract_id"=>$contractId);
        return self::publish("new.oil.direct","sell.contract.business.check.pass",json_encode($data));
    }

    /**
     * [publishLadingBillSettlementCheckPass 预估利润报表：入库通知单结算完成事件]
     * @param
     * @param  [bigint] $billId [入库通知单id]
     * @return [bool]
     */
    public static function publishLadingBillSettlementCheckPass($billId)
    {
        $data=array("bill_id"=>$billId);
        return self::publish("new.oil.direct","lading.bill.settlement.pass",json_encode($data));
    }
    /**
     * [publishDeliveryOrderSettlementCheckPass 预估利润报表：发货单结算完成事件]
     * @param
     * @param  [bigint] $billId [发货单id]
     * @return [bool]
     */
    public static function publishDeliveryOrderSettlementCheckPass($billId)
    {
        $data=array("bill_id"=>$billId);
        return self::publish("new.oil.direct","delivery.order.settlement.pass",json_encode($data));
    }

    /**
     * [publishReceiveConfirm 收款确认事件]
     * @param
     * @param  [bigint] $projectId [项目id]
     * @return [bool]
     */
    public static function publishReceiveConfirm($projectId)
    {
        $data=array("project_id"=>$projectId);
        return self::publish("new.oil.direct","receive.confirm",json_encode($data));
    }

    /**
     * [publishPayConfirm 付款确认事件]
     * @param
     * @param  [bigint] $projectId [项目id]
     * @return [bool]
     */
    public static function publishPayConfirm($projectId)
    {
        $data=array("project_id"=>$projectId);
        return self::publish("new.oil.direct","pay.confirm",json_encode($data));
    }

}