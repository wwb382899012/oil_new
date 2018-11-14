<?php

use ddd\Split\Domain\Model\SplitEnum;

/**
 * Desc: 出库单
 */
class StockOutService {
    private static $stockOutCodeKey = 'stock.out.code';

    public static function getInstance(){
        return new self();
    }

    /**
     * @desc 检查是否可添加出库单
     * @param int $orderId
     * @return bool
     */
    public static function checkIsCanAdd($orderId) {
        if (Utility::checkQueryId($orderId)) {
            $deliveryDetail = DeliveryOrderDetail::model()->findAll('order_id = :orderId', array('orderId' => $orderId)); //发货明细
            if (Utility::isNotEmpty($deliveryDetail)) {
                foreach ($deliveryDetail as $row) {
                    $outDetail = StockOutDetail::model()->findAll('order_id = :orderId and detail_id = :detailId', array('orderId' => $orderId, 'detailId' => $row['detail_id']));
                    if (Utility::isEmpty($outDetail)) {
                        return true;
                    }
                }
            }
        }

        return false;
    }

    /**
     * @param $details
     * @param null $outDetails
     * @return array
     */
    public static function detailsFormat($details, $outDetails = null) {
        $stores = array();
        $storeGoods = array();
        $map = Map::$v;
        foreach ($details as $detail) {
            $unit_store=$detail->contractGoods['unit_store'];//计量单位id
            $unit_store= Map::$v['goods_unit'][$unit_store]['name'];//计量单位名称
            foreach ($detail['stockDeliveryDetail'] as $stockDeliveryDetail) {
                if(empty($stockDeliveryDetail['store']))
                {
                    $store=array("store_id"=>0,"name"=>"虚拟库");
                }
                else
                    $store=$stockDeliveryDetail['store'];
                $stores[$store['store_id']] = $store['name'];
                $goods = array();
                $goods['detail_id'] = $stockDeliveryDetail['detail_id'];
                $goods['stock_detail_id'] = $stockDeliveryDetail['stock_detail_id'];
                $goods['store_id'] = $store['store_id'];
                $goods['store_name'] = $store['name'];
                $goods['contract_id'] = $detail['contract']['contract_id'];
                $goods['contract_code'] = $detail['contract']['contract_code'];
                $goods['goods_name'] = $detail['goods']['name'];
                $goods['goods_id'] = $detail['goods']['goods_id'];
                $goods['stock_in_id'] = $stockDeliveryDetail['stock']['stockIn']['stock_in_id'];
                $goods['stock_in_code'] = $stockDeliveryDetail['stock']['stockIn']['code'];
                $goods['stock_id'] = $stockDeliveryDetail['stock_id'];
                $goods['cross_detail_id'] = $stockDeliveryDetail['cross_detail_id'];
                $goods['unit'] = $stockDeliveryDetail['stock']['unit'];
                $goods['quantity_str'] = $stockDeliveryDetail['quantity'] . $map['goods_unit'][$goods['unit']]['name'];
                $goods['quantity_stock'] = $stockDeliveryDetail['quantity'];
                $goods['out_id'] = 0;
                $goods['quantity'] = $stockDeliveryDetail['quantity'];
                $goods['unit_store']=$unit_store;

                if (!empty($outDetails)) {
                    $outDetail = $outDetails[$goods['stock_detail_id']];
                    if (!empty($outDetail)) {
                        //$goods['quantity_saved'] = $outDetail['quantity'];
                        $goods['quantity'] = $outDetail['quantity'];
                    } else {
                        $goods['quantity'] = 0;
                    }
                }


                /*if(!is_null($out_order_id)) {
                    $outOrderDetail = StockOutDetail::model()->find(array('condition'=>'out_order_id=:out_order_id and stock_detail_id=:stock_detail_id', 'select'=>'quantity', 'params'=>array('stock_detail_id'=>$goods['stock_detail_id'], 'out_order_id'=>$out_order_id)));
                    $goods['quantity_saved'] = $outOrderDetail['quantity'];

                } else {
                    $goods['quantity_saved'] = 0;

                }*/
                $storeGoods[] = $goods;
            }
        }

        return array($stores, $storeGoods);
    }

    // 生成审核通过的直调出库单
    static public function addDirectTransferDeliveryDetail($order_id) {
        $deliveryOrder = DeliveryOrder::model()->with('stockIn')->findByPk($order_id);
        $details = StockDeliveryDetail::model()->with('deliveryOrderDetail')->findAll(array('condition' => "t.order_id=:order_id", 'params' => array('order_id' => $deliveryOrder->order_id)));
        $stockOutOrder = new StockOutOrder();
        $stockOutOrder->out_order_id = IDService::getStoreOutOrderId();
        $stockOutOrder->order_id = $deliveryOrder->order_id;
        $stockOutOrder->out_date = $deliveryOrder->delivery_date;
        $stockOutOrder->status = StockOutOrder::STATUS_SUBMITED;
        $stockOutOrder->type = $deliveryOrder->type;
        $stockOutOrder->corporation_id = $deliveryOrder->corporation_id;
        $stockOutOrder->partner_id = $deliveryOrder->partner_id;
        $stockOutOrder->code = StockOutService::generateStockOutCode($order_id);
        $stockOutOrder->save();
        foreach ($details as $detail) {
            $stockOutDetail = new StockOutDetail();
            $stockOutDetail->order_id = $deliveryOrder->order_id;
            $stockOutDetail->out_order_id = $stockOutOrder->out_order_id;
            $stockOutDetail->detail_id = $detail->detail_id;
            $stockOutDetail->type = $stockOutOrder->type;
            $stockOutDetail->stock_id = $detail->stock_id;
            $stockOutDetail->store_id = $detail->store_id;
            $stockOutDetail->project_id = $detail->deliveryOrderDetail->project_id;
            $stockOutDetail->contract_id = $detail->deliveryOrderDetail->contract_id;
            $stockOutDetail->stock_detail_id = $detail->stock_detail_id;
            $stockOutDetail->quantity = $detail->quantity;
            $stockOutDetail->out_date = $deliveryOrder->delivery_date;
            $stockOutDetail->goods_id = $detail->goods_id;
            $stockOutDetail->quantity = $detail->quantity;
            $stockOutDetail->quantity_actual = $detail->quantity;
            $stockOutDetail->remark = $detail->remark;
            $stockOutDetail->save();
            StockOutService::outStore($detail->stock_id, $detail->quantity, 0, ConstantMap::DISTRIBUTED_NORMAL, $stockOutDetail->out_id);
        }
        return $stockOutOrder->out_order_id;
    }

    /**
     * 生成出库单号
     * @param $orderId
     * @param null $deliveryOrderModel
     * @return string
     */
    public static function generateStockOutCode($orderId, $deliveryOrderModel = null) {

        $sql = "select count(*) as n from " . StockOutOrder::model()->tableName() . " where order_id=" . $orderId;
        $data = Utility::query($sql);
        $n = 1;
        if (Utility::isNotEmpty($data)) {
            $n = $n + $data[0]["n"];
        }

        if (empty($deliveryOrderModel)) {
            $deliveryOrderModel = DeliveryOrder::model()->findByPk($orderId);
        }

        $code = $deliveryOrderModel->code . '-' . $n;

        return $code;
    }

    /**
     * 完成出库更新库存信息
     * @param $stockId
     * @param $quantity
     * @param int $crossDetailId
     * @param int $type 操作类型   1：正常发货  2：借货  3：还货
     * @param int $out_id
     * @throws Exception
     */
    public static function outStore($stockId, $quantity, $crossDetailId, $type, $out_id) {
        $res = StockService::unFreeze($stockId, $quantity);
        if (!$res) {
            throw new Exception("解除冻结库存失败");
        }
        $res = StockService::out($stockId, $quantity);
        if (!$res) {
            throw new Exception("出库失败");
        }


        if (!empty($crossDetailId)) {
            // 借货等出库减库存
            $res = CrossOrderService::unFreezeCross($crossDetailId, $quantity);
            if (!$res) {
                throw new Exception("解除借货冻结库存失败");
            }

            $res = CrossOrderService::outCross($crossDetailId, $quantity);
            if (!$res) {
                throw new Exception("借货出库失败");
            }
        }

        //添加库存操作日志
        $stock = Stock::model()->findByPk($stockId);
        if (empty($stock)) {
            BusinessException::throw_exception(OilError::$STOCK_NOT_EXIST, array('stock_id' => $stockId));
        }
        $stockArr = $stock->getAttributes('stock_id', 'goods_id', 'unit');
        $stockArr['type'] = StockLog::TYPE_OUT;
        $stockArr['method'] = $type;
        //$stockArr['to_contract_id'] = $stock->contract_id;
        $stockArr['quantity_balance'] = $stock->quantity_balance + $stock->quantity_frozen;
        $stockArr['quantity'] = $quantity;
        $stockArr['relation_id'] = $out_id;
        StockLog::addStockLog($stockArr);
    }

    /**
     * @desc 历史数据生成出库单
     * @param int $order_id
     * @return bool
     * @throws
     */
    static public function addStockOutForHistoryData($order_id) {
        $details = StockDeliveryDetail::model()->with('deliveryOrder', 'deliveryOrderDetail')->findAll(array('condition' => "t.order_id=:order_id", 'params' => array('order_id' => $order_id)));
        if (Utility::isNotEmpty($details)) {
            //出库单
            $stockOutOrder = StockOutOrder::model()->find('order_id=' . $order_id . ' and out_date=' . $details[0]->deliveryOrder->delivery_date . ' and type=' . $details[0]->deliveryOrder->type);
            if (empty($stockOutOrder)) {
                $stockOutOrder = new StockOutOrder();
                $stockOutOrder->order_id = $order_id;
                $stockOutOrder->out_date = $details[0]->deliveryOrder->delivery_date;
                $stockOutOrder->status = StockOutOrder::STATUS_SUBMITED;
                $stockOutOrder->status_time = Utility::getDateTime($stockOutOrder->out_date);
                $stockOutOrder->type = $details[0]->deliveryOrder->type;
                $stockOutOrder->code = StockOutService::generateStockOutCode($order_id);
                $stockOutOrder->remark = '历史数据系统导入';
                $stockOutOrder->create_user_id = - 1;
                $stockOutOrder->create_time = Utility::getDateTime();
                $stockOutOrder->update_user_id = - 1;
            }
            $stockOutOrder->update_time = Utility::getDateTime();
            $res = $stockOutOrder->save();
            if ($res === true) {
                foreach ($details as $detail) {
                    //根据配货明细生成出库单明细
                    $stockOutDetail = StockOutDetail::model()->find('out_order_id=' . $stockOutOrder->out_order_id . ' and stock_detail_id=' . $detail->stock_detail_id);
                    if (empty($stockOutDetail)) {
                        $stockOutDetail = new StockOutDetail();
                        $stockOutDetail->order_id = $order_id;
                        $stockOutDetail->out_order_id = $stockOutOrder->out_order_id;
                        $stockOutDetail->detail_id = $detail->detail_id;
                        $stockOutDetail->type = $stockOutOrder->type;
                        $stockOutDetail->stock_id = $detail->stock_id;
                        $stockOutDetail->store_id = $detail->store_id;
                        $stockOutDetail->project_id = $detail->project_id;
                        $stockOutDetail->contract_id = $detail->contract_id;
                        $stockOutDetail->stock_detail_id = $detail->stock_detail_id;
                        $stockOutDetail->quantity = $detail->quantity;
                        $stockOutDetail->quantity_actual = $detail->quantity;
                        $stockOutDetail->out_date = $detail->deliveryOrder->delivery_date;
                        $stockOutDetail->goods_id = $detail->goods_id;
                        $stockOutDetail->remark = '历史数据系统导入';
                        $stockOutDetail->create_user_id = - 1;
                        $stockOutDetail->create_time = Utility::getDateTime();
                        $stockOutDetail->update_user_id = - 1;
                    }
                    $stockOutDetail->update_time = Utility::getDateTime();
                    $res1 = $stockOutDetail->save();
                    if ($res1 === true) {
                        StockOutService::outStoreForHistoryData($detail->stock_id, $detail->quantity, $stockOutDetail->out_id);
                    } else {
                        throw new Exception('delivery order id:' . $order_id . ' save to stock out detail error, result is:' . $res1);
                    }
                }
            } else {
                throw new Exception('delivery order id:' . $order_id . ' save to stock out error, result is:' . $res);
            }
        } else {
            throw new Exception('the delivery order id:' . $order_id . ' not have stock delivery details.');
        }
    }

    /**
     * 历史数据完成出库更新库存信息
     * @param $stockId
     * @param $quantity
     * @param int $out_id
     * @throws Exception
     */
    public static function outStoreForHistoryData($stockId, $quantity, $out_id) {
        $res = StockService::out($stockId, $quantity);
        if (!$res) {
            throw new Exception("出库失败:stock_id:" . $stockId . ',quantity:' . $quantity . ',out_id:' . $out_id);
        }

        //添加库存操作日志
        $stock = Stock::model()->findByPk($stockId);
        if (empty($stock)) {
            BusinessException::throw_exception(OilError::$STOCK_NOT_EXIST, array('stock_id' => $stockId));
        }
        $stockArr = $stock->getAttributes('stock_id', 'goods_id', 'unit');
        $stockArr['type'] = StockLog::TYPE_OUT;
        $stockArr['method'] = 1;
        $stockArr['quantity_balance'] = $stock->quantity_balance + $stock->quantity_frozen;
        $stockArr['quantity'] = $quantity;
        $stockArr['relation_id'] = $out_id;
        StockLog::addStockLog($stockArr);
    }
    /**
     * @desc 获取出库单附件信息
     * @param $id
     * @param $type
     * @return array
     */
    public static function getAttachment($id, $type = '') {
        if (empty($id)) {
            return array();
        }
        if (!empty($type)) {
            $type = ' and type=' . $type;
        }

        $sql = "select * from t_delivery_attachment where base_id=" . $id . " and status=1" . $type . " order by type asc";
        $data = Utility::query($sql);

        $attachments = array();
        foreach ($data as $v) {
            $attachments[$v["type"]][] = $v;
        }

        return $attachments;
    }

    public function getCanInvalidStockOutOrder($id){
       return StockOutOrder::model()->findByPk($id,'status IN('.implode(',',
               array(StockOutOrder::STATUS_SAVED, StockOutOrder::STATUS_BACK ,StockOutOrder::STATUS_REVOCATION )).')');
    }

    public function getCanRevocationStockOutOrder($id){
        return StockOutOrder::model()->findByPk($id,'status=:status',
            array(':status'=>StockOutOrder::STATUS_SUBMIT));
    }

    /**
     * 撤销
     * @param $model
     * @return int
     */
    public static function revocationStockOutBill(StockOutOrder $model){
        $trans = Utility::beginTransaction();
        try{
            //解冻库存补丁
            $outOrder = StockOutOrder::model()->with("details")->findByPk($model->out_order_id);
            foreach($outOrder->details as $detail){
                StockOutService::unFreezeStockPatch($detail);
            }

            $result = StockOutOrder::model()->updateByPk($model->out_order_id,array(
                'status'=> StockOutOrder::STATUS_REVOCATION
            ),'status=:status',array(':status'=> StockOutOrder::STATUS_SUBMIT));
            if(!$result){
                throw new Exception("撤销出货单失败");
            }

            //撤销审核任务
            $result = FlowService::revocationFlow(FlowService::BUSINESS_STOCK_OUT_CHECK,$model->out_order_id);
            if(1 !== $result){
                throw new Exception($result);
            }
            $trans->commit();

            Utility::addActionLog(json_encode(array('oldStatus' => $model->status)), "撤销出库单审核", "StockOutOrder", $model->out_order_id);
            return true;
        }catch(Exception $e){
            try{
                $trans->rollback();
            }catch(Exception $ee){
            }
        }

        return false;
    }

    /**
     * 作废
     * @param $model
     * @return int
     */
    public static function invalidStockOutBill(StockOutOrder $model, $remark =''){

        $updateData = array('status'=> StockOutOrder::STATUS_INVALIDITY);
        if(!empty($remark)){
            $updateData['remark'] = $model->remark.'；作废理由：'.$remark;
        }

        $trans = Utility::beginTransaction();
        try{

            $result = StockOutOrder::model()->updateByPk($model->out_order_id,$updateData,
                'status IN('.implode(',',array(StockOutOrder::STATUS_SAVED, StockOutOrder::STATUS_BACK , StockOutOrder::STATUS_REVOCATION)) .')');

            if(!$result){
                throw new Exception("作废出货单失败");
            }

            //撤销审核驳回待修改任务
            TaskService::trashTask($model->out_order_id, Action::ACTION_STOCK_OUT_CHECK_BACK);
            if(1 !== $result){
                throw new Exception($result);
            }

            $trans->commit();

            Utility::addActionLog(json_encode(array('oldStatus' => $model->status)), "作废出库单", "StockOutOrder", $model->out_order_id);

            return true;
        }catch(Exception $e){
            
            try{
                $trans->rollback();
            }catch(Exception $ee){
            }

            throw $e;
        }


        return false;
    }

    /**
     * TODO: 后面可能重写或删除
     * 检查出库单明细
     * @deprecated
     * @author phpdraogn
     * @param DeliveryOrder $model
     * @param array $goodsDeteils
     * @throws Exception
     */
    public static function checkGoodsDetailsForAddStockOut(DeliveryOrder $model,array $goodsDeteils){
        if(empty($model->order_id) || empty($goodsDeteils)){
            throw new Exception("发货单信息有误");
        }

        $new_goods_out_stock_detail = array();
        foreach($goodsDeteils as & $val){
            $new_goods_out_stock_detail[$val['detail_id']] = $val['quantity'];
        }

        //发货通知单明细信息
        $goods_delivery_deteils = array();
        foreach($model->details as & $val){
            $goods_delivery_deteils[$val['detail_id']] = $val['quantity'];
        }

        //已经占用的出库单商品明细
        $sql = 'SELECT sod.detail_id,SUM(sod.quantity) AS quantity,g.`name` FROM `t_stock_out_order` AS soo '
            .' INNER JOIN t_stock_out_detail AS sod ON sod.out_order_id = soo.out_order_id '
            .' INNER JOIN t_goods AS g ON g.goods_id = sod.goods_id '
            .' WHERE soo.order_id = '.$model->order_id.' AND soo.status > '.StockOutOrder::STATUS_SAVED.' GROUP BY sod.detail_id;';
        $data = Utility::query($sql);
        $goods_name_array = array();
        $goods_out_stock_detail = array();
        foreach($data as & $val){
            $quantity = isset($new_goods_out_stock_detail[$val['detail_id']]) ? $new_goods_out_stock_detail[$val['detail_id']] : 0;
            $goods_out_stock_detail[$val['detail_id']] = $val['quantity'] + $quantity;
            $goods_name_array[$val['detail_id']] = $val['name'];
        }

        foreach($goods_delivery_deteils as $detail_id => & $quantity){
            if(isset($goods_out_stock_detail[$detail_id]) && $goods_out_stock_detail[$detail_id] > $quantity){
                throw new Exception("仓库出库明细中【".$goods_name_array[$detail_id]."】的总出库数量大于发货通知单发货数量");
            }
        }
    }

    /**
     * 解冻出库单超出的库存
     * @param $stockOutDetail
     */
    public static function unFreezeStockPatch(StockOutDetail $stockOutDetail){
        self::handleStockPatch($stockOutDetail,false);
    }

    /**
     * 冻结出库单溢出的库存
     * @param StockOutDetail $stockOutDetail
     */
    public static function freezeStockPatch(StockOutDetail $stockOutDetail){
        self::handleStockPatch($stockOutDetail,true);
    }

    private static function handleStockPatch(StockOutDetail $detail,$isFreeze){
        $details=StockOutDetail::model()->with("stockOutOrder","stockDeliveryDetail")
            ->findAll('t.stock_detail_id='.$detail->stock_detail_id. ' and stockOutOrder.status IN ('.implode(',',array(StockOutOrder::STATUS_SUBMIT,StockOutOrder::STATUS_SUBMITED)).')');
        if(Utility::isEmpty($details)){
            BusinessException::throw_exception(OilError::$STOCK_OUT_DETAIL_NOT_EXIST);
            return;
        }

        $deliveryDetail = StockDeliveryDetail::model()->findByPk($detail->stock_detail_id);
        $deliveryQuantity = $deliveryDetail->quantity;

        //出库总和
        $total = 0;
        foreach ($details as & $row){
            if($row->out_id == $detail->out_id){
                continue;
            }
            $total += $row["quantity"];
        }

        $quantity = 0;
        if($isFreeze){
            //阈值
            $threshold = $deliveryQuantity * 0.1;
            //溢出总和
            $overflowTotal = 0;
            //剩余可配的数量
            $residue_quantity = 0;
            //剩余的溢出数量
            $overflow_quantity = 0;

            $diff = $total - $deliveryQuantity;

            if($diff >= 0){//之前就有溢出
                $quantity = $detail->quantity;
                //溢出总和
                $overflowTotal = $detail->quantity + $diff;
            }else{
                $quantity =  $detail->quantity + $diff;
                //溢出总和
                $overflowTotal = $quantity;
                //剩余可配的数量
                $residue_quantity = $deliveryQuantity - $total;
            }

            //TODO: 暂时不做判断，后期优化
            //如果总溢出大于规定的阈值10%
            if($overflowTotal > $threshold && false){
                $goods_model = Goods::model()->findByPk($detail->goods_id);
                //剩余的溢出数量
                $overflow_quantity = $deliveryQuantity > $total ? $threshold : $threshold + $deliveryQuantity - $total;
                if($overflow_quantity > 0 || $residue_quantity > 0){
                    BusinessException::throw_exception(OilError::$OUT_QUANTITY_GT_ALLOW_PERCENTAGE,array('goods_name'=>$goods_model->name,'quantity'=> $residue_quantity,'overflow'=> $overflow_quantity));
                }
                BusinessException::throw_exception(OilError::$OUT_QUANTITY_NOT_ALLOW,array('goods_name'=>$goods_model->name));
            }
        }else{
            $diff = $total + $detail->quantity - $deliveryQuantity;
            //本次驳回的出库单明细的溢出的数量，小于出库数量，则解冻全部
            $quantity = ($diff < $detail->quantity) ? $diff : $detail->quantity;
        }

        if($quantity <= 0){
            return;
        }

        $result = $isFreeze ? StockService::freeze($detail->stock_id,$quantity) : StockService::unFreeze($detail->stock_id,$quantity);
        if (!$result) {
            if($isFreeze){
                $stockModel = Stock::model()->findByPk($detail->stock_id);
                BusinessException::throw_exception(OilError::$OUT_STOCK_FREEZE_ERROR, array('stock_id' => $detail->stock_id, 'quantity' => $detail->quantity,'residue_quantity'=>$stockModel->quantity_balance));
            }
            BusinessException::throw_exception(OilError::$STOCK_UNFREEZE_ERROR, array('stock_id' => $detail->stock_id, 'quantity' => $detail->quantity));
        }
    }

    /**
     * 判断是否可以修改
     * @param $status
     * @return bool
     */
    public static function isCanEdit($status) {
        return in_array($status,array(
            StockOutOrder::STATUS_SAVED, //出库单保存
            StockOutOrder::STATUS_BACK, //审核驳回
            StockOutOrder::STATUS_REVOCATION  //撤销
        ));
    }

    /**
     * 判断是否可以提交审核
     * @param $status
     * @return bool
     */
    public static function isCanSubmit($status){
        return in_array($status,array(
            StockOutOrder::STATUS_SAVED, //出库单保存
            StockOutOrder::STATUS_BACK, //审核驳回
            StockOutOrder::STATUS_REVOCATION, //审核撤销
        ));
    }

    /**
     * 是否能撤销出库单审批
     * @param $status
     * @return bool
     */
    public static function isCanRevocation($status){
        //提交审批之后才可以撤销
        return (StockOutOrder::STATUS_SUBMIT == $status);
    }

    /**
     * 是否能作废出库单
     * @param $status
     * @return bool
     */
    public static function isCanInvalid($status){
        //驳回、撤销之后才可以作废
        return (StockOutOrder::STATUS_SAVED == $status || StockOutOrder::STATUS_BACK == $status || StockOutOrder::STATUS_REVOCATION == $status);
    }

    /**
     * 是否显示状态信息
     * @param $status
     * @return bool
     */
    public static function isCanShowStatus($status){
        return in_array($status,array(
            StockOutOrder::STATUS_INVALIDITY, //作废
            //StockOutOrder::STATUS_SAVED, //保存
            StockOutOrder::STATUS_REVOCATION, //撤回
        ));
    }

    /**
     * 是否作废了
     * @param $status
     * @return bool
     */
    public static function isInvalid($status){
        return StockOutOrder::STATUS_INVALIDITY == $status;
    }

    /**
     * 是否可以显示审核状态信息
     * @param $status
     * @return bool
     */
    public static function isCanShowAuditStatus($status){
        return in_array($status,array(
            StockOutOrder::STATUS_SUBMIT,
            StockOutOrder::STATUS_BACK,
            StockOutOrder::STATUS_SUBMITED,
            StockOutOrder::STATUS_SETTLED
        ));
    }

    /**
     * 是否显示审核状态备注
     * @param $status
     * @return bool
     */
    public static function isShowAuditRemark($status){
        return in_array($status,array(
            StockOutOrder::STATUS_BACK,
            StockOutOrder::STATUS_SUBMIT,
            StockOutOrder::STATUS_SUBMITED,
            StockOutOrder::STATUS_SETTLED,
        ));
    }

    /**
     * 是否可以结算
     * @param $status
     * @return bool
     */
    public static function isCanSettlement($status){
        return in_array($status,array(
            StockOutOrder::STATUS_INVALIDITY,
            StockOutOrder::STATUS_SUBMITED,
        ));
    }

    public static function isVirtualBill($isSplit){
        return SplitEnum::IS_VIRTUAL == $isSplit;
    }

    /**
     * @desc 根据发货单id更新入库单结算状态
     * @param int $orderId
     * @return bool
     */
    public static function updateSettledStatusByDeliveryId($orderId) {
        if(Utility::checkQueryId($orderId) && $orderId > 0) {
            $stockOuts = StockOutOrder::model()->findAll('order_id=:orderId and status=:status', array("orderId" => $orderId, 'status'=>StockOutOrder::STATUS_SUBMITED));
            if (Utility::isNotEmpty($stockOuts)) {
                foreach ($stockOuts as $stockOut) {
                    if($stockOut->deliveryOrder->status == DeliveryOrder::STATUS_SETTLE_PASS) {
                        $rows = $stockOut::model()->updateByPk($stockOut->out_order_id,
                            array(
                                'status' => StockOutOrder::STATUS_SETTLED,
                                'update_time' => new CDbExpression('now()'),
                                'update_user_id' => Utility::getNowUserId()
                            )
                        );
                        if($rows != 1) {
                            return false;
                        }
                    }
                }
            }
            return true;
        }
    }
}