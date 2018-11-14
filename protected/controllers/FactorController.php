<?php

/**
 * Desc: 保理
 * User: susiehuang
 * Date: 2017/10/23 0009
 * Time: 10:03
 */
class FactorController extends ExportableController {
    public function pageInit() {
        $this->filterActions = 'computeInterest';
        $this->rightCode = 'factoring';
        $this->attachmentType = Attachment::C_FACTORING;
    }

    protected function getFileExtras() {
        $detail_id = Mod::app()->request->getParam("detail_id");
        $project_id = Mod::app()->request->getParam("project_id");

        return array('project_id' => $project_id, 'detail_id' => $detail_id);
    }

    public function actionExport() {
        $attr = Mod::app()->request->getParam('search');
        $fields = 'a.contract_code as 保理对接流水号, a.contract_code_fund as 资金对接流水号, a.apply_id as 付款申请编号, c.contract_code as 采购合同编号, cf.code_out as 外部合同编号, 
                   g.contract_code as 保理对接编号, g.contract_code_fund as 资金对接编号, a.status, a.pay_date as 合同放款时间, a.return_date as 合同回款时间,  
                   d.name as 上游合作方, e.name as 交易主体, a.rate, b.amount/100 as 付款申请金额, a.amount/100 as 对接本金, f.name as 申请人, a.create_time as 申请时间';

        $sql = 'select ' . $fields . ' from t_factoring_detail a 
                left join t_factoring g on g.factor_id=a.factor_id
                left join t_pay_application b on b.apply_id = a.apply_id 
                left join t_contract c on c.contract_id = a.contract_id 
                left join t_partner d on d.partner_id = c.partner_id 
                left join t_corporation e on e.corporation_id = a.corporation_id  
                left join t_system_user f on f.user_id = a.create_user_id 
                left join t_contract_file cf on cf.contract_id = c.contract_id and cf.is_main=1 and cf.type=1 ' .
                $this->getWhereSql($attr) . ' and g.status >= ' . Factor::STATUS_CONFIRMED . ' and ' . AuthorizeService::getUserDataConditionString('g') . ' order by a.detail_id desc';

        $user = Utility::getNowUser();
        if (!empty($user['corp_ids'])) {
            $data = Utility::query($sql);
            if (Utility::isNotEmpty($data)) {
                foreach ($data as $key => $row) {
                    $data[$key]['年化利率'] = ($row['rate'] * 100) . '%';
                    unset($data[$key]['rate']);
                    $data[$key]['保理状态'] = Map::$v['factor_detail_status'][$row['status']];
                    unset($data[$key]['status']);
                }
            }
        } else {
            $data = array();
        }
        $this->exportExcel($data);
    }

    public function actionIndex() {
        $attr = Mod::app()->request->getParam('search');
        $fields = 'a.detail_id, a.contract_code as water_code, a.contract_code_fund as fund_water_code, a.contract_id, a.corporation_id, a.project_id, a.status, a.amount, a.create_time, 
                   a.apply_id, b.amount as pay_apply_amount, b.currency, g.contract_code, g.contract_code_fund, c.contract_code as c_code, c.partner_id, d.name as partner_name, 
                   e.name as corporation_name, f.name as create_name, a.rate, a.pay_date, a.return_date, cf.code_out';
        $sql = 'select {col} from t_factoring_detail a 
                left join t_factoring g on g.factor_id=a.factor_id
                left join t_pay_application b on b.apply_id = a.apply_id 
                left join t_contract c on c.contract_id = a.contract_id 
                left join t_partner d on d.partner_id = c.partner_id 
                left join t_corporation e on e.corporation_id = a.corporation_id  
                left join t_system_user f on f.user_id = a.create_user_id 
                left join t_contract_file cf on cf.contract_id = c.contract_id and cf.is_main=1 and cf.type=1 ' . $this->getWhereSql($attr) . ' and g.status >= ' . Factor::STATUS_CONFIRMED . ' 
                and ' . AuthorizeService::getUserDataConditionString('g') . ' order by a.detail_id desc {limit}';

        $user = Utility::getNowUser();
        if (!empty($user['corp_ids'])) {
            $data = $this->queryTablesByPage($sql, $fields);
        } else {
            $data = array();
        }
        $this->pageTitle = '保理对接列表';
        $this->render('index', $data);
    }

    public function actionList() {
        $attr = Mod::app()->request->getParam('search');
        $fields = 'a.factor_id, a.apply_id, a.contract_code, a.contract_code_fund, a.amount, a.rate, a.project_id, a.contract_id, a.corporation_id, c.contract_code as c_code, 
                   c.partner_id, d.name as partner_name, c.corporation_id, e.name as corporation_name, b.amount as pay_apply_amount, b.currency, b.create_time, p.project_code, p.type';
        $sql = 'select {col} from t_factoring a 
                left join t_pay_application b on b.apply_id = a.apply_id 
                left join t_contract c on c.contract_id = a.contract_id 
                left join t_partner d on d.partner_id = c.partner_id 
                left join t_corporation e on e.corporation_id = b.corporation_id  
                left join t_project p on p.project_id = a.project_id ' . $this->getWhereSql($attr) . ' and a.status >= ' . Factor::STATUS_CONFIRMED . ' 
                and ' . AuthorizeService::getUserDataConditionString('a') . ' order by a.factor_id desc {limit}';

        $user = Utility::getNowUser();
        if (!empty($user['corp_ids'])) {
            $data = $this->queryTablesByPage($sql, $fields);
        } else {
            $data = array();
        }
        if (Utility::isNotEmpty($data['data']['rows'])) {
            foreach ($data['data']['rows'] as $key => $row) {
                $data['data']['rows'][$key]['checking_amount'] = FactoringDetailService::getFactorAmountById($row['factor_id'], ' and status = ' . FactorDetail::STATUS_SUBMIT);
                $data['data']['rows'][$key]['butted_amount'] = FactoringDetailService::getFactorAmountById($row['factor_id'], ' and status >= ' . FactorDetail::STATUS_PASS);
            }
        }
        $this->pageTitle = '保理申请';
        $this->render('list', $data);
    }

    public function actionAdd() {
        $factor_id = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($factor_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $factorModel = Factor::model()->findByPk($factor_id);
        if (empty($factorModel->factor_id)) {
            $this->renderError(BusinessError::outputError(OilError::$FACTOR_NOT_EXIST, array('factor_id' => $factor_id)));
        }

        if (!FactoringDetailService::checkIsCanAdd($factor_id)) {
            $this->renderError(BusinessError::outputError(OilError::$FACTOR_NOT_ALLOW_ADD));
        }

        $detail = $factorModel->getAttributes(array('factor_id', 'apply_id', 'corporation_id', 'project_id', 'contract_id', 'rate'));
        $detail['detail_id'] = IDService::getFactorDetailId();

        $attachments = FactoringDetailService::getAttachments($detail['detail_id'], $factor_id);

        $this->pageTitle = '保理对接';
        $this->render('edit', array('factor' => $factorModel, 'data' => $detail, 'attachments' => $attachments));
    }

    public function actionEdit() {
        $detail_id = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($detail_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $factorDetailModel = FactorDetail::model()->findByPk($detail_id);
        if (empty($factorDetailModel->detail_id)) {
            $this->renderError(BusinessError::outputError(OilError::$FACTOR_DETAIL_NOT_EXIST, array('detail_id' => $detail_id)));
        }

        if (!$factorDetailModel->isCanEdit($factorDetailModel->status)) {
            $this->renderError(BusinessError::outputError(OilError::$FACTOR_NOT_ALLOW_EDIT));
        }

        $factor = Factor::model()->findByPk($factorDetailModel->factor_id);
        $detail = $factorDetailModel->getAttributes(true, Utility::getCommonIgnoreAttributes());
        if (empty($detail['return_date'])) {
            $detail['return_date'] = Utility::getDate();
        }
        if (empty($detail['pay_date'])) {
            $detail['pay_date'] = Utility::getDate();
        }

        $attachments = FactoringDetailService::getAttachments($detail_id, $factorDetailModel->factor_id);

        $this->pageTitle = '保理信息修改';
        $this->render('edit', array('factor' => $factor, 'data' => $detail, 'attachments' => $attachments));
    }

    public function actionComputeInterest() {
        $params = Mod::app()->request->getParam('data');
        $requiredParams = array('amount', 'rate', 'start_date', 'end_date');
        if (!Utility::checkMustExistParams($params, $requiredParams)) {
            $this->returnError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        $days360 = Utility::diffDays360($params['start_date'], $params['end_date']);
        $interest = round($params['amount'] * $params['rate'] / 360 * $days360);
        $this->returnSuccess($interest);
    }

    public function actionSave() {
        $params = Mod::app()->request->getParam('data');

        $requiredParams = array('detail_id', 'factor_id', 'apply_id', 'project_id', 'corporation_id', 'contract_id', 'pay_date', 'return_date');
        if (!Utility::checkRequiredParamsNoFilterInject($params, $requiredParams) || !Utility::checkMustExistParams($params, array('amount'))) {
            $this->returnError(BusinessError::outputError(OilError::$REQUIRED_PARAMS_CHECK_ERROR));
        }

        $factorModel = Factor::model()->findByPk($params['factor_id']);
        if (empty($factorModel->factor_id)) {
            $this->returnError(BusinessError::outputError(OilError::$FACTOR_NOT_EXIST, array('factor_id' => $params['factor_id'])));
        }
        if (empty($factorModel->payApply)) {
            $this->returnError(BusinessError::outputError(OilError::$PAY_APPLICATION_NOT_EXIST, array('apply_id' => $factorModel->apply_id)));
        }
        if (empty($factorModel->contract)) {
            $this->returnError(BusinessError::outputError(OilError::$PROJECT_CONTRACT_NOT_EXIST, array('contract_id' => $factorModel->contract_id)));
        }
        if (empty($factorModel->project)) {
            $this->returnError(BusinessError::outputError(OilError::$PROJECT_NOT_EXIST, array('contract_id' => $factorModel->project_id)));
        }

        if (bccomp($params['amount'], $params['balance_amount'], 2) == 1) {
            $this->returnError(BusinessError::outputError(OilError::$FACTOR_AMOUNT_GT_BALANCE_AMOUNT, array('amount' => $params['amount'] / 100, 'balance_amount' => $params['balance_amount'] / 100)));
        }

        //必传附件校验
        $attachCheckRes = FactoringDetailService::checkRequiredAttachmens($params['detail_id'], $params['factor_id']);
        if ($attachCheckRes !== true) {
            $this->returnError($attachCheckRes);
        }

        $factorDetail = FactorDetail::model()->findByPk($params['detail_id']);
        if (!empty($factorDetail)) {
            if (!$factorDetail->isCanEdit($factorDetail['status'])) {
                $this->renderError(BusinessError::outputError(OilError::$FACTOR_NOT_ALLOW_EDIT));
            }
        } else {
            $factorDetail = new FactorDetail();
            $factorDetail->detail_id = $params['detail_id'];
            if (Utility::lock(FactoringDetailService::FACTOR_DETAIL_CONTRACT_CODE_LOCK)) { //加锁，并发控制
                $factorDetail->contract_code = FactoringDetailService::generateFactorDetailContractCode($factorModel->factor_id, 3);
            } else {
                $this->returnError(BusinessError::outputError(OilError::$SYSTEM_BUSY));
            }
            $factorDetail->contract_code_fund = FactoringDetailService::generateFactorDetailContractCodeFund($factorModel->factor_id, 3, $factorModel);
        }

        $factorDetail->status_time = Utility::getDateTime();

        $logRemark = ActionLog::getEditRemark($factorDetail->isNewRecord, "保理对接");
        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try {
            unset($params['detail_id']);
            $factorDetail->setAttributes($params, false);
            $factorDetail->save();

            Utility::clearCache(FactoringDetailService::FACTOR_DETAIL_CONTRACT_CODE_LOCK); //解锁

            if ($factorDetail->status == FactorDetail::STATUS_SUBMIT) {
                FlowService::startFlowForCheck14($factorDetail->detail_id);

                if (!FactoringDetailService::checkIsCanAdd($factorModel->factor_id)) { //保理对接金额申请完，消去保理申请代办
                    TaskService::doneTask($factorModel->factor_id, Action::ACTION_FACTOR_APPLY, ActionService::getActionRoleIds(Action::ACTION_FACTOR_APPLY));
                }
            }
            TaskService::doneTask($factorDetail->detail_id, Action::ACTION_35);

            $trans->commit();
            Utility::addActionLog(json_encode($factorDetail->oldAttributes), $logRemark, "FactorDetail", $factorDetail->detail_id);
            $this->returnSuccess($factorDetail->detail_id);
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
        $params = Mod::app()->request->getParam('data');
        if (!Utility::checkQueryId($params['detail_id'])) {
            $this->returnError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $factorDetailModel = FactorDetail::model()->findByPk($params['detail_id']);
        if (empty($factorDetailModel->detail_id)) {
            $this->returnError(BusinessError::outputError(OilError::$FACTOR_DETAIL_NOT_EXIST, array('detail_id' => $params['detail_id'])));
        }

        if ($params['status'] == FactorDetail::STATUS_SUBMIT && !$factorDetailModel->isCanEdit($factorDetailModel->status)) {
            $this->returnError(BusinessError::outputError(OilError::$FACTOR_DETAIL_NOT_ALLOW_SUBMIT));
        }

        $factorDetailModel->status_time = Utility::getDateTime();
        $oldStatus = $factorDetailModel->status;
        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try {
            unset($params["detail_id"]);
            if (!empty($params['interest'])) {
                $params['interest'] *= 100;
            }
            $factorDetailModel->setAttributes($params, false);
            $factorDetailModel->save();
            FlowService::startFlowForCheck14($factorDetailModel->detail_id);
            if (!FactoringDetailService::checkIsCanAdd($factorDetailModel->factor_id)) { //保理对接金额申请完，消去保理申请代办
                TaskService::doneTask($factorDetailModel->factor_id, Action::ACTION_FACTOR_APPLY, ActionService::getActionRoleIds(Action::ACTION_FACTOR_APPLY));
            }
            TaskService::doneTask($factorDetailModel->detail_id, Action::ACTION_35);

            $trans->commit();

            Utility::addActionLog(json_encode(array('oldStatus' => $oldStatus)), "提交保理对接", "FactorDetail", $factorDetailModel->detail_id);

            $this->returnSuccess();
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
        $detail_id = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($detail_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $factorDetailModel = FactorDetail::model()->findByPk($detail_id);
        if (empty($factorDetailModel->detail_id)) {
            $this->renderError(BusinessError::outputError(OilError::$FACTOR_DETAIL_NOT_EXIST, array('detail_id' => $detail_id)));
        }

        $factor = Factor::model()->findByPk($factorDetailModel->factor_id);
        $attachments = FactoringDetailService::getAttachments($detail_id, $factorDetailModel->factor_id);

        $this->pageTitle = '保理对接申请明细';
        $this->render('detail', array('factor' => $factor, 'detail' => $factorDetailModel, 'attachments' => $attachments));
    }
}