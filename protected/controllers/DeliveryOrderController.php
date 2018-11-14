<?php

/**
 * Desc: 发货单
 * User: susiehuang
 * Date: 2017/10/17 0009
 * Time: 10:03
 */
class DeliveryOrderController extends AttachmentController {
    public function pageInit() {
        $this->attachmentType = Attachment::C_STOCK_DELIVERY;
        $this->filterActions = 'saveFile,getFile,delFile,selectStockIns,getStockInBuyContracts';
        $this->filterActions .= ',selectContracts,getContractsForDirectTransfer,getContractsDetails';

        $this->rightCode = 'deliveryOrder';
        $this->newUIPrefix="new_";
    }

    public function actionIndex() {
        $this->pageTitle = '新建发货单';
        $data = array();

        $user = Utility::getNowUser();
        if (!empty($user['corp_ids'])) {
//            $attr = Mod::app()->request->getParam('search');
            $attr = $this->getSearch();

            $sql = 'select {col} from t_delivery_order a 
                left join t_partner b on b.partner_id = a.partner_id 
                left join t_corporation c on c.corporation_id = a.corporation_id 
                left join t_stock_in d on d.stock_in_id = a.stock_in_id 
                LEFT JOIN t_contract e ON e.contract_id = a.contract_id ' . $this->getWhereSql($attr) . ' 
                and ' . AuthorizeService::getUserDataConditionString('a') . ' order by a.order_id desc {limit}';

            $fileds = array(
                'e.contract_id, e.contract_code',
                'a.order_id, a.type, a.corporation_id, a.code, a.partner_id, a.stock_in_id, a.status, a.is_virtual',
                'b.name as partner_name, c.name as corporation_name, d.code as stock_in_code'
            );
            $data = $this->queryTablesByPage($sql, implode(',',$fileds));
        }

        $this->render('index', $data);
    }

    /**
     * @desc 直调入库单选择列表
     */
    public function actionSelectStockIns() {
        $attr = Mod::app()->request->getParam('search');

        $sql = 'select {col} from t_stock_in a 
                left join t_contract b on b.contract_id = a.contract_id 
                left join t_partner c on c.partner_id = b.partner_id ' . $this->getWhereSql($attr)
            . ' and a.is_virtual = 0 and a.type = ' . ConstantMap::STOCK_NOTICE_TYPE_DIRECT_TRANSFER . ' and a.status >= ' . StockIn::STATUS_PASS
            . ' and exists (select stock_id from t_stock where stock_in_id = a.stock_in_id and quantity_balance > 0) '
            . ' and ' . AuthorizeService::getUserDataConditionString('b') . ' order by a.stock_in_id desc {limit}';
        $user = Utility::getNowUser();
        if (!empty($user['corp_ids'])) {
            $data = $this->queryTablesByPage($sql, 'a.stock_in_id, a.contract_id, a.code, a.entry_date, b.contract_code, b.partner_id, c.name as partner_name');
        } else {
            $data = array();
        }

        $this->pageTitle = '新建直调发货单';
        $this->render('stockInList', $data);
    }

    public function actionAdd() {
        $type = Mod::app()->request->getParam('type');
        if ($type == ConstantMap::STOCK_NOTICE_TYPE_BY_WAREHOUSE) {
            $this->_addOrderByWareHouse();
            return;
        }

        $stock_in_id = Mod::app()->request->getParam('stock_in_id');
        if (!Utility::checkQueryId($stock_in_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $stockInModel = $this->_getContractsDetailsIds($stock_in_id);

        //入库单数据初始化
        $deliveryOrder = array(
            'type'=> ConstantMap::STOCK_NOTICE_TYPE_DIRECT_TRANSFER,
            'order_id'=> IDService::getDeliveryOrderId(),
            'contract_id'=>0,
            'contract_code'=>'',
            'code_out'=>'',
            'project_id'=>0,
            'project_code'=>'',
            'project_type'=>'',
            'partner_id'=>0,
            'partner_name'=>'',
            'corporation_id' => $stockInModel['corporation_id'],
            'corporation_name'=>'',
            //
            'stock_in_id'=> $stock_in_id,
            'delivery_date'=>$stockInModel['entry_date'],
        );

        //获取交易主体名称
        $corporationModel =  Corporation::model()->findByPk($stockInModel['corporation_id']);
        $deliveryOrder['corporation_name'] = $corporationModel->name;

        $this->pageTitle = '新建直调发货单';
        $this->render('edit', array(
            'stockIn' => $stockInModel['self'],
            'deliveryOrder' => $deliveryOrder,
            'goodsItems' => array(),
            'allGoodsItems' => array(),
        ));
    }

    /**
     * 添加经仓发货单
     */
    private function _addOrderByWareHouse(){
        $contractId = Mod::app()->request->getParam('contract_id');
        if (!Utility::checkQueryId($contractId)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        //获取新建经仓发货单所必要的初始数据
        $fileds = array(
            'c.contract_id,c.contract_code,c.code_out',
            'p.project_id,p.project_code,p.type AS project_type',
            'c.partner_id,dp.name as partner_name',
            'p.corporation_id,co.name as corporation_name',
        );
        $sql = 'select '.implode(',',$fileds).' from t_contract AS c
                left join t_project AS p on p.project_id = c.project_id 
                left join t_partner dp on dp.partner_id = c.partner_id  
                left join t_corporation co on p.corporation_id = co.corporation_id'
            . ' WHERE c.partner_id > 0 AND c.type = ' . ConstantMap::SALE_TYPE
            . ' AND c.status>='.Contract::STATUS_BUSINESS_CHECKED
            . ' AND  '.AuthorizeService::getUserDataConditionString('c')
            . ' AND c.contract_id = '.$contractId
            . ' ORDER BY c.contract_id desc';

        $data = Utility::query($sql);
        if(Utility::isEmpty($data)){
            $this->renderError(BusinessError::outputError(OilError::$DELIVERY_ORDER_NOT_ALLOW_ADD));
        }
        $allGoodsItems =  ContractGoodsService::getSaleContractsDetails($contractId);
        if(Utility::isEmpty($allGoodsItems)){
            $this->renderError(BusinessError::outputError(OilError::$DELIVERY_ORDER_GOODS_NOT_EXIST));
        }

        //入库单数据初始化
        $deliveryOrder = array(
            'type'=> ConstantMap::STOCK_NOTICE_TYPE_BY_WAREHOUSE,
            'order_id'=> IDService::getDeliveryOrderId(),
            'contract_id'=>0,
            'contract_code'=>'',
            'code_out'=>'',
            'project_id'=>0,
            'project_code'=>'',
            'project_type'=>'',
            'partner_id'=>0,
            'partner_name'=>'',
            'corporation_id'=>0,
            'corporation_name'=>'',
            //
            'stock_in_id'=> 0,
            'delivery_date'=> date('Y-m-d'), //默认今天
        );
        $deliveryOrder = array_merge($deliveryOrder,$data['0']);
        $this->pageTitle = '新建经仓发货单';
        $this->render('edit', array(
            'stockIn' => null,
            'deliveryOrder' => $deliveryOrder,
            'goodsItems' => $allGoodsItems, //添加时，默认选中所有
            'allGoodsItems' => $allGoodsItems,
        ));
    }

    public function actionEdit() {
        $order_id = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($order_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        //查询发货单信息
        $deliveryOrderModel = DeliveryOrder::model()->with('contract','corporation','partner', 'details.stockDeliveryDetail')->findByPk($order_id);
        if (empty($deliveryOrderModel->order_id)) {
            $this->returnError(BusinessError::outputError(OilError::$DELIVERY_ORDER_NOT_EXIST, array('order_id' => $order_id)));
        }

        //是否可修改
        if (!DeliveryOrderService::isCanEdit($deliveryOrderModel['status'])) {
            $this->returnError(BusinessError::outputError(OilError::$DELIVERY_ORDER_NOT_ALLOW_EDIT));
        }

        $deliveryOrder = $deliveryOrderModel->getAttributes(true, Utility::getCommonIgnoreAttributes());
        //初始化一些数据
        $deliveryOrder['contract_code'] = $deliveryOrderModel->contract->contract_code;
        $deliveryOrder['partner_name'] = $deliveryOrderModel->partner->name;
        $deliveryOrder['corporation_name'] = $deliveryOrderModel->corporation->name;
        $deliveryOrder['goods_ids'] = array();

        //对应保存的销售合同商品明细
        $details = array();
        if (Utility::isNotEmpty($deliveryOrderModel->details)) {
            foreach ($deliveryOrderModel->details as $key => & $row) {
                $details[$key] = $row->getAttributes(true, Utility::getCommonIgnoreAttributes());
                $details[$key]['contract_code'] = $row->contract->contract_code;
                $details[$key]['goods_name'] = $row->goods->name;
                $contractGoods = $row->contractGoods;
                $unitStoreDesc = Map::$v['goods_unit'][$contractGoods->unit_store]['name'];
                $distributedQuantity = StockDeliveryDetailService::getDistributedQuantity($row->contract_id, $row->goods_id);
                $stockOutQuantity = StockDeliveryDetailService::getStockOutQuantity($row->contract_id, $row->goods_id);
                $details[$key]['contract_quantity'] = $contractGoods->quantity . Map::$v['goods_unit'][$contractGoods->unit]['name'] . '±' . ($contractGoods->more_or_less_rate * 100) . '%';;
                $details[$key]['distributed_quantity'] = (!empty($distributedQuantity) ? $distributedQuantity : 0) . $unitStoreDesc;
                $details[$key]['stock_out_quantity'] = (!empty($stockOutQuantity) ? $stockOutQuantity : 0) . $unitStoreDesc;
                $details[$key]['unit_store_desc'] = $unitStoreDesc;
                $details[$key]['contract_detail_id'] = $contractGoods->detail_id;
                $details[$key]['type'] = $deliveryOrder->type;
                $details[$key]['distribute_detail'] = '';
                $stockDeliveryDetail = array();
                if (!empty($row->stockDeliveryDetail)) {
                    foreach ($row->stockDeliveryDetail as $k => $v) {
                        $stockDeliveryDetail[$k] = $v->getAttributes(true, Utility::getCommonIgnoreAttributes());
                        $stockDeliveryDetail[$k]['buy_contract_id'] = $v->stock->stockIn->contract->contract_id;
                        $stockDeliveryDetail[$k]['stock_in_code'] = !empty($v->stock_id) ? $v->stock->stockIn->code : '';
                        $stockDeliveryDetail[$k]['cross_code'] = !empty($v->cross_detail_id) ? $v->crossStock->stock->stockIn->code : '';
                        $stockDeliveryDetail[$k]['unit_store_desc'] = $unitStoreDesc;
                    }
                }
                $details[$key]['stock_delivery_detail'] = $stockDeliveryDetail;
            }
        }

        if ($deliveryOrderModel->type == ConstantMap::STOCK_NOTICE_TYPE_DIRECT_TRANSFER) { //直调
            $stockInModel = StockIn::model()->with('details', 'contract', 'details.sub')->findByPk($deliveryOrderModel->stock_in_id); //直调入库单信息
            $this->pageTitle = '修改直调发货单';


            //获取入库单的商品id数组,给直调选择销售合同提供筛选条件
            $goods_ids = array();
            foreach($stockInModel->details as & $item){
                $goods_ids[] = $item['goods_id'];
            }
            $deliveryOrder['goods_ids'] = $goods_ids;
        } else {
            $stockInModel = null;
            $this->pageTitle = '修改经仓发货单';
        }

        //所有可供选择的销售合同商品明细,只取入库单中存在的明细
        $allGoodsItems = ContractGoodsService::getSaleContractsDetails($deliveryOrder['contract_id'],$deliveryOrderModel->stock_in_id);

        $this->render('edit', array(
            'stockIn' => $stockInModel,
            'deliveryOrder' => $deliveryOrder,
            'goodsItems'=> $details,
            'allGoodsItems'=> $allGoodsItems,
        ));
    }

    /**
     * 获取直调配货信息对应的销售合同明细列表
     */
    public function actionGetContractsForDirectTransfer() {
        $stockInId = Mod::app()->request->getParam('stock_in_id');
        if (!Utility::checkQueryId($stockInId)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $stockInData = $this->_getContractsDetailsIds($stockInId);

        $fields = array(
            'c.contract_id,c.contract_code,c.partner_id,f.code_out',
            'p.project_id,p.project_code,p.type AS project_type',
            'dp.name as partner_name',
            'p.corporation_id,co.name as corporation_name',
        );

        //获取当前入库单交易主体、当前项目下，大于等于入库单商品明细的销售合同
        $sql = 'SELECT '.implode(',', $fields).' FROM t_contract AS c 
                LEFT JOIN t_corporation AS co ON c.corporation_id = co.corporation_id
                LEFT JOIN t_contract_goods AS cg ON cg.contract_id = c.contract_id 
                LEFT JOIN t_project AS p ON p.project_id = c.project_id 
                LEFT JOIN t_partner AS dp ON dp.partner_id = c.partner_id 
                LEFT JOIN t_contract_file f ON f.contract_id = c.contract_id AND f.is_main = 1 AND f.type = 1'
            . ' WHERE c.partner_id > 0 '
            . ' AND c.type = ' . ConstantMap::SALE_TYPE
            . ' AND c.status>='.Contract::STATUS_BUSINESS_CHECKED
            . ' AND c.status < '.Contract::STATUS_SETTLED
            . ' AND cg.goods_id IN (' .implode(',',$stockInData['goods_ids'] ) .')'
            . ' AND c.corporation_id = '.$stockInData['corporation_id']
            . ' AND c.project_id = '.$stockInData['project_id']
            . ' GROUP BY c.contract_id ORDER BY c.contract_id DESC';

        $data = Utility::query($sql);

        foreach($data as & $item){
            $item['project_type_name'] = isset(Map::$v['project_type'][$item['project_type']]) ? Map::$v['project_type'][$item['project_type']]: '';
        }

        $this->returnSuccess($data);
    }

    /**
     * 新建经仓发货单，选择销售合同列表
     */
    public function actionSelectContracts() {
        $attr = Mod::app()->request->getParam('search');

        $fileds = array(
            'c.contract_id,c.contract_code,f.code_out',
            'p.project_id,p.project_code,p.type AS project_type',
            'c.partner_id,dp.name as partner_name',
            'p.corporation_id,co.name as corporation_name',
        );
        $sql = 'SELECT {col} FROM t_contract AS c
                LEFT JOIN t_project AS p ON p.project_id = c.project_id 
                LEFT JOIN t_partner dp ON dp.partner_id = c.partner_id  
                LEFT JOIN t_corporation co ON p.corporation_id = co.corporation_id 
                LEFT JOIN t_contract_file f ON c.contract_id = f.contract_id AND f.is_main=1 AND f.type=1'
            . $this->getWhereSql($attr)
            . ' AND c.partner_id > 0 AND c.type = ' . ConstantMap::SALE_TYPE
            . ' AND c.status>='.Contract::STATUS_BUSINESS_CHECKED
            . ' AND c.status < '.Contract::STATUS_SETTLED
            . ' AND  '.AuthorizeService::getUserDataConditionString('c')
            . ' ORDER BY c.contract_id DESC';

        $data = $this->queryTablesByPage($sql, implode(',',$fileds));

        $this->pageTitle = '新建经仓发货单';
        $this->render('contractList', $data);
    }

    /**
     * 获取销售合同明细，新建直调发货单用
     */
    public function actionGetContractsDetails(){
        $contractId = Mod::app()->request->getParam('contract_id');
        $stockInId = Mod::app()->request->getParam('stock_in_id');
        if (!Utility::checkQueryId($contractId) || !Utility::checkQueryId($stockInId)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        //所有可供选择的销售合同商品明细,只取入库单中存在的明细
        $goodsItems =  ContractGoodsService::getSaleContractsDetails($contractId, $stockInId);
        if(Utility::isEmpty($goodsItems)){
            $this->renderError(BusinessError::outputError(OilError::$DELIVERY_ORDER_GOODS_NOT_EXIST));
        }
        $this->returnSuccess($goodsItems);
    }

    /**
     * 获取该入库单下所有的商品明细ID数组
     * @param $stockInId
     * @return mixed
     */
    private function _getContractsDetailsIds($stockInId){
        $stockInModel = StockIn::model()->with('details', 'contract')->findByPk($stockInId, 't.status >= ' . StockIn::STATUS_PASS);
        if(empty($stockInModel)){
            $this->renderError(BusinessError::outputError(OilError::$STOCK_IN_NOT_EXIST,array('stock_in_id'=>$stockInId)));
        }

        //获取入库单的商品id数组,给直调选择销售合同提供筛选条件
        $goods_ids = array();
        foreach($stockInModel->details as & $item){
            $goods_ids[] = $item['goods_id'];
        }

        return array(
            'self'=> $stockInModel,
            'entry_date'=>$stockInModel->entry_date,
            'corporation_id'=>$stockInModel->contract->corporation_id,
            'project_id' => $stockInModel->project_id,
            'goods_ids'=>$goods_ids,
        );
    }

    /**
     * @desc 获取经仓配货信息对应的采购合同明细列表
     */
    public function actionGetStockInBuyContracts() {
        $detail_id = Mod::app()->request->getParam('contract_detail_id');
        if (!Utility::checkQueryId($detail_id)) {
            $this->returnError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $contractGoods = ContractGoods::model()->findByPk($detail_id);
        if (empty($contractGoods->detail_id)) {
            $this->returnError(BusinessError::outputError(OilError::$CONTRACT_GOODS_NOT_EXIST, array('detail_id' => $detail_id)));
        }

        $sqlSelf = 'select a.stock_id, a.contract_id, a.project_id, a.goods_id, a.quantity_balance, b.contract_code, a.store_id, 
                    d.name as partner_name, e.code as stock_in_code, f.name as store_name, a.stock_in_id, g.unit_store, 
                    case when a.stock_id > 0 then "本项目" else "" end as resource, 
                    case when a.stock_id > 0 then 1 else 0 end as type, 
                    case when a.stock_id > 0 then 0 end as cross_detail_id,   
                    case when a.stock_id > 0 then "" end as cross_code  
                    from t_stock a 
                    left join t_contract b on a.contract_id = b.contract_id 
                    left join t_partner d on d.partner_id = b.partner_id 
                    left join t_stock_in e on e.stock_in_id = a.stock_in_id 
                    left join t_storehouse f on f.store_id = a.store_id 
                    left join t_contract_goods g on g.contract_id = a.contract_id and g.goods_id = a.goods_id 
                    where a.goods_id = ' . $contractGoods->goods_id . ' and a.project_id = ' . $contractGoods->project_id . ' and a.quantity_balance > 0 and e.status >= ' . StockIn::STATUS_PASS;

        $sqlOther = 'select a.stock_id, a.contract_id, a.project_id, a.goods_id, a.quantity_balance, c.contract_code, a.store_id, 
                     d.name as partner_name, f.code as stock_in_code, g.name as store_name, e.stock_in_id, h.unit_store, 
                     case when a.type = 1 then "借货" when a.type = 3 then "还货" end as resource, 
                     a.type, a.detail_id as cross_detail_id, b.cross_code 
                     from t_cross_detail a 
                     left join t_cross_order b on b.cross_id = a.cross_id 
                     left join t_contract c on c.contract_id = a.contract_id 
                     left join t_partner d on d.partner_id = c.partner_id 
                     left join t_stock e on e.stock_id = a.stock_id 
                     left join t_stock_in f on f.stock_in_id = e.stock_in_id 
                     left join t_storehouse g on g.store_id = a.store_id 
                     left join t_contract_goods h on h.contract_id = a.contract_id and h.goods_id = a.goods_id 
                     where b.contract_id = ' . $contractGoods->contract_id . ' and b.goods_id = ' . $contractGoods->goods_id . ' 
                     and a.type=' . ConstantMap::ORDER_CROSS_TYPE . ' 
                     and a.quantity_balance > 0 and b.status >= ' . CrossOrder::STATUS_PASS . ' 
                     and f.status >= ' . StockIn::STATUS_PASS;

        $sqlBack = 'select a.stock_id, a.contract_id, a.project_id, a.goods_id, a.quantity_balance, c.contract_code, a.store_id, 
                     d.name as partner_name, f.code as stock_in_code, g.name as store_name, e.stock_in_id, h.unit_store, 
                     case when a.type = 1 then "借货" when a.type = 3 then "还货" end as resource, 
                     a.type, a.detail_id as cross_detail_id, b.cross_code 
                     from t_cross_detail a 
                     left join t_cross_order b on b.cross_id = a.cross_id 
                     left join t_contract c on c.contract_id = a.contract_id 
                     left join t_partner d on d.partner_id = c.partner_id 
                     left join t_stock e on e.stock_id = a.stock_id 
                     left join t_stock_in f on f.stock_in_id = e.stock_in_id 
                     left join t_storehouse g on g.store_id = a.store_id 
                     left join t_contract_goods h on h.contract_id = a.contract_id and h.goods_id = a.goods_id 
                     where b.project_id = ' . $contractGoods->project_id . ' 
                     and b.goods_id = ' . $contractGoods->goods_id . ' 
                     and a.type=' . ConstantMap::ORDER_BACK_TYPE . ' 
                     and a.quantity_balance > 0 and b.status >= ' . CrossOrder::STATUS_PASS . ' 
                     and f.status >= ' . StockIn::STATUS_PASS;

        $sql = $sqlSelf . ' union ' . $sqlOther. ' union ' . $sqlBack;
        $data = Utility::query($sql);
        if (Utility::isNotEmpty($data)) {
            foreach ($data as $key => $row) {
                $data[$key]['unit_store_desc'] = Map::$v['goods_unit'][$row['unit_store']]['name'];
                $data[$key]['quantity'] = 0;
                $data[$key]['contract_detail_id'] = $detail_id;
                if (!empty($row['cross_detail_id'])) {
                    $data[$key]['type'] = $row['type'] == ConstantMap::ORDER_CROSS_TYPE ? ConstantMap::DISTRIBUTED_LOAN : (ConstantMap::ORDER_BACK_TYPE ? ConstantMap::DISTRIBUTED_RETURN : 0);
                    $data[$key]['resource'] = $row['resource'] . ' <span class="text-red">' . $row['cross_code'] . '</span>';
                }
            }
        }

        $this->returnSuccess(array('buyContracts' => $data));
    }

    public function actionSave() {
        $params = Mod::app()->request->getParam('data');
        $deliveryOrderDetail = $params['deliveryOrderDetail'];
        unset($params['deliveryOrderDetail']);

        $requiredParams = array('project_id','contract_id','corporation_id','partner_id', 'delivery_date', 'type');
        if ($params['type'] == ConstantMap::STOCK_NOTICE_TYPE_DIRECT_TRANSFER) {
            array_push($requiredParams, 'stock_in_id');
        }
        if (!Utility::checkRequiredParamsNoFilterInject($params, $requiredParams)) {
            $this->returnError(BusinessError::outputError(OilError::$REQUIRED_PARAMS_CHECK_ERROR));
        }

        if (!empty($params['stock_in_id'])) {
            $stockInModel = StockIn::model()->findByPk($params['stock_in_id']);
            if (empty($stockInModel->stock_in_id)) {
                $this->returnError(BusinessError::outputError(OilError::$STOCK_IN_NOT_EXIST, array('stock_in_id' => $params['stock_in_id'])));
            }
        }

        //发货明细参数校验
        $detailsCheckRes = DeliveryOrderDetailService::checkParamsValid($deliveryOrderDetail, $params);
        if ($detailsCheckRes !== true) {
            $this->returnError($detailsCheckRes);
        }

        if (!empty($params['order_id'])) {
            $deliveryOrderModel = DeliveryOrder::model()->findByPk($params['order_id']);
        }else{
            $this->returnError("参数有误！");
        }

        if (!empty($deliveryOrderModel->order_id)) {
            if (!DeliveryOrderService::isCanEdit($deliveryOrderModel['status'])) {
                $this->returnError(BusinessError::outputError(OilError::$DELIVERY_ORDER_NOT_ALLOW_EDIT));
            }
        } else {
            $deliveryOrderModel = new DeliveryOrder();
            $codeInfo = CodeService::getDeliveryOrderCode($params['corporation_id']);
            if ($codeInfo['code'] == ConstantMap::INVALID) {
                $this->returnError($codeInfo['msg']);
            }
            $deliveryOrderModel->code = $codeInfo['data'];
            $deliveryOrderModel->status = DeliveryOrder::STATUS_NEW;
            $deliveryOrderModel->status_time = Utility::getDateTime();
        }

        if ($params['isSubmit']) {
            $deliveryOrderModel->status = DeliveryOrder::STATUS_SUBMIT;
        }

        $logRemark = ActionLog::getEditRemark($deliveryOrderModel->isNewRecord, "发货单");
        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try {
            $deliveryOrderModel->setAttributes($params, false);
            $contract = Contract::model()->findByPk($params['contract_id']);
            $deliveryOrderModel->currency = $contract->currency;
            $deliveryOrderModel->save();

            //保存发货单明细&配货明细
            DeliveryOrderDetailService::saveDetails($deliveryOrderModel, $deliveryOrderDetail);

            if ($deliveryOrderModel->status == DeliveryOrder::STATUS_SUBMIT) {
                FlowService::startFlowForCheck9($deliveryOrderModel->order_id);

                //根据配货明细冻结库存
                StockDeliveryDetailService::freezeStockByOrderId($deliveryOrderModel->order_id);
            }

            TaskService::doneTask($deliveryOrderModel->order_id, Action::ACTION_33);
            $trans->commit();

            Utility::addActionLog(json_encode($deliveryOrderModel->oldAttributes), $logRemark, "DeliveryOrder", $deliveryOrderModel->order_id);
            $this->returnSuccess($deliveryOrderModel->order_id);
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

    public function actionSubmit() {
        $order_id = Mod::app()->request->getParam("id");
        if (!Utility::checkQueryId($order_id)) {
            $this->returnError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $deliveryOrderModel = DeliveryOrder::model()->findByPk($order_id);
        if (empty($deliveryOrderModel->order_id)) {
            $this->returnError(BusinessError::outputError(OilError::$DELIVERY_ORDER_NOT_EXIST, array('order_id' => $order_id)));
        }

        if ($deliveryOrderModel->status >= DeliveryOrder::STATUS_SUBMIT) {
            $this->returnError(BusinessError::outputError(OilError::$DELIVERY_ORDER_NOT_ALLOW_SUBMIT));
        }

        $oldStatus = $deliveryOrderModel->status;
        $deliveryOrderModel->status = DeliveryOrder::STATUS_SUBMIT;
        $deliveryOrderModel->status_time = Utility::getDateTime();

        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try {
            $deliveryOrderModel->save();

            //保存发货单明细&配货明细
            DeliveryOrderDetailService::updateDetailsIsSubmit($deliveryOrderModel);

            FlowService::startFlowForCheck9($deliveryOrderModel->order_id);

            //根据配货明细冻结库存
            StockDeliveryDetailService::freezeStockByOrderId($deliveryOrderModel->order_id);

            //直调提交时生成配货明细出库单
            /*if ($deliveryOrderModel->type == ConstantMap::STOCK_NOTICE_TYPE_DIRECT_TRANSFER) {
                StockOutService::addDirectTransferDeliveryDetail($deliveryOrderModel->order_id);
            }*/
            
            TaskService::doneTask($deliveryOrderModel->order_id, Action::ACTION_33);
            $trans->commit();

            Utility::addActionLog(json_encode(array('oldStatus' => $oldStatus)), "提交发货单", get_class($deliveryOrderModel), $deliveryOrderModel->getPrimaryKey());
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

    public function actionDetail() {
        $order_id = Mod::app()->request->getParam('id');
        if (!Utility::checkQueryId($order_id)) {
            $this->renderError(BusinessError::outputError(OilError::$PARAMS_PASS_ERROR));
        }

        $deliveryOrderModel = DeliveryOrder::model()->with('details','details.stockDeliveryDetail.stock')->findByPk($order_id);
        if (empty($deliveryOrderModel->order_id)) {
            $this->renderError(BusinessError::outputError(OilError::$DELIVERY_ORDER_NOT_EXIST, array('order_id' => $order_id)));
        }

        $this->pageTitle = '发货单明细';
        $this->render('detail', array('deliveryOrder' => $deliveryOrderModel));
    }
}