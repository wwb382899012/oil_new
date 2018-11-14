<?php

/**
 * Desc: 保理对接款
 * User: susiehuang
 * Date: 2017/10/23 0009
 * Time: 10:03
 */
class FactorAmountController extends Controller {
    public function pageInit() {
        $this->filterActions = '';
        $this->rightCode = 'factoramount';
//        $this->newUIPrefix = 'new_';
    }

    public function actionIndex() {
        $attr = Mod::app()->request->getParam('search');
        $fields = 'a.apply_id, a.contract_code, a.contract_code_fund, a.status, a.amount, a.rate, a.apply_amount, c.contract_code as c_code, a.corporation_id, a.contract_id, cf.code_out,
                   a.factor_id, a.project_id, c.partner_id, a.status, b.amount as pay_apply_amount, b.currency, p.project_code, p.type, d.name as partner_name, e.name as corp_name, a.actual_pay_date';
        $sql = 'select {col} from t_factoring a 
                left join t_pay_application b on b.apply_id = a.apply_id 
                left join t_contract c on c.contract_id = a.contract_id 
                left join t_partner d on d.partner_id = c.partner_id 
                left join t_corporation e on e.corporation_id = b.corporation_id 
                left join t_project p on p.project_id = a.project_id 
                left join t_contract_file cf on cf.contract_id=c.contract_id and cf.is_main=1 and cf.type=1 ' . $this->getWhereSql($attr) . ' 
                and ' . AuthorizeService::getUserDataConditionString('a') . ' and a.status >= ' . Factor::STATUS_SUBMIT . ' and a.status <= ' . Factor::STATUS_CONFIRMED . ' and b.status >= ' . PayApplication::STATUS_SUBMIT . ' order by a.factor_id desc {limit}';
        $user = Utility::getNowUser();
        if (!empty($user['corp_ids'])) {
            $data = $this->queryTablesByPage($sql, $fields);
        } else {
            $data = array();
        }
        $this->pageTitle = '保理对接款管理';
        $this->render('index', $data);
    }

    public function actionEdit() {
        $factor_id = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($factor_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $factorModel = Factor::model()->findByPk($factor_id);
        if (empty($factorModel->factor_id)) {
            $this->renderError(BusinessError::outputError(OilError::$FACTOR_NOT_EXIST, array('factor_id' => $factor_id)));
        }

        if (!FactoringService::checkIsCanConfirm($factorModel->status)) {
            $this->renderError(BusinessError::outputError(OilError::$FACTOR_NOT_ALLOW_EDIT));
        }

        $factor = $factorModel->getAttributes(array('factor_id', 'amount', 'rate', 'status'));
        $factor['pay_apply_amount'] = $factorModel->payApply->amount * $factorModel->payApply->exchange_rate;

        $this->pageTitle = '保理对接款确认';
        $this->render('edit', array('data' => $factorModel, 'factor' => $factor));
    }

    public function actionSubmit() {
        $params = Mod::app()->request->getParam('data');
        if (!Utility::checkQueryId($params['factor_id'])) {
            $this->returnError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $factorModel = Factor::model()->findByPk($params['factor_id']);
        if (empty($factorModel->factor_id)) {
            $this->returnError(BusinessError::outputError(OilError::$FACTOR_NOT_EXIST, array('factor_id' => $params['factor_id'])));
        }

        if ($factorModel->status == Factor::STATUS_TRASHED) {
            $this->returnError("该保理对接对应付款申请单已经作废", -1);
        }

        if (!FactoringService::checkIsCanConfirm($factorModel->status)) {
            $this->returnError(BusinessError::outputError(OilError::$FACTOR_NOT_ALLOW_DOCONFIRM));
        }

        $factorModel->status_time = Utility::getDateTime();
        unset($params["factor_id"]);
        $oldStatus = $factorModel->status;
        $factorModel->setAttributes($params, false);
        $factorModel->rate = $params['rate'] / 100;
        $res = $factorModel->save();
        if ($res === true) {
            TaskService::doneTask($factorModel->factor_id, Action::ACTION_FACTOR_AMOUNT_CONFIRM, ActionService::getActionRoleIds(Action::ACTION_FACTOR_AMOUNT_CONFIRM));
            if (FactoringDetailService::checkIsCanAdd($factorModel->factor_id)) { //保理申请代办
                TaskService::addTasks(Action::ACTION_FACTOR_APPLY, $factorModel->factor_id, ActionService::getActionRoleIds(Action::ACTION_FACTOR_APPLY), 0, $factorModel->corporation_id, array('contractCode' => $factorModel->contract->contract_code, 'applyId' => $factorModel->apply_id, 'factorCode' => $factorModel->contract_code));
            }
            Utility::addActionLog(json_encode(array('oldStatus' => $oldStatus)), "确认保理对接款", "Factor", $factorModel->factor_id);
            $this->returnSuccess();
        } else {
            $this->returnError(BusinessError::outputError(OilError::$OPERATE_FAILED, array('reason' => $res)));
        }
    }
}