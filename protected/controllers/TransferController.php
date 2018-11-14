<?php

use ddd\Split\Domain\Model\SplitEnum;

/**
 * Created by vector.
 * DateTime: 2017/10/20 15:58
 * Describe：调货处理
 */
class TransferController  extends AttachmentController
{

    public function pageInit()
    {
        $this->rightCode="transfer";
        $this->filterActions="add,getCrossInfo,getCrossDetail,submit";
    }


    public function actionIndex()
    {
        $attr = $_GET[search];

        $user = SystemUser::getUser(Utility::getNowUserId());

        $fields  = "cd.detail_id,o.cross_id,o.cross_code,
                    cr.cross_id as return_cross_id,cr.status as return_cross_status,
                    c.contract_id as sell_id,c.contract_code as sell_code,
                    ct.contract_id as buy_id,ct.contract_code as buy_code,
                    pa.partner_id,pa.name as partner_name,g.name as goods_name,
                    g.goods_id,co.corporation_id,co.name as corporation_name,
                    scf.code_out as sell_code_out, bcf.code_out as buy_code_out";

        $sql1   = "select ".$fields."
            from t_cross_contract_detail cd 
            left join t_cross_order o on cd.cross_id=o.cross_id
            left join t_cross_order cr on cd.detail_id=cr.detail_id and cr.status<".CrossOrder::STATUS_PASS."
            left join t_contract c on o.contract_id=c.contract_id 
            left join t_contract_file scf on scf.contract_id=c.contract_id and scf.is_main=1 and scf.type=1 
            left join t_system_user u on c.manager_user_id=u.user_id
            left join t_partner pa on c.partner_id=pa.partner_id
            left join t_goods g on o.goods_id=g.goods_id
            left join t_corporation co on c.corporation_id=co.corporation_id
            left join t_contract ct on cd.contract_id=ct.contract_id 
            left join t_contract_file bcf on bcf.contract_id=ct.contract_id and bcf.is_main=1 and bcf.type=1 
            ". $this->getWhereSql($attr);

        $sql1  .= " and o.type=".ConstantMap::ORDER_CROSS_TYPE." 
                        and o.status>=".CrossOrder::STATUS_PASS." 
                        and c.corporation_id in (".$user['corp_ids'].") 
                       group by ct.contract_id,o.cross_id order by cd.detail_id desc";

        $sql = 'select {col} from (' . $sql1 . ') as gs where 1=1 {limit}';
        
        $data=$this->queryTablesByPage($sql,'*');

        $data["search"]=$attr;
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
        $id             = Mod::app()->request->getParam("id");
        $contractId     = Mod::app()->request->getParam("contract_id");
        if(!Utility::checkQueryId($id) || !Utility::checkQueryId($contractId))
            $this->renderError("参数有误！", "/transfer/");

        $crossOrder     = CrossOrder::model()->with('contract', 'goods', 'project', 'contractGoods')->findByPk($id);
        $contract       = $crossOrder->contract;
        $goods          = $crossOrder->goods;
        $project        = $crossOrder->project;
        $contractGoods  = $crossOrder->contractGoods;
        $partner        = Partner::model()->findByPk($contract->partner_id);
        $buyContract   = Contract::model()->findByPk($contractId);//被借采购合同对象

        $map = Map::$v;
        $data['detail_id']      = $contractGoods->detail_id;
        $data['contract_id']    = $contract->contract_id;
        $data['contract_code']  = $contract->contract_code;
        $data['goods_id']       = $goods->goods_id;
        $data['goods_name']     = $goods->name;
        $data['project_id']     = $project->project_id;
        $data['project_code']   = $project->project_code;
        $data['partner_id']     = $partner->partner_id;
        $data['partner_name']   = $partner->name;
        $data['corporation_id']         = $contract->corporation_id;
        $data['relation_cross_id']      = $crossOrder->cross_id;
        $data['relation_cross_code']    = $crossOrder->cross_code;
        $data['cross_date']     = $crossOrder->cross_date;
        $data['reason']         = $crossOrder->remark;
        $data['buy_id']         = $buyContract->contract_id;  //被借采购合同编号
        $data['buy_code']       = $buyContract->contract_code; //被借采购合同编码
        $data['buy_project_id'] = $buyContract->project_id; //被借采购合同项目编号
        $data['unit_format']    = $map['goods_unit'][$contractGoods->unit_store]['name'];

        $orderDetail = CrossOrderService::getOrderDetailById($data['relation_cross_id'], $contractId);
        if(Utility::isEmpty($orderDetail)){
            $this->renderError("当前调货信息不存在！", "/cross/");
        }
        // print_r($orderDetail);die;
        $crossDetail= array();
        $total_quantity = 0;
        $total_quantity_out = 0;
        foreach ($orderDetail as $key=>$value) {
            $total_quantity += $value['quantity']; 
            $total_quantity_out += $value['quantity_out']; 
            $crossDetail['detail'][$key] = $value;
        }

        if(!empty($crossDetail)){
            $crossDetail['total_quantity']      = $total_quantity;
            $crossDetail['total_quantity_out']  = $total_quantity_out;
            $crossDetail['unit']                = $contractGoods->unit_store;
        }

        //获取被借采购合同最新一条记录
        $crossReturn = CrossOrderService::getCrossDetailById($data['relation_cross_id'], $contractId);

        if(Utility::isNotEmpty($crossReturn))
            $data['order_index'] = $crossReturn['order_index'];

        /*$outArr = array();
        foreach ($crossDetail['detail'] as $k => $v) {
            $temp = CrossOrderService::getAllOutOrder($v['cross_detail_id']);
            if(Utility::isNotEmpty($temp))
                $outArr[] = $temp;
        }

        $outOrder   = array();
        $detailArr  = array();
        $out_quantity = 0;
        if(Utility::isNotEmpty($outArr)){
            foreach ($outArr as $key => $out) {
                foreach ($out as $k => $v) {
                    $outOrder['detail'][] = $v;
                    $out_quantity += $v['quantity_actual'];
                    $detailArr[] = CrossDetail::model()->findAllToArray("out_id=".$v['out_id']." and stock_id=".$v['stock_id']);
                }
            }
            $outOrder['out_total_quantity'] = $out_quantity;
            $outOrder['unit']         = $contractGoods->unit_store;
        }
        
        $crossArr = array();
        if(Utility::isNotEmpty($detailArr)){
            foreach ($detailArr as $detail) {
                foreach ($detail as $k => $v) {
                    $crossArr[$v['out_id']]['quantity'] += $v['quantity'];
                    $crossArr[$v['out_id']]['type'][$v['type']]['quantity'] += $v['quantity'];
                }
            }
        }

        $buy_desc = "";
        $pay_desc = "";
        $crossItmes = array();
        $map = Map::$v;
        if(Utility::isNotEmpty($outOrder['detail'])){
            foreach ($outOrder['detail'] as $key => $value) {
                $unit = $map['goods_unit'][$value['unit']]['name'];
                $outOrder['detail'][$key]['unit_format'] = $map['goods_unit'][$value['unit']]['name'];
                $outOrder['detail'][$key]['quantity_done'] = $crossArr[$value['out_id']]['quantity'];
                $outOrder['detail'][$key]['quantity_done_format'] = number_format($crossArr[$value['out_id']]['quantity'],2).$unit;
                $typeArr = $crossArr[$value['out_id']]['type'];
                if(Utility::isNotEmpty($typeArr)){
                    foreach ($typeArr as $k => $v) {
                        if($k==ConstantMap::ORDER_BUY_TYPE){
                            $buy_desc = number_format($v['quantity'], 2).$map['goods_unit'][$value['unit']]['name'].$map['cross_done_desc'][$k];
                        }else{
                            $pay_desc = number_format($v['quantity'], 2).$map['goods_unit'][$value['unit']]['name'].$map['cross_done_desc'][$k];
                        }
                    }
                }

                if(!empty($buy_desc) && !empty($pay_desc)){
                    $done_desc = $buy_desc.",".$pay_desc;
                }else{
                    $done_desc = !empty($buy_desc) ? $buy_desc : $pay_desc;
                }
                $outOrder['detail'][$key]['goods_name']=$data['goods_name'];
                $outOrder['detail'][$key]['done_desc'] = $done_desc;
                $outOrder['detail'][$key]['quantity_actual_format'] = number_format($value['quantity_actual'],2).$unit;
                $outOrder['detail'][$key]['quantity_balance'] = $value['quantity_actual'] - $outOrder['detail'][$key]['quantity_done'];
                $outOrder['detail'][$key]['quantity_balance_format'] = number_format($outOrder['detail'][$key]['quantity_balance'], 2).$unit;
                if($outOrder['detail'][$key]['quantity_balance']>0){
                    $crossItmes[] = $outOrder['detail'][$key];
                }
            }
        }*/

        // print_r($crossItmes);die;

        $this->pageTitle="调货处理";
        $this->render("add",array(
            'data' => $data, 
            'crossDetail'=>$crossDetail,
            // 'outOrder'=>$outOrder,
            // 'crossItmes'=>$crossItmes
            )
        );
    }


    /**
     * @desc 根据projectId和goodsId获取借货信息
     */
    public function actionGetCrossInfo() {
        $goods_id = Mod::app()->request->getParam("goods_id");
        $project_id = Mod::app()->request->getParam("project_id");
        if(empty($goods_id) || empty($project_id))
            $this->returnError("参数有误！");

        $sql = "select temp.contract_code as contractCode,temp.name as partnerName,
                s.stock_in_id,d.stock_id,s.code as stockCode,g.name as goodsName from (
                select c.contract_id,c.contract_code,pa.name
                from t_contract c 
                left join t_partner pa on c.partner_id=pa.partner_id
                where c.type=".ConstantMap::BUY_TYPE." and c.project_id=".$project_id." and c.status>=".Contract::STATUS_BUSINESS_CHECKED.") as temp
                left join t_stock_in s on temp.contract_id=s.contract_id
                left join t_stock_in_detail d on s.stock_in_id=d.stock_in_id
                left join t_stock k on d.stock_id=k.stock_id
                left join t_goods g on d.goods_id=g.goods_id
                where d.goods_id=".$goods_id." and s.status=".StockIn::STATUS_PASS. ' and s.is_virtual = '.SplitEnum::IS_REALITY.
                " and k.quantity_balance>0";
                //left join t_contract_file f on c.contract_id=f.contract_id 
                //" and f.is_main=1".
                //" and f.type=".ConstantMap::ELECTRON_DOUBLE_SIGN_CONTRACT_MODULE." and f.status=".ContractFile::STATUS_CHECKING
                //and s.type=".ConstantMap::STOCK_NOTICE_TYPE_BY_WAREHOUSE."
        $data = Utility::query($sql);

        $this->returnSuccess($data);
    }

    /**
     * @desc 根据contractId、goodsId和入库单号获取还货明细
     */
    public function actionGetCrossDetail() {
        $goods_id   = Mod::app()->request->getParam("goods_id");
        $id_str     = Mod::app()->request->getParam("id_str");
        if(empty($goods_id) || empty($id_str))
            $this->returnError("参数有误！");

        $data =array();
        $sql = "select t.stock_in_id,t.code as stock_code,s.stock_id,
                c.contract_id,c.contract_code,c.project_id,s.store_id,
                g.goods_id,g.name as goods_name,s.quantity_balance,s.unit
                from t_stock s 
                left join t_stock_in t on s.stock_in_id=t.stock_in_id
                left join t_contract c on s.contract_id=c.contract_id
                left join t_goods g on s.goods_id=g.goods_id
                where FIND_IN_SET(s.stock_id, '".$id_str."') and s.goods_id=".$goods_id;
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
        $id             = Mod::app()->request->getParam("id");
        $contractId     = Mod::app()->request->getParam("contract_id");
        if(!Utility::checkQueryId($id) || !Utility::checkQueryId($contractId))
            $this->renderError("参数有误！", "/transfer/");

        $crossOrder     = CrossOrder::model()->with('contract', 'goods', 'project', 'contractGoods')->findByPk($id);
        $contract       = $crossOrder->contract;
        $goods          = $crossOrder->goods;
        $project        = $crossOrder->project;
        $contractGoods  = $crossOrder->contractGoods;
        $partner        = Partner::model()->findByPk($contract->partner_id);
        $buyContract    = Contract::model()->findByPk($contractId);//被借采购合同对象

        $map = Map::$v;
        $data['detail_id']      = $contractGoods->detail_id;
        $data['contract_id']    = $contract->contract_id;
        $data['contract_code']  = $contract->contract_code;
        $data['goods_id']       = $goods->goods_id;
        $data['goods_name']     = $goods->name;
        $data['project_id']     = $project->project_id;
        $data['project_code']   = $project->project_code;
        $data['partner_id']     = $partner->partner_id;
        $data['partner_name']   = $partner->name;
        $data['corporation_id']         = $contract->corporation_id;
        $data['relation_cross_id']      = $crossOrder->cross_id;
        $data['relation_cross_code']    = $crossOrder->cross_code;
        $data['cross_date']     = $crossOrder->cross_date;
        $data['reason']         = $crossOrder->remark;
        $data['buy_id']         = $buyContract->contract_id;  //被借采购合同编号
        $data['buy_code']       = $buyContract->contract_code; //被借采购合同编码
        $data['buy_project_id'] = $buyContract->project_id; //被借采购合同项目编号
        $data['unit_format']    = $map['goods_unit'][$contractGoods->unit_store]['name'];

        $orderDetail = CrossOrderService::getOrderDetailById($data['relation_cross_id'], $contractId);
        if(Utility::isEmpty($orderDetail)){
            $this->renderError("当前调货信息不存在！", "/transfer/");
        }
        // print_r($orderDetail);die;
        $crossDetail= array();
        $total_quantity = 0;
        $total_quantity_out = 0;
        foreach ($orderDetail as $key=>$value) {
            $total_quantity += $value['quantity']; 
            $total_quantity_out += $value['quantity_out']; 
            $crossDetail['detail'][$key] = $value;
        }

        if(!empty($crossDetail)){
            $crossDetail['total_quantity']      = $total_quantity;
            $crossDetail['total_quantity_out']  = $total_quantity_out;
            $crossDetail['unit']                = $contractGoods->unit_store;
        }

        $crossReturn = CrossOrderService::getCrossDetailById($data['relation_cross_id'], $contractId);
        // print_r($crossReturn);die;

        $goodsItems = $crossReturn['details'];
        
        unset($crossReturn['details']);

        $data['cross_id']   = $crossReturn['cross_id'];
        $data['cross_code'] = $crossReturn['cross_code'];
        $data['relation_cross_id'] = $crossReturn['relation_cross_id'];
        $data['order_index'] = $crossReturn['order_index'];
        $data['type'] = $crossReturn['type'];
        $data['quantity'] = $crossReturn['quantity'];
        $data['remark'] = $crossReturn['remark'];

        // print_r($targetArr);die;
        
        $this->pageTitle="操作调货";
        $this->render("add",array(
            'data' => $data, 
            'crossDetail'=> $crossDetail,
            'goodsItems'=>$goodsItems
            )
        );
    }

    public function actionDetail()
    {
        $id  = Mod::app()->request->getParam("id");
        $contract_id  = Mod::app()->request->getParam("contract_id");
        if(!Utility::checkQueryId($id) || !Utility::checkQueryId($contract_id))
            $this->renderError("参数有误！", "/transfer/");

        $sql = "select o.cross_id,o.cross_code,c1.contract_id as sell_contract_id,st.name as store_name,
                c1.contract_code as sell_contract_code,g.goods_id,g.name as goods_name,
                c2.contract_id as buy_contract_id,c2.contract_code as buy_contract_code,s.unit,
                cd.quantity,cd.quantity_out,o.cross_date,o.remark,i.code as stock_code,i.stock_in_id
                from t_cross_order o 
                left join t_cross_detail cd on o.cross_id=cd.cross_id
                left join t_contract c1 on o.contract_id=c1.contract_id
                left join t_contract c2 on cd.contract_id=c2.contract_id
                left join t_goods g on cd.goods_id=g.goods_id
                left join t_stock s on cd.stock_id=s.stock_id
                left join t_stock_in i on s.stock_in_id=i.stock_in_id
                left join t_storehouse st on cd.store_id=st.store_id
                where o.cross_id=".$id;

        $data = Utility::query($sql);

        $cross= array();
        if(Utility::isNotEmpty($data)){
            $cross['contract_id']    = $data[0]['sell_contract_id'];
            $cross['contract_code']  = $data[0]['sell_contract_code'];
            $cross['cross_date']     = $data[0]['cross_date'];
            $cross['remark']         = $data[0]['remark'];
            $cross['cross_code']     = $data[0]['cross_code'];
            $cross['goods_name']     = $data[0]['goods_name'];
        }else{
            $this->renderError("当前信息不存在！", "/transfer/");
        }

        $crossOrder = CrossOrderService::getReturnDetail($id, $contract_id);
        // print_r($crossOrder);die;
        /*if(Utility::isEmpty($crossOrder)){
            $this->renderError("当前信息不存在！", "/transfer/");
        }*/
        // print_r($data);die;

        $this->pageTitle="查看详情";
        $this->render("detail",array(
            "data"          => $data,
            "crossOrder"    => $crossOrder,
            'cross'         => $cross,
            )
        );
    }

    public function actionSave()
    {
        $params = Mod::app()->request->getParam('data');
        $goodsItems = $params['goodsItems'];
        // $doneItems  = $params['doneItems'];
        // print_r($params);die;
        unset($params['goodsItems']);
        // unset($params['doneItems']);
        // print_r($params);die;

        /*if(Utility::isEmpty($doneItems))
            $this->returnError("请选择调货处理列表！");*/

        if($params['type']==ConstantMap::ORDER_BACK_TYPE && Utility::isEmpty($goodsItems))
            $this->returnError("请添加还货明细！");
        
        $requiredParams = array('type', 'goods_id', 'contract_id', 'project_id', 'contract_code', 'relation_cross_id', 'relation_cross_code');
        $filterInjectParams = Utility::checkRequiredParams($params, $requiredParams);
        if(!$filterInjectParams['isValid'])
            $this->returnError(BusinessError::outputError(OilError::$REQUIRED_PARAMS_CHECK_ERROR));
        $params = $filterInjectParams['params'];

        $nowUserId  = Utility::getNowUserId();
        $nowTime    = new CDbExpression("now()");
        if(empty($params['cross_id'])){
            $obj = new CrossOrder();
            $order_index            = $params['order_index']+1;
            $obj->cross_code        = $params['relation_cross_code'].'_'.$order_index;
            $obj->order_index       = $order_index;
            $obj->status_time       = $nowTime;
            $obj->create_time       = $nowTime;
            $obj->create_user_id    = $nowUserId;
        }else{
            $obj = CrossOrder::model()->findByPk($params['cross_id']);
            if(empty($obj->cross_id))
                $this->returnError("当前信息不存在！");
        }

        if($params['type']==ConstantMap::ORDER_BACK_TYPE)
                $params['quantity'] = 0.0;

        unset($params['cross_id']);
        unset($params['order_index']);

        $params['contract_id']  = $params['buy_id'];
        $params['project_id']   = $params['buy_project_id'];

        $obj->setAttributes($params, false);

        if(empty($params['isSave'])){
            $obj->status = CrossOrder::STATUS_CHECKING;
            $obj->status_time = $nowTime;
        }else{
            $obj->status = CrossOrder::STATUS_SAVED;
        }
        
        $obj->update_time       = $nowTime;
        $obj->update_user_id    = $nowUserId;

        $crossContractDetail=CrossContractDetail::model()->with("cross")
            ->find("cross.cross_id=".$obj->relation_cross_id." and t.goods_id=".$obj->goods_id);

        $obj->detail_id=$crossContractDetail->detail_id;

        $logRemark = ActionLog::getEditRemark($obj->isNewRecord, "调货处理单");
        $trans = Utility::beginTransaction();
        try {

            $obj->save();

            // CrossOrderService::saveCrossDetail($doneItems, $obj->cross_id, $params['isSave'], $params['type']);

            if($params['type']==ConstantMap::ORDER_BACK_TYPE){
                CrossOrderService::saveCrossDetail($goodsItems, $obj->cross_id, $params['isSave'], $params['type']);
            }else{
                $sql    = "select detail_id from t_cross_detail where cross_id=" . $obj->cross_id;
                $data   = Utility::query($sql);
                $p      = array();
                if (Utility::isNotEmpty($data)) {
                    foreach ($data as $v) {
                        $p[$v["detail_id"]] = $v['detail_id'];
                    }
                    CrossDetail::model()->deleteAll('detail_id in(' . implode(',', $p) . ')');
                }
            }
            

            if(empty($params['isSave'])){
                FlowService::startFlowForCheck12($obj->cross_id);
            }

            TaskService::doneTask($obj->detail_id,Action::ACTION_CROSS_RETURN_CHECK_BACK);

            // TaskService::addTasks(Action::ACTION_11, $contract->contract_id, ActionService::getActionRoleIds(Action::ACTION_11), 0, $contract->corporation_id);
            
            $trans->commit();

            Utility::addActionLog(json_encode($obj->oldAttributes), $logRemark, "CrossOrder", $obj->cross_id);
            $this->returnSuccess($obj->relation_cross_id, $params['buy_id']);
            
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
            $this->renderError("参数错误！", "/transfer/");
        }

        $order = CrossOrder::model()->with("crossDetail")->findByPk($id);
        if(!$this->checkIsCanEdit($order->status))
            $this->returnError("当前状态下不可提交调货处理信息！");

        $trans = Utility::beginTransaction();
        try{
            $oldStatus = $order->status;
            $order->status = CrossOrder::STATUS_CHECKING;
            $order->status_time      = new CDbExpression("now()");
            $order->update_time      = new CDbExpression("now()");
            $order->update_user_id   = Utility::getNowUserId();
            $order->save();

            $crossDetail = $order->crossDetail;
            if(Utility::isNotEmpty($crossDetail)){
                foreach ($crossDetail as $detail) {
                    $r = StockService::freeze($detail->stock_id, $detail->quantity);
                    if(!$r){
                        throw new Exception("库存ID为".$detail->stock_id.",解冻库存失败！");
                    }
                }
            }

            FlowService::startFlowForCheck12($order->cross_id);
            TaskService::doneTask($order->detail_id,Action::ACTION_CROSS_RETURN_CHECK_BACK);
            $trans->commit();

            Utility::addActionLog(json_encode(array('oldStatus'=>$oldStatus)), "提交调货处理单", "CrossOrder", $order->cross_id);
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