<?php
/**
 * Created by youyi000.
 * DateTime: 2018/3/1 11:55
 * Describe：
 */

class LadingController extends AttachmentController
{
    public function pageInit()
    {
        $this->attachmentType = Attachment::C_STOCK_NOTICE;
        $this->filterActions = "";
        $this->rightCode = "";
    }

    public function actionIndex()
    {
        $attr = Mod::app()->request->getParam('search');

        $sql = 'select {col} from t_contract a 
                left join t_project b on a.project_id = b.project_id 
                left join t_partner c on c.partner_id = a.partner_id 
                left join t_corporation d on d.corporation_id = a.corporation_id ' . $this->getWhereSql($attr) . ' 
                and a.type = ' . ConstantMap::BUY_TYPE . ' and a.status >= ' . Contract::STATUS_BUSINESS_CHECKED . '
                 
                order by a.contract_id desc {limit}';
        $data = $this->queryTablesByPage($sql, 'a.contract_id, a.contract_code, b.project_id, b.project_code, a.partner_id, c.name as partner_name, a.corporation_id, d.name as corporation_name');

        $this->pageTitle = '添加入库通知单';
        $this->render('/stockNotice/index', $data);
    }

    public function actionAdd()
    {
        $contract_id = Mod::app()->request->getParam('id');

        if (!Utility::checkQueryId($contract_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        $contract=\ddd\repository\contract\ContractRepository::repository()->findByPk($contract_id);
        $contractService=new \ddd\application\contract\ContractService();
        $lading=$contractService->createLadingBill($contract);

        $ladingDTO=new \ddd\application\dto\stock\LadingBillDTO($lading);
        //$ladingDTO->fromEntity($lading);
        $contractDTO=new \ddd\application\dto\contract\ContractDTO();
        $contractDTO->fromEntity($contract);
        $allGoods=array();
        $stockNoticeGoods=array();
        if(is_array($contract->goodsItems))
        {
            foreach($contract->goodsItems as $k=>$v)
            {
                $item=new \ddd\application\dto\stock\ContractGoodsDTO();
                $item->fromEntity($v);
                $allGoods[]=$item->getAttributes();
            }
        }
        $stockNoticeGoods=$ladingDTO->entitiesToArrays($ladingDTO->items);
        /*if(is_array($ladingDTO->items))
        {
            foreach($ladingDTO->items as $k=>$v)
            {
                $stockNoticeGoods[]=$v->getAttributes();
            }
        }*/

        $this->pageTitle = '添加入库通知单';
        $this->render('edit', array('contract' => $contractDTO,
                                     'attachments' => array(),
                                    'allGoods' => $allGoods,
                                    'stockNoticeGoods' => $stockNoticeGoods,
                                    'data'=>$ladingDTO->getAttributes(),
                                    'lading' => $ladingDTO));
    }

    public function actionEdit() {
        $batch_id = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($batch_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $lading=\ddd\repository\LadingBillRepository::repository()->findByPk($batch_id);
        if (empty($lading->id)) {
            $this->returnError(BusinessError::outputError(OilError::$STOCK_BATCH_NOT_EXIST, array('batch_id' => $batch_id)));
        }
        //$lading=new \domain\entity\stock\LadingBill();
        if(!$lading->isCanEdit())
            $this->returnError(BusinessError::outputError(OilError::$STOCK_BATCH_NOT_ALLOW_EDIT));

        $ladingDTO=new \ddd\application\dto\stock\LadingBillDTO();
        $ladingDTO->fromEntity($lading);
        //查询入库通知单信息
        /*$stockNoticeModel = StockNotice::model()->with('details','details.sub', 'contract')->findByPk($batch_id);
        if (empty($stockNoticeModel->batch_id)) {
            $this->returnError(BusinessError::outputError(OilError::$STOCK_BATCH_NOT_EXIST, array('batch_id' => $batch_id)));
        }*/

        //是否可修改
        /*if (!$this->checkIsCanEdit($stockNoticeModel['status'])) {
            $this->returnError(BusinessError::outputError(OilError::$STOCK_BATCH_NOT_ALLOW_EDIT));
        }*/

        $stockNotice = $ladingDTO->getAttributes(array('batch_id', 'project_id', 'contract_id', 'code', 'type', 'batch_date', 'order_index', 'remark'));
        $attachs = StockNoticeService::getAttachment($stockNotice['batch_id']); //入库通知单附件


        $stockNoticeGoods=array();
        $stockNoticeGoods=$ladingDTO->entitiesToArrays($ladingDTO->items);
        /*if(!empty($stockNoticeModel->details))
        {
            foreach ($stockNoticeModel->details as $v)
            {
                $row=$v->getAttributes(true, Utility::getCommonIgnoreAttributes());
                $row["goods_name"]=$v->goods->name;
                $row["quantity_sub"]= $v->quantity_sub;
                // $row["quantity_sub"]= !empty($v->sub->quantity) ? $v->sub->quantity : 0;
                $row["unit_sub"]=!empty($v->sub->unit) ? $v->sub->unit : $v->unit;
                $stockNoticeGoods[]=$row;
            }
        }*/

        //$stockNoticeGoods = StockNoticeService::formatStockNoticeGoods($stockNoticeModel->details, $stockNotice['batch_id']); //入库通知单明细

        $storehouses = Storehouse::getAllActiveStorehouse();

        //查询合同信息
        /*$contractModel = Contract::model()->findByPk($stockNotice['contract_id']);
        if (empty($contractModel->contract_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PROJECT_CONTRACT_NOT_EXIST, array('contract_id' => $stockNotice['contract_id'])));
        }*/


        $contract=\ddd\repository\contract\ContractRepository::repository()->findByPk($lading->contract_id);
        $contractDTO=new \ddd\application\dto\contract\ContractDTO();
        $contractDTO->fromEntity($contract);
        $allGoods=array();

        if(is_array($contract->goodsItems))
        {
            foreach($contract->goodsItems as $k=>$v)
            {
                $item=new \ddd\application\dto\stock\ContractGoodsDTO();
                $item->fromEntity($v);
                $allGoods[]=$item->getAttributes();
            }
        }
        //合同明细
        //$transactions = ContractService::getContractGoodsInfo($stockNotice['contract_id']);
        //$allGoods=ContractGoodsService::getContractAllGoods($contractModel->contractGoods);
        $this->pageTitle = '修改入库通知单';
        $this->render('edit', array('contract' => $contractDTO, 'transactions' => $transactions,
                                    'allGoods'=>$allGoods,
                                    'storehouses' => $storehouses,
                                    'data' => $ladingDTO->getAttributes(),
                                    'attachments' => $attachs,
                                    'stockNoticeGoods' => $stockNoticeGoods));
    }

    public function actionSave() {
        $params = Mod::app()->request->getParam('data');
        $stockNoticeGoods = $params['stockNoticeGoods'];
        unset($params['stockNoticeGoods']);


        $requiredParams = array('batch_id', 'contract_id', 'project_id', 'batch_date', 'type');
        if (!Utility::checkRequiredParamsNoFilterInject($params, $requiredParams)) {
            $this->returnError(BusinessError::outputError(OilError::$REQUIRED_PARAMS_CHECK_ERROR));
        }

        $contractModel = Contract::model()->with("contractGoods")->findByPk($params['contract_id']);
        if (empty($contractModel->contract_id)) {
            $this->returnError(BusinessError::outputError(OilError::$PROJECT_CONTRACT_NOT_EXIST, array('contract_id' => $params['contract_id'])));
        }

        //入库通知单明细参数校验
        $goodsItemsCheckRes = StockNoticeDetailService::checkParamsValid($stockNoticeGoods);
        if ($goodsItemsCheckRes !== true) {
            $this->returnError($goodsItemsCheckRes);
        }

        if (!empty($params['batch_id'])) {
            $stockNoticeModel = StockNotice::model()->findByPk($params['batch_id']);
        }

        if (!empty($stockNoticeModel->batch_id)) {
            if (!$this->checkIsCanEdit($stockNoticeModel['status'])) {
                $this->returnError(BusinessError::outputError(OilError::$STOCK_BATCH_NOT_ALLOW_EDIT));
            }
        } else {
            if (!ContractService::checkCanAddStockNotice($params['contract_id'])) {
                $this->returnError(BusinessError::outputError(OilError::$STOCK_BATCH_NOT_ALLOW_ADD, array('contract_id' => $params['contract_id'])));
            }
            $stockNoticeModel = new StockNotice();
            $stockNoticeModel->batch_id = $params['batch_id'];
            $stockNoticeModel->code = StockNoticeService::generateStockNoticeCode($params['contract_id']);
            $stockNoticeModel->status = StockNotice::STATUS_NEW;
            $stockNoticeModel->status_time = Utility::getDateTime();
        }

        if ($params['isSubmit']) {
            $stockNoticeModel->status = StockNotice::STATUS_SUBMIT;
        }

        $logRemark = ActionLog::getEditRemark($stockNoticeModel->isNewRecord, "入库通知单");
        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try {
            unset($params["batch_id"]);
            $stockNoticeModel->setAttributes($params, false);
            $stockNoticeModel->save();

            if ($params['isSubmit']) {
                foreach ($stockNoticeGoods as $v)
                {
                    $r=ContractGoodsService::checkGoodsStoreUnit($contractModel->contract_id,$v["goods_id"],$v["unit_sub"]);
                    if(!$r)
                        BusinessException::throw_exception(OilError::$GOODS_UNIT_CHANGED, array('goods_id'=>$v["goods_id"]));
                }
            }

            //保存入库通知单明细
            StockNoticeDetailService::saveGoodsTransactions($stockNoticeGoods, $stockNoticeModel->batch_id);

            if ($stockNoticeModel->status == StockNotice::STATUS_SUBMIT) {
                BuyLockService::insertLockRecord($stockNoticeModel->batch_id);
            }

            $trans->commit();

            Utility::addActionLog(json_encode($stockNoticeModel->oldAttributes), $logRemark, "StockNotice", $stockNoticeModel->batch_id);
            $this->returnSuccess($stockNoticeModel->batch_id);
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

    /**
     * 判断是否可以修改，子类需要修改该方法
     * @param $status
     * @return bool
     */
    public function checkIsCanEdit($status) {
        if ($status < StockNotice::STATUS_SUBMIT) {
            return true;
        } else {
            return false;
        }
    }

    public function actionDetail() {
        $contract_id = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($contract_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $contractModel = Contract::model()->with('contractGoods')->findByPk($contract_id);
        if (empty($contractModel->contract_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PROJECT_CONTRACT_NOT_EXIST, array('contract_id' => $contract_id)));
        }

        $contract = $contractModel->getAttributes(array('contract_id', 'project_id', 'contract_code', 'currency', 'exchange_rate', 'amount', 'amount_cny'));
        $transactions = ContractService::getContractGoodsInfo($contract_id);

        $stockNoticeModel = StockNotice::model()->with('details', 'details.sub')->findAll('t.contract_id = :contractId', array('contractId' => $contract_id));

        $this->pageTitle = '采购合同入库通知单详情';
        $this->render('detail', array('contract' => $contract, 'transactions' => $transactions, 'stockNotices' => $stockNoticeModel));
    }

    public function actionSubmit() {
        $batch_id = Mod::app()->request->getParam("id");
        if (!Utility::checkQueryId($batch_id)) {
            $this->returnError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $stockNoticeModel = StockNotice::model()->findByPk($batch_id);
        if (empty($stockNoticeModel->batch_id)) {
            $this->returnError(BusinessError::outputError(OilError::$STOCK_BATCH_NOT_EXIST, array('batch_id' => $batch_id)));
        }

        if ($stockNoticeModel->status >= StockNotice::STATUS_SUBMIT) {
            $this->returnError(BusinessError::outputError(OilError::$STOCK_BATCH_NOT_ALLOW_SUBMIT));
        }

        $oldStatus = $stockNoticeModel->status;
        $stockNoticeModel->status = StockNotice::STATUS_SUBMIT;
        $stockNoticeModel->status_time = Utility::getDateTime();

        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try {
            $stockNoticeModel->save();

            $stockNoticeGoods = StockNoticeDetail::model()->with('sub')->findAll('batch_id = :batchId', array('batchId' => $batch_id));

            foreach ($stockNoticeGoods as $v)
            {
                $r=ContractGoodsService::checkGoodsStoreUnit($v['contract_id'],$v["goods_id"],$v->unit_sub);
                if(!$r)
                    BusinessException::throw_exception(OilError::$GOODS_UNIT_CHANGED, array('goods_id'=>$v["goods_id"]));
            }


            BuyLockService::insertLockRecord($batch_id);



            $trans->commit();

            Utility::addActionLog(json_encode(array('oldStatus'=>$oldStatus)), "提交入库通知单", "StockNotice", $stockNoticeModel->batch_id);
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