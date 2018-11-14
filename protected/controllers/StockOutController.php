<?php

/**
 *    出库单
 */
class StockOutController extends AttachmentController{
    public function pageInit(){
        $this->attachmentType = Attachment::C_STOCK_OUT;
        $this->filterActions = "submit,saveFile,delFile,getFile";
        $this->rightCode = "stockOut";
        $this->authorizedActions = array("list");
        $this->newUIPrefix="new_";
    }

    public function actionIndex(){
//        $attr = Mod::app()->request->getParam('search');
        $attr = $this->getSearch();
        //发货单审核通过，并且发货单未提交结算时，方可添加出库单
        $sql = 'select {col} from t_delivery_order a 
        		left join t_stock_in b on a.stock_in_id = b.stock_in_id
                left join t_partner c on c.partner_id = a.partner_id 
                left join t_corporation d on d.corporation_id = a.corporation_id '
            .$this->getWhereSql($attr).' and '.AuthorizeService::getUserDataConditionString('a')
            .' AND a.status >= ' . DeliveryOrder::STATUS_PASS
            .' order by a.order_id desc {limit}';

        $user = Utility::getNowUser();
        if(!empty($user['corp_ids'])){
            $data = $this->queryTablesByPage($sql, 'a.*, c.name as partner_name, d.name as corporation_name, b.code as stock_in_code');
        }else{
            $data = array();
        }
        $this->pageTitle = '添加出库单';
        $this->render('index', $data);
    }

    public function actionAdd(){
        $this->pageTitle = "发货单出库";

        $order_id = Mod::app()->request->getParam('id');
        if(!Utility::checkQueryId($order_id)){
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        $deliveryOrder = DeliveryOrder::model()->with('details', 'details.contract', 'details.goods', 'details.stockDetail', 'details.stockDetail.stock', 'details.stockDetail.store', 'details.stockDetail.stock.stockIn')->findByPk($order_id);
        if($deliveryOrder->type != ConstantMap::STOCK_NOTICE_TYPE_BY_WAREHOUSE){
            $this->actionDetail();
            return;
        }

        if(!DeliveryOrderService::isCanAddStockOutOrder($deliveryOrder->type,$deliveryOrder->status,$deliveryOrder->is_virtual)){
            $this->renderError(BusinessError::outputError(OilError::$STOCK_OUT_ORDER_NOT_ALLOW_ADD));
        }

        // 配货明细
        $details = DeliveryOrderDetail::model()->with('contract', 'contract.partner', 'goods', 'stockDeliveryDetail', 'stockDeliveryDetail.store', 'stockDeliveryDetail.stock', 'stockDeliveryDetail.stock.stockIn')->findAll(array('condition' => 't.order_id=:order_id', 'params' => array('order_id' => $deliveryOrder->order_id),));
        list($stores, $storeGoods) = StockOutService::detailsFormat($details);
        $outOrders = StockOutOrder::model()->with("deliveryOrder", "details")->findAll(array("condition" => "t.order_id=".$order_id."", "order" => "t.out_order_id desc"));
        $out_order_id = IDService::getStoreOutOrderId();

        $default_store_id = array_keys($stores);
        $default_store_id = $default_store_id[0];
        $this->render('edit', array(
            'deliveryOrder' => $deliveryOrder,
            'outOrders' => $outOrders,
            'data' => array(
                'details' => $details,
                'stores' => $stores,
                'store_id' => $default_store_id,
                'storeGoods' => $storeGoods,
//                'batch_id' => $batch_id,
                'out_order_id' => $out_order_id
            )
        ));
    }

    public function actionEdit(){
        $this->pageTitle = "修改发货单出库信息";
        $id = Mod::app()->request->getParam("out_order_id");
        if(!Utility::checkQueryId($id))
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));

        $outOrder = StockOutOrder::model()->with("details")->findByPk($id);
        if(empty($outOrder)){
            $this->renderError("当前出库单不存在");
        }

        $deliveryOrder = DeliveryOrder::model()->with('details', 'details.contract', 'details.goods', 'details.stockDetail', 'details.stockDetail.stock', 'details.stockDetail.store', 'details.stockDetail.stock.stockIn')->findByPk($outOrder->order_id);
        if($deliveryOrder->type != ConstantMap::STOCK_NOTICE_TYPE_BY_WAREHOUSE){
            $this->actionDetail();
            return;
        }
        // 配货明细
        $details = DeliveryOrderDetail::model()->with('contract', 'contract.partner', 'goods', 'stockDeliveryDetail', 'stockDeliveryDetail.store', 'stockDeliveryDetail.stock', 'stockDeliveryDetail.stock.stockIn')->findAll(array('condition' => 't.order_id=:order_id', 'params' => array('order_id' => $deliveryOrder->order_id),));
        $outDetails = $outOrder->details;

        $outDetails = ModelService::modelsToKeyModels($outDetails, "stock_detail_id");
        list($stores, $storeGoods) = StockOutService::detailsFormat($details, $outDetails);

        $default_store_id = array_keys($stores);
        $default_store_id = $default_store_id[0];

        $this->render('edit', array('deliveryOrder' => $deliveryOrder, 'data' => array('details' => $details, 'stores' => $stores, 'store_id' => $outOrder['store_id'], 'storeGoods' => $storeGoods, 'remark' => $outOrder->remark, 'out_order_id' => $outOrder->out_order_id)));
    }

    public function actionSave(){
        $params = $_POST['data'];
        $items = $params['items'];
        $order_id = $params['order_id'];
        $out_order_id = $params['out_order_id'];

        if(!Utility::checkQueryId($order_id)){
            $this->returnError('发货单号有误');
        }
        if(!empty($out_order_id) && !Utility::checkQueryId($out_order_id)){
            $this->returnError('出库单号有误');
        }

        if(Utility::isEmpty($items)){
            $this->returnError(BusinessError::outputError(OilError::$STOCK_OUT_DETAIL_NOT_EXIST));
        }

        $deliveryOrder = DeliveryOrder::model()->with('details',"stockDetails")->findByPk($order_id);
        if(empty($deliveryOrder)){
            $this->returnError('当前发货单号的发货单不存在');
        }

        if(!DeliveryOrderService::isCanAddStockOutOrder($deliveryOrder->type,$deliveryOrder->status,$deliveryOrder->is_virtual)){
            $this->renderError(BusinessError::outputError(OilError::$STOCK_OUT_ORDER_NOT_ALLOW_ADD));
        }

        //配货明细信息
        $stockDetails = ModelService::modelsToKeyModels($deliveryOrder["stockDetails"]);

        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try{
            if(!empty($out_order_id) ) {
                $outOrder = StockOutOrder::model()->with("details")->findByPk($out_order_id);
            }

            $outOrder = empty($outOrder) ? new StockOutOrder() : $outOrder;
            $outOrder->out_order_id = $out_order_id;
            $outOrder->contract_id = $deliveryOrder->contract_id;
            $outOrder->order_id = $deliveryOrder->order_id;
            $outOrder->partner_id = $deliveryOrder->partner_id;
            $outOrder->corporation_id = $deliveryOrder->corporation_id;
            $outOrder->type = $deliveryOrder->type;
            $outOrder->remark = $params['remark'];
            $outOrder->out_date = $params['out_date'];
            $outOrder->store_id = $params['store_id'];
            $outOrder->status = $params['status'];
            $outOrder->status_time = new CDbExpression("now()");
            if($outOrder->isNewRecord){
                $outDetails = array();
                $outOrder->code = StockOutService::generateStockOutCode($deliveryOrder->order_id, $deliveryOrder);
            }else{
                $outDetails = $outOrder->details;
            }

            $logRemark = ActionLog::getEditRemark($outOrder->isNewRecord, "出库单");
            $outOrder->save();
            if(!$outOrder->out_order_id){
                throw new Exception("出库单保存失败");
            }

            $outDetails = ModelService::modelsToKeyModels($outDetails, "stock_detail_id");
            $outIds = array();

            $stock_ids = array();
            foreach($items as $item){
                $detail = $outDetails[$item["stock_detail_id"]];
                $stockDetail = $stockDetails[$item["stock_detail_id"]];

                if(empty($detail)){
                    $detail = new StockOutDetail();
                    $detail->stock_detail_id = $item["stock_detail_id"];
                    $detail->order_id = $outOrder->order_id;
                    $detail->out_order_id = $outOrder->out_order_id;
                    $detail->cross_detail_id = $stockDetail->cross_detail_id;
                    $detail->store_id = $outOrder->store_id;
                    $detail->detail_id = $stockDetail->detail_id;
                    $detail->project_id = $stockDetail->project_id;
                    $detail->contract_id = $stockDetail->contract_id;
                    $detail->stock_id = $stockDetail->stock_id;
                    $detail->goods_id = $stockDetail->goods_id;
                    $detail->type = $stockDetail->type;
                }
                $detail->quantity = $item["quantity"];
                $detail->save();

                $outIds[] = $detail->out_id;
                $stock_ids[$detail->stock_id] = $detail;
            }

            //删除多余的出库明细
            StockOutDetail::model()->deleteAll('out_order_id='.$out_order_id.' and out_id not in('.implode(",", $outIds).')');

            if($outOrder->status == StockOutOrder::STATUS_SUBMIT){
                foreach($stock_ids as $detail){
                    //冻结库存补丁
                    StockOutService::freezeStockPatch($detail);
                }
            }

            //开始审批流
            TaskService::doneTask($outOrder->out_order_id, Action::ACTION_STOCK_OUT_CHECK_BACK);
            if($outOrder->status == StockOutOrder::STATUS_SUBMIT){
                FlowService::startFlow(FlowService::BUSINESS_STOCK_OUT_CHECK,$outOrder->out_order_id);
            }

            $trans->commit();

            Utility::addActionLog(json_encode($outOrder->oldAttributes), $logRemark, "StockOutOrder", $outOrder->out_order_id);
            $this->returnSuccess($outOrder->out_order_id);
        }catch(Exception $e){
            try{
                $trans->rollback();
            }catch(Exception $ee){
                Mod::log(__CLASS__.'->'.__FUNCTION__.' in line '.__LINE__.' trans execute error:'.$ee->getMessage(), CLogger::LEVEL_ERROR);
            }

            Mod::log(__CLASS__.'->'.__FUNCTION__.' in line '.__LINE__.' trans execute error:'.$e->getMessage(), CLogger::LEVEL_ERROR);

            $this->returnError(BusinessError::outputError(OilError::$OPERATE_FAILED, array('reason' => $e->getMessage())));
        }
    }

    public function actionSubmit(){
        $id = Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
            $this->returnError("参数错误");

        $stockOutModel = StockOutOrder::model()->with("details")->findByPk($id);
        if(empty($stockOutModel))
            $this->returnError("出库单不存在");
        if(!$stockOutModel->isCanEdit()){
            $this->returnError("出库单当前状态不允许提交");
        }

        $oldStatus = $stockOutModel->status;
        $stockOutModel->status = StockOutOrder::STATUS_SUBMIT;
        $stockOutModel->status_time = new CDbExpression("now()");

        $trans = Utility::beginTransaction();
        try{
            $stockOutModel->save();

            //解冻库存补丁
            foreach($stockOutModel->details as $detail){
                StockOutService::freezeStockPatch($detail);
            }

            //添加审批流
            TaskService::doneTask($stockOutModel->out_order_id, Action::ACTION_STOCK_OUT_WAIT_CHECK);
            FlowService::startFlow(FlowService::BUSINESS_STOCK_OUT_CHECK,$stockOutModel->out_order_id);

            $trans->commit();
            Utility::addActionLog(json_encode(array('oldStatus' => $oldStatus)), "提交出库单", "StockOutOrder", $stockOutModel->out_order_id);
            $this->returnSuccess("提交操作成功");
        }catch(Exception $e){
            try{
                $trans->rollback();
            }catch(Exception $ee){
            }
            $this->returnError("提交操作失败：".$e->getMessage());
        }

    }

    public function actionList(){

        $orderId = Mod::app()->request->getParam('id');
        if(!Utility::checkQueryId($orderId))
            $this->returnError("参数错误");

        $deliveryOrder = DeliveryOrder::model()->with('details', 'details.contract', 'details.goods', 'details.stockDetail', 'details.stockDetail.stock', 'details.stockDetail.store', 'details.stockDetail.stock.stockIn')->findByPk($orderId);
        //直调
        if($deliveryOrder->type != ConstantMap::STOCK_NOTICE_TYPE_BY_WAREHOUSE){
            $this->actionDetail();
            return;
        }

        $delivery = DeliveryOrder::model()->findByPk($orderId);
        if(empty($delivery))
            $this->returnError("发货单信息不存在");
        $this->pageTitle = "发货单 ".$delivery["code"]." 的出库单列表";
        $outOrders = StockOutOrder::model()->with("deliveryOrder", "details")->findAll(array("condition" => "t.order_id=".$orderId."", "order" => "t.out_order_id desc"));

        $this->render('detailForWareHouse', array('deliveryOrder' => $deliveryOrder, 'outOrders' => $outOrders));
    }

    public function actionDetail(){
        $this->pageTitle = "仓库出库单详情";

        $order_id = Mod::app()->request->getParam('id');
        if(!Utility::checkQueryId($order_id)){
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        $deliveryOrder = DeliveryOrder::model()->with('details', 'details.stockDetail', 'details.stockDetail.stock', 'details.stockDetail.stock.stockIn')->findByPk($order_id);
        if($deliveryOrder->type == ConstantMap::STOCK_NOTICE_TYPE_BY_WAREHOUSE){
            $outOrders = StockOutOrder::model()->with('details.contract', 'details.contract.partner', 'details.goods', 'details.stockDetail', 'details.stockDetail.store', 'details.stockDetail.stock')->findAll(array('condition' => 't.order_id=:order_id', 'params' => array('order_id' => $deliveryOrder->order_id),));
            $this->render('detailDirectTransfer', array('deliveryOrder' => $deliveryOrder, 'outOrders' => $outOrders));
        }else{
            $outOrders = StockOutOrder::model()->with('details.contract', 'details.contract.partner', 'details.goods', 'details.stockDetail', 'details.stockDetail.store', 'details.stockDetail.stock')->findAll(array('condition' => 't.order_id=:order_id', 'params' => array('order_id' => $deliveryOrder->order_id),));
            $this->render('detailDirectTransfer', array('deliveryOrder' => $deliveryOrder, 'outOrders' => $outOrders));
        }
    }
}