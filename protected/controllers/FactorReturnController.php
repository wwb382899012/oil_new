<?php

/**
 * Desc: 保理回款
 * User: susiehuang
 * Date: 2017/10/23 0009
 * Time: 10:03
 */
class FactorReturnController extends Controller {
    public function pageInit() {
        $this->filterActions = 'calculateReturnAmount';
        $this->rightCode = 'factorreturn';
    }

    public function actionIndex() {
        $attr = Mod::app()->request->getParam('search');

        $sql = 'select {col} from t_factoring_detail a 
                left join t_factoring f on f.factor_id = a.factor_id
                left join t_pay_application b on a.apply_id = b.apply_id 
                left join t_contract c on c.contract_id = a.contract_id 
                left join t_project d on d.project_id = a.project_id 
                left join t_corporation e on e.corporation_id = a.corporation_id 
                left join t_contract_file cf on cf.contract_id = c.contract_id and cf.is_main=1 and cf.type=1 ' . $this->getWhereSql($attr) . ' 
                and a.status >= ' . FactorDetail::STATUS_PASS . ' and f.status >= ' . Factor::STATUS_CONFIRMED . ' 
                and ' . AuthorizeService::getUserDataConditionString('a') . ' order by a.detail_id desc {limit}';

        $fields = 'a.detail_id, a.contract_code as water_code, a.status, a.contract_id, a.project_id, a.apply_id, a.corporation_id, f.contract_code, f.contract_code_fund, c.contract_code as c_code, 
                   d.project_code, e.name as corp_name, a.return_date, a.pay_date, a.amount, a.interest, (a.amount + a.interest) as total_amount, a.rate, cf.code_out, 
                   a.return_capital, a.return_interest, a.return_amount, a.amount * 0.5 / 100 as factor_service_fee, a.amount * 0.275 / 100 as service_fee, a.factor_id';
        $user = Utility::getNowUser();
        if ($user['corp_ids']) {
            $data = $this->queryTablesByPage($sql, $fields);
        } else {
            $data = array();
        }

        if (Utility::isNotEmpty($data['data']['rows'])) {
            foreach ($data['data']['rows'] as $key => $row) {
                $data['data']['rows'][$key]['contract_period'] = Utility::diffDays360($row['pay_date'], $row['return_date']);
                //$data['data']['rows'][$key]['actual_pay_date'] = FactoringReturnService::getActualPaymentDay($row['detail_id']);
                $data['data']['rows'][$key]['balance_capital'] = 0;
                $data['data']['rows'][$key]['balance_interest'] = 0;
                if ($row['status'] < FactorDetail::STATUS_RETURNED) {
                    $data['data']['rows'][$key]['balance_capital'] = $row['amount'] - $row['return_capital'];
                    $lastReturnInfo = FactoringReturnService::getLastReturnDate($row['detail_id']);
                    if ($lastReturnInfo['res'] == ConstantMap::INVALID) {
                        $this->renderError($lastReturnInfo['msg']);
                    }
                    $data['data']['rows'][$key]['balance_interest'] = FactoringService::calculatePeriodInterest($row['detail_id'], $lastReturnInfo['last_return_date'], Utility::getDate());
                }
            }
        }
        $this->pageTitle = '保理回款列表';
        $this->render('index', $data);
    }

    public function actionAdd() {
        $detail_id = Mod::app()->request->getParam('detail_id');
        if (!Utility::checkQueryId($detail_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        if (!FactoringReturnService::checkIsCanAddFactorReturn($detail_id)) {
            $this->renderError(BusinessError::outputError(OilError::$FACTOR_RETURN_NOT_ALLOW_ADD));
        }

        /*$actualPayDate = FactoringReturnService::getActualPaymentDay($detail_id);
        if (empty($actualPayDate)) {
            $this->renderError(BusinessError::outputError(OilError::$FACTOR_NOT_ACTUAL_PAY));
        }*/

        $factorDetail = FactorDetail::model()->findByPk($detail_id);
        if (empty($factorDetail)) {
            $this->renderError(BusinessError::outputError(OilError::$FACTOR_DETAIL_NOT_EXIST, array('detail_id' => $detail_id)));
        }

        $factorDetailInfo = FactoringReturnService::getFactorDetailInfo($detail_id, $factorDetail);

        $factorReturn['detail_id'] = $detail_id;
        $factorReturn['factor_id'] = $factorDetail->factor_id;
        $factorReturn['return_date'] = $factorDetailInfo['curr_return_date'];
        try {
            $res = FactoringReturnService::getBalanceAmount($detail_id, $factorReturn['return_date'], $factorDetail);
            $factorReturn['amount'] = $res['amount'];
            $factorReturn['capital_amount'] = $res['capital_amount'];
            $factorReturn['interest'] = $res['interest'];
        } catch (Exception $e) {
            $this->renderError(BusinessError::outputError(OilError::$OPERATE_FAILED, array('reason' => $e->getMessage())));
        }
        $this->pageTitle = '添加保理回款信息';
        $this->render('edit', array('factor' => $factorDetailInfo, 'data' => $factorReturn));
    }

    public function actionCalculateReturnAmount() {
        $detailId = Mod::app()->request->getParam('detail_id');
        $returnDate = Mod::app()->request->getParam('return_date');
        if (!Utility::checkQueryId($detailId) || empty($returnDate)) {
            $this->returnError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        try {
            $res = FactoringReturnService::getBalanceAmount($detailId, $returnDate);
            $this->returnSuccess($res);
        } catch (Exception $e) {
            $this->returnError(BusinessError::outputError(OilError::$OPERATE_FAILED, array('reason' => $e->getMessage())));
        }
    }

    public function actionEdit() {
        $id = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $factorReturnModel = FactorReturn::model()->findByPk($id);
        if (empty($factorReturnModel->id)) {
            $this->renderError(BusinessError::outputError(OilError::$FACTOR_RETURN_NOT_EXIST, array('id' => $id)));
        }

        if ($factorReturnModel->status > FactorReturn::STATUS_NEW) {
            $this->renderError(BusinessError::outputError(OilError::$FACTOR_RETURN_NOT_ALLOW_EDIT));
        }

        $factorReturn = $factorReturnModel->getAttributes(true, Utility::getCommonIgnoreAttributes());
        $factorDetailInfo = FactoringReturnService::getFactorDetailInfo($factorReturn['detail_id']);

        $this->pageTitle = '修改保理回款信息';
        $this->render('edit', array('factor' => $factorDetailInfo, 'data' => $factorReturn));
    }

    public function actionSave() {
        $params = Mod::app()->request->getParam('data');

        $requiredParams = array('detail_id', 'factor_id', 'return_date', 'amount');
        if (!Utility::checkRequiredParamsNoFilterInject($params, $requiredParams)) {
            $this->returnError(BusinessError::outputError(OilError::$REQUIRED_PARAMS_CHECK_ERROR));
        }

        $factorDetailModel = FactorDetail::model()->findByPk($params['detail_id']);
        if (empty($factorDetailModel->detail_id)) {
            $this->returnError(BusinessError::outputError(OilError::$FACTOR_DETAIL_NOT_EXIST, array('detail_id' => $params['detail_id'])));
        }

        if ($factorDetailModel->status < FactorDetail::STATUS_PASS) {
            $this->returnError(BusinessError::outputError(OilError::$FACTOR_NOT_ALLOW_RETURN));
        }

        /*$actualPayDate = FactoringReturnService::getActualPaymentDay($params['detail_id']);
        if (empty($actualPayDate)) {
            $this->returnError(BusinessError::outputError(OilError::$FACTOR_NOT_ACTUAL_PAY));
        }*/

        if (bccomp($params['actual_interest'], $params['amount'], 2) == 1) {
            $this->returnError(BusinessError::outputError(OilError::$FACTOR_ACTUAL_INTEREST_GR_AMOUNT));
        }

        $r = bccomp($params['amount'], ($params['capital_amount'] + $params['interest']), 2);
        if ($r != 0) {
            $this->returnError(BusinessError::outputError(OilError::$AMOUNT_CAPITAL_INTEREST_NOT_MATCH));
        }

        if (!empty($params['id'])) {
            $factorReturnModel = FactorReturn::model()->findByPk($params['id']);
        }

        if (empty($factorReturnModel->id)) {
            $factorReturnModel = new FactorReturn();
        }

        unset($params['id']);
        $factorReturnModel->setAttributes($params, false);
        $logRemark = ActionLog::getEditRemark($factorReturnModel->isNewRecord, "保理回款");
        $res = $factorReturnModel->save();
        if ($res === true) {
            Utility::addActionLog(json_encode($factorReturnModel->oldAttributes), $logRemark, "FactorReturn", $factorReturnModel->id);
            $this->returnSuccess($factorReturnModel->id);
        } else {
            $this->returnError(BusinessError::outputError(OilError::$OPERATE_FAILED, array('reason' => $res)));
        }
    }

    public function actionDetail() {
        $detail_id = Mod::app()->request->getParam('detail_id');
        if (!Utility::checkQueryId($detail_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $factorDetailModel = FactorDetail::model()->findByPk($detail_id);
        if (empty($factorDetailModel->detail_id)) {
            $this->renderError(BusinessError::outputError(OilError::$FACTOR_DETAIL_NOT_EXIST, array('detail_id' => $detail_id)));
        }

        $sql = 'select {col} from t_factoring_return where detail_id = ' . $detail_id . ' order by id asc  {limit}';
        $fields = 'id, return_date, amount, capital_amount, interest, status';
        $data = $this->queryTablesByPage($sql, $fields);
        if (Utility::isNotEmpty($data['data']['rows'])) {
            foreach ($data['data']['rows'] as $key => $row) {
                $data['data']['rows'][$key]['period'] = FactoringReturnService::getActualReturnedPeriod($row['id']);
                $data['data']['rows'][$key]['overdue_period'] = strtotime($row['return_date']) > strtotime($factorDetailModel->return_date) ? Utility::diffDays360($factorDetailModel->return_date, $row['return_date']) : 0;
            }
        }

        $this->pageTitle = '保理回款明细';
        $this->render('detail', $data);
    }

    public function actionSubmit() {
        $params = Mod::app()->request->getParam('data');
        if (!Utility::checkQueryId($params['id'])) {
            $this->returnError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $factorReturnModel = FactorReturn::model()->findByPk($params['id']);
        if (empty($factorReturnModel->id)) {
            $this->returnError(BusinessError::outputError(OilError::$FACTOR_RETURN_NOT_EXIST, array('id' => $params['id'])));
        }

        if ($factorReturnModel->status >= FactorReturn::STATUS_SUBMIT) {
            $this->returnError(BusinessError::outputError(OilError::$FACTOR_RETURN_NOT_ALLOW_SUBMIT));
        }

        unset($params["id"]);
        $oldStatus = $factorReturnModel->status;
        $factorReturnModel->setAttributes($params, false);
        $res = $factorReturnModel->save();
        if ($res === true) {
            Utility::addActionLog(json_encode(array('oldStatus' => $oldStatus)), "提交保理回款", "FactorReturn", $factorReturnModel->id);
            $this->returnSuccess($factorReturnModel->id);
        } else {
            $this->returnError(BusinessError::outputError(OilError::$OPERATE_FAILED, array('reason' => $res)));
        }
    }
}