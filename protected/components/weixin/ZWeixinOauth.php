<?php
/**
 * Created by youyi000.
 * DateTime: 2017/11/9 17:20
 * Describe：
 */

class ZWeixinOauth extends ZBaseWeixinCorp
{
    const USERINFO_URL = "https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token=%s&code=%s";

    public function getUserInfo($code) {
        if (empty($code))
            return false;

        try {
            $token = $this->getToken();
            $url = sprintf(self::USERINFO_URL, $token, $code);
            $resStr = Mod::app()->curl->get($url);
            $res = json_decode($resStr, true);
            if (empty($res) || $res['errcode'] != 0 || empty($res['UserId'])) {
                Mod::log(__METHOD__ . "Weixin fetch user info failed:code=".$code." res:".$resStr);
                return false;
            }

            return $res;
        } catch (Exception $e) {
            Mod::log(__METHOD__ . "Weixin fetch user info error: ".$e->getMessage().", the code is: ".$code,"error");
            return false;
        }
    }
}