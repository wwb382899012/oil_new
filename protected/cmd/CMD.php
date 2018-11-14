<?php

/**
 * Created by youyi000.
 * DateTime: 2016/10/9 12:00
 * Describe：
 */
abstract class CMD
{
    const CMD_SUCCESS=1;

    const CMD_FAILURE=0;

    public $actionMap=array();

    public function invoke($params)
    {
        Mod::log(json_encode($params),"info","new_oil.out");
        $start_time = Utility::microtime_float();
        $fn=$this->actionMap[$params["cmd"]];
        if(empty($fn))
        {
            return $this->returnResult(self::CMD_FAILURE,CMDCode::CODE_CMD_INVALID);
        }

        $res = $this->$fn($params["data"]);

        $end_time = Utility::microtime_float();

        $span = $end_time - $start_time;

        $code = $res['code']==self::CMD_SUCCESS ? 0 : 1;

//        Monitor::log_profile($params["cmd"],$span,$code,$res["data"]);
        return $res;

    }


    /**
     * 返回操作结果
     * @param $code
     * @param $data
     * @return array
     */
    protected function returnResult($code,$data=null)
    {
        $res = array("code"=>$code);
        if(!empty($data))
        {
            $res["data"]= $data;
        }
        /*if(!empty($msg))
        {
            $res["msg"]= $msg; ;
        }*/
        return $res ;
    }

    protected function returnSuccess($data=null)
    {
        return $this->returnResult(self::CMD_SUCCESS,$data);
    }

    protected function returnError($msg=null)
    {
        return $this->returnResult(self::CMD_FAILURE,$msg);
    }

}