<?php

/**
 *   入库通知单结算
 */
class Check8Controller extends CheckController {
    public $businessId = 8;
    public $mainRightCode = "check8_";

    public function pageInit() {
        parent::pageInit();
        $attr = $_REQUEST["search"];
//        $attr = $this->getSearch();
        $checkStatus = $attr["checkStatus"];
        $this->treeCode = $this->mainRightCode . $checkStatus;
        $this->businessId = 8;
        $this->rightCode = "check8_";
        $this->mainUrl = "/check8/";
        $this->checkViewName = "/check8/check";
        $this->detailViewName = "/check8/detail";

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
            left join t_stock_in_batch b on a.obj_id=b.batch_id
            left join t_lading_settlement s on s.lading_id=b.batch_id
            left join t_contract e on b.contract_id = e.contract_id 
            left join t_project f on e.project_id = f.project_id 
            left join t_partner c on c.partner_id = e.partner_id 
            left join t_corporation g on g.corporation_id = e.corporation_id  
            left join t_contract_file cf on cf.contract_id=e.contract_id and cf.is_main=1 and cf.type=1 
            " . $this->getWhereSql($attr);
        $fields = "e.contract_id, e.contract_code,s.settle_date, f.project_id, f.project_code, b.type, e.partner_id, c.name as partner_name, f.corporation_id, g.name as corporation_name, b.batch_id, b.code, a.obj_id, a.detail_id, cf.code_out";
       
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
                $sql .= " and b.status=" . StockNotice::STATUS_SETTLE_SUBMIT . " and a.status=0 and a.check_status=0";
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
        if(!empty($data)){
        $batch_id = $data[0]['obj_id'];
        //入库通知单
        $StockInBatchService = new \ddd\application\stock\LadingBillService();
        $stockInBatch=$StockInBatchService->getLadingBill($batch_id);
        $data[0]['stockInBatch']=$stockInBatch;
        //入库单
        $StockInService = new \ddd\application\stock\StockInService();
        $data[0]['stockIn']=$StockInService->getStockInByBatchId($batch_id);
        //审核记录
        $checkLogs=FlowService::getCheckLog($batch_id,8);
        $data[0]['checkLogs']=$checkLogs;
        //入库通知单商品结算
        $stockBatchSettlementService = new \ddd\application\contractSettlement\StockBatchSettlementService();
        $stockBatchSettlement=$stockBatchSettlementService->getStockBatchSettlement($batch_id);
        $data[0]['stockInBatchBalance']=$stockBatchSettlement;
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
        $batch_id = $data[0]['obj_id'];
        //入库通知单
        $StockInBatchService = new \ddd\application\stock\LadingBillService();
        $stockInBatch=$StockInBatchService->getLadingBill($batch_id);
        $data[0]['stockInBatch']=$stockInBatch;
        //入库单
        $StockInService = new \ddd\application\stock\StockInService();
        $data[0]['stockIn']=$StockInService->getStockInByBatchId($batch_id);
        //审核记录
        $checkLogs=FlowService::getCheckLog($batch_id,8);
        $data[0]['checkLogs']=$checkLogs;
        //入库通知单商品结算
        $stockBatchSettlementService = new \ddd\application\contractSettlement\StockBatchSettlementService();
        $stockBatchSettlement=$stockBatchSettlementService->getStockBatchSettlement($batch_id);
        $data[0]['stockInBatchBalance']=$stockBatchSettlement;
        }
        
        return $data;
    }

    public function checkIsCanEdit($status) {
        return $status == StockBatchSettlement::STATUS_SUBMIT;
    }
}