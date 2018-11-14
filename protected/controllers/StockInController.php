<?php

use ddd\Split\Domain\Model\StockSplit\StockSplitEnum;

/**
 * Desc: 入库单
 * User: susiehuang
 * Date: 2017/10/9 0009
 * Time: 10:03
 */
class StockInController extends AttachmentController {
    public function pageInit() {
        $this->attachmentType = Attachment::C_STOCK_IN;
        $this->filterActions = "";
        $this->rightCode = "stockIn";
        $this->newUIPrefix="new_";
    }

    public function actionIndex() {
        $attr =$this->getSearch();// Mod::app()->request->getParam('search');

        $sql = 'select {col} from t_stock_in_batch a 
                left join t_contract b on b.contract_id = a.contract_id 
                left join t_contract_file f on b.contract_id = f.contract_id and f.is_main=1 and f.type=1
                left join t_project c on c.project_id = a.project_id 
                left join t_partner d on d.partner_id = b.partner_id 
                left join t_corporation e on e.corporation_id = b.corporation_id ' . $this->getWhereSql($attr) . ' 
                and a.status >= ' . StockNotice::STATUS_SUBMIT. ' and b.type = ' . ConstantMap::BUY_TYPE . '
                and ' . AuthorizeService::getUserDataConditionString('b') . ' order by a.batch_id desc {limit}';
        $user = Utility::getNowUser();
        if (!empty($user['corp_ids'])) {
            $data = $this->queryTablesByPage($sql, 'a.contract_id, f.code_out, a.project_id, a.batch_id, a.type, a.code, a.status, a.is_virtual, b.contract_code, c.project_code, b.partner_id, d.name as partner_name, b.corporation_id, e.name as corporation_name');
        } else {
            $data = array();
        }

        $this->pageTitle = '添加入库单';
        $this->render('index', $data);
    }

    public function actionAdd() {
        $batch_id = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($batch_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        //查询入库通知单信息
        $stockNoticeModel = StockNotice::model()->with('details', 'details.sub')->findByPk($batch_id);
        if (empty($stockNoticeModel->batch_id)) {
            $this->renderError(BusinessError::outputError(OilError::$STOCK_BATCH_NOT_EXIST, array('batch_id' => $batch_id)));
        }

        if(!StockNoticeService::isCanAddStockIn($stockNoticeModel->status,$stockNoticeModel->is_virtual)){
            $this->renderError(BusinessError::outputError(OilError::$STOCK_IN_NOT_ALLOW_ADD, array('batch_id' => $batch_id)));
        }

        //初始化入库单信息
        $stockInData['stock_in_id'] = IDService::getStockInId();
        $stockInData['project_id'] = $stockNoticeModel->project_id;
        $stockInData['contract_id'] = $stockNoticeModel->contract_id;
        $stockInData['batch_id'] = $stockNoticeModel->batch_id;
        $stockInData['type'] = $stockNoticeModel->type;

        //入库单附件
        $attachs = StockInService::getAttachment($stockInData['stock_in_id']);

        $stockIns = StockIn::model()->with("store", "details", "details.sub", "details.goods")->findAllToArray('t.batch_id=:batch_id',array("batch_id"=>$batch_id));

        //入库单明细
        $stockInGoods = StockInService::formatStockInGoods($stockNoticeModel->details, $stockInData['stock_in_id']);
        $storehouses = Storehouse::getAllActiveStorehouse();
        $this->pageTitle = '添加入库单';
        $this->render('edit', array(
            'stockNotice' => $stockNoticeModel,
            'data' => $stockInData,
            'stockInAttachs' => $attachs,
            'storehouses' => $storehouses,
            'stockInGoods' => $stockInGoods,
            'stockIns' => $stockIns
        ));
    }

    public function actionEdit() {
        $stock_in_id = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($stock_in_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        //查询入库单信息
        $stockInModel = StockIn::model()->with('details', 'details.sub')->findByPk($stock_in_id);
        if (empty($stockInModel->stock_in_id)) {
            $this->renderError(BusinessError::outputError(OilError::$STOCK_IN_NOT_EXIST, array('stock_in_id' => $stock_in_id)));
        }

        //是否可修改
        if (!StockInService::isCanEdit($stockInModel['status'])) {
            $this->renderError(BusinessError::outputError(OilError::$STOCK_BATCH_NOT_ALLOW_EDIT));
        }

        //查询入库通知单信息
        $stockNoticeModel = StockNotice::model()->with('details', 'details.sub')->findByPk($stockInModel->batch_id);
        if (empty($stockNoticeModel->batch_id)) {
            $this->renderError(BusinessError::outputError(OilError::$STOCK_BATCH_NOT_EXIST, array('batch_id' => $stockInModel->batch_id)));
        }

        $stockIn = $stockInModel->getAttributes(true, array('create_user_id', 'create_time', 'update_user_id', 'update_time', 'currency', 'exchange_rate', 'amount_cny', 'amount'));
        $attachs = StockInService::getAttachment($stockIn['stock_in_id']); //入库单附件
        $stockInGoods = StockInService::formatstockInGoods($stockNoticeModel->details, $stockIn['stock_in_id'], $stockInModel->details); //入库单明细

        $storehouses = Storehouse::getAllActiveStorehouse(); //可用仓库

        $this->pageTitle = '修改入库单';
        $this->render('edit', array('stockNotice' => $stockNoticeModel, 'storehouses' => $storehouses, 'data' => $stockIn, 'stockInAttachs' => $attachs, 'stockInGoods' => $stockInGoods));
    }

    public function actionSave() {
        $params = Mod::app()->request->getParam('data');
        $stockInDetail = $params['stockInDetail'];
        unset($params['stockInDetail']);

        $requiredParams = array('batch_id', 'contract_id', 'project_id', 'entry_date', 'stock_in_id', 'type');
        if ($params['type'] == ConstantMap::STOCK_NOTICE_TYPE_BY_WAREHOUSE) {
            array_push($requiredParams, 'store_id');
        }
        if (!Utility::checkRequiredParamsNoFilterInject($params, $requiredParams)) {
            $this->returnError(BusinessError::outputError(OilError::$REQUIRED_PARAMS_CHECK_ERROR));
        }

        $stockNoticeModel = StockNotice::model()->findByPk($params['batch_id']);
        if (empty($stockNoticeModel->batch_id)) {
            $this->returnError(BusinessError::outputError(OilError::$STOCK_BATCH_NOT_EXIST, array('batch_id' => $params['batch_id'])));
        }

        //入库单明细参数校验
        $goodsItemsCheckRes = StockInDetailService::checkParamsValid($stockInDetail);
        if ($goodsItemsCheckRes !== true) {
            $this->returnError($goodsItemsCheckRes);
        }

        if (!empty($params['stock_in_id'])) {
            $stockInModel = StockIn::model()->findByPk($params['stock_in_id']);
        }

        if (!empty($stockInModel->stock_in_id)) {
            if (!StockInService::isCanEdit($stockInModel['status'])) {
                $this->returnError(BusinessError::outputError(OilError::$STOCK_IN_NOT_ALLOW_EDIT));
            }
        } else {
            if ($stockNoticeModel->status < StockNotice::STATUS_SUBMIT) {
                $this->returnError(BusinessError::outputError(OilError::$STOCK_IN_NOT_ALLOW_ADD, array('batch_id' => $params['batch_id'])));
            }
            $stockInModel = new StockIn();
            $stockInModel->stock_in_id = $params['stock_in_id'];
            $stockInModel->code = StockInService::generateStockInCode($params['batch_id']);
            $stockInModel->status = StockIn::STATUS_NEW;
            $stockInModel->status_time = Utility::getDateTime();
        }

        if ($params['isSubmit']) {
            $stockInModel->status = StockIn::STATUS_SUBMIT;
        }

        $logRemark = ActionLog::getEditRemark($stockInModel->isNewRecord, "入库单");
        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try {
            unset($params["stock_in_id"]);
            $stockInModel->setAttributes($params, false);
            $stockInModel->save();

            //保存入库单明细
            StockInDetailService::saveGoodsTransactions($stockInDetail, $stockInModel->stock_in_id, $stockInModel->store_id);

            //开始审批流
            TaskService::doneTask($stockInModel->stock_in_id, Action::ACTION_32);
            if ($stockInModel->status == StockIn::STATUS_SUBMIT) {
                FlowService::startFlow(FlowService::BUSINESS_STOCK_IN_CHECK, $stockInModel->stock_in_id);
            }

            $trans->commit();

            Utility::addActionLog(json_encode($stockInModel->oldAttributes), $logRemark, "StockIn", $stockInModel->stock_in_id);
            $this->returnSuccess($stockInModel->batch_id);
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

    public function actionDetail() {
        $batch_id = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($batch_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $stockNoticeModel = StockNotice::model()->with('details', 'details.sub')->findByPk($batch_id);
        if (empty($stockNoticeModel->batch_id)) {
            $this->renderError(BusinessError::outputError(OilError::$STOCK_BATCH_NOT_EXIST, array('batch_id' => $batch_id)));
        }

        $stockInModel = StockIn::model()->with('details', 'details.sub')->findAll('t.batch_id = :batchId', array('batchId' => $batch_id));
        $this->pageTitle = '入库通知单明细';
        $this->render('detail', array('stockIns' => $stockInModel, 'stockNotice' => $stockNoticeModel));
    }

    public function actionSubmit() {
        $stock_in_id = Mod::app()->request->getParam("id");
        if (!Utility::checkQueryId($stock_in_id)) {
            $this->returnError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $stockInModel = StockIn::model()->findByPk($stock_in_id);
        if (empty($stockInModel->stock_in_id)) {
            $this->returnError(BusinessError::outputError(OilError::$STOCK_IN_NOT_EXIST, array('stock_in_id' => $stock_in_id)));
        }

        if ($stockInModel->status >= StockIn::STATUS_SUBMIT) {
            $this->returnError(BusinessError::outputError(OilError::$STOCK_IN_NOT_ALLOW_SUBMIT));
        }

        $oldStatus = $stockInModel->status;
        $stockInModel->status = StockIn::STATUS_SUBMIT;
        $stockInModel->status_time = Utility::getDateTime();

        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try {
            $stockInModel->save();

            //开始审批流
            TaskService::doneTask($stockInModel->stock_in_id, Action::ACTION_32);
            FlowService::startFlow(FlowService::BUSINESS_STOCK_IN_CHECK, $stockInModel->stock_in_id);

            $trans->commit();

            Utility::addActionLog(json_encode(array('oldStatus' => $oldStatus)), "提交入库单", "StockIn", $stockInModel->stock_in_id);
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