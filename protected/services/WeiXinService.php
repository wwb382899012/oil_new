<?php
/**
 * Created by youyi000.
 * DateTime: 2017/11/8 16:32
 * Describe：
 *  微信企业号相关功能封闭
 */

class WeiXinService
{
    /**
     * 创建企业微信号用户
     * @param $userModel
     * @return mixed
     */
    public static function createWeiXinUser($userModel)
    {

        $isAuto=Mod::app()->params["isAutoUpdateWeixinCorp"];
        if($isAuto!=1)
            return true;

        $wx = Mod::app()->weiXinUser;
        if (empty($userModel->identity))
            return false;

        $user = array(
            //'userid' => $userModel->user_id,
            'userid' => $userModel->identity,
            'name' => $userModel->name,
            "department"=>array(1),
        );

        if (!empty($userModel->weixin))
            $user["weixinid"] = $userModel->weixin;
        if (!empty($userModel->email))
            $user["email"] = $userModel->email;
        if (!empty($userModel->phone))
            $user["mobile"] = $userModel->phone;

        return $wx->create($user);
    }

    /**
     * 变更企业微信号用户信息，自动判断name、weixin、email、phone是否有变更
     * @param $userModel
     * @return mixed
     */
    public static function updateWeiXinUser($userModel)
    {
        $isAuto=Mod::app()->params["isAutoUpdateWeixinCorp"];
        if($isAuto!=1)
            return true;

        if (empty($userModel->identity))
            return false;

        $wxUser = Mod::app()->weiXinUser;

        $params=array();
        if($userModel->attributeIsModified("name"))
            $params["name"]=$userModel->name;

        if($userModel->attributeIsModified("weixin"))
            $params["weixinid"]=$userModel->weixin;

        if($userModel->attributeIsModified("email"))
            $params["email"] = $userModel->email;

        if($userModel->attributeIsModified("phone"))
            $params["mobile"] = $userModel->phone;

        if(count($params)>0)
        {
            return $wxUser->update($userModel->identity,$params);
        }
        else
            return true;
    }

    /**
     * 发送微信提醒消息
     * @param $userWeixin  user的微信或数组
     * @param $msg
     * @return mixed
     */
    public static function send($userWeixin,$msg)
    {
        $wx = Mod::app()->weiXinMsg;
        return $wx->send($userWeixin,$msg);
    }

    /**
     * 发送图文消息
     * @param $userWeixin  user的微信或数组
     * @param $msg
     * @return mixed
     */
    public static function sendSingleNews($userWeixin, $title, $msg, $link)
    {
        $wx = Mod::app()->weiXinMsg;
        return $wx->sendSingleNews($userWeixin, $title, $msg, $link);
    }

    /**
     * @desc 获取授权登录url
     * @param $returnUrl
     * @return mixed
     */
    public static function getOauthUrl($returnUrl) {
        $wxUser = Mod::app()->weiXinUser;
        return $wxUser->getOauthUrl($returnUrl);
    }

    /**
     * @desc 判断是否在微信浏览器
     * @return bool
     */
    public static function inWeixin() {
        $user_agent = strtolower($_SERVER['HTTP_USER_AGENT']);
        if (preg_match('/micromessenger/i', $user_agent))
            return true;

        return false;
    }

    public static function getUserInfo($code) {
        $wxUser = Mod::app()->weiXinOauth;
        $userInfo = $wxUser->getUserInfo($code);
        if ($userInfo == false)
            return false;

        return $userInfo;
    }

    // todo list
    public static function getUserDetail($code) {
        $userBase = self::getUserInfo($code);
    }
    
}