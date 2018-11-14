<?php

/**
 *   采购合同结算审核
 */
class Check21Controller extends CheckController {
    public $businessId = 21;
    public $mainRightCode = "check21_";

    public function pageInit() {
        parent::pageInit();
        $attr = $_REQUEST["search"];
//        $attr = $this->getSearch();
        $checkStatus = $attr["checkStatus"];
        $this->treeCode = $this->mainRightCode . $checkStatus;
        $this->businessId = 21;
        $this->rightCode = "check21_";
        $this->mainUrl = "/check21/";
        $this->checkViewName = "/check21/check";
        $this->detailViewName = "/check21/detail";

        $this->filterActions = "index,check,save,detail";
        $this->newUIPrefix = 'new_';
    }

    public function actionIndex() {
        //$this->renderNewWeb();return ;
//        $attr = $_REQUEST['search'];
        $attr = $this->getSearch();
        $checkStatus = 1;
        if (!empty($attr["checkStatus"])) {
            $checkStatus = $attr["checkStatus"];
            unset($attr['checkStatus']);
        }
        $query = "";
        $userId = Utility::getNowUserId();
        $roleId = UserService::getNowUserMainRoleId();
        $sql = "select {col}
            from t_check_detail a
            left join t_check_log d on d.detail_id=a.detail_id
            left join t_contract e on a.obj_id = e.contract_id
            left join t_contract_settlement s on s.contract_id = e.contract_id
            left join t_system_user as b on b.user_id = e.manager_user_id
            left join t_project f on e.project_id = f.project_id
            left join t_partner c on c.partner_id = e.partner_id
            left join t_corporation g on g.corporation_id = e.corporation_id
            left join t_contract_file cf on cf.contract_id=e.contract_id and cf.is_main=1 and cf.type=1
            " . $this->getWhereSql($attr);
        $fields = "a.obj_id,a.check_id,a.detail_id,e.contract_id,b.name as manager_user_name, e.contract_code,e.category, f.project_id, f.project_code,
         e.partner_id, c.name as partner_name, f.corporation_id, g.name as corporation_name , cf.code_out,b.name as manager_user_name,
         s.settle_date";
        switch ($checkStatus) {
            case 2:
                // 审核通过
                $sql .= " and a.status=1 and a.check_status=1";
                $fields .= ",0 isCanCheck, " . $checkStatus . " as checkStatus ";
                break;
            case 3:
                // 审核驳回
                $sql .= " and a.status=1 and a.check_status=-1";
                $fields .= ",0 isCanCheck, " . $checkStatus . " as checkStatus ";
                break;
            case 1:
            default:
                // 待审核
                $sql .= " and a.status=0 and a.check_status=0";
                $fields .= ",1 isCanCheck, " . $checkStatus . " as checkStatus ";
                $checkStatus = 1;
                break;
        }
        
        $sql .= $query . " and " . AuthorizeService::getUserDataConditionString('e') . " and (a.role_id= {$roleId} or a.check_user_id={$userId}) and a.business_id={$this->businessId} order by a.check_id desc {limit}";
        $user = Utility::getNowUser();
        if (!empty($user['corp_ids'])) {
            $data = $this->queryTablesByPage($sql, $fields);
        } else {
            $data = array();
        }
        
        $attr["checkStatus"] = $checkStatus;
        $data["search"] = $attr;
        $data["b"] = $this->businessId;
        $this->render("index", $data);
       
    }


    public function getCheckData($id) {
        $data = Utility::query("
              select a.*
              from t_check_item a
                left join t_stock_batch_settlement b on a.obj_id=b.settle_id
                left join t_check_detail c on c.check_id = a.check_id
                where c.check_status = 0 and a.business_id=" . $this->businessId . " and a.obj_id=" . $id);
        //合同信息
        if(!empty($data)){
        $ContractService = new \ddd\application\contractSettlement\ContractService();
        $contract=$ContractService->getContract($id);
        $data[0]['contract']=$contract;
        //审核记录
        $checkLogs=FlowService::getCheckLog($id,21);
        $data[0]['checkLogs']=$checkLogs;
        //合同结算
        $BuyContractSettlementService = new \ddd\application\contractSettlement\BuyContractSettlementService();
        $buyContractSettlement=$BuyContractSettlementService->getBuyContractSettlement($id);
        $data[0]['contractSettlement']=$buyContractSettlement;
        }
            
        return $data;
    }

    public function getDetailData($detailId) {
        $data = Utility::query("
              select b.*
              from t_check_detail a
                left join t_check_log b on b.check_id = a.check_id
                where a.business_id=" . $this->businessId . " and 
                a.detail_id=" . $detailId);
        if(!empty($data)){
        $id=$data[0]['obj_id'];
        //合同信息
        $ContractService = new \ddd\application\contractSettlement\ContractService();
        $contract=$ContractService->getContract($id);
        $data[0]['contract']=$contract;
        //审核记录
        $checkLogs=FlowService::getCheckLog($id,21);
        $data[0]['checkLogs']=$checkLogs;
        //合同结算
        $BuyContractSettlementService = new \ddd\application\contractSettlement\BuyContractSettlementService();
        $buyContractSettlement=$BuyContractSettlementService->getBuyContractSettlement($id);
        $data[0]['contractSettlement']=$buyContractSettlement;
        }
        return $data;
    }

    public function checkIsCanEdit($status) {
        return $status == StockBatchSettlement::STATUS_SUBMIT;
    }
}