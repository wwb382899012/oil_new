
<?php
/**
*	银行流水认领
*/
class ReceiveConfirmController extends ExportableController {

    public function pageInit() {
        $this->attachmentType = Attachment::C_RECEIVE_CONFIRM_IMPORT;
        $this->filterActions = "ajaxContract,ajaxProject,ajaxContractPayments,ajaxDel,delFile";
        $this->rightCode = "receiveConfirm";
        $this->newUIPrefix = 'new_';
    }

    public function actionIndex() {
        // $attr = Mod::app()->request->getParam('search');
        $attr   = $this->getSearch();
        $search = $attr;


        $sql = 'select {col} 
                from t_bank_flow a 
        		left join t_corporation b on a.corporation_id = b.corporation_id
                left join t_partner c on c.partner_id = a.pay_partner ' . $this->getWhereSql($attr). '
                and '.AuthorizeService::getUserDataConditionString('a').' order by a.flow_id desc {limit}';
        $col = 'a.*, c.name as partner_name, b.name as corporation_name, b.code as stock_in_code';
        $export_str = Mod::app()->request->getParam('export_str');
        $user = Utility::getNowUser();
        if(!empty($export_str)) {
            if(!empty($user['corp_ids'])) {
                $this->export($sql, $col, $export_str);
            }
            return;
        } else {
            if(!empty($user['corp_ids'])) {
                $data = $this->queryTablesByPage($sql, $col);
            }else{
                $data = array();
            }
        }

        $data["search"]=$search;

        $this->pageTitle = '收款流水认领';
        $this->render('index', $data);
    }

    public function actionAdd()
    {
        $flow_id = Mod::app()->request->getParam('flow_id');
        if (!Utility::checkQueryId($flow_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        $bankFlow = BankFlow::model()->findByPk($flow_id);
        $receiveConfirmId = IDService::getReceiveConfirmId();
        $user = Utility::getNowUser();
        $this->render('edit', array('bankFlow'=>$bankFlow, 'receiveConfirm'=>array('receive_id'=>$receiveConfirmId, 'creator'=>$user['name'])));
    }

    public function actionEdit() {
        $receive_id = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($receive_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        list($receiveConfirm, $payments) = ReceiveConfirmService::getReceiveConfirmDetail($receive_id);
        if(empty($receiveConfirm)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        $bankFlow = BankFlow::model()->with("account", "corporation", 'partner')->findByPk($receiveConfirm['flow_id']);
        $attachs = ReceiveConfirmService::getAttachment($receiveConfirm['receive_id']);
        $receiveConfirm['contract_code'] = empty($receiveConfirm['contract_out_code'])?$receiveConfirm['contract_code']:$receiveConfirm['contract_code'].'('.$receiveConfirm['contract_out_code'].')';
        $this->render('edit', array('bankFlow'=>$bankFlow, 'receiveConfirm'=>$receiveConfirm, 'payments'=>$payments, 'attachments'=>$attachs));
    }

    public function actionSave() {
    	$data = $_POST;
    	$flow_id = $data['flow_id'];
    	$receive_id = $data['receive_id'];

        $requiredParams = array('flow_id', 'receive_id');
        if (!Utility::checkRequiredParamsNoFilterInject($data, $requiredParams)) {
            $this->returnError(BusinessError::outputError(OilError::$REQUIRED_PARAMS_CHECK_ERROR));
        }
        if (!empty($flow_id) && !Utility::checkQueryId($flow_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $flow = BankFlow::model()->findByPk($data['flow_id']);
        if(empty($flow->attributes)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try{
        	$receiveConfirm = ReceiveConfirm::model()->findByPk($receive_id);
        	$receiveConfirm = empty($receiveConfirm->attributes) ? new ReceiveConfirm() : $receiveConfirm;
        	$receiveConfirm->contract_id = empty($data['contract_id'])?0:$data['contract_id'];
        	$receiveConfirm->project_id = empty($data['project_id'])?0:$data['project_id'];
        	$receiveConfirm->flow_id = $data['flow_id'];
        	$receiveConfirm->receive_id = $data['receive_id'];
        	$receiveConfirm->sub_contract_type = $data['sub_contract_type'];
            $sub_contract_code = explode("(",$data['sub_contract_code']);
        	$receiveConfirm->sub_contract_code = $sub_contract_code[0];
            $contract_code = explode("(",$data['contract_code']);
            $receiveConfirm->contract_out_code = isset($contract_code[1])?substr($contract_code[1],0,-1):'';
        	$receiveConfirm->amount = $data['amount'];
        	$receiveConfirm->subject = $data['subject'];
        	$receiveConfirm->remark = $data['remark'];
            $receiveConfirm->currency = $flow['currency'];
            $receiveConfirm->exchange_rate = $flow['exchange_rate'];
            $receiveConfirm->receive_date = $flow['receive_date'];

        	ReceiveDetail::model()->deleteAll('receive_id=:receive_id', array('receive_id'=>$receive_id));
            $hasDetail = false;
        	if(!empty($data['payments'])) {
        		$amount = 0;
	        	$planMap = array();
	        	$paymentAmountStatus = true;
	        	$payments = PaymentPlan::model()->findAll(
	        		array(
	        			'condition'=>'contract_id=:contract_id', 
	        			'params'=>array('contract_id'=>$data['contract_id'])
	        		)
	        	);
        		foreach ($payments as $planKey => $planValue) {
	        		foreach($data['payments'] as $payment) {
	        			if($payment['plan_id'] == $planValue->plan_id && ($payment['check'] != false && $payment['check'] != 'false')) {
		        			$detail = new ReceiveDetail();
		        			$detail->receive_id = $data['receive_id'];;
		        			$detail->project_id = $planValue->project_id;
		        			$detail->contract_id = $planValue->contract_id;
		        			$detail->plan_id = $planValue->plan_id;
		        			$detail->amount = $payment['amount_input'];
		        			$planMap[$planValue->plan_id] = $detail->amount;
		        			$amount = $amount + $detail->amount;
		        			$detail->save();
                            $hasDetail = true;
	        			}
	        		}
        		}
        		$receiveConfirm->amount = ($hasDetail) ? $amount:$receiveConfirm->amount;
        		if($data['status'] == 1) {
        			foreach ($planMap as $plan_id => $amount) {
        				$paymentAmountStatus = $paymentAmountStatus && ReceiveConfirmService::updatePaymentPlanAmount($plan_id, $amount);
        			}
        		}

        		if(!$paymentAmountStatus) {
        			throw new Exception("收款计划超出未收金额", 1);
        			
        		}
        	}
            //人民币金额
            if($flow['currency']==ConstantMap::CURRENCY_RMB){
                $receiveConfirm->amount_cny = empty($receiveConfirm->amount)?0:$receiveConfirm->amount;
            }else{
                $receiveConfirm->amount_cny = ($receiveConfirm->amount)*$flow['exchange_rate'];
            }

    		if($data['status'] == 1) {
        	    if($flow->status != BankFlow::STATUS_SUBMITED) {
                    BusinessException::throw_exception(OilError::$BANK_FLOW_NOT_ALLOW_CLAIM, array('flow_id' => $flow_id));
                }

    			$bankFlowStatus = ReceiveConfirmService::updateBankFlowAmount($flow_id, ($receiveConfirm->amount)?$receiveConfirm->amount:0);
    			if(!$bankFlowStatus) {
        			throw new Exception("认领金额超出", 1);
    			}
    			$receiveConfirm->status = ReceiveConfirm::STATUS_SUBMITED;
    		} else {
    			$receiveConfirm->status = ReceiveConfirm::STATUS_NEW;
    		}
            $logRemark = ActionLog::getEditRemark($receiveConfirm->isNewRecord, "银行流水认领");

        	$receiveConfirm->save();

        	//收款流水认领到合同时，调整合作方额度
        	if ($receiveConfirm->status == ReceiveConfirm::STATUS_SUBMITED && !empty($receiveConfirm->contract_id) && in_array($receiveConfirm->subject, explode(',',ConstantMap::GOODS_FEE_SUBJECT_ID))) {
                if(!($receiveConfirm->subject == ConstantMap::TAX_DEPOSIT_SUBJECT_ID && $receiveConfirm->contract->agent_type == ConstantMap::AGENT_TYPE_PURE)) {
                    $receiptClaimEntity = \ddd\infrastructure\DIService::getRepository(\ddd\domain\iRepository\receipt\IReceiptClaimRepository::class)->findByPk($receiveConfirm->receive_id);
                    if (empty($receiptClaimEntity->receive_id)) {
                        throw new \ddd\infrastructure\error\ZEntityNotExistsException($receiveConfirm->receive_id, \ddd\domain\entity\receipt\ReceiptClaim::class);
                    }

                    $res = \ddd\application\receipt\ReceiptService::service()->submitReceiptClaim($receiveConfirm->receive_id, $receiptClaimEntity);
                    if ($res !== true) {
                        throw new Exception($res);
                    }
                }
            }
            $trans->commit();
            if($receiveConfirm->status == ReceiveConfirm::STATUS_SUBMITED) {
                //更新利润报表的收付款利润
                \ddd\Profit\Application\PayReceiveEventService::service()->onReceiveConfirm($receiveConfirm->contract_id, $receiveConfirm->subject);
                //发出mq事件
                \AMQPService::publishReceiveConfirm($receiveConfirm->project_id);
            }
            Utility::addActionLog(json_encode($receiveConfirm->oldAttributes), $logRemark, "ReceiveConfirm", $receiveConfirm->receive_id);
            $this->returnSuccess($receiveConfirm->receive_id);
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

    public function actionSubmit() {
        $receive_id = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($receive_id) || $receive_id < 0) {
            $this->returnError(BusinessError::outputError(OilError::$REQUIRED_PARAMS_CHECK_ERROR));
        }

        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try{
            $receiveConfirm = ReceiveConfirm::model()->findByPk($receive_id);
            if(empty($receiveConfirm->attributes)) {
                $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
            }
            if($receiveConfirm->status >= ReceiveConfirm::STATUS_SUBMITED) {
                throw new Exception("本次认领操作已经提交,请勿重复提交", 1);
            }
            $flow = BankFlow::model()->findByPk($receiveConfirm->flow_id);
            if(empty($flow->attributes)) {
                $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
            }
            if($flow->status != BankFlow::STATUS_SUBMITED) {
                BusinessException::throw_exception(OilError::$BANK_FLOW_NOT_ALLOW_CLAIM, array('flow_id' => $receiveConfirm->flow_id));
            }
            $details = ReceiveDetail::model()->findAll('receive_id=:receive_id', array('receive_id'=>$receive_id));
            foreach ($details as $detail) {
                $planAmountUpdateRes = ReceiveConfirmService::updatePaymentPlanAmount($detail->plan_id, $detail->amount);
                if (!$planAmountUpdateRes) {
                    throw new Exception("更新收付款计划实付金额失败！");
                }
            }
            $bankFlowStatus = ReceiveConfirmService::updateBankFlowAmount($receiveConfirm->flow_id, ($receiveConfirm->amount)?$receiveConfirm->amount:0);
            if(!$bankFlowStatus) {
                throw new Exception("认领金额超出待认领金额", 1);
            }

            $oldStatus=$receiveConfirm->status;
            $receiveConfirm->status = ReceiveConfirm::STATUS_SUBMITED;
            $receiveConfirm->save();

            //收款流水认领到合同时，调整合作方额度
            if ($receiveConfirm->status == ReceiveConfirm::STATUS_SUBMITED && !empty($receiveConfirm->contract_id) && in_array($receiveConfirm->subject, explode(',', ConstantMap::GOODS_FEE_SUBJECT_ID))) {
                if (!($receiveConfirm->subject == ConstantMap::TAX_DEPOSIT_SUBJECT_ID && $receiveConfirm->contract->agent_type == ConstantMap::AGENT_TYPE_PURE)) {
                    $receiptClaimEntity = \ddd\infrastructure\DIService::getRepository(\ddd\domain\iRepository\receipt\IReceiptClaimRepository::class)->findByPk($receiveConfirm->receive_id);
                    if (empty($receiptClaimEntity->receive_id)) {
                        throw new \ddd\infrastructure\error\ZEntityNotExistsException($receiveConfirm->receive_id, \ddd\domain\entity\receipt\ReceiptClaim::class);
                    }

                    $res = \ddd\application\receipt\ReceiptService::service()->submitReceiptClaim($receiveConfirm->receive_id, $receiptClaimEntity);
                    if ($res !== true) {
                        throw new Exception($res);
                    }
                }
            }
            $trans->commit();
            Utility::addActionLog(json_encode(array('oldStatus'=>$oldStatus)), "提交银行流水认领", "ReceiveConfirm", $receiveConfirm->receive_id);
            $this->returnSuccess($receiveConfirm->receive_id);
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

    public function actionDetail() {
        $receive_id = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($receive_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        $receiveConfirm = ReceiveConfirm::model()->with('receiveDetail', 'receiveDetail.paymentPlan', 'creator', 'finSubject')->findByPk($receive_id);
        if(empty($receiveConfirm->attributes)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        $bankFlow = BankFlow::model()->with("account", "corporation", 'partner')->findByPk($receiveConfirm->flow_id);

        $attachs = ReceiveConfirmService::getAttachment($receiveConfirm->receive_id);
        $this->render('detail', array('bankFlow'=>$bankFlow, 'receiveConfirm'=>$receiveConfirm, 'attachs'=>$attachs));
    }

    public function actionView() {

        $flow_id = Mod::app()->request->getParam('flow_id');
        if (!Utility::checkQueryId($flow_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        $sql = 'select {col} 
                from t_bank_flow a 
                join t_receive_confirm d on d.flow_id=a.flow_id
                left join t_project p on p.project_id = d.project_id 
                left join t_contract c on c.contract_id = d.contract_id 
                left join t_system_user u on u.user_id = d.create_user_id 
                left join t_finance_subject fs on fs.subject_id=d.subject 
                where a.flow_id ='.$flow_id.'
                and '.AuthorizeService::getUserDataConditionString('a').' order by a.flow_id desc {limit}';
        $col = ' d.*, a.currency, c.contract_code, c.type as contract_type, p.project_code, p.type as project_type, u.name as user_name, fs.name as subject_name';
        $user = Utility::getNowUser();
        if(!empty($user['corp_ids'])) {
            $data = $this->queryTablesByPage($sql, $col);
        }else{
            $data = array();
        }
        $this->pageTitle = '收款管理 > 认领明细管理列表';
        $this->render('view', $data);


        // $bankFlow = BankFlow::model()->with("account", "corporation", 'partner', 'receiveConfirm', 'receiveConfirm.receiveDetail', 'receiveConfirm.receiveDetail.paymentPlan')->findByPk($flow_id);
        // if(empty($bankFlow->attributes)) {
        //     $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        // }
        // $this->pageTitle = '收款管理 > 认领明细管理列表';
        // $this->render('view', array('bankFlow'=>$bankFlow));
    }

    public function actionAjaxDel() {

        $receive_id = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($receive_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        $receiveConfirm = ReceiveConfirm::model()->findByPk($receive_id, 'status='.ReceiveConfirm::STATUS_NEW);
        if(empty($receiveConfirm)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $receiveConfirm->status = ReceiveConfirm::STATUS_ABORTED;
        $status = $receiveConfirm->save();
        if($status) {
            $this->returnSuccess();
        } else {
            $this->returnError('系统繁忙请重试');
        }
    }

    public function actionAjaxContract() {
    	$search = Mod::app()->request->getParam('search');
    	$corporation_id = Mod::app()->request->getParam('corporation_id');
    	if(is_string($search) && !empty($search)&&Utility::checkQueryId($corporation_id)) {
    		$search = Utility::filterInject($search);
    		$db = Mod::app()->db;
    		$data = Utility::query("select
                                            c.contract_id, c.contract_code, c.type, p.project_id, p.project_code, p.type as project_type,cf.code_out
                                    from    t_contract c
                                            left join t_project p on c.project_id = p.project_id
                                            left join t_contract_file as cf on c.contract_id = cf.contract_id and cf.is_main=1 and cf.type=1
                                    where  (contract_code like '{$search}%' or cf.code_out like '%{$search}%') and p.project_id=c.project_id and c.corporation_id={$corporation_id} limit 20");
	    	// $contracts = Contract::model()->findAllToArray(
	    	// 	array(
	    	// 		'condition'=>"contract_code like '{$search}%'", 
	    	// 		'select'=>'contract_id, contract_code, type',
	    	// 		'limit'=>20
	    	// 		));
	    	$this->returnSuccess($data);
    	} else {
    		$this->returnSuccess(array());
    	}
    }

    public function actionAjaxProject() {
    	$search = Mod::app()->request->getParam('search');
    	$corporation_id = Mod::app()->request->getParam('corporation_id');
    	if(is_string($search) && !empty($search)&&Utility::checkQueryId($corporation_id)) {
    		$search = Utility::filterInject($search);
	    	$projects = Project::model()->findAllToArray(
	    		array(
	    			'condition'=>"project_code like '{$search}%' and corporation_id='{$corporation_id}'", 
	    			'select'=>'project_id, project_code, type',
	    			'limit'=>20
	    			));
	    	$this->returnSuccess($projects);
    	} else {
    		$this->returnSuccess(array());
    	}
    }

    public function actionAjaxContractPayments() {
    	$search = Mod::app()->request->getParam('search');
    	if(is_string($search) && !empty($search)) {
    		$search = Utility::filterInject($search);
    		$payments = PaymentPlan::model()->findAllToArray(
    			array(
    				'condition'=>'contract_id=:contract_id', 
    				'params'=>array('contract_id'=>$search),
	    			'select'=>'plan_id, project_id, contract_id, expense_type,amount,amount_paid,currency,period,expense_name,pay_date',
    				)
    			);
	    	$this->returnSuccess($payments);
    	} else {
    		$this->returnSuccess(array());
    	}
    }
}