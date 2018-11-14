<?php
/**
 * Created by youyi000.
 * DateTime: 2017/11/9 16:39
 * Describe：
 */

abstract  class ZBaseWeixinCorp extends CApplicationComponent
{
    /**
     * 微信企业号id
     * @var string
     */
    public $corp_id="";

    public $secret="";

    public $agent_id="0";

    const GET_ACCESS_TOKEN_URL = "https://qyapi.weixin.qq.com/cgi-bin/gettoken?corpid=%s&corpsecret=%s";
    const CALL_BASE_URL = "https://qyapi.weixin.qq.com/cgi-bin/%s/%s";
    //const OAUTH_URL = "https://qyapi.weixin.qq.com/cgi-bin/user/getuserinfo";
    //const OAUTH_SUCC_URL = "https://qyapi.weixin.qq.com/cgi-bin/user/authsucc";
    const OAURH_URL = "https://open.weixin.qq.com/connect/oauth2/authorize?appid=%s&redirect_uri=%s&response_type=code&scope=%s&agentid=%d&state=STATE#wechat_redirect";


    /**
     * post数据
     * @param $module
     * @param $action
     * @param $data
     * @return mixed
     */
    public function post ($module,$action,$data)
    {
        $token = $this->getToken();
        $url = sprintf(self::CALL_BASE_URL,$module,$action)."?access_token=".$token;
        $res = Mod::app()->curl->post($url,self::cnJsonEncode($data));
        return $res;
    }

    /**
     * get获取数据
     * @param $module
     * @param $action
     * @param $params
     * @return mixed
     */
    public function get ($module,$action,$params)
    {
        $token = $this->getToken();
        if(is_array($params))
            $param=implode("&",$params);
        else
            $param=$params;
        if(!empty($param))
            $param="&".$param;
        $url = sprintf(self::CALL_BASE_URL,$module,$action)."?access_token=".$token.$param;
        $res = Mod::app()->curl->get($url);
        return $res;
    }

    /**
     * 获取缓存key
     * @return string
     */
    public function getCacheKey()
    {
        return 'weixin_corp_token_'.$this->corp_id."_".$this->agent_id;
    }

    /**
     * 获取token
     * @return string
     */
    public function getToken()
    {
        $redis = Mod::app()->redis;
        $key=$this->getCacheKey();
        if ($redis->exists($key))
        {
            $token = $redis->get($key);
            return $token;
        }
        else
        {
            return $this->resetToken();
        }
    }

    /**
     * 重置并返回token
     * @return string
     * @throws BusinessException
     */
    public function resetToken()
    {
        $res = Mod::app()->curl->get(sprintf(self::GET_ACCESS_TOKEN_URL,$this->corp_id,$this->secret));
        $res = json_decode($res);

        if ($res->errcode)
        {
            Mod::log(sprintf('ResetToken error, the code: %s, the message: %s',$res->errcode,$res->errmsg),'error');
            throw new BusinessException('获取token失败');
        }
        $token = $res->access_token;
        $redis = Mod::app()->redis;
        $key=$this->getCacheKey();
        $redis->setex($key ,300, $token );
        return $token;
    }


    /**
     * 对请求参数进行编码
     * @param $value
     * @return mixed|string
     */
    public static function cnJsonEncode($value)
    {
        if (defined('JSON_UNESCAPED_UNICODE'))
            return json_encode($value,JSON_UNESCAPED_UNICODE);
        else{
            $encoded = urldecode(json_encode(self::urlEncode($value)));
            return preg_replace(array('/\r/','/\n/'), array('\\r','\\n'), $encoded);
        }
    }


    /**
     * 对数据进行url编码
     * @param $value
     * @return array|string
     */
    public static function urlEncode($value)
    {
        if (is_array($value))
        {
            return array_map(array('ZBaseWeixinCorp','urlEncode'),$value);
        }
        elseif (is_bool($value) || is_numeric($value))
        {
            return $value;
        }
        else
        {
            return urlencode(addslashes($value));
        }
    }

    public function getOauthUrl($returnUrl, $scope='snsapi_base', $agentId=null) {
        $returnUrl = urlencode($returnUrl);
        $agentId = $agentId === null ? $this->agent_id : $agentId;
        $url = sprintf(self::OAURH_URL, $this->corp_id, $returnUrl, $scope, $agentId);
        return $url;
    }
}