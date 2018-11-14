<?php
/**
*   出库通知单结算
*/
class DeliverySettlementController extends AttachmentController {
    public function pageInit() {
        $this->attachmentType = Attachment::C_DELIVERY_ORDER_SETTLEMENT;
        $this->rightCode = "deliverySettlement";
        $this->filterActions = "saveFile,getFile,delFile,submit";
        $this->newUIPrefix = 'new_';
    }

    public function actionIndex() {
        //$this->renderNewWeb();return ;
        // $attr = Mod::app()->request->getParam('search');
        $attr = $this->getSearch();
        if(!is_array($attr) || !array_key_exists("status",$attr))
        {
            $attr["status"]="-2";
        }
        
        $query="";
        $status="";
        
        if(isset($attr["status"]) && $attr["status"]=="-2"){
            $status="-2";
            $query=" and (a.status=".DeliveryOrder::STATUS_PASS." or a.status=".DeliveryOrder::STATUS_SETTLE_BACK.")";
            unset($attr["status"]);
        }else if($attr["status"]=="-1"){
            $status="-1";
            $query=" and a.status=".DeliveryOrder::STATUS_SETTLE_BACK;
            unset($attr["status"]);
        }else if($attr["status"]=="0"){
            $status="0";
            $query=" and a.status=".DeliveryOrder::STATUS_PASS;
            unset($attr["status"]);
        }else if($attr["status"]=="1"){
            $status="1";
            $query=" and a.status=".DeliveryOrder::STATUS_SETTLE_SUBMIT;
            unset($attr["status"]);
        }else if($attr["status"]=="2"){
            $status="2";
            $query=" and a.status=".DeliveryOrder::STATUS_SETTLE_PASS;
            unset($attr["status"]);
        }
        
        $user = SystemUser::getUser(Utility::getNowUserId());
        
        $sql = 'select {col} from t_delivery_order a
                left join t_delivery_settlement as s on s.order_id = a.order_id
                left join t_contract b on b.contract_id = a.contract_id
                left join t_partner c on c.partner_id = a.partner_id
                left join t_project p on p.project_id = a.project_id
                left join t_system_user as u on u.user_id = b.manager_user_id
                left join t_stock_in e on e.stock_in_id = a.stock_in_id
                left join t_corporation d on d.corporation_id = a.corporation_id
                left join t_contract_file f on b.contract_id = f.contract_id and f.is_main=1 and f.type=1 ' . $this->getWhereSql($attr) .
                $query . ' and a.corporation_id in ('.$user['corp_ids'].') and a.status>='.DeliveryOrder::STATUS_PASS. ' and (b.settle_type=' . Contract::SETTLE_TYPE_DELIVERY .' or b.settle_type is null)'.
                ' and exists(select order_id FROM t_stock_out_order e WHERE e.order_id=a.order_id )
                order by a.order_id desc {limit}';
                $data = $this->queryTablesByPage($sql,
                    'a.partner_id, c.name as partner_name, a.corporation_id,s.settle_date,a.project_id,a.contract_id,b.settle_type,p.project_code,
            a.contract_id,b.contract_code,f.code_out,
            a.type, d.name as corporation_name, a.order_id, a.code,
            a.status, a.stock_in_id, e.code as stock_in_code,
            s.status as settle_status,ifnull(s.settle_id,0)as settle_id,
            case when a.status='.DeliveryOrder::STATUS_PASS.' then 0
            when a.status='.DeliveryOrder::STATUS_SETTLE_BACK.' then -1
            when a.status='.DeliveryOrder::STATUS_SETTLE_SUBMIT.' then 1
            when a.status='.DeliveryOrder::STATUS_SETTLE_PASS.' then 2 end as status_desc');
                
        if($status=="-2" || $status=="-1" || $status=="0" || $status=="1" || $status=="2")
        $attr['status'] = $status;
        $data["search"]=$attr;

        $this->pageTitle = '发货单结算';
        $this->render('index', $data);
    }

    public function actionEdit() {
        $order_id = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($order_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        //发货单
        $DeliveryOrderService = new \ddd\application\stock\DeliveryOrderService();
        $deliveryOrder = $DeliveryOrderService->getDeliveryOrder($order_id);
        if(empty($deliveryOrder))
            $this->renderError('发货单不存在！');
        $isCanSettle = $DeliveryOrderService->isCanSettle($order_id);
        if(is_string($isCanSettle))
            $this->renderError($isCanSettle);
        $data['deliveryOrder']=$deliveryOrder;
        //出库单
        $StockOutService = new \ddd\application\stock\StockOutService();
        $stockOut = $StockOutService->getStockOutByOrderId($order_id);
        $data['stockOut']=$stockOut;
        //审核记录
        $checkLogs=FlowService::getCheckLog($order_id,10);
        $data['checkLogs']=$checkLogs;
        
        //发货单商品结算
        $DeliveryOrderSettlementService = new \ddd\application\contractSettlement\DeliveryOrderSettlementService();
        $deliveryOrderSettlement=$DeliveryOrderSettlementService->getDeliveryOrderSettlement($order_id);
        if(is_string($deliveryOrderSettlement)) $this->renderError($deliveryOrderSettlement);//抛出异常
        $data['settlement']=$deliveryOrderSettlement;
        //是否可结算
        $deliveryOrderSettlementEntity = \ddd\repository\contractSettlement\DeliveryOrderSettlementRepository::repository()->find('t.order_id='.$order_id);
        $isCanSubmit = 0;
        if(!empty($deliveryOrderSettlementEntity)&&$DeliveryOrderSettlementService->isCanSubmit($deliveryOrderSettlementEntity)){
            $isCanSubmit=1;
        }
        $data['isCanSubmit']=$isCanSubmit;

        $data['settlement']['bill_id'] = $data['settlement']['order_id'];
        $data['settlement']['bill_code'] = $data['settlement']['order_code'];
        $data['settlement']['settle_type'] =3;

        $this->pageTitle = "结算";

//        print_r($data);die;
        $this->render('edit',
            array(
                'data'=>$data,
                'settleFileConfig'=>Map::$v['delivery_settlement_attachment'][3],
                'goodsOtherFileConfig'=>Map::$v['delivery_settlement_attachment'][4]
            )
        );
    }

    public function actionSave() {
        $params = json_decode($_POST['settlement'], true);
        // print_r($params);die;

        $order_id = $params['bill_id'];
        if (!Utility::checkQueryId($order_id)) {
            $this->returnJsonError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        $post=SettlementService::dataConvert($params,2);

        //发货单
        $DeliveryOrderService = new \ddd\application\stock\DeliveryOrderService();
        $deliveryOrder = $DeliveryOrderService->getDeliveryOrder($order_id);
        if(empty($deliveryOrder))
            $this->returnJsonError('发货单不存在');
        $isCanSettle = $DeliveryOrderService->isCanSettle($order_id);
        if(is_string($isCanSettle))
            $this->returnJsonError($isCanSettle);
        //获取DTO并赋值
        $deliveryOrderSettlementService = new \ddd\application\contractSettlement\DeliveryOrderSettlementService();
        $newDeliveryOrderSettlementDTO = $deliveryOrderSettlementService->AssignDTO($order_id,$post);
        if(is_string($newDeliveryOrderSettlementDTO))
            $this->returnJsonError($newDeliveryOrderSettlementDTO);
        //保存结算单
        $re = $deliveryOrderSettlementService->saveDeliveryOrderSettlement($newDeliveryOrderSettlementDTO);
        
        if(is_array($re)){
            $this->returnValidateError($re,'state');
        }else{
            if(is_string($re))
                $this->returnJsonError($re);
            else
                $this->returnJson('保存成功');
                    
        }
    }

    public function actionDetail() {
        $order_id = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($order_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        //发货单
        $DeliveryOrderService = new \ddd\application\stock\DeliveryOrderService();
        $deliveryOrder = $DeliveryOrderService->getDeliveryOrder($order_id);
        if(empty($deliveryOrder))
            $this->renderError('发货单不存在！');
        $data['deliveryOrder']=$deliveryOrder;
        //出库单
        $StockOutService = new \ddd\application\stock\StockOutService();
        $stockOut = $StockOutService->getStockOutByOrderId($order_id);
        $data['stockOut']=$stockOut;
        //审核记录
        $checkLogs=FlowService::getCheckLog($order_id,10);
        $data['checkLogs']=$checkLogs;
        
        //发货单商品结算
        $DeliveryOrderSettlementService = new \ddd\application\contractSettlement\DeliveryOrderSettlementService();
        $deliveryOrderSettlement=$DeliveryOrderSettlementService->getDeliveryOrderSettlement($order_id);
        if(is_string($deliveryOrderSettlement)) $this->renderError($deliveryOrderSettlement);//抛出异常
        $data['settlement']=$deliveryOrderSettlement;
        //是否可结算
        $deliveryOrderSettlementEntity = \ddd\repository\contractSettlement\DeliveryOrderSettlementRepository::repository()->find('t.order_id='.$order_id);
        $isCanEdit = 0;
        if(!empty($deliveryOrderSettlementEntity)&&$DeliveryOrderSettlementService->isCanEdit($deliveryOrderSettlementEntity)){
            $isCanEdit=1;
        }
        $isCanSubmit = 0;
        if(!empty($deliveryOrderSettlementEntity)&&$DeliveryOrderSettlementService->isCanSubmit($deliveryOrderSettlementEntity)){
            $isCanSubmit=1;
        }

        $data['isCanEdit'] = $isCanEdit;
        $data['isCanSubmit']=$isCanSubmit;

//        print_r($data);die;
        $this->pageTitle = "详情";
        
        $this->render('detail', array('data'=>$data));
    }

    public function checkIsCanEdit($status) {
        if ($status == DeliveryOrder::STATUS_PASS || $status == DeliveryOrder::STATUS_SETTLE_BACK) {
            return true;
        } else {
            return false;
        }
    }

    public function actionSubmit(){

        $id = Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
            $this->returnJsonError("参数有误");
        $deliveryOrder = DeliveryOrder::model()->findByPk($id);
        if(empty($deliveryOrder->order_id))
            $this->returnJsonError("当前信息不存在");
            
        $oldStatus = $deliveryOrder->status;

        //是否可结算
        $DeliveryOrderService = new \ddd\application\stock\DeliveryOrderService();
        $isCanSettle = $DeliveryOrderService->isCanSettle($id);
        if(is_string($isCanSettle))
            $this->returnJsonError($isCanSettle);
        //是否可提交
        $deliveryOrderSettlementEntity = \ddd\repository\contractSettlement\DeliveryOrderSettlementRepository::repository()->find('t.order_id='.$id);
        $DeliveryOrderSettlementService = new \ddd\application\contractSettlement\DeliveryOrderSettlementService();
        $isCanSubmit = $DeliveryOrderSettlementService->isCanSubmit($deliveryOrderSettlementEntity);
        if(!$isCanSubmit) {
            $this->returnJsonError(BusinessError::outputError(OilError::$DELIVERY_ORDER_SETTLE_NOT_ALLOW_SUBMIT));
        }
        
        $trans = Utility::beginTransaction();
        try{
            $DeliveryOrderSettlementService->submit($deliveryOrderSettlementEntity);
            //先更新任务，不然会有bug
            TaskService::doneTask($id, Action::ACTION_48);
            FlowService::startFlowForCheck10($id);
            
            $trans->commit();
            
            Utility::addActionLog(json_encode(array('oldStatus'=>$oldStatus)), "提交发货单结算", "DeliveryOrder", $deliveryOrder->order_id);
            $this->returnJson($deliveryOrder->order_id);
            
        }catch(Exception $e){
            try{ $trans->rollback(); }catch(Exception $ee){}
            $this->returnJsonError(BusinessError::outputError(OilError::$OPERATE_FAILED, array('reason' => $e->getMessage())));
        }
    }
}