<?php

/**
 * Created by vector.
 * DateTime: 2017/10/27 11:20
 * Describe：
 */
class PayConfirmController extends AttachmentController
{
    public $businessId = 13;

    public function pageInit() {
        $this->filterActions = "saveFile,getFile,delFile,detail,getAccPayDetail,getAccounts,submit,print";
        $this->rightCode = "payConfirm";
        $this->attachmentType = Attachment::C_PAYMENT;
        $this->newUIPrefix = 'new_';
    }


    public function actionIndex() {
        $attr = $this->getSearch();//$_GET[search];
        if (!is_array($attr) || !array_key_exists("status", $attr)) {
            $attr["status"] = "0";
        }

        $query = "";
        $status = "";
        if (isset($attr["status"]) && $attr["status"] == "0") {
            $status = "0";
            $query = " and a.status>=" . PayApplication::STATUS_CHECKED . " and a.status<=" . PayApplication::STATUS_IN_MANUAL_PAYMENT;
            unset($attr["status"]);
        } else if ($attr["status"] == "1") {
            $status = "1";
            $query = " and a.status>" . PayApplication::STATUS_IN_MANUAL_PAYMENT;
            unset($attr["status"]);
        } else if ($attr['status'] == '2') {
            $status = "2";
            $query = " and ae.status =" . PayApplicationExtra::STATUS_CHECKING;
            unset($attr["status"]);
        } else if ($attr['status'] == '3') {
            $status = "3";
            $query = " and ae.status =" . PayApplicationExtra::STATUS_PASS;
            unset($attr["status"]);
        }

        $start_date = '';
        $end_date = '';
        if (!empty($attr["start_date"])) {
            $start_date = $attr["start_date"];
            unset($attr["start_date"]);
        }

        if (!empty($attr["end_date"])) {
            $end_date = $attr["end_date"];
            unset($attr["end_date"]);
        }

        if (!empty($start_date) && !empty($end_date))
            $query .= " and a.create_time between '" . $start_date . "' and '" . date("Y-m-d", strtotime("$end_date +1 day")) . "'";
        else if (!empty($start_date))
            $query .= " and a.create_time between '" . $start_date . "' and '" . date("Y-m-d", strtotime("+1 day")) . "'";
        else if (!empty($end_date))
            $query .= " and a.create_time between '" . date('Y-m-d') . "' and '" . date("Y-m-d", strtotime("$end_date +1 day")) . "'";

        $user = SystemUser::getUser(Utility::getNowUserId());

        $sql = "select {col}"
            . " from t_pay_application a "
            . " left join t_corporation c on c.corporation_id=a.corporation_id  "
            . " left join t_project p on p.project_id=a.project_id  "
            . " left join t_system_user u on a.create_user_id=u.user_id "
            . " left join t_pay_application_extra ae on ae.apply_id = a.apply_id"
            . $this->getWhereSql($attr);
        $sql .= $query;
        $sql .= " and a.corporation_id in (" . $user['corp_ids'] . ") and a.status>=" . PayApplication::STATUS_CHECKED . " order by a.apply_id desc {limit} ";

        $payStatusField = "case when ae.status=" . PayApplicationExtra::STATUS_CHECKING . " then 2 ";
        $payStatusField .= "when ae.status=" . PayApplicationExtra::STATUS_PASS . " then 3 ";
        $payStatusField .= "when a.status>=" . PayApplication::STATUS_CHECKED . " and a.status<=" . PayApplication::STATUS_IN_MANUAL_PAYMENT . " then 0 ";
        $payStatusField .= " else 1 end as pay_status";
        $fields = " a.*,{$payStatusField},c.name as corporation_name,u.name as user_name,p.project_code, (a.amount - a.amount_paid) as amount_stop, ae.status as pay_stop_status";
        $data = $this->queryTablesByPage($sql, $fields);

        if ($status == "0" || $status == "1" || $status == "2" || $status == "3")
            $attr["status"] = $status;

        if (!empty($start_date))
            $attr['start_date'] = $start_date;
        if (!empty($end_date))
            $attr['end_date'] = $end_date;

        $data['search'] = $attr;

        $this->render('index', $data);
    }

    /**
     * @name:actionPrint
     * @desc: 打印页面
     * @param: apply_id 数组
     * @throw:
     * @return:
     */
    public function actionPrint() {
        //$attr = $this->getSearch();//$_GET[search];
        $attr = $_GET[search];

        $sql = "select {col}"
            . " from t_pay_application a "
            . " left join t_corporation c on c.corporation_id=a.corporation_id  "
            . " left join t_project p on p.project_id=a.project_id  "
            . " left join t_system_user u on a.create_user_id=u.user_id "
            . " left join t_pay_application_extra ae on ae.apply_id = a.apply_id"
            . " left join t_finance_subject s on s.subject_id = a.subject_id"
            . $this->getWhereSql($attr);
        $user = SystemUser::getUser(Utility::getNowUserId());
        $sql .= " and a.apply_id in (" . $attr . ")";
        $sql .= " and a.corporation_id in (" . $user['corp_ids'] . ") and a.status>=" . PayApplication::STATUS_CHECKED . " group by a.apply_id order by a.apply_id desc  {limit} ";

        $payStatusField = "case when ae.status=" . PayApplicationExtra::STATUS_CHECKING . " then 2 ";
        $payStatusField .= "when ae.status=" . PayApplicationExtra::STATUS_PASS . " then 3 ";
        $payStatusField .= "when a.status>=" . PayApplication::STATUS_CHECKED . " and a.status<=" . PayApplication::STATUS_IN_MANUAL_PAYMENT . " then 0 ";
        $payStatusField .= " else 1 end as pay_status";
        $fields = " a.*,{$payStatusField},c.name as corporation_name,u.name as user_name,p.project_code,p.type project_type,s.name subject_name, (a.amount - a.amount_paid) as amount_stop, ae.status as pay_stop_status";
        $data = $this->queryTablesByPage($sql, $fields);
        //print_r($data);
        $data['data']['businessId'] = $this->businessId;
        $this->render('print', $data['data']);
    }

    /**
     * 判断是否可以修改，子类需要修改该方法
     * @param $status
     * @return bool
     */
    public function checkIsCanEdit($status) {
        if ($status < Payment::STATUS_SUBMITED) {
            return true;
        } else
            return false;
    }

    public function actionAdd() {
        $id = Mod::app()->request->getParam("id");
        if (!Utility::checkQueryId($id))
            $this->renderError("参数错误");

        $apply = PayApplication::model()->with("details", "contract", "details.payment", "extra")->findByPk($id);
        if (empty($apply))
            $this->renderError("付款申请信息不存在");
        if ($apply->status == PayApplication::STATUS_IN_AUTO_PAYMENT)
            $this->renderError("当前付款申请单已对接资金系统自动实付中，不能进行手动实付！");

        $map = Map::$v;
        $data['payment_id'] = IDService::getPayConfirmId();
        $data['corporation_id'] = $apply->corporation_id;
        $data['apply_id'] = $apply->apply_id;
        $data['currency'] = $apply->currency;
        $data['balance_amount'] = $apply->amount - $apply->amount_paid;
        $data['currency_ico'] = $map['currency'][$data['currency']]['ico'];

        $accounts = Account::model()->findAllToArray(array("condition" => "corporation_id=" . $data['corporation_id'], "order" => " account_id asc"));

        $confirms = PayService::getAllPayComfirmInfo($apply->apply_id);
        if ($confirms[0]['status'] == Payment::STATUS_SAVED)
            $this->renderError("当前状态下不可添加", $this->mainUrl);
        // $payInfo = PayService::getUpAllPay($data[0]['project_id']);
        $mark = 1;
        $this->pageTitle = "付款实付";
        $this->render("edit", array(
            "data" => $data,
            "model" => $apply,
            "accounts" => $accounts,
            "payInfo" => $confirms,
            "mark" => $mark
        ));
    }

    public function actionEdit() {
        $id = Mod::app()->request->getParam("id");
        if (!Utility::checkQueryId($id))
            $this->renderError("参数错误", $this->mainUrl);

        $apply = PayApplication::model()->with("details", "contract", "details.payment", "extra")->findByPk($id);
        if (empty($apply))
            $this->renderError("付款申请信息不存在", $this->mainUrl);

        $confirms = PayService::getAllPayComfirmInfo($id);
        if ($confirms[0]['status'] != Payment::STATUS_SAVED)
            $this->renderError("当前状态下不可修改", $this->mainUrl);

        $map = Map::$v;
        $data = $confirms[0];
        $data['corporation_id'] = !empty($data['account_corp_id']) ? $data['account_corp_id'] : $apply->corporation_id;
        $data['bank_name'] = $data['account_id'];
        $data['apply_id'] = $apply->apply_id;
        $data['currency'] = $apply->currency;
        $data['balance_amount'] = $apply->amount - $apply->amount_paid;
        $data['currency_ico'] = $map['currency'][$data['currency']]['ico'];

        $accounts = Account::model()->findAllToArray(array("condition" => "corporation_id=" . $data['corporation_id'], "order" => " account_id asc"));

        if (bccomp($data['exchange_rate'], 0) < 1) {
            unset($data['exchange_rate']);
        }
        $mark = 1;
        unset($confirms[0]);

        $this->pageTitle = "付款实付";
        $this->render("edit", array(
            "data" => $data,
            "model" => $apply,
            "accounts" => $accounts,
            "payInfo" => $confirms,
            "mark" => $mark
        ));
    }


    public function actionGetAccounts() {
        $id = Mod::app()->request->getParam('corporation_id');
        if (empty($id))
            $this->returnError("参数错误");

        $data = Account::model()->findAllToArray(array("condition" => "corporation_id=" . $id, "order" => " account_id asc"));

        $this->returnSuccess($data);
    }


    public function actionDetail() {
        $id = Mod::app()->request->getParam("id");
        if (!Utility::checkQueryId($id))
            $this->renderError("参数错误");

        $apply = PayApplication::model()->with("details", "contract", "details.payment", "extra")->findByPk($id);
        if (empty($apply))
            $this->renderError("付款申请信息不存在");

        $payInfo = PayService::getAllPayComfirmInfo($apply->apply_id);
        $data = $payInfo[0];

        $this->pageTitle = "付款实付详情";
        $this->render('detail', array(
            "data" => $data,
            "model" => $apply,
            "payInfo" => $payInfo
        ));
    }


    public function actionSave() {
        $params = $_POST["data"];
        // print_r($params);die;

        $requiredParams = array('apply_id', 'currency', 'payment_id', 'pay_date', 'amount');
        $filterInjectParams = Utility::checkRequiredParams($params, $requiredParams);
        if (!$filterInjectParams['isValid'])
            $this->returnError(BusinessError::outputError(OilError::$REQUIRED_PARAMS_CHECK_ERROR));
        $params = $filterInjectParams['params'];

        if (PayService::isPendingPayStop($params['apply_id']))
            $this->returnError(BusinessError::outputError(OilError::$PENDING_PAY_STOP), -2);

        $obj = Payment::model()->findByPk($params['payment_id']);
        $nowUserId = Utility::getNowUserId();
        $nowTime = new CDbExpression("now()");
        if (empty($obj->payment_id)) {
            $obj = new Payment();
            $obj->create_time = $nowTime;
            $obj->create_user_id = $nowUserId;
        } else {
            if (!$this->checkIsCanEdit($obj->status)) {
                $this->returnError("当前状态下不可操作实付信息！");
            }
        }
        $obj->setAttributes($params, false);

        if (empty($params['isSave'])) {
            $obj->status = Payment::STATUS_SUBMITED;
            $currUser = Utility::getNowUser();
            $obj->operator = $currUser['name'];
        } else {
            $obj->status = Payment::STATUS_SAVED;
        }

        if ($params['currency'] == 1)
            $obj->exchange_rate = 1;

        $obj->update_time = $nowTime;
        $obj->update_user_id = $nowUserId;

        if ($obj->apply->status == PayApplication::STATUS_IN_AUTO_PAYMENT) {
            $this->returnError("当前付款申请单已对接资金系统自动实付中，不能进行手动实付！");
        }

        $logRemark = ActionLog::getEditRemark($obj->isNewRecord, "付款实付");
        $trans = Utility::beginTransaction();
        try {

            $obj->save();

            if (empty($params['isSave'])) {
                // PayService::updateAmountPaid($obj->apply_id, $obj->amount);
                $obj->apply->amount_paid += $obj->amount;
                $obj->apply->amount_paid_cny += $obj->amount_cny;
                $obj->apply->status = PayApplication::STATUS_IN_MANUAL_PAYMENT; //手动实付中
                $obj->apply->status_time = Utility::getDateTime();
                $obj->apply->save();
                if (bccomp($obj->apply->amount, $obj->apply->amount_paid) == 0) {
                    PayService::donePaidPayApplication($obj->apply);
                    TaskService::doneTask($obj->apply_id, Action::ACTION_ACTUAL_PAY, ActionService::getActionRoleIds(Action::ACTION_ACTUAL_PAY));
                }

                //调整合作方额度(合同下付款且是非税款)
                if (($obj->apply->type == PayApplication::TYPE_CONTRACT || $obj->apply->type == PayApplication::TYPE_SELL_CONTRACT) && in_array($obj->apply->subject_id, explode(',', ConstantMap::GOODS_FEE_SUBJECT_ID))) {
                    if (!($obj->apply->subject_id == ConstantMap::TAX_DEPOSIT_SUBJECT_ID && $obj->apply->contract->agent_type == ConstantMap::AGENT_TYPE_PURE)) {
                        $payConfirmEntity = \ddd\infrastructure\DIService::getRepository(\ddd\domain\iRepository\payment\IPayConfirmRepository::class)->findByPk($obj->payment_id);
                        if (empty($payConfirmEntity->payment_id)) {
                            throw new \ddd\infrastructure\error\ZEntityNotExistsException($obj->payment_id, \ddd\domain\entity\payment\PayConfirm::class);
                        }

                        $res = \ddd\application\payment\PaymentService::service()->submitPayConfirm($obj->payment_id, $payConfirmEntity);
                        if ($res !== true) {
                            throw new Exception($res);
                        }
                    }
                }

                /*if($obj->currency==1){
                    if(bccomp($params['balance_amount'],$params['amount'])==0)
                        PayService::donePaidPayApplication($obj->apply);
                }else{
                    if(bccomp($params['balance_amount'], $params['currnecy_amount'])==0)
                        PayService::donePaidPayApplication($obj->apply);
                }*/
            }

            // TaskService::addTasks(Action::ACTION_11, $contract->contract_id, ActionService::getActionRoleIds(Action::ACTION_11), 0, $contract->corporation_id);

            $trans->commit();
            Utility::addActionLog(json_encode($obj->oldAttributes), $logRemark, "Payment", $obj->payment_id);
            if ($obj->status == Payment::STATUS_SUBMITED) {
                //发出mq事件
                \AMQPService::publishPayConfirm($obj->apply->project_id);
                // 触发利润报表实付成功事件 单合同
                if (in_array($obj->apply->type, [PayApplication::TYPE_CONTRACT, PayApplication::TYPE_SELL_CONTRACT])) {
                    \ddd\Profit\Application\PayReceiveEventService::service()->onPayConfirm($obj->apply->contract_id, $obj->apply->subject_id);
                }
                //多合同付款
                if ($obj->apply->type == PayApplication::TYPE_MULTI_CONTRACT) {
                    $details = $obj->apply->details;
                    if (Utility::isNotEmpty($details)) {
                        foreach ($details as $detail) {
                            \ddd\Profit\Application\PayReceiveEventService::service()->onPayConfirm($detail->contract_id, $obj->apply->subject_id);
                        }
                    }
                }
                //项目下付款实付
                if ($obj->apply->type == PayApplication::TYPE_PROJECT) {
                    \ddd\Profit\Application\ProjectPayEventService::service()->onPayConfirm($obj->apply->project_id, $obj->payment_id);
                }
            }

            $this->returnSuccess($obj->apply_id);

        } catch (Exception $e) {
            try {
                $trans->rollback();
            } catch (Exception $ee) {
                Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $ee->getMessage(), CLogger::LEVEL_ERROR);
            }

            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $e->getMessage(), CLogger::LEVEL_ERROR);

            $this->returnError(BusinessError::outputError(OilError::$PAY_CONFIRM_SAVE_ADD_ERROR, array('reason' => $e->getMessage())));
        }

    }


    public function actionSubmit() {
        $id = Mod::app()->request->getParam("id");
        if (!Utility::checkQueryId($id)) {
            $this->renderError("参数错误！", $this->mainUrl);
        }

        $obj = Payment::model()->findByPk($id);
        if (!$this->checkIsCanEdit($obj->status))
            $this->returnError("当前状态下不可提交付款实付信息！");

        $oldStatus = $obj->status;

        if ($obj->apply->status == PayApplication::STATUS_IN_AUTO_PAYMENT) {
            $this->returnError("当前付款申请单已对接资金系统自动实付中，不能进行手动实付！");
        }

        $trans = Utility::beginTransaction();
        try {
            $obj->status = Payment::STATUS_SUBMITED;
            $obj->update_time = new CDbExpression("now()");
            $obj->update_user_id = Utility::getNowUserId();
            $obj->save();

            // PayService::updateAmountPaid($obj->apply_id, $obj->amount);
            $obj->apply->amount_paid += $obj->amount;
            $obj->apply->amount_paid_cny += $obj->amount_cny;
            $obj->apply->status = PayApplication::STATUS_IN_MANUAL_PAYMENT;
            $obj->apply->status_time = Utility::getDateTime();
            $obj->apply->save();

            if (bccomp($obj->apply->amount, $obj->apply->amount_paid) == 0) {
                PayService::donePaidPayApplication($obj->apply);
                TaskService::doneTask($obj->apply_id, Action::ACTION_ACTUAL_PAY, ActionService::getActionRoleIds(Action::ACTION_ACTUAL_PAY));
            }

            //调整合作方额度(合同下付款且是非税款)
            if (($obj->apply->type == PayApplication::TYPE_CONTRACT || $obj->apply->type == PayApplication::TYPE_SELL_CONTRACT) && in_array($obj->apply->subject_id, explode(',', ConstantMap::GOODS_FEE_SUBJECT_ID))) {
                if (!($obj->apply->subject_id == ConstantMap::TAX_DEPOSIT_SUBJECT_ID && $obj->apply->contract->agent_type == ConstantMap::AGENT_TYPE_PURE)) {
                    $payConfirmEntity = \ddd\infrastructure\DIService::getRepository(\ddd\domain\iRepository\payment\IPayConfirmRepository::class)->findByPk($id);
                    if (empty($payConfirmEntity->payment_id)) {
                        throw new \ddd\infrastructure\error\ZEntityNotExistsException($id, \ddd\domain\entity\payment\PayConfirm::class);
                    }

                    $res = \ddd\application\payment\PaymentService::service()->submitPayConfirm($id, $payConfirmEntity);
                    if ($res !== true) {
                        throw new Exception($res);
                    }
                }
            }

            // print_r(bccomp($obj->apply->amount,($obj->apply->amount_paid+$obj->amount)));die;
            /*if($obj->currency==1){
                if(bccomp($obj->apply->amount,($obj->apply->amount_paid+$obj->amount))==0)
                    PayService::donePaidPayApplication($obj->apply);
            }else{
                if(bccomp($obj->apply->amount, $params['currnecy_amount'])==0)
                    PayService::donePaidPayApplication($obj->apply);
            }*/


            $trans->commit();
            Utility::addActionLog(json_encode(array('oldStatus' => $oldStatus)), "提交付款实付", "Payment", $obj->payment_id);


            $this->returnSuccess();

        } catch (Exception $e) {
            try {
                $trans->rollback();
            } catch (Exception $ee) {
                Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $ee->getMessage(), CLogger::LEVEL_ERROR);
            }

            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $e->getMessage(), CLogger::LEVEL_ERROR);

            $this->returnError(BusinessError::outputError(OilError::$PAY_CONFIRM_SAVE_ADD_ERROR, array('reason' => $e->getMessage())));
        }

    }


}