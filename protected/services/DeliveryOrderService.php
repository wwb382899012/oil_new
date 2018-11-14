<?php

use ddd\Split\Domain\Model\SplitEnum;

/**
 * Desc: 发货单服务
 * User: susiehuang
 * Date: 2017/10/9 0009
 * Time: 10:03
 */
class DeliveryOrderService {
    private static $stockInCodeKey = 'stock.in.code';

    /**
     * @desc 生成入库单编码
     * @param int $batch_id
     * @return string
     */
    public static function generateStockInCode($batch_id) {
        $code = '';
        if (Utility::checkQueryId($batch_id)) {
            $stockNoticeModel = StockNotice::model()->findByPk($batch_id);
            if (!empty($stockNoticeModel->batch_id) && !empty($stockNoticeModel->code)) {
                $code .= $stockNoticeModel->code . '-' . IDService::getSerialNum(self::$stockInCodeKey . '.' . $batch_id);
            }
        }

        return $code;
    }

    /**
     * @desc 保存历史数据发货信息
     * @param array $params
     * @param array $history_params
     * @return string|bool
     */
    public static function saveHistoryStockOutInfo($params, $history_params) {
        $resourceParams = $params;
        $deliveryOrderDetail = $params['deliveryOrderDetail'];
        unset($params['deliveryOrderDetail']);

        /*$requiredParams = array('corporation_id', 'partner_id', 'delivery_date', 'type');
        if ($params['type'] == ConstantMap::STOCK_NOTICE_TYPE_DIRECT_TRANSFER) {
            array_push($requiredParams, 'stock_in_id');
        }
        if (!Utility::checkRequiredParamsNoFilterInject($params, $requiredParams)) {
            return __CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' params pass error:' . json_encode($params);
        }*/

        if (!empty($params['stock_in_id'])) {
            $stockInModel = StockIn::model()->findByPk($params['stock_in_id']);
            if (empty($stockInModel->stock_in_id)) {
                return __CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' stock in id not exist:' . $params['stock_in_id'];
            }
        }

        //发货明细参数校验
        /*$detailsCheckRes = DeliveryOrderDetailService::checkParamsValid($deliveryOrderDetail, $params);
        if ($detailsCheckRes !== true) {
            return __CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' delivery order detail check error:' . $detailsCheckRes;
        }*/

        if (!empty($params['order_id'])) {
            $deliveryOrderModel = DeliveryOrder::model()->findByPk($params['order_id']);
        }

        if (empty($deliveryOrderModel->order_id)) {
            $deliveryOrderModel = new DeliveryOrder();
        }

        $db = Mod::app()->db;
        $trans = $db->beginTransaction();
        try {
            unset($params["order_id"]);
            $deliveryOrderModel->setAttributes($params, false);
            $deliveryOrderModel->save();

            //保存发货单明细&配货明细
            DeliveryOrderDetailService::saveDetails($deliveryOrderDetail, $deliveryOrderModel);

            //添加出库信息&发货单结算相关信息
            StockOutService::addStockOutForHistoryData($deliveryOrderModel->order_id);

            //添加发货单结算明细
            DeliverySettlementService::saveDeliverySettlementForHistoryData($deliveryOrderModel->order_id, $history_params);

            $trans->commit();

            return true;
        } catch (Exception $e) {
            try {
                $trans->rollback();
            } catch (Exception $ee) {
                return __CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $ee->getMessage();
            }

            return __CLASS__ . '->' . __FUNCTION__ . ' in line ' . __LINE__ . ' trans execute error:' . $e->getMessage().' and params are:'.json_encode($resourceParams);
        }
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

    /**
     * @desc 检查发货单结算是否可修改
     * @param int $batchId
     * @return bool
     */
    public static function checkIsCanEdit($order_id) {
        if(Utility::checkQueryId($order_id)) {
            $settles = DeliverySettlementDetail::model()->findAll('order_id = :order_id', array('order_id' => $order_id));
            $deliveryOrder = DeliveryOrder::model()->findByPk($order_id);
            if($deliveryOrder->status == DeliveryOrder::STATUS_SETTLE_BACK){
                return true;
            }
            if (Utility::isNotEmpty($settles)) {
                foreach ($settles as $key => $row) {
                    if($row->status >= DeliverySettlementDetail::STATUS_SUBMIT) {
                        return false;
                    }
                }
                return true;
            }
        }
        
        return false;
    }
    /*
     * @desc 获取发货单下商品的配货信息
     * @param int $detail_id  t_delivery_order_detail表主键
     * @return total_out
     * */
    public static function getGoodsStockDelivery($order_id,$detail_id){
        $deliveryOrderModel=\DeliveryOrder::model()->findByPk($order_id);
        $details = $deliveryOrderModel->details;
        $stockDeliveryDetail=array();
        if(!empty($details)){
            foreach ($details as $m=>$n){
                if($n->detail_id==$detail_id){
                    if(!empty($n['stockDeliveryDetail'])){
                        foreach ($n['stockDeliveryDetail'] as $k=>$v){
                            $arr=array(
                                'stock_in_id'=>$v->stock->stock_in_id,
                                'code'=>$v->stock->stockIn->code,
                                'stock_delivery_quantity'=>$v->quantity,
                                'store_name'=>$v->store->name,
                                'remark'=>$v->remark
                            );

                            $arr['out_quantity']=\DeliveryOrderService::getGoodsOutQuantity($order_id, $v['stock_detail_id']);
                            $arr['no_out_quantity']=$arr['stock_delivery_quantity']-$arr['out_quantity'];
                            $stockDeliveryDetail[]=$arr;
                        }
                    }
                  
                }
            }
        }
        //var_dump($stockDeliveryDetail);
        /*$details=$deliveryOrderModel->details;
        $stock_delivery_quantity=0;
        $store_name="";
        if(!empty($details)){
            foreach ($details as $m=>$n){
                if($n->detail_id==$detail_id){
                    $stock_delivery_quantity = $n->stockDeliveryDetail[0]->quantity;
                    $store_name = $n->stockDeliveryDetail[0]->store['name'];
                }
                
            }
        }
        return array('stock_delivery_quantity'=>$stock_delivery_quantity,'store_name'=>$store_name); */
        return $stockDeliveryDetail;
    }
   
    /*
     * @desc 获取发货单下商品的总出库数量
     * @param int $detail_id  t_delivery_order_detail表主键
     * @return total_out
     * */
    public static function getGoodsOutQuantity($order_id,$stock_detail_id){
        $deliveryOrderModel=\DeliveryOrder::model()->findByPk($order_id);
        $orderQuantity=$deliveryOrderModel->stockOuts;
        $total_out=0;
        if(!empty($orderQuantity)){
            foreach ($orderQuantity as $e=>$f){

                if($f['status']==StockOutOrder::STATUS_SUBMITED || $f['status']==StockOutOrder::STATUS_SETTLED){
                    if(!empty($f->details)){
                        foreach ($f->details as $ee=>$ff){
                            if($ff['stock_detail_id']==$stock_detail_id)
                                $total_out+=$ff['quantity'];
                        }
                    }
                }
                
            }
        }

        return $total_out;

    }
    /**
     * 是否可以显示审核状态信息
     * @param $status
     * @return bool
     */
    public static function isCanShowAuditStatus($status){
        return $status != DeliveryOrder::STATUS_NEW;
    }

    /**
     * 是否显示审核状态备注
     * @param $status
     * @return bool
     */
    public static function isShowAuditRemark($status){
        return in_array($status,array(
            DeliveryOrder::STATUS_BACK,
            DeliveryOrder::STATUS_PASS,
            DeliveryOrder::STATUS_SETTLE_SUBMIT, // 提交发货单结算
            DeliveryOrder::STATUS_SETTLE_BACK, // 结算打回
            DeliveryOrder::STATUS_SETTLE_PASS, // 结算审核通过
        ));
    }

    /**
     * 是否显示状态信息
     * @param $status
     * @return bool
     */
    public static function isCanShowStatus($status){
        return $status == DeliveryOrder::STATUS_NEW;
    }

    /**
     * 判断是否可以修改
     * @param $status
     * @return bool
     */
    public static function isCanEdit($status){
        if ($status < DeliveryOrder::STATUS_SUBMIT) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * 判断当前出库单下是否可以添加出库单
     * @param $type
     * @param $status
     * @param $isSplit  是否平移生成
     * @return bool
     */
    public static function isCanAddStockOutOrder($type,$status,$isSplit){
        //经仓发货单才允许
        if($type != ConstantMap::STOCK_NOTICE_TYPE_BY_WAREHOUSE || SplitEnum::IS_VIRTUAL == $isSplit){
            return false;
        }

        return in_array($status,array(DeliveryOrder::STATUS_PASS,DeliveryOrder::STATUS_SETTLE_BACK,DeliveryOrder::STATUS_SETTLE_INVALIDITY));
    }

    /**
     * 出库单审核通过，更新发货单明细实际出库数量
     */
    public static function updateDeliveryOrderActualOutQuantity($detailId, $quantity)
    {
        if(empty($detailId))
            return ;
        
        DeliveryOrderDetail::model()->updateByPk($detailId, array("quantity_actual"=>new CDbExpression("quantity_actual+".$quantity)));
    }

}