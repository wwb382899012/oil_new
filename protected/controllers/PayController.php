<?php
/**
 * Created by youyi000.
 * DateTime: 2017/10/23 11:04
 * Describe：
 */

class PayController extends AttachmentController
{
    public function pageInit() {
        $this->attachmentType = Attachment::C_PAY_APPLICATION;
        $this->authorizedActions = array("route", "contracts", "getProjects", "getContracts", "getAccount");
        $this->rightCode = "pay_application";
        $this->newUIPrefix = 'new_';
    }

    public function actionIndex() {
//        $attr=$_GET["search"];
        $attr = $this->getSearch();
        $sql = "select {col} 
              from t_pay_application a 
              left join t_corporation c on c.corporation_id=a.corporation_id 
              left join t_finance_subject fs on fs.subject_id=a.subject_id 
              left join t_contract co on co.contract_id=a.contract_id 
              left join t_contract_file cf on cf.contract_id=co.contract_id and cf.is_main=1 and cf.type=1 
              left join t_project p on p.project_id = a.project_id
              left join t_system_user su on su.user_id=a.create_user_id
              ".$this->getWhereSql($attr)." and ".AuthorizeService::getUserDataConditionString("a")." and a.status<>" . PayApplication::STATUS_NOT_SAVE . " order by a.apply_id desc {limit}";
        $fields='a.*,(a.amount - a.amount_paid) as amount_stop, c.name as corp_name,fs.name as subject_name,co.type as contract_type,co.category as contract_category,co.contract_code,su.name as create_name, p.project_code, p.project_id, cf.code_out';
        $data=$this->queryTablesByPage($sql,$fields);
        $this->render("index",$data);
    }
    
    public function actionAdd() {
        $type = Mod::app()->request->getParam("type");


        $model = new PayApplication();
        $model->currency = 1;
        switch ($type) {
            case PayApplication::TYPE_CONTRACT:
            case PayApplication::TYPE_SELL_CONTRACT:
                $view = "contractEdit";

                $contractId = Mod::app()->request->getParam("contractId");
                if (!Utility::checkQueryId($contractId))
                    $this->renderError("参数错误");

                $contract = Contract::model()->with("corporation", "project", "partner", "filesBase", "payPlans")->findByPk($contractId);
                if (empty($contract))
                    $this->renderError("合同不存在");

                $data["contract"] = $contract;
                $data["actual_paid_amount"] = PayService::getContractActualPaidAmount($contractId);

                $attributes = $contract->getAttributesWithRelations(null);

                $data["payments"] = $attributes["payPlans"];

                $model->corporation_id = $contract->corporation_id;
                $model->project_id = $contract->project_id;
                $model->contract_id = $contract->contract_id;
                if (is_array($contract->payPlans) && count($contract->payPlans) > 0)
                    $model->currency = $contract->payPlans[0]["currency"];
                break;

            case PayApplication::TYPE_MULTI_CONTRACT:
                $view = "multiEdit";

                break;
            case PayApplication::TYPE_CORPORATION:
            case PayApplication::TYPE_CLAIM:
            case PayApplication::TYPE_PROJECT:
                $view = "edit";
                break;
            default:
                $view = "edit";
                break;
        }

        $data["type"] = $type;

        $model->setId();

        $model->type = $type;

        $this->pageTitle = Map::$v["pay_application_type"][$type] . "申请";
        $data["data"] = $model->getAttributes(null);
        $data["business_directors"] = UserService::getBusinessDirectors();
        $this->render($view, $data);
    }

    public function actionEdit() {
        $id = Mod::app()->request->getParam("id");
        if (!Utility::checkQueryId($id))
            $this->renderError("参数错误");

        $apply = PayApplication::model()->with("details", "extra", "details.contract", "details.project")->findByPk($id);
        if (empty($apply))
            $this->renderError("付款申请不存在");


        if (!$apply->isCanEdit())
            $this->renderError("当前付款申请不允许修改");

        switch ($apply->type) {
            case PayApplication::TYPE_CONTRACT:
            case PayApplication::TYPE_SELL_CONTRACT:
                $view = "contractEdit";

                $contract = Contract::model()->with("corporation", "project", "partner", "filesBase", "payPlans")
                    ->findByPk($apply->contract_id);
                if (empty($contract))
                    $this->renderError("合同不存在");

                $data["contract"] = $contract;
                $data["actual_paid_amount"] = PayService::getContractActualPaidAmount($apply->contract_id);

                $attributes = $contract->getAttributesWithRelations(null);

                $data["payments"] = array_key_exists('payPlans', $attributes) ? $attributes["payPlans"] : array();

                $details = array();
                if (Utility::isNotEmpty($apply['details'])) {
                    foreach ($apply["details"] as $k => $v) {
                        $details[$v["plan_id"]] = $v;
                    }
                }
                if (Utility::isNotEmpty($data['payments'])) {
                    foreach ($data["payments"] as $k => $v) {
                        if (isset($details[$v["plan_id"]])) {
                            $data["payments"][$k]["checked"] = 1;
                            $data["payments"][$k]["pay_amount"] = $details[$v["plan_id"]]["amount"];
                        }
                    }
                }

                break;

            case PayApplication::TYPE_MULTI_CONTRACT:
                $view = "multiEdit";
                $data["contracts"] = PayService::getContracts($apply->corporation_id);

                $data["details"] = $apply->modelsToArray($apply->details, Utility::getCommonIgnoreAttributes());
                foreach ($data["details"] as $k => $v) {
                    if (!empty($v["contract"])) {
                        $data["details"][$k]["contract_code"] = $v["contract"]["contract_code"];
                        $data["details"][$k]["contract_type"] = $v["contract"]["type"];
                        unset($data["details"][$k]["contract"]);
                    }
                    if (!empty($v["project"])) {
                        $data["details"][$k]["project_code"] = $v["project"]["project_code"];
                        unset($data["details"][$k]["project"]);
                    }
                }
                break;
            case PayApplication::TYPE_CORPORATION:


                $view = "edit";
                break;
            case PayApplication::TYPE_PROJECT:

                $data["projects"] = PayService::getProjects($apply->corporation_id);
                $view = "edit";
                break;
            case PayApplication::TYPE_CLAIM:


                $view = "edit";
                break;
            default:
                $view = "edit";
                break;
        }
        $this->pageTitle = Map::$v["pay_application_type"][$apply->type] . "申请修改";
        $data["data"] = $apply->getAttributes(null);
        $data["data"]["remark"] .= $apply->extra->remark;
        if ($apply->is_factoring) {
            $factor = Factor::model()->find('apply_id=:applyId', array('applyId' => $apply->apply_id));
        }
        $data['factor'] = !empty($factor) ? $factor : null;
        $data["business_directors"] = UserService::getBusinessDirectorsByCorp($apply->corporation_id);
        $lastCheckUser = $this->getLastCheckUser($apply->apply_id, $apply->type);
        $data['data']['check_user'] = $lastCheckUser;
        $data['data']['check_user_validate'] = $lastCheckUser;
        $this->render($view, $data);
    }

    private function getLastCheckUser($apply_id, $type) {
        $lastCheckUser = 0;
        if ($type != PayApplication::TYPE_CONTRACT && $type != PayApplication::TYPE_SELL_CONTRACT) {
            $checkLog = FlowService::getCheckLog($apply_id, FlowService::BUSINESS_PAY_APPLICATION);
            if (Utility::isNotEmpty($checkLog)) {
                foreach ($checkLog as $val) {
                    if ($val['role_ids'] == 3) {//业务 驳回
                        $lastCheckUser = $val['user_id'];
                        break;
                    }
                }

            }
        }
        return $lastCheckUser;
    }

    public function actionRoute() {
        $type = Mod::app()->request->getParam("type");
        switch ($type) {
            case PayApplication::TYPE_CONTRACT:
            case PayApplication::TYPE_SELL_CONTRACT:
                $this->redirect("/pay/contracts?type=" . $type);
                break;

            default:
                $this->redirect("/pay/add?type=" . $type);

                break;
        }
    }

    public function actionContracts() {
        $type = Mod::app()->request->getParam("type");
        $attr = $_GET["search"];
        if (empty($type)) {
            $type = $attr["type"];
            unset($attr["type"]);
        }


        $sql = "select {col} from t_contract c 
                left join t_project p on p.project_id=c.project_id
               left join t_corporation cor on c.corporation_id=cor.corporation_id
               left join t_partner b on c.partner_id=b.partner_id 
               left join t_contract_file cf on cf.contract_id=c.contract_id and cf.is_main=1 and cf.type=1 
              " . $this->getWhereSql($attr) . "  
              and " . AuthorizeService::getUserDataConditionString("c") . " 
              and " . PayService::getPayContractCondition("c") . " order by c.contract_id desc";

        $data = $this->queryTablesByPage($sql, 'c.*,p.project_code,p.type as project_type,cor.name as corp_name,b.name as partner_name,cf.code_out');
        $attr["type"] = $type;
        $data["search"] = $attr;
        $data["type"] = $type;
        $this->render("contracts", $data);
    }

    public function actionSave() {
        $params = $_POST["data"];

        if (empty($params['apply_id'])) {
            $this->returnError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $paramsCheckRes = PayService::checkParamsValid($params);
        if (!$paramsCheckRes) {
            $this->returnError($paramsCheckRes);
        }

        $params['account'] = str_replace(' ', '', $params['account']);
        /*if($params["is_factoring"] == 'true')
            $params["is_factoring"]=1;
        else
            $params["is_factoring"]=0;*/

        if (!empty($params["apply_id"])) {
            $obj = PayApplication::model()->with("details", "extra")->findByPk($params["apply_id"]);
        }

        if (empty($obj)) {
            $obj = new PayApplication();
            $obj->apply_id = $params["apply_id"];
            $obj->create_user_id = $this->userId;
            $obj->create_time = date('Y-m-d H:i:s');
        } else {
            unset($params["type"]);
        }

        if (!$obj->isCanEdit())
            $this->returnError("当前付款申请不允许修改");

        $noWhitespaceKeys = array("payee", "bank", "account_name", "account");
        foreach ($noWhitespaceKeys as $k) {
            if (array_key_exists($k, $params))
                $params[$k] = trim($params[$k]);
        }

        $remark = $params["remark"];
        unset($params["remark"]);
        $obj->setAttributes($params);

        $logRemark = ActionLog::getEditRemark($obj->isNewRecord, "付款申请");
        $trans = Utility::beginTransaction();
        try {

            if ($obj->type == PayApplication::TYPE_CLAIM)
                $obj->category = PayApplication::CATEGORY_CLAIMING;
            else
                $obj->category = PayApplication::CATEGORY_NORMAL;

            if ($obj->is_factoring) {
                if ($obj->amount_factoring <= 0)
                    throw new Exception("保理金额不能为0");
            }

            switch ($obj->type) {
                case PayApplication::TYPE_CONTRACT:
                case PayApplication::TYPE_SELL_CONTRACT:

                    if (!empty($params["sub_contract_id"])) {
                        $file = ContractFile::model()->findByPk($params["sub_contract_id"]);
                        if (!empty($file)) {
                            $obj->sub_contract_type = $file->category;
                            $obj->sub_contract_code = $file->code;
                        }
                    }


                    break;

            }

            $obj->status_time = new CDbExpression("now()");

            $res = $obj->save();
            if (!$res)
                throw new Exception("保存失败");

            //保存备注
            if (empty($obj->extra)) {
                $obj->extra = new PayApplicationExtra();
                $obj->extra->apply_id = $obj->apply_id;
            }
            $obj->extra->remark = $remark;
            $obj->extra->save();

            switch ($obj->type) {
                case PayApplication::TYPE_CONTRACT:
                case PayApplication::TYPE_SELL_CONTRACT:

                    //按合同付款
                    if (!empty($obj->contract_id)) {
                        $contract = Contract::model()->findByPk($obj->contract_id);
                        if (empty($contract))
                            throw new Exception("合同信息错误");

                        if (!empty($obj->details) || !empty($params["items"])) {
                            $details = array();
                            foreach ($obj->details as $d)
                                $details[$d["plan_id"]] = $d;

                            $newDetails = array();

                        if (Utility::isNotEmpty($params["items"])) {
                            foreach ($params["items"] as $v)
                            {
                                if(isset($details[$v["plan_id"]]))
                                {
                                    $detail=$details[$v["plan_id"]];
                                    unset($details[$v["plan_id"]]);

                                    $detail->amount=$v["amount"];
                                    $detail->currency=$obj->currency;
                                    $detail->save();
                                    $newDetails[$detail->plan_id]=$detail;
                                }
                                else
                                {
                                    $detail=new PayApplicationDetail();
                                    $detail->apply_id=$obj->apply_id;
                                    $detail->project_id=$obj->project_id;
                                    $detail->contract_id=$obj->contract_id;
                                    $detail->plan_id=$v["plan_id"];
                                    $detail->amount=$v["amount"];
                                    $detail->currency=$obj->currency;
                                    $detail->save();
                                    $newDetails[$detail->plan_id]=$detail;
                                }

                            }
                        }
                        foreach ( $details as $v)
                            $v->delete();
                    }
                }

                    break;
                case PayApplication::TYPE_MULTI_CONTRACT:

                    if (!empty($obj->details) || !empty($params["details"])) {
                        $details = array();
                        foreach ($obj->details as $d)
                            $details[$d["contract_id"]] = $d;

                        $newDetails = array();

                        foreach ($params["details"] as $v) {
                            if (isset($details[$v["contract_id"]])) {
                                $detail = $details[$v["contract_id"]];
                                unset($details[$v["contract_id"]]);

                                $detail->amount = $v["amount"];
                                $detail->save();
                                $newDetails[$detail->contract_id] = $detail;
                            } else {
                                $detail = new PayApplicationDetail();
                                $detail->apply_id = $obj->apply_id;
                                $detail->project_id = $v["project_id"];
                                $detail->contract_id = $v["contract_id"];
                                $detail->plan_id = 0;
                                $detail->amount = $v["amount"];
                                $detail->currency = $obj->currency;
                                $detail->save();
                                $newDetails[$detail->plan_id] = $detail;
                            }

                        }

                        foreach ($details as $v)
                            $v->delete();
                    }

                    break;
            }

            if ($obj->is_factoring) {
                $res = FactoringService::applyFactoring($obj);
                if (!$res)
                    throw new Exception("发起保理申请失败");
            } else {
                $factor = Factor::model()->find('apply_id=' . $obj->apply_id);
                if (!empty($factor)) {
                    FactoringService::deleteFactor($factor->factor_id, $factor);
                }
            }
            $check_user = isset($params['check_user']) ? $params['check_user'] : 0;
            //提交审核
            if ($obj->status == PayApplication::STATUS_SUBMIT) {
                PayService::submitPayApplication($obj, $check_user);
            }

            $trans->commit();
            Utility::addActionLog(json_encode($obj->oldAttributes), $logRemark, "PayApplication", $obj->apply_id);
            $this->returnSuccess($obj->apply_id);
        } catch (Exception $e) {
            try {
                $trans->rollback();
            } catch (Exception $ee) {
            }
            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $e->getMessage(), CLogger::LEVEL_ERROR);

            $this->returnError(BusinessError::outputError(OilError::$PAY_APPLICATION_SAVE_ADD_ERROR, array('reason' => $e->getMessage())));
        }

    }

    public function actionDetail() {
        $id = Mod::app()->request->getParam("id");
        if (!Utility::checkQueryId($id))
            $this->renderError("参数错误");

        $apply = PayApplication::model()->with("details", "contract", "details.payment", "extra")->findByPk($id);
        if (empty($apply))
            $this->renderError("付款申请不存在");


        $view = "detail";
        $data["apply"] = $apply;
        $data["actual_paid_amount"] = PayService::getContractActualPaidAmount($apply->contract_id);
        $data["business_directors"] = UserService::getBusinessDirectorsByCorp($apply->corporation_id);
        $data["applyInfo"] = $apply->getAttributes();
        $lastCheckUser = $this->getLastCheckUser($apply->apply_id, $apply->type);
        $data['applyInfo']['check_user'] = $lastCheckUser;
        $data['applyInfo']['check_user_validate'] = $lastCheckUser;
        $this->render($view, $data);
    }

    public function actionSubmit() {
        $id = Mod::app()->request->getParam("id");
        $check_user = Mod::app()->request->getParam("check_user");
        $check_user = $check_user ? $check_user : 0;
        if (!Utility::checkQueryId($id))
            $this->returnError("参数错误");

        $apply = PayApplication::model()->findByPk($id);
        if (empty($apply))
            $this->returnError("付款申请不存在");
        if (!$apply->isCanEdit()) {
            $this->returnError("付款申请当前状态不允许提交");
        }

        $oldStatus = $apply->status;
        $res = PayService::submitPayApplication($apply, $check_user);
        if ($res) {
            Utility::addActionLog(json_encode(array('oldStatus' => $oldStatus)), "提交付款申请", "PayApplication", $apply->apply_id);
            $this->returnSuccess("Success");
        } else
            $this->returnError("提交操作失败");
    }

    public function actionGetProjects() {
        $id = Mod::app()->request->getParam("corpId");
        if (!Utility::checkQueryId($id))
            $this->renderError("参数错误");

        $data = PayService::getProjects($id);
        $this->returnSuccess($data);
    }

    public function actionGetContracts() {
        $id = Mod::app()->request->getParam("corpId");
        if (!Utility::checkQueryId($id))
            $this->renderError("参数错误");

        $data = PayService::getContracts($id);
        $this->returnSuccess($data);
    }

    public function actionTrash() {
        $id = Mod::app()->request->getParam("id");
        if (!Utility::checkQueryId($id))
            $this->returnError("参数错误");

        $apply = PayApplication::model()->with("factor")->findByPk($id);
        if (empty($apply))
            $this->returnError("付款申请不存在");
        if (!$apply->isCanTrash()) {
            $this->returnError("付款申请当前状态不允许作废");
        }

        try {
            $res = PayService::trashPayApplication($apply);
            if ($res === true) {
                Utility::addActionLog(null, "付款申请作废", "PayApplication", $apply->apply_id);
                $this->returnSuccess("Success");
            } else
                $this->returnError("作废失败：" . $res);
        } catch (Exception $e) {
            $this->returnError("作废失败");
        }
    }

    public function actionWithdraw() {
        $id = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($id))
            $this->returnError("参数错误");

        try {
            $res = PayService::withdrawPayApplication($id);
            if ($res === true) {
                Utility::addActionLog(null, "付款申请审核撤回", "PayApplication", $id);
                $this->returnSuccess("Success");
            } else
                $this->returnError("撤回失败：" . $res, -1);
        } catch (Exception $e) {
            $this->returnError("撤回失败", -2);
        }
    }

    public function actionGetAccount() {
        $name = Mod::app()->request->getParam("name");
        $name = Utility::filterInject($name);
        if (empty($name)) {
            $this->returnError("");
        }

        $account = PayService::getPayeeAccount($name);
        $this->returnSuccess($account);
    }
    
    public function actionCopy()
    {
        $apply_id = Mod::app()->request->getParam('apply_id');
        if (!Utility::checkQueryId($apply_id) || $apply_id <=0 ) {
            $this->returnError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $old_apply = PayApplication::model()->findByPk($apply_id);
        if(empty($old_apply)) {
            $this->returnError(BusinessError::outputError(OilError::$PAY_APPLICATION_NOT_EXIST, array('apply_id' => $apply_id)));
        }
        $params = $old_apply->getAttributes(true, Utility::getCommonIgnoreAttributes(['apply_id', 'remark', 'status', 'amount_paid', 'amount_paid_cny', 'amount_balance', 'amount_claim', 'status_time']));

        $obj = new PayApplication();
        $obj->setAttributes($params);
        $obj->status = PayApplication::STATUS_NOT_SAVE;
        $obj->status_time=Utility::getDateTime();

        $logRemark = ActionLog::getEditRemark($obj->isNewRecord,"付款申请复制");
        $trans = Utility::beginTransaction();
        try
        {
            $obj->setId();
            $res=$obj->save();
            if(!$res)
                throw new Exception("保存失败");

            //复制备注
            $extra_obj = new PayApplicationExtra();
            $extra_obj->apply_id = $obj->apply_id;
            $extra_obj->remark = $old_apply->extra->remark;
            $extra_obj->save();

            //复制附件
            $attachs = PayAttachment::model()->findAll('base_id=' . $old_apply->apply_id . ' and type=1 and status=1');
            if(Utility::isNotEmpty($attachs)) {
                foreach ($attachs as $attach) {
                    $attach_obj = new PayAttachment();
                    $attach_params = $attach->getAttributes(true, Utility::getCommonIgnoreAttributes(['id', 'base_id']));
                    $attach_obj->base_id = $obj->apply_id;
                    $attach_obj->setAttributes($attach_params);
                    $attach_obj->save();
                }
            }

            //复制付款申请明细
            if(Utility::isNotEmpty($old_apply->details)) {
                foreach ($old_apply->details as $detail) {
                    $detail_obj = new PayApplicationDetail();
                    $detail_params = $detail->getAttributes(true, Utility::getCommonIgnoreAttributes(['detail_id', 'apply_id', 'amount_paid', 'amount_paid_cny']));
                    $detail_obj->apply_id = $obj->apply_id;
                    $detail_obj->setAttributes($detail_params);
                    $detail_obj->save();
                }
            }

            if($obj->is_factoring)
            {
                $res=FactoringService::applyFactoring($obj);
                if(!$res)
                    throw new Exception("发起保理申请失败");
            }else{
                $factor = Factor::model()->find('apply_id='.$obj->apply_id);
                if(!empty($factor)) {
                    FactoringService::deleteFactor($factor->factor_id, $factor);
                }
            }

            $trans->commit();
            Utility::addActionLog(json_encode($obj->oldAttributes), $logRemark, "PayApplication", $obj->apply_id);
            $this->returnSuccess($obj->apply_id);
        }
        catch (Exception $e) {
            try { $trans->rollback();  } catch (Exception $ee) {}
            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $e->getMessage(), CLogger::LEVEL_ERROR);

            $this->returnError(BusinessError::outputError(OilError::$PAY_APPLICATION_SAVE_ADD_ERROR, array('reason' => $e->getMessage())));
        }
    }
}