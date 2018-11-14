<?php

/**
 *   入库通知单结算
 */
class StockBatchSettlementController extends AttachmentController {
    public function pageInit() {
        $this->attachmentType = Attachment::C_STOCK_BATCH_SETTLEMENT;
        $this->filterActions = "ajaxGetBuyLockList";
        $this->rightCode = "stockBatchSettlement";
        $this->newUIPrefix = 'new_';
    }

    public function actionIndex() {
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
            $query=" and (a.status=".StockNotice::STATUS_SUBMIT." or a.status=".StockNotice::STATUS_SETTLE_BACK.")";
            unset($attr["status"]);
        }else if($attr["status"]=="-1"){
            $status="-1";
            $query=" and a.status=".StockNotice::STATUS_SETTLE_BACK;
            unset($attr["status"]);
        }else if($attr["status"]=="0"){
            $status="0";
            $query=" and a.status=".StockNotice::STATUS_SUBMIT;
            unset($attr["status"]);
        }else if($attr["status"]=="1"){
            $status="1";
            $query=" and a.status=".StockNotice::STATUS_SETTLE_SUBMIT;
            unset($attr["status"]);
        }else if($attr["status"]=="2"){
            $status="2";
            $query=" and a.status=".StockNotice::STATUS_SETTLED;
            unset($attr["status"]);
        }
        $sql = 'select {col} from t_stock_in_batch a 
                left join t_lading_settlement as s on s.lading_id = a.batch_id
                left join t_contract e on a.contract_id = e.contract_id 
                left join t_project b on e.project_id = b.project_id 
                left join t_partner c on c.partner_id = e.partner_id 
                left join t_corporation d on d.corporation_id = e.corporation_id 
                left join t_contract_file f on e.contract_id = f.contract_id and f.is_main=1 and f.type=1 ' . $this->getWhereSql($attr) . ' and (e.settle_type='.Contract::SETTLE_TYPE_LADING.' or e.settle_type is null) and a.status >=' . StockNotice::STATUS_SUBMIT . ' and 
                exists (select * from t_stock_in si where si.batch_id = a.batch_id )
                 '. $query .' and '.AuthorizeService::getUserDataConditionString('e') . ' order by a.batch_id desc, a.order_index desc {limit}';
        $user = Utility::getNowUser();
        if (!empty($user['corp_ids'])) {
            $data = $this->queryTablesByPage($sql, 'e.contract_id, e.contract_code,e.settle_type, b.project_id, b.project_code, e.partner_id, c.name as partner_name, e.corporation_id, d.name as corporation_name, 
                a.batch_id, a.code, a.type, a.status, f.code_out,
                s.status as settle_status,ifnull(s.settle_id,0) as settle_id,s.settle_date,
                case when a.status='.StockNotice::STATUS_SUBMIT.' then 0
                when a.status='.StockNotice::STATUS_SETTLE_BACK.' then -1
                 when a.status='.StockNotice::STATUS_SETTLE_SUBMIT.' then 1
                 when a.status='.StockNotice::STATUS_SETTLED.' then 2 end as status_desc');
        } else {
            $data = array();
        }
        
        $attr['status'] = $status;
        $data["search"]=$attr;

        $this->pageTitle = '入库通知单结算';
        $this->render('index', $data);
    }

    public function actionEdit() {
        $batch_id = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($batch_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        //入库通知单
        $StockInBatchService = new \ddd\application\stock\LadingBillService();
        $stockInBatch=$StockInBatchService->getLadingBill($batch_id);
        if(empty($stockInBatch))
            $this->renderError('入库通知单不存在！');
        $isCanSettle = $StockInBatchService->isCanSettle($batch_id);
        if(is_string($isCanSettle))
            $this->renderError($isCanSettle);
        $data['stockInBatch']=$stockInBatch;
        //入库单
        $StockInService = new \ddd\application\stock\StockInService();
        $data['stockIn']=$StockInService->getStockInByBatchId($batch_id);
        //审核记录
        $checkLogs=FlowService::getCheckLog($batch_id,8);
        $data['checkLogs']=$checkLogs;
        //入库通知单商品结算
        $stockBatchSettlementService = new \ddd\application\contractSettlement\StockBatchSettlementService();
        $stockBatchSettlement=$stockBatchSettlementService->getStockBatchSettlement($batch_id);
        if(is_string($stockBatchSettlement)) $this->renderError($stockBatchSettlement);//抛出异常
        $data['settlement']=$stockBatchSettlement;
        //是否可结算
        $stockBatchSettlementEntity = \ddd\repository\contractSettlement\LadingBillSettlementRepository::repository()->find('t.lading_id='.$batch_id);
        $isCanSubmit = 0;
        if(!empty($stockBatchSettlementEntity)&&$stockBatchSettlementService->isCanSubmit($stockBatchSettlementEntity)){
            $isCanSubmit=1;
        }
       // print_r($data);die;
        $data['isCanSubmit']=$isCanSubmit;
        $data['settlement']['bill_id'] = $data['settlement']['batch_id'];
        $data['settlement']['bill_code'] = $data['settlement']['batch_code'];
        $data['settlement']['settle_type'] =1;
        $this->pageTitle = "结算";
//        $data = array('stockNotice' => $data['stockInBatch'], 'attachments' => $data['stockInBatch']['files'], 'stockNoticeGoods' =>  $data['stockInBatch']['items'], 'stockIns' => $data['stockIn'], 'goodsInfos' => $data['stockInBatchBalance']);

//       print_r($data);die;
        $this->render('edit', array(
            'data'=>$data,
            'settleFileConfig'=>Map::$v['stock_batch_settlement_type'][1],
            'goodsOtherFileConfig'=>Map::$v['stock_batch_settlement_type'][11]));
    }

    public function actionAjaxGetBuyLockList() {
        /*$detail_id = Mod::app()->request->getParam('detail_id');
        if (!Utility::checkQueryId($detail_id)) {
            $this->returnError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }*/
        $batch_id = Mod::app()->request->getParam('batch_id');
        $goods_id = Mod::app()->request->getParam('goods_id');
        if (!Utility::checkQueryId($batch_id) || !Utility::checkQueryId($goods_id)) {
            $this->returnError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

//        $stockBatchDetail = StockNoticeDetail::model()->findByPk($detail_id);
        $stockBatchDetail = StockNoticeDetail::model()->find("batch_id=".$batch_id." and goods_id=".$goods_id);
        if (!empty($stockBatchDetail)) {
            $contractGood = ContractGoods::model()->find(array('condition' => "contract_id=:contract_id and goods_id=:goods_id", 'params' => array('goods_id' => $stockBatchDetail->goods_id, 'contract_id' => $stockBatchDetail->contract_id)));
            if ($contractGood->lock_type == ConstantMap::LOCK_PUT_ORDER) {
                // 按入库通知单锁价
                $buyLocks = LockPrice::model()->with('lockPriceDetail', 'lockPriceDetail.target')->findAllToArray('t.batch_id=:batch_id and t.goods_id=:goods_id', array('batch_id' => $stockBatchDetail->batch_id, 'goods_id' => $stockBatchDetail->goods_id));
            } else {
                // 按照入库通知单目前没有details内容
                $buyLocks = LockPrice::model()->with('lockPriceDetail', 'lockPriceDetail.target')->findAllToArray('t.contract_id=:contract_id and t.goods_id=:goods_id', array('contract_id' => $stockBatchDetail->contract_id, 'goods_id' => $stockBatchDetail->goods_id));
            }
            $rollOvers = ContractGoodsRollover::model()->with("target", "oldTarget", 'lockPrice', 'target.lockPriceDetail')->findAllToArray("t.contract_id=:contract_id and t.goods_id=:goods_id", array("contract_id" => $stockBatchDetail->contract_id, 'goods_id' => $stockBatchDetail->goods_id));
        } else {
            $this->renderError(BusinessError::outputError(OilError::$STOCK_BATCH_DETAIL_NOT_EXIST, array('batch_id' => $batch_id,'goods_id'=>$goods_id)));
        }

        $this->layout = "empty";
        $this->render('buyLockList', array('buyLocks' => $buyLocks, 'rollOvers' => $rollOvers));
    }

    public function actionSave() {
        $params = json_decode($_POST['settlement'], true);
//        print_r($params);die;
       
        $batch_id = $params['bill_id'];  //入库通知单id
        if (!Utility::checkQueryId($batch_id)) {
            $this->returnJsonError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        $post=SettlementService::dataConvert($params,1);
       
        //入库通知单
        $StockInBatchService = new \ddd\application\stock\LadingBillService();
        $batchInBatchEntity=$StockInBatchService->getLadingBill($batch_id);
        if(empty($batchInBatchEntity))
            $this->returnJsonError('入库通知单不存在！');
        $isCanSettle = $StockInBatchService->isCanSettle($batch_id);
        if(is_string($isCanSettle))
            $this->returnJsonError($isCanSettle);
        //获取DTO并赋值
        $stockBatchSettlementService = new \ddd\application\contractSettlement\StockBatchSettlementService();
        $newStockInBatchSettlementDTO = $stockBatchSettlementService->AssignDTO($batch_id, $post);//对DTO进行赋值
        if(is_string($newStockInBatchSettlementDTO))
            $this->returnJsonError($newStockInBatchSettlementDTO,'-1');
        //保存结算单
        $re = $stockBatchSettlementService->saveLadingBillSettlement($newStockInBatchSettlementDTO);
        //print_r($status);
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
        $batch_id = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($batch_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        //入库通知单
        $StockInBatchService = new \ddd\application\stock\LadingBillService();
        $stockInBatch=$StockInBatchService->getLadingBill($batch_id);
        if(empty($stockInBatch))
            $this->renderError('入库通知单不存在！');
        $data['stockInBatch']=$stockInBatch;
        //入库单
        $StockInService = new \ddd\application\stock\StockInService();
        $data['stockIn']=$StockInService->getStockInByBatchId($batch_id);
        //审核记录
        $checkLogs=FlowService::getCheckLog($batch_id,8);
        $data['checkLogs']=$checkLogs;
        //入库通知单商品结算
        $stockBatchSettlementService = new \ddd\application\contractSettlement\StockBatchSettlementService();
        $stockBatchSettlement=$stockBatchSettlementService->getStockBatchSettlement($batch_id);
        if(is_string($stockBatchSettlement)) $this->renderError($stockBatchSettlement);//抛出异常
        $data['settlement']=$stockBatchSettlement;
        //是否可结算
        $stockBatchSettlementEntity = \ddd\repository\contractSettlement\LadingBillSettlementRepository::repository()->find('t.lading_id='.$batch_id);
        $isCanSubmit = 0;
        if(!empty($stockBatchSettlementEntity)&&$stockBatchSettlementService->isCanSubmit($stockBatchSettlementEntity)){
            $isCanSubmit=1;
        }
        $isCanEdit = 0;
        if(!empty($stockBatchSettlementEntity)&&$stockBatchSettlementService->isCanEdit($stockBatchSettlementEntity)){
            $isCanEdit=1;
        }
        $data['isCanSubmit']=$isCanSubmit;
        $data['isCanEdit'] = $isCanEdit;
        $this->pageTitle = "详情";
//        print_r($data);die;
        $this->render('detail', array('data'=>$data));
    }

    public function actionSubmit() {
        $batchId = Mod::app()->request->getParam("batch_id");
        if (!Utility::checkQueryId($batchId)) {
            $this->returnJsonError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        
        $stockNotice = StockNotice::model()->findByPk($batchId);
        if (empty($stockNotice->batch_id)) {
            $this->returnJsonError(BusinessError::outputError(OilError::$STOCK_BATCH_NOT_EXIST, array('batch_id' => $batchId)));
        }

        $StockInBatchService = new \ddd\application\stock\LadingBillService();
        $isCanSettle = $StockInBatchService->isCanSettle($batchId);
        if(is_string($isCanSettle))
            $this->returnJsonError($isCanSettle);
        //是否可提交
        $stockBatchSettlementEntity = \ddd\repository\contractSettlement\LadingBillSettlementRepository::repository()->find('t.lading_id='.$batchId);
        $stockBatchSettlementService = new \ddd\application\contractSettlement\StockBatchSettlementService();
        if(empty($stockBatchSettlementEntity))
            $this->returnJsonError('该入库通知单没有结算信息');
        $isCanSubmit = $stockBatchSettlementService->isCanSubmit($stockBatchSettlementEntity);
        if(!$isCanSubmit) {
            $this->returnJsonError(BusinessError::outputError(OilError::$STOCK_BATCH_SETTLE_NOT_ALLOW_SUBMIT));
        }
        
        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try {
            $stockBatchSettlementService->submit($stockBatchSettlementEntity);
            FlowService::startFlowForCheck8($batchId);
            TaskService::doneTask($batchId, Action::ACTION_STOCK_BATCH_SETTLE_BACK);
            $trans->commit();
            Utility::addActionLog(null, "提交入库通知单结算审核", "StockBatchSettlement", $batchId);
            $this->returnJson('提交成功');
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
}