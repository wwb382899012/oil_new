
<?php
/**
*	银行流水
*/
class BankFlowController extends ExportableController {
    public function pageInit() {
        $this->attachmentType = Attachment::C_BANK_FLOW_IMPORT;
        $this->filterActions = "ajaxRollBack";
        $this->rightCode = "bankFlow";
        $this->newUIPrefix = 'new_';
    }

    public function actionExport()
    {
        $attr = $this->getSearch();

        $fields = "a.status, a.receive_date 收款时间, format(a.amount/100, 2) 收款金额, a.pay_partner 付款公司, b.name 交易主体, concat(a.flow_id, ' ') ID, concat(a.code, ' ') 银行流水编号, a.bank_name 收款银行, a.account_name 银行账户名, a.pay_bank 付款银行";
        $sql = "select ".$fields."
                from t_bank_flow a 
        		left join t_corporation b on a.corporation_id = b.corporation_id 
                " . $this->getWhereSql($attr). " and " . AuthorizeService::getUserDataConditionString('a') . " 
                order by a.flow_id desc";

        $data = Utility::query($sql);
        if(Utility::isEmpty($data))
        {
            $this->renderError("当前信息不存在！");
        } else {
            foreach ($data as $key => $row) {
                $data[$key]['状态'] = Map::$v['bank_flow_status'][$row['status']];
                unset($data[$key]['status']);
            }
        }

        $this->exportExcel($data);
    }

    public function actionIndex() {
        $attr = $this->getSearch();
        $sql = 'select {col} 
                from t_bank_flow a 
        		left join t_corporation b on a.corporation_id = b.corporation_id 
                ' . $this->getWhereSql($attr). ' and ' . AuthorizeService::getUserDataConditionString('a') . ' 
                order by a.flow_id desc {limit}';
        $col = 'a.*, a.pay_partner partner_name, b.name as corporation_name, b.code as stock_in_code, (select count(1) from t_receive_confirm d where d.flow_id=a.flow_id) as receive_count';

        $user = Utility::getNowUser();

        if(!empty($user['corp_ids'])) {
            $data = $this->queryTablesByPage($sql, $col);
        }else{
            $data = array();
        }
        $this->pageTitle = '收款流水录入';
        $this->render('index', $data);
    }

    public function actionAdd()
    {
        $this->render('edit', array('bankFlow'=>array()));
    }

    public function actionEdit() {
        $flow_id = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($flow_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        $bankFlow = BankFlow::model()->findByPk($flow_id);
        if(empty($bankFlow)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        $bankFlowDetail = $bankFlow->getAttributes(true, array("status_time", "create_user_id", "create_time", "update_user_id", "update_time"));
        $this->render('edit', array('bankFlow'=>$bankFlowDetail));
    }

    public function actionSave() {
        $data = $_POST;
        if (!empty($data['flow_id']) && !Utility::checkQueryId($data['flow_id'])) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $requiredParams = array('code', 'corporation_id', 'account', 'account_name', 'pay_partner', 'pay_bank', 'amount', 'currency', 'receive_date', 'exchange_rate');
        if (!Utility::checkRequiredParamsNoFilterInject($data, $requiredParams)) {
            $this->returnError(BusinessError::outputError(OilError::$REQUIRED_PARAMS_CHECK_ERROR));
        }

        if (!Utility::checkQueryId($data['account'])) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $account = Account::model()->findByPk($data['account']);
        if(empty($account->attributes)) {
            $this->returnError(BusinessError::outputError(OilError::$REQUIRED_PARAMS_CHECK_ERROR));
        }

        if(!empty($data['flow_id'])) {
            $flow = BankFlow::model()->findByPk($data['flow_id']);
        } 
        $flow = empty($flow->attributes)?new BankFlow():$flow;
        if(empty($flow->flow_id)) {
            $flow->flow_id = IDService::getBankFlowId();
        }

        // 查重
        $bank_name = $account->bank_name;
        $exists = $this->checkFlowExist(trim($data['flow_id']), trim($data['code']), trim($account->bank_name));
        if($exists) {
            $this->returnError("{$bank_name} : {$code} 流水已经存在,请检查数据是否重复");
        }
        $flow->bank_name = $bank_name;

        unset($data['flow_id']);
        $flow->setAttributes($data, false);
        $logRemark = ActionLog::getEditRemark($flow->isNewRecord, "收款流水");
        $res = $flow->save();
        if($res) {
            Utility::addActionLog(json_encode($flow->oldAttributes), $logRemark, "BankFlow", $flow->flow_id);
            $this->returnSuccess($flow->flow_id);
        } else {
            $this->returnError("系统繁忙请重试");
        }
    }

    public function actionSubmit() {
        $flow_id = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($flow_id) || $flow_id < 0) {
            $this->returnError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        $bankFlow = BankFlow::model()->findByPk($flow_id);
        if (empty($bankFlow)) {
            $this->returnError(BusinessError::outputError(OilError::$BANK_FLOW_NOT_EXIST, array('flow_id' => $flow_id)));
        }

        if ($bankFlow->status >= BankFlow::STATUS_SUBMITED) {
            $this->returnError(BusinessError::outputError(OilError::$BANK_FLOW_NOT_ALLOW_SUBMIT, array('flow_id'=>$flow_id)));
        }
        $oldStatus = $bankFlow->status;
        $bankFlow->status = BankFlow::STATUS_SUBMITED;
        $bankFlow->status_time = Utility::getDateTime();
        $res = $bankFlow->save();
        if($res) {
            Utility::addActionLog(json_encode(array('oldStatus' => $oldStatus)), "提交银行流水", get_class($bankFlow), $bankFlow->getPrimaryKey());
            $this->returnSuccess();
        } else {
            $this->returnError(BusinessError::outputError(OilError::$OPERATE_FAILED));
        }
    }

    public function actionAjaxRollback() {
        $flow_id = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($flow_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        $bankFlow = BankFlow::model()->with("receiveConfirm")->findByPk($flow_id);
        if(empty($bankFlow)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        if(count($bankFlow->receiveConfirm)>0) {
            $this->returnError('该流水已被认领,无法撤回');
        }

        $oldStatus=$bankFlow->status;
        $bankFlow->status = BankFlow::STATUS_NEW;
        $status = $bankFlow->save();
        if($status) {
            Utility::addActionLog(json_encode(array('oldStatus'=>$oldStatus)), "收款流水回滚", "BankFlow", $flow_id);
            $this->returnSuccess();
        } else {
            $this->returnError('系统繁忙请重试');
        }

    }

    public function actionDetail() {
        $flow_id = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($flow_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $bankFlow = BankFlow::model()->with("account", "corporation")->findByPk($flow_id);
        $this->render('detail', array('bankFlow'=>$bankFlow));
    }


    public function actionImport() {
        $this->pageTitle="银行流水导入";
        $temp_id = IDService::getBankFlowImportId();
        $this->render('import', array('temp_id'=>$temp_id));
    }

    public function actionImportSave() {
        $temp_id = Mod::app()->request->getParam('temp_id');
        if (!Utility::checkQueryId($temp_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        $tempBankFlow = BankFlowTemp::model()->findAll(array('condition'=>'temp_id=:temp_id', 'params' => array('temp_id'=>$temp_id)));
        if(count($tempBankFlow) == 0) {
            $this->returnError('导入数据为空,请重试');
        }
        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try{
            foreach($tempBankFlow as $temp) {
                $exists = $this->checkFlowExist(0, $temp->code, $temp->bank_name);
                if(empty($exists)) {
                    $tempValue = $temp->getAttributes(true, array("temp_id", "flow_id", "status_time", "create_user_id", "create_time", "update_user_id", "update_time"));
                    $bankFlow = new BankFlow();
                    $bankFlow->setAttributes($tempValue);
                    $bankFlow->status = BankFlow::STATUS_SUBMITED;
                    $bankFlow->flow_id = IDService::getBankFlowId();
                    $bankFlow->save();
                } else {
                    throw new Exception("系统发现问题,请重新上传");
                }
            }

            BankFlowTemp::model()->deleteAll('temp_id=:temp_id', array('temp_id'=>$temp_id));
            $trans->commit();
            $fileInfo = BankFlowFileTempAttachement::model()->find(
                array(
                    'condition'=>'base_id=:base_id', 
                    'params'=>array('base_id'=>$file_id),
                    'order' => 'create_time desc'
                    )
                );
            $filePath = $fileInfo->file_path;
            $res = @unlink ($filePath);
            $this->returnSuccess($res);
        } catch (Exception $e) {
            try {
                $trans->rollback();
            } catch (Exception $ee) {
                Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $ee->getMessage(), CLogger::LEVEL_ERROR);
            }

            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $e->getMessage(), CLogger::LEVEL_ERROR);

            $this->returnError(BusinessError::outputError(OilError::$OPERATE_FAILED, array('reason' => $e->getMessage())));
        }
    }

    public function actionReadFile() {
        $temp_id = Mod::app()->request->getParam('temp_id');
        if (!Utility::checkQueryId($temp_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        $fileInfo = BankFlowFileTempAttachement::model()->find(
            array(
                'condition'=>'base_id=:base_id', 
                'params'=>array('base_id'=>$temp_id),
                'order' => 'create_time desc'
                )
            );
        $filePath = $fileInfo->file_path;
        try{
            $data=$this->readExcel($filePath);
            $returnArray = array();
            $success = true;
            if(!is_array($data))
            {
                $res = @unlink ($filePath);
                $this->returnError("没有数据需要处理！");
            }

            // 避免重复提交,删除原来的数据
            BankFlowTemp::model()->deleteAll('temp_id=:temp_id', array('temp_id'=>$temp_id));
            $db = Mod::app()->db;
            $trans = $db->beginTransaction();
            $flow_codes = array();
            $today = new DateTime();
            foreach($data as $row) {
                $returnValue = $row;
                if($returnValue['银行流水编号'] == '') {
                    continue;
                }
                $returnValue['has_error'] = 0;
                $returnValue['error_message'] = array();
                if($this->checkRequiredParams($returnValue)) {
                    $returnValue['error_message'][] = "字段不齐全";
                    $returnValue['has_error'] = 1;
                }

                $account = Account::model()->find(array('condition'=>'account_no=:account_no', 'params'=>array('account_no'=>$returnValue['银行帐号'])));
                if(empty($account->attributes)) {
                    $returnValue['error_message'][] = "银行帐号不存在";
                    $returnValue['has_error'] = 1;
                } else {
                    $returnValue['收款银行'] = $account->bank_name;
                }
                $code = $returnValue['银行流水编号'];
                $bank_name = $returnValue['银行流水编号'];
                if($this->checkFlowExist(0, $code, $account->bank_name)) {
                    $returnValue['error_message'][] = "流水重复录入";
                    $returnValue['has_error'] = 1;
                }
                if(in_array($code, $flow_codes, true)) {
                    $returnValue['error_message'][] = "流水重复";
                    $returnValue['has_error'] = 1;
                }
                $flow_codes[] = $code;

                $currency = $this->getCurrency($returnValue['币种']);
                if($currency == 0) {
                    $returnValue['error_message'][] = "币种有误";
                    $returnValue['has_error'] = 1;
                }


                if($currency != 1 && empty($returnValue['汇率'])) {
                    $returnValue['error_message'][] = "需填写汇率";
                    $returnValue['has_error'] = 1;
                }


                if($currency != 1 && empty($returnValue['汇率'])) {
                    $returnValue['error_message'][] = "需填写汇率";
                    $returnValue['has_error'] = 1;
                }

                $dateTime = date_create_from_format("m/d/Y", $returnValue['收款时间']);
                if(is_null($dateTime) || empty($dateTime)) {
                    $returnValue['error_message'][] = "日期格式错误";
                    $returnValue['has_error'] = 1;
                } else {
                    $diffFormat = '%a';
                    $diff = ceil(($dateTime->getTimestamp() - $today->getTimestamp()) / 86400);
                    // date diff 函数没有正负值
                    if($diff > 2 || $diff <-365) {
                        $returnValue['error_message'][] = "日期错误, 需过去一年到未来两天之间";
                        $returnValue['has_error'] = 1;
                    }
                }

                if($returnValue['has_error'] != 1) {
                    $tempBankFlow = new BankFlowTemp();
                    $tempBankFlow->temp_id = $temp_id;
                    $tempBankFlow->code = $returnValue['银行流水编号'];
                    $tempBankFlow->corporation_id = $account->corporation_id;
                    $tempBankFlow->bank_name = $account->bank_name;
                    $tempBankFlow->account = $account->account_id;
                    $tempBankFlow->account_name = $returnValue['银行账户名'];
                    $tempBankFlow->pay_partner = $returnValue['付款公司'];
                    $tempBankFlow->pay_bank = $returnValue['付款银行'];
                    $tempBankFlow->amount = str_replace(',', '', $returnValue['收款金额'])*100;
                    $dateTime = date_create_from_format("m/d/Y", $returnValue['收款时间']);
                    $tempBankFlow->receive_date = !empty($dateTime) ? date_format($dateTime, 'Y-m-d'):$returnValue['收款时间'];
                    $tempBankFlow->currency = $currency;
                    $tempBankFlow->exchange_rate = empty($returnValue['汇率'])?1:$returnValue['汇率'];
                    $tempBankFlow->remark = $returnValue['备注'];
                    $tempBankFlow->save();
                    $returnValue['error_message'][] = 'ok';
                } else {
                    $success= false;
                }
                if(!$success) {
                    $res = @unlink ($filePath);
                }
                $returnArray[] = $returnValue;
            }
            $trans->commit();
            $this->layout = 'empty';
            $this->render('readFile', array('returnArray'=>$returnArray));
        } catch(Exception $e) {
            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $e->getMessage(), CLogger::LEVEL_ERROR);
        }
    }

    /**
    *   键值对
    */
    private function checkFlowExist($flow_id, $code, $bank_name) {
        $code = Utility::filterInject($code);
        $bank_name = Utility::filterInject($bank_name);
        if(empty($flow_id)) {
            $exists = BankFlow::model()->exists("code=:code and bank_name=:bank_name and status<>" . BankFlow::STATUS_ABORTED, array('code'=>$code, 'bank_name'=>$bank_name));
        } else {
            $exists = BankFlow::model()->exists("flow_id <> :flow_id and code=:code and bank_name=:bank_name and status<>" . BankFlow::STATUS_ABORTED, array('flow_id'=>$flow_id, 'code'=>$code, 'bank_name'=>$bank_name));
        }
        return $exists;
    }


    private function checkRequiredParams($value) {
        $require_keys = array('银行流水编号', '银行帐号', '交易主体', '银行账户名', '付款单位', '付款银行', '币种', '收款金额');
        $value_keys = array_keys($value);
        $key_less = array_diff($require_keys, $value_keys);
        $not_has_need_value = false;
        foreach ($require_keys as $key) {
            $not_has_need_value = $not_has_need_value || empty($value[$key]);
        }
        return !empty($key_less) && !$not_has_need_value;
    }

    private function getCurrency($currencyName) {
        $map = Map::$v['currency'];
        $currencyName = str_replace(' ', '', $currencyName);
        foreach ($map as $currencyType) {
            if($currencyType['name']==$currencyName) {
                return $currencyType['id'];
            }
        }
        return 0;
    }
}