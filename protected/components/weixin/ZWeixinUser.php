<?php
/**
 * Created by youyi000.
 * DateTime: 2017/11/9 17:20
 * Describe：
 */

class ZWeixinUser extends ZBaseWeixinCorp
{

    const USERINFO_URL = "https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo?access_token=%s&code=%s";

    /**
     * 创建用户
     * @param $params
     * @return bool
     */
    public function create($params)
    {
        try
        {
            $ret=$this->post('user', 'create', $params);
            $res = json_decode($ret);

            if ($res->errcode != 0)
            {
                Mod::log(sprintf('Weixin user create error, the result is: %s, the params is: %s', $ret, json_encode($params)), 'error');
                //throw new BusinessException('新增用户失败');
                return false;
            }
            return true;
        }
        catch (Exception $e)
        {
            Mod::log("Weixin user create error: ".$e->getMessage().", the params is: ".json_encode($params),"error");
            return false;
        }

    }

    public function getDepartmentMembers($departmentId, $fetchChild=true, $status=0) {
        try {
            $params = array(
                "department_id=$departmentId",
                "fetch_child=$fetchChild",
                "status=$status"
            );
            $ret = $this->get('user', 'simplelist', $params);
            $res = json_decode($ret, true);

            if ($res->errcode != 0) {
                Mod::log(sprintf('Fetch department members error, the result is: %s, the params is: %s', $ret, json_encode($params)), 'error');
                return false;
            }

            return $res;
        } catch (Exception $e) {
            Mod::log("Fetch department members error: ".$e->getMessage().", the params is: ".json_encode($params),"error");
            return false;
        }
    }

    /**
     * 更新用户信息
     * @param $userId
     * @param $params
     * @return bool
     */
    public function update($userId,$params)
    {
        if(!is_array($params) || count($params)<1)
            return false;

        $params["userid"]=$userId;

        try
        {
            $ret=$this->post('user', 'update', $params);
            $res = json_decode($ret);

            if ($res->errcode != 0)
            {
                Mod::log(sprintf('Weixin user update error, the result is: %s, the params is: %s', $ret, json_encode($params)), 'error');
                return false;
            }
            return true;
        }
        catch (Exception $e)
        {
            Mod::log("Weixin user update error: ".$e->getMessage().", the params is: ".json_encode($params),"error");
            return false;
        }
    }

    /**
     * 删除用户
     * @param $userId
     * @return bool
     */
    public function delete($userId)
    {
        try
        {
            $param="userid=".$userId;
            $res=$this->get('user', 'delete', $param);
            $res = json_decode($res);
            if ($res->errcode != 0)
            {
                Mod::log(sprintf('Weixin user delete error, the code is: %s, the message is: %s, the params is: %s', $res->errorcode, $res->errormsg,$param), 'error');
                return false;
            }
            return true;
        }
        catch (Exception $e)
        {
            Mod::log("Weixin delete update error: ".$e->getMessage().", the params is: ".$param,"error");
            return false;
        }
    }

    public function getUserInfo($code) {
        if (empty($code))
            return false;

        try {
            $token = $this->getToken();
//            echo $token . "<br/>";
//            echo $code;exit;
            $url = sprintf(self::USERINFO_URL, $token, $code);
//            echo $url;exit;
            $resStr = Mod::app()->curl->get($url);
            $res = json_decode($resStr, true);
//            var_dump($res);exit;
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