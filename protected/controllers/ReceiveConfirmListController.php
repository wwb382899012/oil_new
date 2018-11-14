
<?php
/**
*	银行流水认领
*/
class ReceiveConfirmListController extends ExportableController {

    public function pageInit() {
        $this->attachmentType = Attachment::C_RECEIVE_CONFIRM_IMPORT;
        $this->filterActions = "ajaxContract,ajaxProject,ajaxContractPayments,getFile,view";
        $this->rightCode = "receiveConfirmList";
        $this->newUIPrefix = 'new_';
    }

    public function actionIndex() {
//        $attr = Mod::app()->request->getParam('search');
        $attr = $this->getSearch();

        $sql = 'select {col} 
                from t_receive_confirm r
                join t_bank_flow a on r.flow_id=a.flow_id 
        		left join t_corporation b on a.corporation_id = b.corporation_id
                left join t_system_user u on u.user_id = r.create_user_id
                left join t_project p on p.project_id=r.project_id
                left join t_contract c on c.contract_id=r.contract_id 
                left join t_finance_subject fs on fs.subject_id=r.subject 
                left join t_contract_file cf on cf.contract_id=c.contract_id and cf.is_main=1 and cf.type=1 
                ' . $this->getWhereSql($attr). ' and '.AuthorizeService::getUserDataConditionString('a').'
                order by a.flow_id desc {limit}';
        $col = 'r.*, a.*, r.amount as received_amount, r.status as receive_status, r.create_time, r.create_user_id, r.subject, b.name as corporation_name, u.name as user_name, 
                p.project_code, p.type as project_type, c.contract_code, c.type as contract_type, cf.code_out, fs.name as subject_name';
        $export_str = Mod::app()->request->getParam('export_str');
        $user = Utility::getNowUser();
        if(!empty($export_str)) {
            if (!empty($user['corp_ids'])) {
                $this->export($sql, $col, $export_str);
            }
            return;
        } else {
            if (!empty($user['corp_ids'])) {
                $data = $this->queryTablesByPage($sql, $col);
            } else {
                $data = array();
            }
        }

        $this->pageTitle = '收款列表';
        $this->render('index', $data);
    }

    public function actionExport()
    {
        $attr = $this->getSearch();

        $col = 'r.receive_id 收款编号, concat(r.flow_id, " ") 收款流水ID, concat(a.code, " ") 银行流水编号, b.name 交易主体, a.bank_name 收款银行, a.account_name 银行账户名,
                a.pay_partner 付款公司, u.name 认领人, p.project_code 项目编号, c.contract_code 货款合同编号, cf.code_out 外部合同编号, r.sub_contract_code 收款合同编号,
                fs.name 用途, case when a.currency=1 then "人民币" when a.currency=2 then "美元" end as 币种, format(r.amount/100, 2) 认领金额,
                a.receive_date 收款时间, r.create_time 认领时间, p.type as project_type, c.type as contract_type, r.status';

        $sql = 'select '.$col.' 
                from t_receive_confirm r
                join t_bank_flow a on r.flow_id=a.flow_id 
        		left join t_corporation b on a.corporation_id = b.corporation_id
                left join t_system_user u on u.user_id = r.create_user_id
                left join t_project p on p.project_id=r.project_id
                left join t_contract c on c.contract_id=r.contract_id 
                left join t_finance_subject fs on fs.subject_id=r.subject 
                left join t_contract_file cf on cf.contract_id=c.contract_id and cf.is_main=1 and cf.type=1 
                ' . $this->getWhereSql($attr). ' and '.AuthorizeService::getUserDataConditionString('a').'
                order by a.flow_id desc';

        $data = Utility::query($sql);
        if(Utility::isEmpty($data))
        {
            $this->renderError("当前信息不存在！");
        } else {
            foreach ($data as $key => $row) {
                $data[$key]['项目类型'] = Map::$v['project_type'][$row['project_type']];
                unset($data[$key]['project_type']);
                $data[$key]['合同类型'] = Map::$v['buy_sell_type'][$row['contract_type']];
                unset($data[$key]['contract_type']);
                $data[$key]['状态'] = Map::$v['receive_confirm_status'][$row['status']];
                unset($data[$key]['status']);
            }
        }

        $this->exportExcel($data);
    }
}