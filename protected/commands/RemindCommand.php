<?php
/**
 * Created by youyi000.
 * DateTime: 2017/11/16 16:13
 * Describe：
 */

class RemindCommand extends AMQPCommand
{
    /**
     * 需要监听的队列信息
     * @var array
     */
    protected $queueConfig = array(
        "new.oil.remind"=>array(
            "fn"=>"remind",
            "exchange"=>"new.oil.direct",
            "routingKey"=>"task.reminder",
        ),
        "new.oil.weinxin.remind"=>array(
            "fn"=>"sendWeixinReminder",
            "exchange"=>"new.oil.direct",
            "routingKey"=>"new.oil.weinxin.remind",
        ),
        "new.oil.weinxin.singlenews.remind"=>array(
            "fn"=>"sendWeixinSingleNewsReminder",
            "exchange"=>"new.oil.direct",
            "routingKey"=>"new.oil.weinxin.singlenews.remind",
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
    public function remind($msg)
    {
        $params=json_decode($msg,true);

        try
        {
            TaskService::sendWeixinReminder($params["task_id"]);
            TaskService::sendEmailReminder($params["task_id"]);
        }
        catch (Exception $e)
        {
            Mod::log("remind error: ".$e->getMessage(),"error");
        }

    }

    /**
     * @desc 发送微信提醒
     * @param $msg
     * @throws Exception
     */
    public function sendWeixinReminder($msg)
    {
        $params = json_decode($msg, true);
        try{
            WeiXinService::send($params['userIds'], $params['content']);
        } catch (Exception $e)
        {
            Mod::log("sendWeixinReminder error: ".$e->getMessage(),"error");
        }
    }

    /**
     * @desc 发送微信图文消息
     * @param $msg
     * @throws Exception
     */
    public function sendWeixinSingleNewsReminder($msg)
    {
        $params = json_decode($msg, true);
        try{
            WeiXinService::sendSingleNews($params['userIds'], $params['title'], $params['msg'], $params['link']);
        } catch (Exception $e)
        {
            Mod::log("sendWeixinReminder error: ".$e->getMessage(),"error");
        }
    }
}