<?php

/**
 * 外部接口
 * User: susiehuang
 * Date: 2018/7/18 0018
 * Time: 11:28
 */
class AutoPaymentCMD extends CMD
{
    public function __construct()
    {
        $this->actionMap = array(
            "80010001" => "processAutoPaymentResult",
        );
    }

    /**
     * 接口验证Key
     * @var string
     */
    private $interfaceKey = "newoil128l7a3dabl03ab#&^865213";

    /**
     * @api {POST} / [80010001]处理自动实付结果
     * @apiName 80010001
     * @apiParam (输入字段) {string} cmd 命令字，<font color=red>必填</font>
     * @apiParam (输入字段) {string} out_order_num 外部订单号，<font color=red>必填</font>
     * @apiParam (输入字段) {string} order_num 订单编号，<font color=red>必填</font>
     * @apiParam (输入字段) {string} order_status 订单状态，<font color=red>必填</font>
     * @apiParam (输入字段) {string} optor 操作人，<font color=red>必填</font>
     * @apiParam (输入字段) {string} pay_bank_name 付款银行名，<font color=red>必填</font>
     * @apiParam (输入字段) {string} pay_bank_account 付款账号，<font color=red>必填</font>
     * @apiParam (输入字段) {string} real_pay_date 实付日期，<font color=red>必填</font>
     * @apiParam (输入字段) {string} secret 加密串，所有参数按照字母顺序排序后，根据a=1&b=2格式连接成新的字符串，并在结尾加入秘钥后，整个字符串进行md5加密结果。 如参数b=1,a=2,key=aabb 则加密前字符串为a=1&b=2aabb，<font color=red>必填</font>
     * @apiExample {json} 输入示例:
     * {
     * "cmd":"80010001"
     * "data":{
     *      "out_order_num":"2018071800001",
     *      "order_num":"money2018071800001"
     *      "order_status":2,
     *      "optor":"admin",
     *      "pay_bank_name":"招商银行",
     *      "pay_bank_account":"1111222233334444",
     *      "real_pay_date":"2018-07-18",
     *      "secret":"abcddfafafawfeawfeffaewfwa",
     *      }
     * }
     * @apiSuccessExample {json} 输出示例:
     * 成功返回：
     * {
     * "code":0,
     * "msg":"succ"
     * }
     * @apiParam (输出字段) {string} code 错误码
     * @apiParam (输出字段) {string} msg 成功或错误信息
     * @apiGroup autoPayment
     * @apiVersion 1.0.0
     */
    protected function processAutoPaymentResult($params)
    {
        Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 自动付款状态回调入参:' . $params);

        if (!empty($params))
        {
            $params = json_decode(RSAUtil::privateDecrypt($params), true);
            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 自动付款状态回调解密后入参:' . json_encode($params));

            if (Utility::isNotEmpty($params))
            {
                try
                {
                    $requiredParams = ['out_order_num', 'order_num', 'order_status', 'pay_status', 'secret'];
                    if (!Utility::checkRequiredParamsNoFilterInject($params, $requiredParams))
                    {
                        return $this->returnError('必填参数传入错误！');
                    }

                    $mustParams = ['optor', 'pay_bank_name', 'pay_bank_account', 'bank_water', 'real_pay_date'];
                    if (!Utility::checkMustExistParams($params, $mustParams))
                    {
                        return $this->returnError('必填参数传入错误！');
                    }

                    $secret = $params['secret'];
                    unset($params['secret']);
                    if ($secret == AutoPaymentService::generateSecret($params, $this->interfaceKey))
                    {
                        $payOrderModel = MoneyPayOrder::model()->findByApplyId($params['out_order_num']);
                        if (empty($payOrderModel))
                        {
                            return $this->returnError('付款命令信息不存在！');
                        }
                        if ($payOrderModel->status == MoneyPayOrder::STATUS_NEW && $payOrderModel->apply->status == PayApplication::STATUS_IN_AUTO_PAYMENT)
                        {
                            AutoPaymentService::processAutoPaymentResult($params);
                        }

                        return $this->returnSuccess();
                    } else
                    {
                        return $this->returnError('非法调用！');
                    }
                } catch (Exception $e)
                {
                    Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 自动付款状态回调处理实付结果异常:' . $e->getMessage());
                }
            } else
            {
                Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 自动付款状态回调解密后参数错误:' . json_encode($params));
            }
        }
    }
}
