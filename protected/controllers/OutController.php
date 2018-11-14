<?php
/**
 * Desc: 外部接口
 * User: susiehuang
 * Date: 2018/7/18 0018
 * Time: 11:28
 */

class OutController extends Controller
{
    /**
     * 接口访问的端口要求
     * @var string
     */
    public static $hostPort = array("80","9091");

    public static $cmdMap = array(
        "80010001" => "AutoPaymentCMD",
    );


    /**
     * 过滤器，check对外提供接口，不作权限验证
     * @return array
     */
    public function filters()
    {
        return array("authValidate - index");
    }

    /**
     * 对外接口入口
     */
    public function actionIndex()
    {
        $req = Mod::app()->request;
        if (!$req->isPostRequest || !in_array($_SERVER['SERVER_PORT'],self::$hostPort)) {
            $this->returnOutError(CMDCode::CODE_METHOD_PORT_INVALID);
        }

        $post = file_get_contents("php://input");
        Mod::log("Request RAW POST:[" . $_SERVER['SERVER_PORT'] . "]" . $post);
        $params = json_decode($post, true);

        if (empty($params)) {
            $this->returnOutError(CMDCode::CODE_NO_POST_DATA);
        }

        $cmd = $params["cmd"];
        if (!isset(self::$cmdMap[$cmd])) {
            $this->returnOutError(CMDCode::CODE_CMD_INVALID);
        }

        $res = $this->invoke($cmd, $params);

        if($res["code"]==1)
        {
            $this->returnOutSuccess($res["data"]);
        }
        else
            $this->returnOutError($res["data"]);
    }

    private function invoke($cmd, $params)
    {
        $service = new self::$cmdMap[$cmd];
        Mod::log("INVOKE:" . self::$cmdMap[$cmd] . ", and Params is :" . json_encode($params));
        try {
            return $service->invoke($params);
        } catch (Exception $e) {
            Mod::log('INVOKE Error[' . json_encode($params) . ']:' . $e->getMessage(), "error");
            $this->returnOutError(CMDCode::CODE_CMD_ERROR);
        }
    }
}