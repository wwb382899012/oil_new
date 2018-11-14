<?php

/**
 * Created by youyi000.
 * DateTime: 2016/8/15 16:58
 * Describe：
 */
class ActionService
{
    public static $redisKeyName="new_oil_task_actions";

    public static function clearActionsCache()
    {
        Utility::clearCache(self::$redisKeyName);
    }

    /**
     * 获取Action
     * @param $actionId
     * @return mixed|null
     */
    public static function getAction($actionId)
    {
        if(!Utility::hExists(self::$redisKeyName,$actionId))
            self::generateActionsCache();
        $action=Utility::hGetCache(self::$redisKeyName,$actionId);
        if(!empty($action))
            return json_decode($action,true);
        else
            return null;
    }

    /**
     * 生成Action的缓存
     */
    public static function generateActionsCache()
    {
        $sql="select * from t_action";
        $data=Utility::query($sql);
        foreach($data as $v)
        {
            Utility::hSetCache(self::$redisKeyName,$v["action_id"],json_encode($v));
        }
    }


    public static function getActionRoleIds($actionId)
    {
        $action=ActionService::getAction($actionId);
        if(empty($action))
            return "0";
        return $action["role_ids"];
    }

    /**
     * 获取action的名称
     * @param $actionId
     * @return string
     */
    public static function getActionName($actionId)
    {
        $action=ActionService::getAction($actionId);
        if(empty($action))
            return "";
        return $action["action_name"];
    }

}