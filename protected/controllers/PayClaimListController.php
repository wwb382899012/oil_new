
<?php
/**
*	付款认领
*/
class PayClaimListController extends ExportableController {

    public function pageInit() {
        $this->filterActions = "";
        $this->rightCode = "payClaimList";
    }

    public function actionIndex() {
        $attr = Mod::app()->request->getParam('search');

        $sql = 'select {col} 
                from t_pay_claim a 
                left join t_contract c on c.contract_id = a.contract_id
                left join t_corporation b on a.corporation_id = b.corporation_id
                ' . $this->getWhereSql($attr). ' and ' . AuthorizeService::getUserDataConditionString('c') . '
                order by a.apply_id desc {limit}';
        $col = 'a.*, c.type as contract_type, c.contract_code, b.name as corporation_name, b.code as stock_in_code';
        $export_str = Mod::app()->request->getParam('export_str');
        $user = Utility::getNowUser();
        if(!empty($export_str)) {
            if(!empty($user['corp_ids'])) {
                $this->export($sql, $col, $export_str);
            }
            return;
        } else {
            if (!empty($user['corp_ids'])) {
                $data = $this->queryTablesByPage($sql, $col);
            }else{
                $data = array();
            }
        }

        $this->pageTitle = '后补项目合同认领列表';
        $this->render('/payClaim/view', $data);
    }
}