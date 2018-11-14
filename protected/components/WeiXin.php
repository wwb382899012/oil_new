<?php
/**
 * Created by youyi000.
 * DateTime: 2017/11/2 10:48
 * Describe：
 *
 *      已经废弃
 */

class WeiXin
{


    /**
     * 发布微信通知
     * @param $msgArray array(array('user_account'=>'youyi000','msg'=>$msg))
     * @return bool
     */
    /*public static function postBatchMessage($msgArray)
    {
        $url      = Mod::app()->params["weiXin_url"];//微信提醒测试环境地址
        $cmdNo    = 24010001;
        $agentId  = Mod::app()->params["wx_agent_id"];
        $params    = array('cmd'=>$cmdNo,
                           'data'=>$msgArray,
                           'agent_id'=>$agentId
        );
        try{
            $res =Utility::cmd($params,$url);
            Mod::log("WeiXin Log, params is ".json_encode($params).", and result is ".json_encode($res));
            return true;
        }
        catch(Exception $e)
        {
            Mod::log("WeiXin error, params is ".json_encode($params).", and error message is ".$e->getMessage(),"error");
            return false;
        }
    }*/
}