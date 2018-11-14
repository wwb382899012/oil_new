<?php
/**
 * Created by youyi000.
 * DateTime: 2017/11/9 17:33
 * Describe：
 */

class ZWeixinMsg extends ZBaseWeixinCorp
{
    /**
     * 发送文本信息
     * @param string|array $users 微信企业号中userid或userid数组
     * @param string $msg 消息内容
     * @return bool
     */
    public function send($users,$msg)
    {
        try
        {
            $toUser = $users;
            if (is_array($users))
                $toUser = implode('|', $users);

            $data = array('touser' => $toUser, 'msgtype' => 'text', 'agentid' => $this->agent_id, 'text' => array('content' => $msg));

            $ret = $this->post('message', 'send', $data);
            $res=json_decode($ret);

            if ($res->errcode != 0)
            {
                Mod::log(sprintf('Weixin send error, the result: %s, the users: %s', $ret, $toUser), 'error');
                //throw new BusinessException('推送消息失败');
                return false;
            }
            return true;
        }
        catch (Exception $e)
        {
            Mod::log("Weixin send error: ".$e->getMessage().", the users is: ".$toUser,"error");
            return false;
        }

    }


    /**
     * 发送单图文通知消息，不带图片
     * @param $users 微信企业号中userid或userid数组
     * @param $msg
     * @return bool
     */
    public function sendSingleNews($users,$title, $msg, $link)
    {
        try
        {
            $toUser = $users;
            if (is_array($users))
                $toUser = implode('|', $users);

            $data = array(
                'touser' => $toUser,
                'msgtype' => 'news',
                'agentid' => $this->agent_id,
                'news' => array(
                    'articles' => array(
                        array(
                            "title" => $title,
                            "description" => $msg,
                            "url" =>  $link,
                            "picurl" => ""
                        )
                    )
                )
            );

            $ret = $this->post('message', 'send', $data);
            $res=json_decode($ret);

            if ($res->errcode != 0)
            {
                Mod::log(sprintf(__METHOD__.' Weixin send error, the result: %s, the users: %s, params:%s', $ret, json_encode($users), json_encode(func_get_args())), 'error');
                return false;
            }
            return true;
        }
        catch (Exception $e)
        {
            Mod::log(sprintf(__METHOD__.' Weixin send error, the result: %s, the users: %s, params:%s, err:%s', $ret, json_encode($users), json_encode(func_get_args())), $e->getMessage(), 'error');
            return false;
        }
    }
}
