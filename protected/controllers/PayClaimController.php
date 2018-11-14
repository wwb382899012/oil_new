<?php

/**
 *    付款认领
 */
class PayClaimController extends ExportableController {

    public function pageInit() {
        $this->filterActions = "getContractById";
        $this->rightCode = "payClaim";
        $this->newUIPrefix = 'new_';
    }

    public function actionIndex() {
//        $attr = $_GET["search"];
        $attr = $this->getSearch();
        $query = '';
        $claimStatus = "1";
        if (isset($attr['claim_status'])) {
            $claimStatus = $attr['claim_status'];
            unset($attr['claim_status']);
        }
        if ($claimStatus == 1) {
            $query .= ' and a.amount_claim < a.amount_paid';
        } elseif ($claimStatus == 2) {
            $query .= ' and a.amount_claim = a.amount_paid';
        }

        $sql = "select {col} 
              from t_pay_application a 
              left join t_corporation c on c.corporation_id=a.corporation_id 
              left join t_finance_subject fs on fs.subject_id=a.subject_id 
              left join t_contract co on co.contract_id=a.contract_id 
              left join t_system_user su on su.user_id=a.create_user_id
              " . $this->getWhereSql($attr) . $query . " and " . AuthorizeService::getUserDataConditionString("a") . " and a.category=" . PayApplication::CATEGORY_CLAIMING . " and a.status>=" . PayApplication::STATUS_CHECKED . " order by a.apply_id desc {limit}";
        $fields = 'a.*,c.name as corp_name,fs.name as subject_name,co.type as contract_type,co.category as contract_category,co.contract_code,su.name as create_name,
                   case when a.amount_claim = a.amount_paid then "已认领" when a.amount_claim < a.amount_paid then "待认领" end as claim_status, a.amount_claim';
        $user = Utility::getNowUser();
        if (!empty($user['corp_ids'])) {
            $data = $this->queryTablesByPage($sql, $fields);
        } else {
            $data = array();
        }
        if (Utility::isNotEmpty($data['data']['rows'])) {
            foreach ($data['data']['rows'] as $key => $row) {
                $data['data']['rows'][$key]['contract_type_desc'] = '';
                if (!empty($row['contract_type']) && !empty($row['contract_category'])) {
                    $data['data']['rows'][$key]['contract_type_desc'] = Map::$v["contract_config"][$row['contract_type']][$row['contract_category']]["name"];
                }
            }
        }

        $attr["claim_status"] = $claimStatus;
        $data['search'] = $attr;
        $this->pageTitle = '后补项目合同付款认领';
        $this->render("index", $data);
    }

    public function actionView() {
        $apply_id = Mod::app()->request->getParam('apply_id');
        if (!Utility::checkQueryId($apply_id) || $apply_id < 0) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        $sql = 'select {col} from t_pay_claim a 
                left join t_contract c on c.contract_id = a.contract_id
                left join t_corporation b on a.corporation_id = b.corporation_id
                where apply_id=' . $apply_id . '
                and ' . AuthorizeService::getUserDataConditionString('a') . ' order by a.apply_id desc {limit}';
        $col = 'a.claim_id, a.apply_id, a.contract_id, a.currency, a.sub_contract_type, a.sub_contract_code, a.amount, a.status_time, c.type as contract_type, c.contract_code, b.name as corporation_name';
        $user = Utility::getNowUser();

        if (!empty($user['corp_ids'])) {
            $data = $this->queryTablesByPage($sql, $col);
        } else {
            $data = array();
        }
        $this->pageTitle = '后补项目合同认领列表';
        $this->render('view', $data);
    }

    public function actionAdd() {
        $apply_id = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($apply_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        $apply = PayApplication::model()->findByPk($apply_id);

        if (!$apply->isCanClaim()) {
            $this->renderError(BusinessError::outputError(OilError::$PAY_CLAIM_NOT_ALLOW_ADD));
        }

        $claim = $apply->getAttributes(array('apply_id', 'corporation_id', 'project_id', 'contract_id', 'sub_contract_id', 'sub_contract_type', 'sub_contract_code', 'type', 'subject_id', 'currency', 'exchange_rate'));
        $claim['amount_claim_balance'] = $apply->amount_paid - $apply->amount_claim;
        $this->pageTitle = '后补项目合同认领';
        $this->render('edit', array('apply' => $apply, 'payClaim' => $claim));
    }

    public function actionSave() {
        $params = Mod::app()->request->getParam('data');
        $requiredParams = array('apply_id', 'subject_id', 'type', 'corporation_id');
        if (!Utility::checkRequiredParamsNoFilterInject($params, $requiredParams) || !Utility::checkMustExistParams($params, array('amount'))) {
            $this->returnError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        //检查付款申请是否存在
        $apply = PayApplication::model()->findByPk($params['apply_id']);
        if (empty($apply)) {
            $this->returnError(BusinessError::outputError(OilError::$PAY_APPLICATION_NOT_EXIST, array('apply_id' => $params['apply_id'])));
        }

        if ($apply->status < PayApplication::STATUS_CHECKED) {
            $this->returnError(BusinessError::outputError(OilError::$PAY_APPLICATION_NOT_ALLOW_CLAIM, array('apply_id' => $params['apply_id'])));
        }

        if (!$apply->isCanClaim()) { //尚未实付，不能认领
            $this->renderError(BusinessError::outputError(OilError::$PAY_CLAIM_NOT_ALLOW_ADD));
        }

        $amountClaimBalance = $apply->amount_paid - $apply->amount_claim; //待认领金额
        if (bccomp($params['amount'], $amountClaimBalance, 2) === 1) { //认领金额超出待认领金额
            $this->returnError(BusinessError::outputError(OilError::$PAY_CLAIM_AMOUNT_OVERFLOW));
        }

        if (isset($params['items']) && Utility::isNotEmpty($params['items'])) {
            $check = PayClaimService::checkPaymentPlanPayClaimValid($params['items']);
            if ($check !== true) {
                $this->returnError($check);
            }
        }

        if (!empty($params['claim_id'])) {
            $payClaim = PayClaim::model()->findByPk($params['claim_id']);
        }

        if (!empty($params['sub_contract_code']) && !empty($params['sub_contract_type'])) {
            $file = ContractFile::model()->find('category = :category and code = :code and type = :type', array('category' => $params['sub_contract_type'], 'code' => $params['sub_contract_code'], 'type' => ConstantMap::FINAL_CONTRACT_FILE));
            if (!empty($file)) {
                $params['sub_contract_id'] = $file->file_id;
            }
        }

        if (!empty($payClaim->claim_id)) {
            if ($payClaim->status >= PayClaim::STATUS_SUBMITED) {
                $this->returnError(BusinessError::outputError(OilError::$PAY_CLAIM_NOT_ALLOW_SUBMIT, array('claim_id' => $payClaim->claim_id)));
            }
        } else {
            $payClaim = new PayClaim();
            $payClaim->status_time = Utility::getDateTime();
        }

        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try {
            //项目合同信息保存
            unset($params["claim_id"]);
            $logRemark = ActionLog::getEditRemark($payClaim->isNewRecord, "后补项目合同付款认领");
            $payClaim->setAttributes($params, false);
            $payClaim->save();

            if ($payClaim->status == PayClaim::STATUS_SUBMITED) {
                PayClaimService::updatePayApplicationAmount($apply, $payClaim->amount);

                //付款认领到合同时，调整合作方额度(合同下付款且是非税款)
                if (!empty($payClaim->contract_id) && in_array($payClaim->apply->subject_id, explode(',', ConstantMap::GOODS_FEE_SUBJECT_ID))) {
                    if(!($payClaim->apply->subject_id == ConstantMap::TAX_DEPOSIT_SUBJECT_ID && $payClaim->contract->agent_type == ConstantMap::AGENT_TYPE_PURE)){
                        $payClaimEntity = \ddd\infrastructure\DIService::getRepository(\ddd\domain\iRepository\payment\IPayClaimRepository::class)->findByPk($payClaim->claim_id);
                        if (empty($payClaimEntity->claim_id)) {
                            throw new \ddd\infrastructure\error\ZEntityNotExistsException($payClaim->claim_id, \ddd\domain\entity\payment\PayClaim::class);
                        }

                        $res = \ddd\application\payment\PaymentService::service()->submitPayClaim($payClaim->claim_id, $payClaimEntity);
                        if ($res !== true) {
                            throw new Exception($res);
                        }
                    }
                }
            }

            if (isset($params['items']) && Utility::isNotEmpty($params['items'])) {
                PayClaimService::savePayClaimDetail($params['items'], $payClaim->claim_id, $payClaim);
            }

            $trans->commit();
            if ($payClaim->status == PayClaim::STATUS_SUBMITED) {
                //更新利润报表的收付款利润
                \ddd\Profit\Application\PayReceiveEventService::service()->onPayClaim($payClaim->contract_id, $payClaim->subject_id);
            }

            Utility::addActionLog(json_encode($payClaim->oldAttributes), $logRemark, "PayClaim", $payClaim->claim_id);
            $this->returnSuccess($payClaim->claim_id);
        } catch (Exception $e) {
            try {
                $trans->rollback();
            } catch (Exception $ee) {
                Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $ee->getMessage(), CLogger::LEVEL_ERROR);
            }

            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $e->getMessage(), CLogger::LEVEL_ERROR);

            $this->returnError(BusinessError::outputError(OilError::$PROJECT_SAVE_ADD_ERROR, array('reason' => $e->getMessage())));
        }
    }

    public function actionDetail() {
        $claim_id = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($claim_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        $payClaim = PayClaim::model()->findByPk($claim_id);
        if (empty($payClaim)) {
            $this->renderError(BusinessError::outputError(OilError::$PAY_CLAIM_NOT_EXIST, array('claim_id' => $claim_id)));
        }
        $this->pageTitle = '后补项目合同认领详情';
        $this->render('detail', array('apply' => $payClaim->apply, 'payClaim' => $payClaim));
    }

    public function actionGetContractById() {
        $contractId = Mod::app()->request->getParam('contract_id');
        if (Utility::checkQueryId($contractId) && $contractId > 0) {
            $contract = Contract::model()->findByPk($contractId);
            if (empty($contract)) {
                $this->returnError(BusinessError::outputError(OilError::$PROJECT_CONTRACT_NOT_EXIST, array('contract_id' => $contractId)));
            }

            $data['contract_id'] = $contractId;
            $data['contract_type'] = $contract->type;
            $data['contract_type_desc'] = Map::$v['buy_sell_type'][$contract->type];
            $data['project_id'] = $contract->project_id;
            $data['project_code'] = $contract->project->project_code;
            $data['project_type_desc'] = Map::$v['project_type'][$contract->project->type];
            $plan = $contract->type == ConstantMap::BUY_TYPE ? $contract->payments : array();
            $expenseMap = $contract->type == ConstantMap::BUY_TYPE ? Map::$v['pay_type'] : Map::$v['proceed_type'];
            $endExpense = end($expenseMap);
            if (Utility::isNotEmpty($plan)) {
                foreach ($plan as $key => $row) {
                    $data['payment_plans'][$key] = $row->getAttributes(array('plan_id', 'period', 'pay_date', 'contract_id', 'amount', 'amount_paid', 'currency'));
                    $data['payment_plans'][$key]['expense_type_desc'] = $row['expense_type'] == $endExpense['id'] ? $row['expense_name'] : $expenseMap[$row['expense_type']]['name'];
                }
            } else {
                $data['payment_plans'] = array();
            }
            $this->returnSuccess($data);
        } else {
            $this->returnError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
    }
}