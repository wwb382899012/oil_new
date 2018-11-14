<?php

/**
 * Desc: 保理编号管理
 * User: susiehuang
 * Date: 2017/10/23 0009
 * Time: 10:03
 */
class FactorCodeController extends ExportableController {
    public function pageInit() {
        $this->filterActions = '';
        $this->rightCode = 'factorcode';
    }

    public function actionExport() {
        $attr = Mod::app()->request->getParam('search');
        $fields = 'ifnull(b.contract_code,"") as 保理对接编号, a.code as 资金对接编号, case when b.apply_id=0 then "" else apply_id end as 付款申请编号, a.create_time as 取号时间, a.type, a.remark as 备注';

        $sql = 'select ' . $fields . ' from t_factoring_fund_code a 
                left join t_factoring b on a.code = b.contract_code_fund ' . $this->getWhereSql($attr) . ' order by id desc';

        $data = Utility::query($sql);
        if (Utility::isNotEmpty($data)) {
            foreach ($data as $key => $row) {
                $data[$key]['保理类型'] = Map::$v['factor_code_type'][$row['type']];
                unset($data[$key]['type']);
            }
        }
        $this->exportExcel($data);
    }

    public function actionIndex() {
        $attr = $_GET[search];
        $sql = 'select {col} from t_factoring_fund_code a 
                left join t_factoring b on a.code = b.contract_code_fund ' . $this->getWhereSql($attr) . ' order by a.id desc {limit}';
        $fields = 'a.code, a.create_time, a.type, a.remark, ifnull(b.contract_code,"") as contract_code, case when b.apply_id=0 then "" else apply_id end as apply_id';
        $data = $this->queryTablesByPage($sql, $fields);
        $this->pageTitle = '保理编号管理';
        $this->render('index', $data);
    }

    public function actionAdd() {
        $this->pageTitle = '保理编号取号';
        $this->render('edit');
    }

    public function actionSubmit() {
        $params = Mod::app()->request->getParam('data');
        $requiredParams = array('remark');
        if ($params['type'] == FactorFundCode::TYPE_INTERNAL) {
            array_push($requiredParams, 'corporation_id');
        }
        if (!Utility::checkRequiredParamsNoFilterInject($params, $requiredParams) || !array_key_exists('type', $params)) {
            $this->returnError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        $params['code'] = CodeService::getFactoringFundCode();
        if ($params['type'] == FactorFundCode::TYPE_INTERNAL) {
            $codeInfo = CodeService::getFactoringCode($params['corporation_id']);
            if ($codeInfo['code'] == ConstantMap::INVALID) {
                $this->returnError(BusinessError::outputError(OilError::$FACTOR_CODE_GENERATE_ERROR));
            }

            $factor = new Factor();
            $factor->contract_code = $codeInfo['data'];
            $factor->contract_code_fund = $params['code'];
            $factor->corporation_id = $params['corporation_id'];
            $factor->remark = '保理编号取号生成记录';
        }
        $obj = FactorFundCode::model()->find('code="' . $params['code'] . '" and type=' . $params['type']);
        if (empty($obj)) {
            $obj = new FactorFundCode();
        }
        $obj->setAttributes($params, false);
        $logRemark = ActionLog::getEditRemark($obj->isNewRecord, "保理编号");

        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try {
            if ($params['type'] == FactorFundCode::TYPE_INTERNAL && !empty($factor)) {
                $factor->save();
            }

            $obj->save();

            $trans->commit();
            if ($params['type'] == FactorFundCode::TYPE_INTERNAL && !empty($factor)) {
                Utility::addActionModelLog($factor, '保理编号取号生成保理对接编号记录');
            }

            Utility::addActionLog(json_encode($obj->oldAttributes), $logRemark, "FactorFundCode", $obj->id);
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
}
