<?php

/**
 * Desc: 库存盘点
 * User: susiehuang
 * Date: 2017/11/13 0013
 * Time: 16:56
 */
class StockInventoryController extends AttachmentController {
    public function pageInit() {
        $this->filterActions = 'getStockDetail,getStockInventoryDetail';
        $this->rightCode = 'stockinventory';
        $this->attachmentType = Attachment::C_STOCK_INVENTORY;
    }

    public function actionIndex() {
        $attr = Mod::app()->request->getParam('search');
        $fields = 'p.corporation_id,a.store_id,a.goods_id,a.unit,sum(a.quantity_balance) AS total_quantity_balance,sum(a.quantity_frozen) AS total_quantity_frozen,sum(a.quantity_balance)+sum(a.quantity_frozen) as total_stock_quantity,c.name as corp_name,s.name as store_name,g.name as goods_name';
        $user = Utility::getNowUser();
        /*$sql = 'select {col} from t_stock a
                left join t_contract p on p.contract_id = a.contract_id
                left join t_corporation c on c.corporation_id = p.corporation_id
                left join t_storehouse s on s.store_id = a.store_id
                left join t_goods g on g.goods_id = a.goods_id ' . $this->getWhereSql($attr) . '
                and c.corporation_id in (' . $user['corp_ids'] . ') and a.store_id > 0 group by p.corporation_id, a.store_id, a.goods_id, a.unit having sum(a.quantity_balance) > 0 or sum(a.quantity_frozen) > 0 {limit}';*/

        $sql1 = 'select ' . $fields . ' from t_stock a 
                 left join t_contract p on p.contract_id = a.contract_id
                 left join t_corporation c on c.corporation_id = p.corporation_id 
                 left join t_storehouse s on s.store_id = a.store_id 
                 left join t_goods g on g.goods_id = a.goods_id ' . $this->getWhereSql($attr) . ' 
                 and c.corporation_id in (' . $user['corp_ids'] . ') and a.store_id > 0 group by p.corporation_id, a.store_id, a.goods_id, a.unit';
        $sql = 'select {col} from (' . $sql1 . ') as ss where 1=1 {limit}';

        $data = $this->queryTablesByPage($sql, '*');
        if (Utility::isNotEmpty($data['rows'])) {
            $data['search'] = $attr;
        }
        $this->pageTitle = '库存盘点列表';
        $this->render('index', $data);
    }

    public function actionGetStockDetail() {
        $params = Mod::app()->request->getParam('params');
        $requiredParams = array('corporationId', 'goodsId', 'unit');
        if (!Utility::checkRequiredParamsNoFilterInject($params, $requiredParams)) {
            $this->returnError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $data['stock_detail'] = StockInventoryService::getStockDetail($params);
        $data['corp_name'] = Corporation::getCorporationName($params['corporationId']);
        $data['store_name'] = StorehouseService::getStoreName($params['storeId']);
        $data['goods_name'] = GoodsService::getSpecialGoodsNames($params['goodsId']);
        $this->returnSuccess($data);
    }

    public function actionAdd() {
        $corp_id = Mod::app()->request->getParam('corp_id');
        $store_id = Mod::app()->request->getParam('store_id');
        $goods_id = Mod::app()->request->getParam('goods_id');
        $unit = Mod::app()->request->getParam('unit');
        if (!Utility::checkQueryId($corp_id) || !Utility::checkQueryId($store_id) || !Utility::checkQueryId($goods_id) || !Utility::checkQueryId($unit)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        if (!StockInventoryService::checkIsCanAdd($corp_id, $store_id, $goods_id, $unit)) {
            $this->returnError(BusinessError::outputError(OilError::$STOCK_INVENTORY_NOT_ALLOW_ADD));
        }

        $data['corporation_id'] = $corp_id;
        $data['store_id'] = $store_id;
        $data['goods_id'] = $goods_id;
        $data['unit'] = $unit;
        $data['unit_desc'] = Map::$v['goods_unit'][$unit]['name'];
        $data['inventory_id'] = IDService::getStockInventoryId();
        $params = array('corporationId' => $corp_id, 'storeId' => $store_id, 'goodsId' => $goods_id, 'unit' => $unit);
        $stockInventoryDetail = StockInventoryService::formatStockInventoryDetail($params, $data['inventory_id']);
        $stockQuantity = StockInventoryService::getStockQuantity($params);
        $data['quantity_active'] = $stockQuantity['quantity_active'];
        $data['quantity_frozen'] = $stockQuantity['quantity_frozen'];
        $data['quantity_before'] = $stockQuantity['quantity_before'];

        $this->pageTitle = '库存盘点';
        $this->render('edit', array('data' => $data, 'stockInventoryDetail' => $stockInventoryDetail));
    }

    public function actionSave() {
        $params = Mod::app()->request->getParam('data');
        Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' pass params are:' . json_encode($params));

        $requiredParams = array('inventory_id', 'corporation_id', 'store_id', 'goods_id', 'unit', 'inventory_date');
        $mustExistParams = array('type', 'quantity', 'quantity_before', 'quantity_active', 'quantity_diff', 'quantity_frozen');
        if (!Utility::checkRequiredParamsNoFilterInject($params, $requiredParams) || !Utility::checkMustExistParams($params, $mustExistParams)) {
            $this->returnError(BusinessError::outputError(OilError::$REQUIRED_PARAMS_CHECK_ERROR));
        }

        $inventoryDetail = $params['stockInventoryDetail'];
        unset($params['stockInventoryDetail']);
        if ($params['quantity_diff'] != 0) { //存在库存损耗
            if (Utility::isNotEmpty($inventoryDetail)) {
                //损耗分摊明细参数校验
                $inventoryDetailCheckRes = StockInventoryDetailService::checkStockInventoryDetailParamsValid($inventoryDetail, $params['quantity_diff']);
                if ($inventoryDetailCheckRes !== true) {
                    $this->returnError($inventoryDetailCheckRes);
                }
            } else {
                $this->returnError(BusinessError::outputError(OilError::$STOCK_INVENTORY_DETAIL_EMPTY));
            }
        } else { //无库存损耗
            unset($inventoryDetail);
        }

        if (!empty($params['inventory_id'])) {
            $stockInventoryModel = StockInventory::model()->findByPk($params['inventory_id']);
        }

        if (!empty($stockInventoryModel->inventory_id)) {
            if (!StockInventoryService::checkIsCanEdit($stockInventoryModel->status)) {
                $this->returnError(BusinessError::outputError(OilError::$STOCK_INVENTORY_NOT_ALLOW_EDIT));
            }
        } else {
            if (!StockInventoryService::checkIsCanAdd($params['corporation_id'], $params['store_id'], $params['goods_id'], $params['unit'])) {
                $this->returnError(BusinessError::outputError(OilError::$STOCK_INVENTORY_NOT_ALLOW_ADD));
            }
            $stockInventoryModel = new StockInventory();
            $stockInventoryModel->inventory_id = $params['inventory_id'];
            $stockInventoryModel->status_time = Utility::getDateTime();
        }

        if ($params['status'] == StockInventory::STATUS_SUBMIT) {
            if (!empty($stockInventoryModel->status) && !StockInventoryService::checkIsCanEdit($stockInventoryModel->status)) {
                $this->returnError(BusinessError::outputError(OilError::$STOCK_INVENTORY_NOT_ALLOW_SUBMIT));
            }
            $logRemark = "提交库存盘点";
        } else {
            $logRemark = ActionLog::getEditRemark($stockInventoryModel->isNewRecord, "库存盘点");
        }

        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try {
            unset($params["inventory_id"]);
            $stockInventoryModel->setAttributes($params, false);
            $stockInventoryModel->save();

            //保存库存盘点明细
            $goodsDetailId = StockInventoryService::saveStockInventoryGoodsDetail($params, $stockInventoryModel->inventory_id);

            //保存损耗分摊明细
            if (Utility::isNotEmpty($inventoryDetail)) {
                StockInventoryDetailService::saveStockInventoryDetail($inventoryDetail, $goodsDetailId);
            }

            if ($stockInventoryModel->status == StockInventory::STATUS_SUBMIT) {
                //根据损耗分摊修改库存
                StockInventoryDetailService::updateGoodsStock($inventoryDetail);

                FlowService::startFlow(FlowService::BUSINESS_STOCK_INVENTORY, $stockInventoryModel->inventory_id);
                TaskService::doneTask($stockInventoryModel->inventory_id, Action::ACTION_STOCK_INVENTORY_BACK);
            }
            $trans->commit();

            Utility::addActionLog(json_encode($stockInventoryModel->oldAttributes), $logRemark, "StockInventory", $stockInventoryModel->inventory_id);
            $this->returnSuccess($stockInventoryModel->inventory_id);
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

    public function actionEdit() {
        $inventoryId = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($inventoryId)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        //查询库存盘点信息
        $stockInventoryModel = StockInventory::model()->with('stockInventoryGoodsDetail', 'stockInventoryDetail')->findByPk($inventoryId);
        if (empty($stockInventoryModel->inventory_id)) {
            $this->returnError(BusinessError::outputError(OilError::$STOCK_INVENTORY_NOT_EXIST, array('inventory_id' => $inventoryId)));
        }

        //是否可修改
        if (!StockInventoryService::checkIsCanEdit($stockInventoryModel['status'])) {
            $this->returnError(BusinessError::outputError(OilError::$STOCK_INVENTORY_NOT_ALLOW_EDIT));
        }

        $stockInventoryData = $stockInventoryModel->getAttributes(true, Utility::getCommonIgnoreAttributes());
        $stockInventoryGoodsDetails = $stockInventoryModel->stockInventoryGoodsDetail;
        $stockInventoryGoodsDetailData = Utility::isNotEmpty($stockInventoryGoodsDetails) ? $stockInventoryGoodsDetails[0]->getAttributes(true, Utility::getCommonIgnoreAttributes()) : array();
        $data = array_merge($stockInventoryData, $stockInventoryGoodsDetailData);
        $data['unit_desc'] = Map::$v['goods_unit'][$data['unit']]['name'];
        $detail = array();
        if (Utility::isNotEmpty($stockInventoryModel->stockInventoryDetail)) {
            foreach ($stockInventoryModel->stockInventoryDetail as $key => $row) {
                $detail[$key] = $row->getAttributes(array('stock_in_id', 'detail_id', 'quantity_diff'));
            }
        }
        $params = array('corporationId' => $data['corporation_id'], 'storeId' => $data['store_id'], 'goodsId' => $data['goods_id'], 'unit' => $data['unit']);
        $stockInventoryDetail = StockInventoryService::formatStockInventoryDetail($params, $data['inventory_id'], $detail);
        $attach = StockInventoryService::getAttachments($data['inventory_id']);

        $this->pageTitle = '库存盘点修改';
        $this->render('edit', array('data' => $data, 'stockInventoryDetail' => $stockInventoryDetail, 'attachments' => $attach));
    }

    public function actionDetail() {
        $corp_id = Mod::app()->request->getParam('corp_id');
        $store_id = Mod::app()->request->getParam('store_id');
        $goods_id = Mod::app()->request->getParam('goods_id');
        $unit = Mod::app()->request->getParam('unit');
        if (!Utility::checkQueryId($corp_id) || !Utility::checkQueryId($store_id) || !Utility::checkQueryId($goods_id) || !Utility::checkQueryId($unit)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }
        $data['corporation_id'] = $corp_id;
        $data['store_id'] = $store_id;
        $data['goods_id'] = $goods_id;
        $data['unit'] = $unit;
        $data['unit_desc'] = Map::$v['goods_unit'][$unit]['name'];
        $stockInventorys = StockInventoryGoodsDetail::model()->with('stockInventory', 'stockInventoryDetail')->findAll('stockInventory.corporation_id = :corporationId and stockInventory.store_id = :storeId and t.goods_id = :goodsId and t.unit = :unit', array('corporationId' => $corp_id, 'storeId' => $store_id, 'goodsId' => $goods_id, 'unit' => $unit));
        $stockInventoryData = array();
        if (Utility::isNotEmpty($stockInventorys)) {
            foreach ($stockInventorys as $key => $row) {
                $stockInventoryData[$key] = $row->getAttributes(true, Utility::getCommonIgnoreAttributes());
                $stockInventoryData[$key]['detail_id'] = StockInventoryService::getNewestCheckDetailId($row->inventory_id);
                $stockInventoryData[$key]['inventory_id'] = $row->stockInventory->inventory_id;
                $stockInventoryData[$key]['inventory_date'] = $row->stockInventory->inventory_date;
                $stockInventoryData[$key]['status'] = $row->stockInventory->status;
                $stockInventoryData[$key]['status_desc'] = Map::$v['stock_inventory_status'][$row->stockInventory->status];
                $detail = array();
                if (Utility::isNotEmpty($row['stockInventoryDetail'])) {
                    foreach ($row['stockInventoryDetail'] as $index => $val) {
                        $detail[$index] = $val->getAttributes(true, Utility::getCommonIgnoreAttributes());
                        $detail[$index]['stock_in_code'] = $val->stockIn->code;
                    }
                }
                $stockInventoryData[$key]['detail'] = $detail;
                $attach = StockInventoryService::getAttachments($row->stockInventory->inventory_id);
                $stockInventoryData[$key]['attach'] = $attach[ConstantMap::STOCK_INVENTORY_ATTACH_TYPE];
            }
        }

        $this->pageTitle = '库存盘点';
        $this->render('detail', array('data' => $data, 'stockInventorys' => $stockInventoryData));
    }

    public function actionSubmit() {
        $inventoryId = Mod::app()->request->getParam("id");
        if (!Utility::checkQueryId($inventoryId)) {
            $this->returnError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $stockInventoryModel = StockInventory::model()->findByPk($inventoryId);
        if (empty($stockInventoryModel->inventory_id)) {
            $this->returnError(BusinessError::outputError(OilError::$STOCK_INVENTORY_NOT_EXIST, array('inventory_id' => $inventoryId)));
        }

        if ($stockInventoryModel->status >= StockInventory::STATUS_SUBMIT) {
            $this->returnError(BusinessError::outputError(OilError::$STOCK_INVENTORY_NOT_ALLOW_SUBMIT));
        }

        $oldStatus = $stockInventoryModel->status;
        $stockInventoryModel->status = StockInventory::STATUS_SUBMIT;
        $stockInventoryModel->status_time = Utility::getDateTime();

        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try {
            $stockInventoryModel->save();
            $stockInventoryDetail = StockInventoryDetail::model()->findAllToArray('inventory_id = :inventoryId', array('inventoryId' => $stockInventoryModel->inventory_id));
            if (Utility::isNotEmpty($stockInventoryDetail)) {
                //根据损耗分摊修改库存
                StockInventoryDetailService::updateGoodsStock($stockInventoryDetail);
            }

            FlowService::startFlow(FlowService::BUSINESS_STOCK_INVENTORY, $stockInventoryModel->inventory_id);
            TaskService::doneTask($stockInventoryModel->inventory_id, Action::ACTION_STOCK_INVENTORY_BACK);

            $trans->commit();
            Utility::addActionLog(json_encode(array('oldStatus'=>$oldStatus)), "提交库存盘点", "StockInventory", $stockInventoryModel->inventory_id);
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

    public function actionGetStockInventoryDetail() {
        $goods_detail_id = Mod::app()->request->getParam('goods_detail_id');
        if (!Utility::checkQueryId($goods_detail_id)) {
            $this->returnError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $stockInventoryGoodsDetail = StockInventoryGoodsDetail::model()->findByPk($goods_detail_id);
        if (empty($stockInventoryGoodsDetail->goods_detail_id)) {
            $this->returnError(BusinessError::outputError(OilError::$STOCK_INVENTORY_GOODS_DETAIL_NOT_EXIST, array('goods_detail_id' => $goods_detail_id)));
        }

        $stockInventoryDetails = StockInventoryDetail::model()->findAll('goods_detail_id = :goodsDetailId', array('goodsDetailId' => $goods_detail_id));
        $details = array();
        if (Utility::isNotEmpty($stockInventoryDetails)) {
            foreach ($stockInventoryDetails as $key => $val) {
                $details[$key] = $val->getAttributes(true, Utility::getCommonIgnoreAttributes());
                $details[$key]['stock_in_code'] = $val->stockIn->code;
            }
        }

        $this->returnSuccess($details);
    }
}