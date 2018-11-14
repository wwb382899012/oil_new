<?php

/**
 *   销售合同结算
 */
class SellContractSettlementController extends AttachmentController {
    public function pageInit() {
        $this->attachmentType = Attachment::C_CONTRACT_SETTLEMENT;
        $this->filterActions = "ajaxGetBuyLockList,submit,saveFile,delFile,getFile,createDetailId";
        $this->rightCode = "sellContractSettlement";
        $this->newUIPrefix = 'new_';
    }

    public function actionIndex(){
        //$this->renderNewWeb();return ;
//        $attr = Mod::app()->request->getParam('search');
        $attr = $this->getSearch();
        
        if(!is_array($attr) || !array_key_exists("status",$attr))
        {
            $attr["status"]="-2";
        }
        
        $query="";
        $status="";
        
        if(isset($attr["status"]) && $attr["status"]=="-2"){
            $status="-2";
            $query=" and (a.status<=".Contract::STATUS_SETTLING." or a.status=".Contract::STATUS_SETTLED_BACK.")";
            unset($attr["status"]);
        }else if($attr["status"]=="-1"){
            $status="-1";
            $query=" and a.status=".Contract::STATUS_SETTLED_BACK;
            unset($attr["status"]);
        }else if($attr["status"]=="0"){
            $status="0";
            $query=" and a.status<=".Contract::STATUS_SETTLING;//未提交
            unset($attr["status"]);
        }else if($attr["status"]=="1"){
            $status="1";
            $query=" and a.status=".Contract::STATUS_SETTLED_SUBMIT;
            unset($attr["status"]);
        }else if($attr["status"]=="2"){
            $status="2";
            $query=" and a.status=".Contract::STATUS_SETTLED;
            unset($attr["status"]);
        }
        
        $sql = 'select {col} from t_contract a
                left join t_project b on b.project_id = a.project_id
                left join t_contract_settlement s on s.contract_id=a.contract_id
                left join t_partner c on c.partner_id = a.partner_id
                left join t_corporation d on d.corporation_id = a.corporation_id
                left join t_system_user as e on e.user_id = a.manager_user_id
                left join t_contract_file f on f.contract_id = a.contract_id and f.is_main=1 and f.type=1 ' . $this->getWhereSql($attr) . ' and
                exists (select * from t_stock_out_detail so where so.contract_id = a.contract_id ) and a.type=2
                '.$query.' and ' . AuthorizeService::getUserDataConditionString('a') . ' order by a.contract_id desc {limit}';
        $user = Utility::getNowUser();
        if (!empty($user['corp_ids'])) {
            $data = $this->queryTablesByPage($sql, 'a.contract_id, a.contract_code, a.project_id, b.project_code,ifnull(a.settle_type,4) as settle_type, a.partner_id,
             c.name as partner_name, a.corporation_id, d.name as corporation_name, a.category , a.status , e.name as manager_user_name , f.code_out,
             s.status as settle_status,ifNull(s.settle_id,0) settle_id,s.settle_date,
             case when a.status<='.Contract::STATUS_SETTLING.' then 0
             when a.status='.Contract::STATUS_SETTLED_BACK.' then -1
             when a.status='.Contract::STATUS_SETTLED_SUBMIT.' then 1
             when a.status='.Contract::STATUS_SETTLED.' then 2 end as status_desc
            ');
        } else {
            $data = array();
        }
        
        $attr['status'] = $status;
        $data["search"]=$attr;
        $this->pageTitle = '销售合同结算';
        $this->render('index',$data);
    }

    public function actionEdit() {
        $contract_id = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($contract_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        //合同信息
        $ContractService = new \ddd\application\contractSettlement\ContractService();
        $contract=$ContractService->getContract($contract_id);
        if(is_string($contract))
            $this->renderError($contract);
        $isCanSettle=$ContractService->isCanSettle($contract_id);
        if(is_string($isCanSettle))
            $this->renderError($isCanSettle);
        $data['contract']=$contract;
        //审核记录
        $checkLogs=FlowService::getCheckLog($contract_id,22);
        $data['checkLogs']=$checkLogs;

        //合同结算
        $SellContractSettlementService = new \ddd\application\contractSettlement\SellContractSettlementService();
        $sellContractSettlement=$SellContractSettlementService->getSellContractSettlement($contract_id);
        if(is_string($sellContractSettlement)) $this->renderError($sellContractSettlement);//抛出异常
        $data['settlement']=$sellContractSettlement;
        //是否可结算
        $sellContractSettlementEntity = \ddd\repository\contractSettlement\SaleContractSettlementRepository::repository()->find('t.contract_id='.$contract_id);
        $isCanSubmit = 0;
        if(!empty($sellContractSettlementEntity)&&$SellContractSettlementService->isCanSubmit($sellContractSettlementEntity)){
            $isCanSubmit=1;
        }
        $data['isCanSubmit']=$isCanSubmit;
        $data['settlement']['bill_id']=$sellContractSettlement['contract_id'];
        $data['settlement']['bill_code']=$sellContractSettlement['contract_code'];
        $data['remark']=$sellContractSettlement['remark'];
        $view="edit";
        if($sellContractSettlement['settle_type']==3)
            $view="editByDelivery";
        else
            $data['settlement']['settle_type'] =4;

        //print_r($data['settlement']);die;
        //
        $this->pageTitle = '结算';

        $this->render($view,array(
            "data"=>$data,
            "settleFileConfig"=>Map::$v["contract_settlement_attachment"][1],
            "goodsOtherFileConfig"=>Map::$v["contract_settlement_attachment"][2],
            "otherFileConfig"=>Map::$v["contract_settlement_other_attachment"][101]
        ));
    }
    /*
     * 生成非货款文件上传时  detail_id
     * */
    public function actionCreateDetailId(){
        $SellContractSettlementService = new \ddd\application\contractSettlement\SellContractSettlementService();
        $detail_id=$SellContractSettlementService->createDetailId();
        $this->returnJson($detail_id);
    }
    
    public function actionAjaxGetBuyLockList() {
       
    }

    public function actionSave() {
        $settlement = Mod::app()->request->getParam('settlement');  //参数
        $settlement_arr = json_decode($settlement,true);
        $contract_id = $settlement_arr['bill_id'];  //合同id
        $post=SettlementService::dataConvert($settlement_arr,2);
        //var_dump($post);die;
        if (!Utility::checkQueryId($contract_id)) {
            $this->returnJsonError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        //合同信息
        $ContractService = new \ddd\application\contractSettlement\ContractService();
        $contract=$ContractService->getContract($contract_id);
        if(is_string($contract))
            $this->returnJsonError($contract);
        $isCanSettle=$ContractService->isCanSettle($contract_id);
        if(is_string($isCanSettle))
            $this->returnJsonError($isCanSettle);
        //获取DTO并赋值
        $SellContractSettlementService = new \ddd\application\contractSettlement\SellContractSettlementService();
        $newSellContractSettlementDTO = $SellContractSettlementService->AssignDTO($contract_id, $post);
        if(is_string($newSellContractSettlementDTO))
            $this->returnJsonError($newSellContractSettlementDTO,'-1');
            //print_r($newSellContractSettlementDTO);die;
        //保存数据
        $re = $SellContractSettlementService->saveDeliveryContractSettlement($newSellContractSettlementDTO);
        //print_r($status);
        if(is_array($re)){
            $this->returnValidateError($re);
        }else{
            if(is_string($re))
                $this->returnJsonError($re);
            else
                $this->returnJson('保存成功');
                    
        }
    }

    public function actionDetail() {
        $contract_id = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($contract_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        //合同信息
        $ContractService = new \ddd\application\contractSettlement\ContractService();
        $contract=$ContractService->getContract($contract_id);
        if(is_string($contract))
            $this->renderError($contract);

        $data['contract']=$contract;
        //审核记录
        $checkLogs=FlowService::getCheckLog($contract_id,22);
        $data['checkLogs']=$checkLogs;
        //合同结算
        $SellContractSettlementService = new \ddd\application\contractSettlement\SellContractSettlementService();
        $sellContractSettlement=$SellContractSettlementService->getSellContractSettlement($contract_id);
        if(is_string($sellContractSettlement)) $this->renderError($sellContractSettlement);//抛出异常
        $data['settlement']=$sellContractSettlement;
        //是否可结算
        $sellContractSettlementEntity = \ddd\repository\contractSettlement\SaleContractSettlementRepository::repository()->find('t.contract_id='.$contract_id);
        $isCanSubmit = 0;
        $isCanEdit =0;
        if(!empty($sellContractSettlementEntity)&&$SellContractSettlementService->isCanSubmit($sellContractSettlementEntity)){
            $isCanSubmit=1;
        }
        if(!empty($sellContractSettlementEntity)&&$SellContractSettlementService->isCanEdit($sellContractSettlementEntity)){
            $isCanEdit=1;
        }
        $data['isCanSubmit']=$isCanSubmit;
        $data['isCanEdit']=$isCanEdit;
        $view="detail";
        $this->pageTitle = '详情';
        
        $this->render($view,array("data"=>$data));
    }

    public function actionSubmit() {
        $contract_id = Mod::app()->request->getParam("contract_id");
        if (!Utility::checkQueryId($contract_id)) {
            $this->returnJsonError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $ContractService = new \ddd\application\contractSettlement\ContractService();
        $contract=$ContractService->getContract($contract_id);
        if(is_string($contract))
            $this->returnJsonError($contract);
        $isCanSettle=$ContractService->isCanSettle($contract_id);
        if(is_string($isCanSettle))
            $this->returnJsonError($isCanSettle);
        
        //是否可提交
        $SaleContractSettlementEntity = \ddd\repository\contractSettlement\SaleContractSettlementRepository::repository()->find('t.contract_id='.$contract_id);
        $SellContractSettlementService = new \ddd\application\contractSettlement\SellContractSettlementService();
        $isCanSubmit = $SellContractSettlementService->isCanSubmit($SaleContractSettlementEntity);
        if(!$isCanSubmit) {
            $this->returnJsonError(BusinessError::outputError(OilError::$STOCK_BATCH_SETTLE_NOT_ALLOW_SUBMIT));
        }
        
        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try {
            $SellContractSettlementService->submit($SaleContractSettlementEntity);
            FlowService::startFlowForCheck22($contract_id);
            
            TaskService::doneTask($contract_id, Action::ACTION_DELIVERY_CONTRACT_SETTLEMENT_BACK);
            $trans->commit();
            Utility::addActionLog(null, "修改销售合同结算信息", "DeliveryContractSettlement", $contract_id);
            $this->returnJson('提交成功！');
        } catch (Exception $e) {
            try {
                $trans->rollback();
            } catch (Exception $ee) {
                Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $ee->getMessage(), CLogger::LEVEL_ERROR);
            }
            
            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $e->getMessage(), CLogger::LEVEL_ERROR);
            
            $this->returnJsonError(BusinessError::outputError(OilError::$OPERATE_FAILED, array('reason' => $e->getMessage())));
        }
    }
   /* //合同结算：其他附件上传
    public function actionSaveFile(){
        $type=Mod::app()->request->getParam("type");
        if($type==101){
            $this->attachmentType = Attachment::C_CONTRACT_OTHER_SETTLEMENT;
        }
       
        parent::actionSaveFile();
    }
    //合同结算：其他附件上传
    public function actionDelFile(){
        $id=Mod::app()->request->getParam("id");
        $sql="select * from t_contract_settlement_goods_attachment where id=".$id;
        $data=Utility::query($sql);
        if(!empty($data)&&$data[0]['type']!=101){
            
            $this->attachmentType = Attachment::C_CONTRACT_OTHER_SETTLEMENT;
        }
        parent::actionDelFile();
    }*/
}