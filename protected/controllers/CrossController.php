<?php

use ddd\Split\Domain\Model\SplitEnum;

/**
 * Created by vector.
 * DateTime: 2017/10/17 15:58
 * Describe：合同调货
 */
class CrossController  extends AttachmentController
{

    public function pageInit()
    {
        $this->rightCode="cross";
        $this->filterActions="add,getCrossInfo,getCrossDetail,submit";
    }


    public function actionIndex()
    {
        $attr = $_GET[search];

        $user = SystemUser::getUser(Utility::getNowUserId());

        $sql = "select {col}
            from t_contract c 
            left join t_contract_goods t on c.contract_id=t.contract_id
            left join t_project p on c.project_id=p.project_id
            left join t_system_user u on c.manager_user_id=u.user_id
            left join t_partner pa on c.partner_id=pa.partner_id
            left join t_goods g on t.goods_id=g.goods_id
            left join t_corporation co on c.corporation_id=co.corporation_id 
            left join t_contract_file cf on cf.contract_id=c.contract_id and cf.is_main=1 and cf.type=1 
            ". $this->getWhereSql($attr); //left join t_contract_file f on c.contract_id=f.contract_id
            //and f.is_main=1 and f.type=11 and f.status=".ContractFile::STATUS_CHECKING."
        $sql    .= " and c.type=2 and t.type=2 and c.status>=".Contract::STATUS_BUSINESS_CHECKED." and c.corporation_id in (".$user['corp_ids'].") order by p.project_id desc,c.contract_id desc,t.detail_id asc {limit}";
        $fields  = "c.contract_id,c.contract_code,c.status as contract_status,p.project_id,p.project_code,pa.partner_id,pa.name as partner_name,g.name as goods_name,
                    t.detail_id,t.goods_id,co.corporation_id,co.name as corporation_name, cf.code_out";
        $data=$this->queryTablesByPage($sql,$fields);

        $data["search"]=$attr;
        $this->pageTitle = '添加调货单';
        $this->render("index", $data);
    }

    public function checkIsCanAdd($status)
    {
        if($status == 0 || $status >= CrossOrder::STATUS_PASS)
        {
            return true;
        }
        else
            return false;
    }

    /**
     * 判断是否可以修改，子类需要修改该方法
     * @param $status
     * @return bool
     */
    public function checkIsCanEdit($status)
    {
        if($status==CrossOrder::STATUS_BACK || $status == CrossOrder::STATUS_SAVED)
        {
            return true;
        }
        else
            return false;
    }


    public function actionAdd()
    {
        $id = Mod::app()->request->getParam("id");
        if(empty($id))
            $this->renderError("参数错误！", "/cross/");

        $contractGoods = ContractGoods::model()->with('contract', 'goods', 'project')->findByPk($id);
        $contract   = $contractGoods->contract;
        $goods      = $contractGoods->goods;
        $project    = $contractGoods->project;
        if(empty($contractGoods->detail_id) || empty($contract->contract_id) 
        || empty($goods->goods_id) || empty($project->project_id)){
            $this->renderError("当前信息不存在！", "/cross/");
        }
        $partner = Partner::model()->findByPk($contract->partner_id);
        $corporation = Corporation::model()->findByPk($contract->corporation_id);

        $map    = Map::$v;
        $contract_unit   = $map['goods_unit'][$contractGoods->unit]['name'];
        $store_unit      = $map['goods_unit'][$contractGoods->unit_store]['name'];
        $data['detail_id']      = $id;
        $data['contract_id']    = $contract->contract_id;
        $data['contract_code']  = $contract->contract_code;
        $data['goods_id']       = $goods->goods_id;
        $data['goods_name']     = $goods->name;
        $data['project_id']     = $project->project_id;
        $data['project_code']   = $project->project_code;
        $data['partner_id']     = $partner->partner_id;
        $data['partner_name']   = $partner->name;
        $data['corporation_id']     = $corporation->corporation_id;
        $data['corporation_name']   = $corporation->name;

        $plus = "";
        if($contractGoods->more_or_less_rate>0){
            $more_or_less_rate = $contractGoods->more_or_less_rate*100;
            $plus = "+".$more_or_less_rate."%";
        }
        $data['contract_quantity'] = number_format($contractGoods->quantity, 2).$contract_unit.$plus;
        $delivery_quantity = CrossOrderService::getAllocateTotal($data['contract_id'], $data['goods_id']);
        $data['delivery_quantity'] = !empty($delivery_quantity) ? number_format($delivery_quantity,2).$store_unit : '0'.$store_unit;

        $out_quantity = CrossOrderService::getOutTotal($data['contract_id'], $data['goods_id']);
        $data['out_quantity'] = !empty($out_quantity) ? number_format($out_quantity,2).$store_unit : '0'.$store_unit;

        $crossDetail= array();
        $orderDetail= array();
        $total_quantity = 0;
        $total_quantity_out = 0;
        $order = CrossOrderService::getAllOrderDetail($data['contract_id'], $data['goods_id']);
        if(Utility::isNotEmpty($order)){
            foreach ($order as $key => $value) {
                $orderDetail[$value['cross_id']][] = $value;
                $total_quantity += $value['quantity']; 
                $total_quantity_out += $value['quantity_out']; 
                $crossDetail['detail'][$key] = $value;
            }
    
            if(count($crossDetail)>0){
                $crossDetail['total_quantity']      = $total_quantity;
                $crossDetail['total_quantity_out']  = $total_quantity_out;
                $crossDetail['unit']                = $contractGoods->unit_store;
            }

            $max_cross_id = max(array_keys($orderDetail));

            $goodsItems = $orderDetail[$max_cross_id];
            if(!$this->checkIsCanAdd($goodsItems[0]['status'])){
                $this->renderError("当前状态下不可添加调货信息！", "/cross/");
            }else{
                $data['order_index'] = $goodsItems[0]['order_index'];
            }
        }

        $this->pageTitle="操作调货";
        $this->render("add",array(
            'data' => $data, 
            'crossDetail'=>$crossDetail
            )
        );
    }


    /**
     * @desc 根据contractId和goodsId获取借货信息
     */
    public function actionGetCrossInfo() {
        $corporation_id = Mod::app()->request->getParam("corporation_id");
        $goods_id = Mod::app()->request->getParam("goods_id");
        $project_id = Mod::app()->request->getParam("project_id");
        if(empty($corporation_id) || empty($goods_id) || empty($project_id))
            $this->returnError("参数有误！");

        $sql = "select temp.contract_code as contractCode,temp.partner_name as partnerName,s.stock_in_id,
                s.code as stockCode,h.store_id as storeId,h.name as storeName,g.name as goodsName from (
                select c.contract_id,c.contract_code,p.name as partner_name
                from t_contract c 
                left join t_partner p on c.partner_id=p.partner_id
                where  c.type=".ConstantMap::BUY_TYPE." and c.corporation_id=".$corporation_id." and c.project_id!=".$project_id." and c.status>=".Contract::STATUS_BUSINESS_CHECKED.") as temp
                left join t_stock_in s on temp.contract_id=s.contract_id
                left join t_stock_in_detail d on s.stock_in_id=d.stock_in_id
                left join t_stock k on d.stock_id=k.stock_id
                left join t_storehouse h on d.store_id=h.store_id
                left join t_goods g on d.goods_id=g.goods_id
                where d.goods_id=".$goods_id." and s.status=".StockIn::STATUS_PASS. ' and s.is_virtual = '.SplitEnum::IS_REALITY.
                " and k.quantity_balance>0";
                //" and f.is_main=1".
                //" and f.type=".ConstantMap::ELECTRON_DOUBLE_SIGN_CONTRACT_MODULE." and f.status=".ContractFile::STATUS_CHECKING
                //left join t_contract_file f on c.contract_id=f.contract_id 
                //and s.type=".ConstantMap::STOCK_NOTICE_TYPE_BY_WAREHOUSE."
        $data = Utility::query($sql);
        $this->returnSuccess($data);
    }

    /**
     * @desc 根据contractId、goodsId和入库单号获取调货明细
     */
    public function actionGetCrossDetail() {
        $goods_id   = Mod::app()->request->getParam("goods_id");
        $id_str     = Mod::app()->request->getParam("id_str");
        if(empty($goods_id) || empty($id_str))
            $this->returnError("参数有误！");
        
        /*$pos    = strpos($id_str, 'all');
        $idStr  = '';
        if($pos!==false)
            $idStr = substr($id_str, $pos+4);
        else
            $idStr = $id_str;*/

        $data =array();
        $sql = "select s.stock_in_id,s.code as stock_code,t.stock_id,c.contract_id,c.contract_code,c.project_id,
                t.goods_id,p.name as partner_name, h.store_id,h.name as store_name,t.quantity_balance,t.unit
                from t_stock_in s 
                left join t_stock t on s.stock_in_id=t.stock_in_id
                left join t_storehouse h on s.store_id=h.store_id
                left join t_contract c on s.contract_id=c.contract_id
                left join t_partner p on c.partner_id=p.partner_id
                where FIND_IN_SET(s.stock_in_id, '".$id_str."') and t.goods_id=".$goods_id;
        $data = Utility::query($sql);
        
        if(Utility::isNotEmpty($data)){
            $map = Map::$v;
            foreach ($data as $key => $value) {
                $data[$key]['quantity_format'] = number_format($value['quantity_balance'], 2).$map['goods_unit'][$value['unit']]['name'];
                $data[$key]['unit_format'] = $map['goods_unit'][$value['unit']]['name'];
            }
        }
        // print_r($data);die;
        $this->returnSuccess($data);
    }


    public function actionEdit()
    {
        $id = Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
        {
            $this->renderError("参数错误！", "/cross/");
        }

        $contractGoods = ContractGoods::model()->with('contract', 'goods', 'project')->findByPk($id);
        $contract   = $contractGoods->contract;
        $goods      = $contractGoods->goods;
        $project    = $contractGoods->project;
        if(empty($contractGoods->detail_id) || empty($contract->contract_id) 
        || empty($goods->goods_id) || empty($project->project_id)){
            $this->renderError("当前信息不存在！", "/cross/");
        }
        $partner = Partner::model()->findByPk($contract->partner_id);
        $corporation = Corporation::model()->findByPk($contract->corporation_id);

        $map    = Map::$v;
        $contract_unit   = $map['goods_unit'][$contractGoods->unit]['name'];
        $store_unit      = $map['goods_unit'][$contractGoods->unit_store]['name'];
        $data['detail_id']      = $id;
        $data['contract_id']    = $contract->contract_id;
        $data['contract_code']  = $contract->contract_code;
        $data['goods_id']       = $goods->goods_id;
        $data['goods_name']     = $goods->name;
        $data['project_id']     = $project->project_id;
        $data['project_code']   = $project->project_code;
        $data['partner_id']     = $partner->partner_id;
        $data['partner_name']   = $partner->name;
        $data['corporation_id']     = $corporation->corporation_id;
        $data['corporation_name']   = $corporation->name;

        $plus = "";
        if($contractGoods->more_or_less_rate>0){
            $more_or_less_rate = $contractGoods->more_or_less_rate*100;
            $plus = "+".$more_or_less_rate."%";
        }
        $data['contract_quantity'] = number_format($contractGoods->quantity, 2).$contract_unit.$plus;
        $delivery_quantity = CrossOrderService::getAllocateTotal($data['contract_id'], $data['goods_id']);
        $data['delivery_quantity'] = !empty($delivery_quantity) ? number_format($delivery_quantity,2).$store_unit : '0'.$store_unit;

        $out_quantity = CrossOrderService::getOutTotal($data['contract_id'], $data['goods_id']);
        $data['out_quantity'] = !empty($out_quantity) ? number_format($out_quantity,2).$store_unit : '0'.$store_unit;

        $order = CrossOrderService::getAllOrderDetail($data['contract_id'], $data['goods_id']);
        if(Utility::isEmpty($order)){
            $this->renderError("当前调货信息不存在！", "/cross/");
        }

        $goodsItems = array();
        $orderDetail= array();
        $crossDetail= array();
        $total_quantity = 0;
        $total_quantity_out = 0;
        foreach ($order as $key => $value) {
            $orderDetail[$value['cross_id']][] = $value;
            $total_quantity += $value['quantity']; 
            $total_quantity_out += $value['quantity_out']; 
            $crossDetail['detail'][$key] = $value;
        }

        if(count($crossDetail)>0){
            $crossDetail['total_quantity']      = $total_quantity;
            $crossDetail['total_quantity_out']  = $total_quantity_out;
            $crossDetail['unit']                = $contractGoods->unit_store;
        }

        $max_cross_id = max(array_keys($orderDetail));

        $goodsItems = $orderDetail[$max_cross_id];
        if(!$this->checkIsCanEdit($goodsItems[0]['status'])){
            $this->renderError("当前状态下不可编辑调货信息！", "/cross/");
        }

        // $map = Map::$v;
        foreach ($goodsItems as $key => $value) {
            $goodsItems[$key]['quantity_format'] = number_format($value['quantity_balance'], 2).$map['goods_unit'][$value['unit']]['name'];
            $goodsItems[$key]['unit_format'] = $map['goods_unit'][$value['unit']]['name'];
        }

        $data['cross_id']   = $goodsItems[0]['cross_id'];
        $data['cross_date'] = $goodsItems[0]['cross_date'];
        $data['remark']     = $goodsItems[0]['remark'];


        // print_r($crossDetail);die;
        
        $this->pageTitle="操作调货";
        $this->render("add",array(
            'data' => $data, 
            'crossDetail'=> $crossDetail,
            'goodsItems'=>$goodsItems,
            'max_cross_id'=>$max_cross_id
            )
        );
    }

    public function actionDetail()
    {
        $id = Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
        {
            $this->renderError("参数错误！", "/cross/");
        }

        $sql = "select i.detail_id,c.contract_code,c.contract_id,g.goods_id,g.name as goods_name,i.more_or_less_rate,i.quantity,i.unit from t_contract c 
                left join t_contract_goods i on c.contract_id=i.contract_id
                left join t_goods g on i.goods_id=g.goods_id
                where c.contract_id=(select contract_id from t_contract_goods where detail_id=".$id.")";
        $data = Utility::query($sql);

        $contract       = array();
        $transactions   = array();
        if(Utility::isNotEmpty($data)){
            $contract['contract_id'] = $data[0]['contract_id'];
            $contract['contract_code'] = $data[0]['contract_code'];
            $contract['detail_id'] = $id;
            $transactions = $data;
            foreach ($data as $key => $value) {
                if($value['detail_id']==$id){
                    $contract['goods_id']   = $value['goods_id'];
                    $contract['goods_name'] = $value['goods_name'];
                }
            }
        }else{
            $this->renderError("当前信息不存在！", "/cross/");
        }

        $order = CrossOrderService::getAllOrderDetail($contract['contract_id'], $contract['goods_id']);

        $crossOrder = array();
        if(Utility::isNotEmpty($order)){
            foreach ($order as $key => $value) {
                $crossOrder[$value['cross_id']]['remark']   = $value['remark'];
                $crossOrder[$value['cross_id']]['cross_date']   = $value['cross_date'];
                $crossOrder[$value['cross_id']]['cross_code']   = $value['cross_code'];
                $crossOrder[$value['cross_id']]['status']   = $value['status'];
                $crossOrder[$value['cross_id']]['order_index']   = $value['order_index'];
                $crossOrder[$value['cross_id']]['details'][]  = $value;
            }
        }else{
            $this->renderError("当前信息不存在！", "/cross/");
        }
        // print_r($crossOrder);die;

        $this->pageTitle="查看详情";
        $this->render("detail",array(
            "crossOrder"    => $crossOrder,
            'contract'      => $contract, 
            'transactions'  => $transactions,
            )
        );
    }

    public function actionSave()
    {
        $params = Mod::app()->request->getParam('data');
        $goodsItems = $params['goodsItems'];
        // print_r($goodsItems);die;
        unset($params['goodsItems']);
        
        $requiredParams = array('goods_id', 'contract_id', 'project_id', 'contract_code', 'remark');
        $filterInjectParams = Utility::checkRequiredParams($params, $requiredParams);
        if(!$filterInjectParams['isValid'])
            $this->returnError(BusinessError::outputError(OilError::$REQUIRED_PARAMS_CHECK_ERROR));
        $params = $filterInjectParams['params'];

        if(Utility::isEmpty($goodsItems))
            $this->returnError("请添加调货明细！");

        // print_r($params);die;
        $detail_id = $params['detail_id'];
        unset($params['detail_id']);
        $nowUserId  = Utility::getNowUserId();
        $nowTime    = new CDbExpression("now()");

        if(empty($params['cross_id'])){
            $obj = new CrossOrder();
            $obj->type              = ConstantMap::ORDER_CROSS_TYPE;
            $obj->goods_detail_id=$detail_id;
            $order_index            = $params['order_index']+1;
            $obj->cross_code        = $params['contract_code'].'-DH'.$order_index;
            $obj->order_index       = $order_index;
            $obj->status_time       = $nowTime;
            $obj->create_time       = $nowTime;
            $obj->create_user_id    = $nowUserId;
        }else{
            $obj = CrossOrder::model()->findByPk($params['cross_id']);
            if(empty($obj->cross_id))
                $this->returnError("当前信息不存在！");
        }
       
        // print_r($params);die;
        unset($params['cross_id']);
        unset($params['order_index']);

        $obj->setAttributes($params, false);

        if(empty($params['isSave'])){
            $obj->status = CrossOrder::STATUS_CHECKING;
            $obj->status_time = $nowTime;
        }else{
            $obj->status = CrossOrder::STATUS_SAVED;
        }
        
        $obj->update_time       = $nowTime;
        $obj->update_user_id    = $nowUserId;

        $logRemark = ActionLog::getEditRemark($obj->isNewRecord, "调货单");
        $trans = Utility::beginTransaction();
        try {

            $obj->save();

            CrossOrderService::saveCrossDetail($goodsItems, $obj->cross_id, $params['isSave']);
            if(empty($params['isSave'])){
                FlowService::startFlowForCheck11($obj->cross_id);
            }

            TaskService::doneTask($detail_id,Action::ACTION_CROSS_CHECK_BACK);

            $trans->commit();
            Utility::addActionLog(json_encode($obj->oldAttributes), $logRemark, "CrossOrder", $obj->cross_id);
            $this->returnSuccess($detail_id);
            
        } catch (Exception $e) {
            try {
                $trans->rollback();
            } catch (Exception $ee) {
                Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $ee->getMessage(), CLogger::LEVEL_ERROR);
            }

            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $e->getMessage(), CLogger::LEVEL_ERROR);

            $this->returnError(BusinessError::outputError(OilError::$CROSS_SAVE_ADD_ERROR, array('reason' => $e->getMessage())));
        }
        
    }

    public function actionSubmit()
    {
        $id = Mod::app()->request->getParam("id");
        if(!Utility::checkQueryId($id))
        {
            $this->renderError("参数错误！", "/cross/");
        }

        $order = CrossOrder::model()->with("crossDetail")->findByPk($id);
        if(!$this->checkIsCanEdit($order->status))
            $this->returnError("当前状态下不可提交调货信息！");

        $trans = Utility::beginTransaction();
        try{
            $oldStatus = $order->status;
           $order->status = CrossOrder::STATUS_CHECKING;
           $order->status_time      = new CDbExpression("now()");
           $order->update_time      = new CDbExpression("now()");
           $order->update_user_id   = Utility::getNowUserId();
           $order->save();

           $crossDetail = $order->crossDetail;

            foreach ($crossDetail as $detail) {
                $r = StockService::freeze($detail->stock_id, $detail->quantity);
                if(!$r){
                    throw new Exception("库存ID为".$detail->stock_id.",冻结库存失败！");
                }
            }

            //$detailId=ContractService::getContractGoodsDetailId($order->contract_id,$order->goods_id);
            TaskService::doneTask($order->goods_detail_id,Action::ACTION_CROSS_CHECK_BACK);
            FlowService::startFlowForCheck11($order->cross_id);

            $trans->commit();

            Utility::addActionLog(json_encode(array('oldStatus'=>$oldStatus)), "提交调货单", "CrossOrder", $order->cross_id);
            $this->returnSuccess();
            
        } catch (Exception $e) {
            try {
                $trans->rollback();
            } catch (Exception $ee) {
                Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $ee->getMessage(), CLogger::LEVEL_ERROR);
            }

            Mod::log(__CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $e->getMessage(), CLogger::LEVEL_ERROR);

            $this->returnError(BusinessError::outputError(OilError::$CROSS_SAVE_ADD_ERROR, array('reason' => $e->getMessage())));
        }
        
    }
}