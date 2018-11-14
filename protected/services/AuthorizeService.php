<?php
/**
 * Created by youyi000.
 * DateTime: 2017/10/24 10:15
 * Describe：
 */

class AuthorizeService
{
    /**
     * 返回用户数据过滤的sql条件
     * @param string $tablePrefix
     * @param int $userId
     * @return string
     */
    public static function getUserDataConditionString($tablePrefix="",$userId=0)
    {
        if(empty($userId))
            $userId=Utility::getNowUserId();
        $user=SystemUser::getUser($userId);
        $pre="";
        if(!empty($tablePrefix))
            $pre=$tablePrefix.".";
        $condition=$pre."corporation_id in (" . $user['corp_ids'] . ")";
        return $condition;
    }

    /**
     * 判断用户交易主体的数据权限
     * @param $corpId
     * @param int $userId
     * @return bool
     */
    public static function checkUserCorpRight($corpId,$userId=0)
    {
        if(empty($userId))
            $userId=Utility::getNowUserId();
        $user=SystemUser::getUser($userId);
        if(!empty($user["corp_ids"]))
        {
            $corpIds=explode(",",$user["corp_ids"]);
             return in_array($corpId,$corpIds);
        }
        return false;
    }

}