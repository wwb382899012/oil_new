<?php
/**
 * Desc: 自动实付服务
 * User: susiehuang
 * Date: 2018/7/16 0016
 * Time: 11:08
 */

class AutoPaymentService
{
    private static $service_map = [
        'sendPaymentOrder' => 'com.jyblife.logic.bg.order.PayOrder',
        'queryPaymentStatus' => 'com.jyblife.logic.bg.order.PayOrderQuery',
    ];

    const ERROR_INTERFACE_CALLED_ERROR = -100; //接口调用失败

    private static function cmd($params) {
        Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ', 自动付款接口调用cmd入参：' . json_encode($params));
        $url = Mod::app()->params['money_system_config']['money_url'];

        try {
            $data = $params['data'];
            unset($params['data']);
            $data['system_flag'] = Mod::app()->params['money_system_config']['system_flag'];
            $data['timestamp'] = time();
            $data['secret'] = self::generateSecret($data);
            $encrypted = RSAUtil::publicEncrypt(json_encode($data));

            $s_params = [
                'version' => '2.0',
                'system_flag' => Mod::app()->params['money_system_config']['system_flag'],
                'secret' => $encrypted
            ];

            $req = [
                "service" => "com.jyblife.logic.bg.layer.HttpAccessLayer",
                'targetService' => $params['cmd'],
                'env' => 'dev',
                'group' => '*',
                'method' => 'access',
                'params' => $s_params,
                'version' => '1.0.0',
                'set' => ''
            ];

            $curl = curl_init();
            $headers = ['Frame-type:JMF'];
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $headers);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
            curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($req));
            $res = curl_exec($curl);
            curl_close($curl);
            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ', 资金系统接口调用url：' . $url . '，调用参数：' . json_encode($req) . '，结果：' . $res);
            if (empty($res)) {
                return array("code" => -1, "msg" => "无返回值");
            } else {
                if (!is_string($res)) {
                    return array("code" => -1, "msg" => "返回值异常");
                }

                $r = json_decode($res, true);
                if (empty($r)) {
                    return array("code" => -1, "msg" => "返回值异常");
                } else {
                    if (array_key_exists('data', $r)) {
                        $r['data'] = json_decode(RSAUtil::privateDecrypt($r['data']), true);
                    }

                    return $r;
                }
            }
        } catch (Exception $e) {
            Mod::log("CMD Log, params is " . json_encode($params) . ", and error message is " . $e->getMessage(), "error");

            return array("code" => -1, "msg" => $e->getMessage());
        }
    }

    /**
     * @desc 付款申请是否可自动实付
     * @param int $applyId
     * @return array [
     *      'code' => 0         #0：可自动实付  -2：验证失败  -1：不能自动实付，只能手动付款
     *      'msg' => ''         #成功/失败信息说明
     * ]
     */
    public static function checkIsCanAutoPayment($applyId) {
        $res = ['code' => 0, 'msg' => 'success'];
        if (Utility::checkQueryId($applyId) && $applyId > 0) {
            $payApplyModel = PayApplication::model()->findByPk($applyId);
            if (empty($payApplyModel)) {
                $res['code'] = -2;
                $res['msg'] = '付款申请:' . $applyId . ' 不存在，请检查!';
            } else {
                $autoPaymentCurrency = Mod::app()->params['money_system_config']['auto_payment_currency'];
                if (!in_array($payApplyModel->currency, $autoPaymentCurrency)) {
                    $res['code'] = -1;
                    $res['msg'] = '实付币种不支持';
                }

                if ($payApplyModel->status != PayApplication::STATUS_SUBMIT) {
                    if (in_array($payApplyModel->status, [PayApplication::STATUS_IN_MANUAL_PAYMENT])) { //不能自动付款
                        $res['code'] = -1;
                        $res['msg'] = '当前状态的申请单只能进行手动付款';
                    } else {
                        if (!in_array($payApplyModel->status, [PayApplication::STATUS_CHECKED, PayApplication::STATUS_IN_AUTO_PAYMENT])) {
                            $res['code'] = -2;
                            $res['msg'] = '当前状态的申请单不能进行自动付款';
                        }
                    }
                }
            }
        } else {
            $res['code'] = -2;
            $res['msg'] = '参数传入错误，applyId:' . $applyId;
        }

        return $res;
    }

    /**
     * @desc 自动实付
     * @param int $applyId
     * @throws Exception
     */
    public static function autoPayment($applyId) {
        if (Utility::checkQueryId($applyId) && $applyId > 0) {
            $checkRes = self::checkIsCanAutoPayment($applyId);
            if ($checkRes['code'] == -2) {
                Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ', 付款申请：' . $applyId . '不能进行自动实付：' . json_encode($checkRes), CLogger::LEVEL_ERROR);
                return;
                //throw new Exception($checkRes['msg']);
            }
            if ($checkRes['code'] == 0) {
                $payApplyModel = PayApplication::model()->findByPk($applyId);
                if (empty($payApplyModel)) {
                    Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ', 付款申请：' . $applyId . '不存在！', CLogger::LEVEL_ERROR);
                    return;
                    //throw new Exception(BusinessError::outputError(OilError::$PAY_APPLICATION_NOT_EXIST, array('apply_id' => $applyId)));
                }
                if ($payApplyModel->status == PayApplication::STATUS_IN_AUTO_PAYMENT) {
                    $payType = self::getPaymentType($payApplyModel->subject_id);
                    //保存自动付款信息，一笔付款申请只能进行一次自动付款
                    $payOrderModel = MoneyPayOrder::model()->findByApplyId($applyId);
                    if (empty($payOrderModel)) {
                        $payOrderModel = new MoneyPayOrder();
                        $payOrderModel->apply_id = $applyId;
                        $payOrderModel->corporation_id = $payApplyModel->corporation_id;
                        $payOrderModel->pay_type = $payType;
                        $payOrderModel->payee = $payApplyModel->payee;
                        $payOrderModel->receive_account = $payApplyModel->account;
                        $payOrderModel->receive_account_name = $payApplyModel->account_name;
                        $payOrderModel->amount = $payApplyModel->amount;
                        $payOrderModel->currency = $payApplyModel->currency;
                        $payOrderModel->status = MoneyPayOrder::STATUS_NEW;
                        $payOrderModel->status_time = Utility::getDateTime();
                        $res = $payOrderModel->save();
                        if ($res !== true) {
                            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ', 保存资金系统付款请求信息失败', CLogger::LEVEL_ERROR);

                            throw new Exception(BusinessError::outputError(OilError::$MONEY_PAY_ORDER_SAVE_FAILED));
                        }
                    } else {
                        return;
                    }

                    $data['out_order_num'] = $applyId;
                    $data['order_pay_type'] = $payType;
                    $data['pay_main_body'] = $payApplyModel->corporation->name;
                    $data['collect_main_body'] = $payApplyModel->payee;
                    $data['collect_bank_account'] = $payApplyModel->account;
                    $data['collect_account_name'] = $payApplyModel->account_name;
                    $data['amount'] = $payApplyModel->amount;
                    $data['currency'] = self::getPaymentCurrency($payApplyModel->currency);
                    $data['require_pay_datetime'] = Utility::getDate();
                    $data['order_create_people'] = $payApplyModel->creator->name;
                    AMQPService::publishForAutoPayment($data);
                } else {
                    Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ', 调用资金系统付款时，本系统付款申请单状态异常', CLogger::LEVEL_ERROR);
                    return;
                    //throw new Exception(BusinessError::outputError(OilError::$MONEY_PAY_ORDER_NOT_ALLOW));
                }
            } else {
                Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ', 该付款申请校验是否可自动实付失败：' . json_encode($checkRes), CLogger::LEVEL_ERROR);
                return;
            }
        } else {
            throw new Exception(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
    }

    /**
     * @desc 调用资金系统进行实付
     * @param array $params
     * @throws Exception
     */
    public static function doAutoPayment($params) {
        $requiredParams = ['out_order_num', 'order_pay_type', 'pay_main_body', 'collect_main_body', 'collect_bank_account', 'collect_account_name', 'amount', 'currency', 'require_pay_datetime', 'order_create_people'];

        if (!Utility::checkRequiredParamsNoFilterInject($params, $requiredParams)) {
            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 必填参数未传，入参:' . json_encode($params), CLogger::LEVEL_ERROR);

            return;
        }

        $payOrderModel = MoneyPayOrder::model()->findByApplyId($params['out_order_num']);
        if (empty($payOrderModel)) {
            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 付款申请不存在，付款申请id:' . $params['out_order_num'], CLogger::LEVEL_ERROR);

            return;
        }
        if ($payOrderModel->apply->status != PayApplication::STATUS_IN_AUTO_PAYMENT) {
            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 付款申请单：' . $params['out_order_num'] . ',状态异常，不能进行自动付款', CLogger::LEVEL_ERROR);

            return;
        }

        $data = array(
            'cmd' => self::$service_map['sendPaymentOrder'],
            'data' => $params
        );

        $res = self::cmd($data);
        Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 调用资金系统实付入参:' . json_encode($params) . ', 资金系统调用结果:' . json_encode($res));

        if (array_key_exists('code', $res)) {
            $trans = Utility::beginTransaction();
            try {
                if ($res['code'] == 0) { //成功
                    if (Utility::isNotEmpty($res['data']) && !empty($res['data']['order_num'])) {
                        $payOrderModel->order_num = $res['data']['order_num'];
                        $payOrderModel->save();

                        //发布延时队列消息，查询实付状态
                        $msg['out_order_num'] = $res['data']['out_order_num'];
                        AMQPService::publishQueryAutoPayStatusToDelayQueue($msg, 3600);
                    }
                } elseif ($res['code'] == -1) {
                    Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 资金系统实付命令结果异常:' . json_encode($res), CLogger::LEVEL_ERROR);

                    throw new Exception(BusinessError::outputError(OilError::$MONEY_PAY_ORDER_ERROR));
                } else { //失败，告警+止付
                    $payOrderModel->status = self::ERROR_INTERFACE_CALLED_ERROR;
                    $payOrderModel->reason = !empty($res['msg']) ? $res['msg'] : '资金系统付款指令调用失败';
                    $payOrderModel->save();
                    self::afterAutoPaymentReject($payOrderModel);

                    //发送提醒
                    $msg = '付款申请编号为：' . $params['out_order_num'] . '的付款申请单调用资金系统付款指令失败，已自动止付，失败原因为：' . $res['msg'] . '，请知悉。';
                    self::sendAutoPaymentResultReminder($payOrderModel, $msg);
                }

                $trans->commit();
            } catch (Exception $e) {
                try {
                    $trans->rollback();
                } catch (Exception $ee) {
                }

                Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 处理自动实付结果异常:' . $e->getMessage(), CLogger::LEVEL_ERROR);
            }
        } else {
            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 资金系统付款指令调用失败:' . $res, CLogger::LEVEL_ERROR);
            throw new Exception('资金系统付款指令调用失败:' . json_encode($res));
        }
    }

    /**
     * @desc 查询资金系统实付状态
     * @param array [
     *      'out_order_num' => 'money000001'    #付款申请编号
     *      'order_num' => 'money000001'    #资金系统付款指令标识
     * ]
     */
    public static function queryAutoPaymentStatus($params) {
        if (!array_key_exists('order_num', $params) && !array_key_exists('out_order_num', $params)) {
            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 必填参数未传，入参:' . json_encode($params), CLogger::LEVEL_ERROR);

            return;
        }
        $errorMsg = '';
        if (array_key_exists('order_num', $params)) {
            $payOrderModel = MoneyPayOrder::model()->findByOrderNum($params['order_num']);
            $errorMsg = '资金系统付款信息不存在，资金系统付款编号:' . $params['order_num'];
        }
        if (array_key_exists('out_order_num', $params)) {
            $payOrderModel = MoneyPayOrder::model()->findByApplyId($params['out_order_num']);
            $errorMsg = '资金系统付款信息不存在，付款申请编号:' . $params['out_order_num'];
        }

        if (empty($payOrderModel)) {
            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ',' . $errorMsg, CLogger::LEVEL_ERROR);

            return;
        }
        //未处理，查询自动实付状态
        if ($payOrderModel->status == MoneyPayOrder::STATUS_NEW && $payOrderModel->apply->status == PayApplication::STATUS_IN_AUTO_PAYMENT) {
            $data = array(
                'cmd' => self::$service_map['queryPaymentStatus'],
                'data' => $params
            );

            $res = self::cmd($data);
            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 调用资金系统查询实付状态入参:' . json_encode($params) . ',资金系统调用结果:' . json_encode($res));

            if (array_key_exists('code', $res)) {
                if ($res['code'] == 0 && array_key_exists('data', $res)) {
                    try {
                        if (Utility::isNotEmpty($res['data']['data']) && array_key_exists('count', $res['data']) && $res['data']['count'] == 1)
                            self::processAutoPaymentResult($res['data']['data'][0]);
                    } catch (Exception $e) {
                        Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 处理资金系统实付结果失败：' . $e->getMessage(), CLogger::LEVEL_ERROR);
                    }

                    return;
                }
            }

            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 资金系统实付状态查询接口调用失败:' . json_encode($res), CLogger::LEVEL_ERROR);

            return;
        }
    }

    /**
     * @desc 处理自动实付结果
     * @param array $data
     * @throws Exception
     */
    public static function processAutoPaymentResult($data) {
        Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 资金系统实付结果:' . json_encode($data));
        $requiredParams = ['out_order_num', 'order_num'];

        if (!Utility::checkRequiredParamsNoFilterInject($data, $requiredParams)) {
            throw new Exception(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $mustParams = ['optor', 'pay_bank_name', 'pay_bank_account', 'bank_water', 'real_pay_date', 'order_status', 'pay_status'];
        if (!Utility::checkMustExistParams($data, $mustParams)) {
            throw new Exception(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        //更新自动付款记录相关信息
        $payOrderModel = MoneyPayOrder::model()->findByApplyId($data['out_order_num']);
        if (empty($payOrderModel)) {
            throw new Exception(BusinessError::outputError(OilError::$PAY_APPLICATION_NOT_EXIST, array('apply_id' => $data['out_order_num'])));
        }

        if ($payOrderModel->status != MoneyPayOrder::STATUS_NEW || $payOrderModel->apply->status != PayApplication::STATUS_IN_AUTO_PAYMENT) {
            throw new Exception('该单据已处理过自动实付结果，不能重复处理！');
        }

        $status = 0;
        //付款单已处理
        if ($data['pay_status'] != 0 && $data['order_status'] != 0) {
            if ($data['pay_status'] == 2) { //付款成功
                $status = 2;
            } else {
                if ($data['order_status'] == 2 || $data['order_status'] == 4) { //拒绝或驳回
                    $status = 4;
                }
            }
        }

        $payOrderModel->pay_bank_name = $data['pay_bank_name'];
        $payOrderModel->pay_bank_account = $data['pay_bank_account'];
        $payOrderModel->bank_water = $data['bank_water'];
        $payOrderModel->status = $status;
        $payOrderModel->status_time = Utility::getDateTime();
        $payOrderModel->operator = $data['optor'];
        $payOrderModel->reason = !empty($data['opt_msg']) ? $data['opt_msg'] : '';
        $payOrderModel->pay_date = $data['real_pay_date'];
        $trans = Utility::beginTransaction();
        try {
            $res = $payOrderModel->save();
            if (!$res) {
                throw new Exception(BusinessError::outputError(OilError::$OPERATE_FAILED, array('reason' => 'MoneyPayOrder save status failed')));
            }
            switch ($status) {
                case MoneyPayOrder::STATUS_PAID: //已实付
                    //发送提醒
                    $msg = '付款申请编号为：' . $payOrderModel->apply_id . '的付款申请单已实付完成，实付日期为：' . $payOrderModel->pay_date . '，请知悉。';
                    self::sendAutoPaymentResultReminder($payOrderModel, $msg);

                    //处理自动付款成功之后相关事件
                    self::afterAutoPaymentSuccess($data, $payOrderModel);
                    break;
                /*case MoneyPayOrder::STATUS_PAY_FAILED: //付款失败
                    //发送提醒
                    $msg = '付款申请编号为：' . $payOrderModel->apply_id . '的付款申请单自动实付失败，失败原因为：' . $payOrderModel->reason . '，请知悉。';
                    self::sendAutoPaymentResultReminder($payOrderModel, $msg);

                    //处理自动付款失败之后相关事件
                    self::afterAutoPaymentFailed($data['out_order_num']);
                    break;*/
                case MoneyPayOrder::STATUS_PAY_REJECT: //付款拒绝
                    //发送提醒
                    $urlHost = Mod::app()->params["url_host"];
                    $msg = '付款申请编号为：' . $payOrderModel->apply_id . '的付款申请单被操作人员：' . $data['optor'] . '驳回/拒绝，驳回原因为：' . $payOrderModel->reason . '，请<a href="http://' . $urlHost . '/pay/detail?id=' . $payOrderModel->apply_id . '">及时处理</a>。';
                    self::sendAutoPaymentResultReminder($payOrderModel, $msg);

                    //处理自动付款拒绝之后相关事件
                    self::afterAutoPaymentReject($payOrderModel);
                    break;
                case MoneyPayOrder::STATUS_NEW: // 未处理，加入延时队列，延时一小时后查询状态
                    $msg['out_order_num'] = $data['out_order_num'];
                    AMQPService::publishQueryAutoPayStatusToDelayQueue($msg, 3600);
                    break;
                default:
                    Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 资金系统实付状态异常，订单码:' . $data['order_status'] . '，付款码:' . $data['pay_status'], CLogger::LEVEL_ERROR);
                    break;
            }

            $trans->commit();
        } catch (Exception $e) {
            try {
                $trans->rollback();
            } catch (Exception $ee) {
            }

            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 处理自动实付结果异常:' . $e->getMessage(), CLogger::LEVEL_ERROR);
        }
    }

    /**
     * @desc 自动付款成功之后，处理相关事件进行自动实付
     * @param array $data
     * @param object $payOrderModel
     * @throws Exception
     */
    private static function afterAutoPaymentSuccess($data, $payOrderModel) {
        $apply = $payOrderModel->apply;
        if (empty($apply)) {
            throw new Exception(BusinessError::outputError(OilError::$PAY_APPLICATION_NOT_EXIST, array('apply_id' => $payOrderModel->apply_id)));
        }
        //生成已提交的实付单
        $paymentModel = Payment::model()->find('apply_id=:applyId and amount=:amount and currency=:currency', array('applyId' => $data['out_order_num'], 'amount' => $payOrderModel->amount, 'currency' => $payOrderModel->currency));
        if (empty($paymentModel)) {
            $paymentModel = new Payment();
            $paymentModel->payment_id = IDService::getPayConfirmId();
            $paymentModel->apply_id = $payOrderModel->apply_id;
            $paymentModel->pay_date = $data['real_pay_date'];
            $paymentModel->amount = $payOrderModel->amount;
            $paymentModel->currency = $payOrderModel->currency;
            $paymentModel->exchange_rate = $apply->exchange_rate;
            $paymentModel->amount_cny = $paymentModel->amount * $paymentModel->exchange_rate;
            $paymentModel->payment_no = $data['bank_water'];
            $paymentModel->remark = '自动实付';

            $accountModel = Account::model()->find('corporation_id=' . $payOrderModel->corporation_id . ' and account_no="' . $data['pay_bank_account'] . '" and bank_name="' . $data['pay_bank_name'] . '"');
            if (empty($accountModel)) {
                $accountModel = new Account();
                $accountModel->account_no = $payOrderModel->pay_bank_account;
                $accountModel->bank_name = $payOrderModel->pay_bank_name;
                $accountModel->corporation_id = $payOrderModel->corporation_id;
                $accountModel->account_name = $payOrderModel->corp->name;
                $accountModel->status = 1;
            } else {
                if ($accountModel->status == 0) {
                    $accountModel->status = 1;
                }
            }
            $accountModel->save();

            $paymentModel->account_id = $accountModel->account_id;
            $paymentModel->operator = $data['optor'];
        }
        $paymentModel->status = Payment::STATUS_SUBMITED;
        $paymentModel->save();

        //更新付款申请已实付金额

        $apply->amount_paid += $paymentModel->amount;
        $apply->amount_paid_cny += $paymentModel->amount_cny;
        $apply->save();

        //完成付款
        if (bccomp($apply->amount, $apply->amount_paid) == 0) {
            PayService::donePaidPayApplication($apply);
        }

        //调整合作方额度(合同下付款且是非税款)
        if (($apply->type == PayApplication::TYPE_CONTRACT || $apply->type == PayApplication::TYPE_SELL_CONTRACT) && in_array($apply->subject_id, explode(',', ConstantMap::GOODS_FEE_SUBJECT_ID))) {
            if (!($apply->subject_id == ConstantMap::TAX_DEPOSIT_SUBJECT_ID && $apply->contract->agent_type == ConstantMap::AGENT_TYPE_PURE)) {
                $payConfirmEntity = \ddd\infrastructure\DIService::getRepository(\ddd\domain\iRepository\payment\IPayConfirmRepository::class)->findByPk($paymentModel->payment_id);
                if (empty($payConfirmEntity->payment_id)) {
                    throw new \ddd\infrastructure\error\ZEntityNotExistsException($paymentModel->payment_id, \ddd\domain\entity\payment\PayConfirm::class);
                }

                $res = \ddd\application\payment\PaymentService::service()->submitPayConfirm($paymentModel->payment_id, $payConfirmEntity);
                if ($res !== true) {
                    throw new Exception($res);
                }
            }
        }

        //触发利润报表付款实付事件
        if ($paymentModel->status == Payment::STATUS_SUBMITED) {
            // 触发利润报表实付成功事件
            Mod::log('自动付款成功，触发利润报表事件--AutoPayConfirm Profit:apply_type:' . $apply->type . ';contract_id:' . $apply->contract_id . ';project_id:' . $apply->project_id . ';apply_id:' . $apply->apply_id . ';payment_id:' . $paymentModel->payment_id);

            AMQPService::publishPayConfirm($apply->project_id);//预估利润报表

            // 触发利润报表实付成功事件 单合同
            if (in_array($apply->type, [PayApplication::TYPE_CONTRACT, PayApplication::TYPE_SELL_CONTRACT])) {
                \ddd\Profit\Application\PayReceiveEventService::service()->onPayConfirm($apply->contract_id, $apply->subject_id);
            }
            //多合同付款
            if ($apply->type == PayApplication::TYPE_MULTI_CONTRACT) {
                $details = $apply->details;
                if (Utility::isNotEmpty($details)) {
                    foreach ($details as $detail) {
                        \ddd\Profit\Application\PayReceiveEventService::service()->onPayConfirm($detail->contract_id, $apply->subject_id);
                    }
                }
            }

            //项目下付款实付
            if ($apply->type == PayApplication::TYPE_PROJECT) {
                \ddd\Profit\Application\ProjectPayEventService::service()->onPayConfirm($apply->project_id, $paymentModel->payment_id);
            }
        }
    }

    /**
     * @desc 自动付款拒绝之后，处理相关事件进行自动止付
     * @param object $payOrderModel
     * @throws Exception
     */
    private static function afterAutoPaymentReject($payOrderModel) {
        if (empty($payOrderModel)) {
            throw new Exception(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        //生成审核通过的止付信息
        $applyModel = $payOrderModel->apply;
        if (empty($applyModel)) {
            throw new Exception(BusinessError::outputError(OilError::$PAY_APPLICATION_NOT_EXIST, array('apply_id' => $payOrderModel->apply_id)));
        }

        $extra = $applyModel->extra;
        if (empty($extra)) {
            $extra = new PayApplicationExtra();
            $extra->apply_id = $payOrderModel->apply_id;
        } else {
            if ($extra->status == PayApplicationExtra::STATUS_CHECKING || $applyModel->status != PayApplication::STATUS_IN_AUTO_PAYMENT) {
                throw new Exception(BusinessError::outputError(OilError::$PAY_STOP_NOT_ALLOW_EDIT));
            }
        }
        $applyModel->status = PayApplication::STATUS_STOP;
        $applyModel->save();

        $totalStopAmount = 0;
        $paidAmount = $applyModel->amount_paid;
        if (Utility::isNotEmpty($applyModel->details)) {
            if (bccomp($paidAmount, $totalStopAmount, 0) != 0) {
                throw new Exception('付款计划合计实付金额与付款单实付金额不一致');
            }
        }
        $extra->status = PayApplicationExtra::STATUS_PASS;
        $extra->stop_remark = $payOrderModel->reason;
        if (empty($extra->stop_code)) {
            $extra->stop_code = 'ZF' . IDService::getPayStopId();
        }
        $extra->save();

        if ($applyModel->type == PayApplication::TYPE_CONTRACT || $applyModel->type == PayApplication::TYPE_SELL_CONTRACT) {
            if (!empty($applyModel->contract_id) && Utility::isNotEmpty($applyModel->details)) {
                foreach ($applyModel->details as $detail) {
                    $res = PayService::updatePaidAmount($detail->detail_id, $detail->amount_paid);
                    if (!$res) {
                        throw new Exception("更新付款申请详情计划失败");
                    }

                    $res = PaymentPlanService::updatePaidAmount($detail->plan_id, -($detail->amount - $detail->amount_paid));
                    if (!$res) {
                        throw new Exception("更新合同付款计划失败");
                    }
                }
            }
        }
    }

    /**
     * @desc 自动付款拒绝之后，处理相关事件
     * @param int $applyId
     * @throws Exception
     */
    private static function afterAutoPaymentFailed($applyId) {
        $apply = PayApplication::model()->findByPk($applyId);
        if (empty($apply)) {
            throw new Exception(BusinessError::outputError(OilError::$PAY_APPLICATION_NOT_EXIST, array('apply_id' => $applyId)));
        }

        // 已申请金额减去本次申请金额
        if (Utility::isNotEmpty($apply->details)) {
            foreach ($apply->details as $detail) {
                $plan = PaymentPlan::model()->findByPk($detail->plan_id);
                if (!empty($plan)) {
                    $plan->updateByPk($plan->plan_id, array('amount_paid' => ($plan->amount_paid - $detail->amount)));
                }
            }
        }

        // 更新状态
        $res = $apply->updateByPk($apply->apply_id, array(
            "status" => PayApplication::STATUS_AUTO_PAYMENT_FAILED,
            "status_time" => Utility::getDateTime(),
            "update_time" => Utility::getDateTime(),
        ), "status=" . PayApplication::STATUS_IN_AUTO_PAYMENT
        );

        if ($res != 1) {
            throw new Exception('自动实付失败更新付款单状态失败！');
        }
    }

    /**
     * @desc 发送自动付款结果消息提醒
     * @param object $payOrderModel
     * @param string $msg
     * @throws Exception
     */
    private static function sendAutoPaymentResultReminder($payOrderModel, $msg) {
        if (empty($payOrderModel)) {
            throw new Exception(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $user = SystemUser::getUser($payOrderModel->apply->create_user_id);
        AMQPService::publishWinxinReminder([$user['identity']], $msg);

        AMQPService::publishEmail($payOrderModel->apply->create_user_id, '石油系统提醒', $msg);
    }

    /**
     * @desc 根据石油系统付款类型获取资金系统付款类型
     * @param int $subjectId
     * @return int
     */
    private static function getPaymentType($subjectId) {
        switch ($subjectId) {
            default:
                return 17;
        }
    }

    /**
     * @desc 根据石油系统币种获取资金系统付款币种
     * @param int $currency
     * @return int
     */
    private static function getPaymentCurrency($currency) {
        switch ($currency) {
            case "1":
                return 'cny';
            case "2":
                return 'usd';
        }
    }

    /**
     * @desc 生成接口请求秘钥
     * @param array $params
     * @param string $interfaceKey
     * @return string
     */
    public static function generateSecret($params, $interfaceKey = '') {
        $strs = [];
        if (Utility::isNotEmpty($params)) {
            ksort($params);
            foreach ($params as $key => $value) {
                if (in_array($key, ['secret', 'sessionToken'])) {
                    continue;
                }
                $strs[] = $key . '=' . $value;
            }
        }
        if (empty($interfaceKey)) {
            $interfaceKey = Mod::app()->params['money_system_config']['money_secret_key'];
        }
        $secret = implode('&', $strs) . $interfaceKey;

        Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' 需加密的参数串:' . $secret);

        return sha1($secret);
    }
}