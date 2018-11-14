<?php
/**
 * Desc:结算相关数据修复服务
 * User:  vector
 * Date: 2018/4/16
 * Time: 17:46
 */


class SettlementRepairService
{
    /**
     * 修复提单结算相关表数据
     */
    public static function repairLadingSettlement()
    {
        $ladingSettlements       = array();
        $contractSettlements    = array();
        $stockDetail = StockBatchSettlement::model()->with('contract','stockBatch')->findAll();
        if(is_array($stockDetail) && !empty($stockDetail)){
            foreach ($stockDetail as $item) {
                $ladingSettlements[$item->batch_id][$item->goods_id] = $item;
                $ladingSettlements[$item->batch_id][$item->goods_id]['currency'] = $item->contract->currency;
                $ladingSettlements[$item->batch_id][$item->goods_id]['status'] = $item->stockBatch->status;
            }


            foreach ($ladingSettlements as $batchId=>$ladings){
                $ladingBill = StockNotice::model()->with('contract')->findByPk($batchId);

                if($ladingBill->contract->settle_type==1){
                    $ladingSettlement = LadingSettlement::model()->find('lading_id='.$batchId);
                    if(empty($ladingSettlement)){
                        $ladingSettlement = new LadingSettlement();
                        $ladingSettlement->settle_id = IDService::getLadingSettlementId();
                        $ladingSettlement->code = IDService::getLadingSettlementCode();
                        $ladingSettlement->lading_id = $batchId;
                        $ladingSettlement->amount_other = 0;
                        $ladingSettlement->remark = "后期数据修复";
                    }

                    $amount = 0;
                    $status     = 0;
                    foreach ($ladings as $lading){
                        $amount += $lading->amount_cny;
                        $ladingSettlement->project_id = $lading->project_id;
                        $ladingSettlement->contract_id = $lading->contract_id;

                        $ladingSettlement->currency = $lading->currency;
                        $status = $lading->status;
                    }

                    switch ($status){
                        case 15:
                            $status = -1;
                            break;
                        case 20:
                            $status = 10;
                            break;
                        case 30:
                            $status = 20;
                            break;
                        default:
                            $status = 1;
                    }

                    $ladingSettlement->status = $status;
                    $ladingSettlement->amount = $amount;
                    $ladingSettlement->amount_goods = $amount;

                    $res = $ladingSettlement->save();
                    if(!$res)
                        Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' batch_id:' . $batchId . ', update error:' . $res . '', CLogger::LEVEL_ERROR);

                    $contractSettlements[$ladingSettlement->contract_id]['status'] = $status;
                    $contractSettlements[$ladingSettlement->contract_id]['amount_goods'] += $ladingSettlement->amount_goods;
                    $contractSettlements[$ladingSettlement->contract_id]['currency'] = $ladingSettlement->currency;
                    $contractSettlements[$ladingSettlement->contract_id]['project_id'] = $ladingSettlement->project_id;
                }
            }

            if(!empty($contractSettlements)){
                foreach ($contractSettlements as $contractId=>$settlement){
                    $contractSettlement = ContractSettlement::model()->find('contract_id='.$contractId);
                    if(empty($contractSettlement)){
                        $contractSettlement = new ContractSettlement();
                        $contractSettlement->settle_id = IDService::getContractSettlementId();
                        $contractSettlement->code = IDService::getContractSettlementCode();
                        $contractSettlement->project_id = $settlement['project_id'];
                        $contractSettlement->contract_id = $contractId;
                        $contractSettlement->currency = $settlement['currency'];
                        $contractSettlement->amount_other = 0;
                    }

                    if($settlement['status']==20){
                        $contractSettlement->amount_goods = $settlement['amount_goods'];
                        $contractSettlement->amount = $settlement['amount_goods'];
                    }


                    $res = $contractSettlement->save();
                    if(!$res)
                        Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' contract_id:' . $contractId . ', update error:' . $res . '', CLogger::LEVEL_ERROR);
                }
            }
        }
    }


    /**
     * 修复发货单结算相关表数据
     */
    public static function repairDeliverySettlement()
    {
        $deliverySettlements    = array();
        $contractSettlements   = array();
        $deliveryDetail = DeliverySettlementDetail::model()->with('contract','deliveryOrder')->findAll();
        if(is_array($deliveryDetail) && !empty($deliveryDetail)){
            foreach ($deliveryDetail as $item) {
                $deliverySettlements[$item->order_id][$item->goods_id] = $item;
                $deliverySettlements[$item->order_id][$item->goods_id]['currency'] = $item->contract->currency;
                $deliverySettlements[$item->order_id][$item->goods_id]['status'] = $item->deliveryOrder->status;
            }

            foreach ($deliverySettlements as $orderId=>$deliverys){
                $deliveryOrder = DeliveryOrder::model()->with('contract')->findByPk($orderId);
                if($deliveryOrder->contract->settle_type==3){
                    $deliverySettlement = DeliverySettlement::model()->find('order_id='.$orderId);
                    if(empty($deliverySettlement)){
                        $deliverySettlement = new DeliverySettlement();
                        $deliverySettlement->settle_id = IDService::getDeliverySettlementId();
                        $deliverySettlement->code = IDService::getDeliverySettlementCode();
                        $deliverySettlement->order_id = $orderId;
                        $deliverySettlement->amount_other = 0;
                        $deliverySettlement->remark = "后期数据修复";
                    }
                    $amount = 0;
                    $status = 0;
                    foreach ($deliverys as $delivery){
                        $amount += $delivery->amount_cny;
                        $deliverySettlement->project_id = $delivery->project_id;
                        $deliverySettlement->contract_id = $delivery->contract_id;

                        $deliverySettlement->currency = $delivery->currency;
                        $status = $delivery->status;
                    }

                    switch ($status){
                        case 40:
                            $status = -1;
                            break;
                        case 30:
                            $status = 10;
                            break;
                        case 50:
                            $status = 20;
                            break;
                        default:
                            $status = 1;
                    }
                    if(!empty($deliveryOrder->settle_date))
                        $deliverySettlement->settle_date = $deliveryOrder->settle_date;
                    $deliverySettlement->status = $status;
                    $deliverySettlement->amount = $amount;
                    $deliverySettlement->amount_goods = $amount;


                    $res = $deliverySettlement->save();
                    if(!$res)
                        Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' order_id:' . $orderId . ', update error:' . $res . '', CLogger::LEVEL_ERROR);

                    $contractSettlements[$deliverySettlement->contract_id]['status'] = $status;
                    $contractSettlements[$deliverySettlement->contract_id]['amount_goods'] += $deliverySettlement->amount_goods;
                    $contractSettlements[$deliverySettlement->contract_id]['currency'] = $deliverySettlement->currency;
                    $contractSettlements[$deliverySettlement->contract_id]['project_id'] = $deliverySettlement->project_id;
                }
            }

            if(!empty($contractSettlements)){
                foreach ($contractSettlements as $contractId=>$settlement){
                    $contractSettlement = ContractSettlement::model()->find('contract_id='.$contractId);
                    if(empty($contractSettlement)){
                        $contractSettlement = new ContractSettlement();
                        $contractSettlement->settle_id = IDService::getContractSettlementId();
                        $contractSettlement->code = IDService::getContractSettlementCode();
                        $contractSettlement->project_id = $settlement['project_id'];
                        $contractSettlement->contract_id = $contractId;
                        $contractSettlement->currency = $settlement['currency'];
                        $contractSettlement->amount_other = 0;
                    }

                    if($settlement['status']==20){
                        $contractSettlement->amount_goods = $settlement['amount_goods'];
                        $contractSettlement->amount = $settlement['amount_goods'];
                    }

                    $res = $contractSettlement->save();
                    if(!$res)
                        Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' contract_id:' . $contractId . ', update error:' . $res . '', CLogger::LEVEL_ERROR);
                }
            }
        }
    }


    /**
     * 修复提单及货款结算明细相关表数据
     */
    public  static function repairLadingGoodsSettlement()
    {
        $ladingSettlements = LadingSettlement::model()->findAll();
        if(is_array($ladingSettlements) && !empty($ladingSettlements)){
            foreach ($ladingSettlements as $ladingSettlement){
                $ladingDetail = StockBatchSettlement::model()->findAll('batch_id='.$ladingSettlement->lading_id);
                if(!empty($ladingDetail)){
                    foreach ($ladingDetail as $lading){
                        $lading->settle_id = $ladingSettlement->settle_id;
                        $noticeDetail = StockNoticeDetail::model()->with('sub')->find("batch_id=".$lading->batch_id." and goods_id=".$lading->goods_id);
                        $lading->quantity_bill = $noticeDetail->quantity_actual;
                        $lading->quantity_actual_sub = $noticeDetail->sub->quantity_actual;
                        $res = $lading->save();
                        if(!$res)
                            Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' item_id:' . $ladingDetail->item_id . ',DeliveryOrderDetail update error:' . $res . '', CLogger::LEVEL_ERROR);

                        $settlementGood = ContractSettlementGoods::model()->with('settleSub')->findByPk($lading->item_id);

                        if(empty($settlementGood)){
                            $settlementGood = new ContractSettlementGoods();
                            $settlementGood->item_id = $lading->item_id;
                            $settlementGood->project_id = $lading->project_id;
                            $settlementGood->contract_id = $lading->contract_id;
                            $settlementGood->relation_id = $lading->batch_id;
                            $settlementGood->goods_id = $lading->goods_id;
                            $settlementGood->unit_rate = $lading->unit_rate;
                            $settlementGood->currency = $lading->currency;
                            $settlementGood->unit_settle = $lading->unit_settle;
                            $settlementGood->unit_sub = $lading->sub->unit;
                            $settlementGood->quantity_actual_sub = $lading->sub->quantity_actual;
                            $settlementGood->quantity_sub = $lading->sub->quantity;
                            $settlementGood->quantity_loss_sub = $lading->sub->quantity_loss;
                        }

                        $settlementGood->settle_id = $ladingSettlement->settle_id;
                        $settlementGood->price = $lading->price;
                        $settlementGood->amount = $lading->amount;
                        $settlementGood->exchange_rate = $lading->exchange_rate;
                        $settlementGood->price_cny = $lading->price_cny;
                        $settlementGood->amount_cny = $lading->amount_cny;
                        $settlementGood->unit = $lading->unit;
                        $settlementGood->quantity_bill = $lading->quantity_bill;
                        $settlementGood->quantity = $lading->quantity;
                        $settlementGood->quantity_loss = $lading->quantity_loss;

                        $res = $settlementGood->save();
                        if(!$res)
                            Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' item_id:' . $settlementGood->item_id . ',ContractSettlementGoods update error:' . $res . '', CLogger::LEVEL_ERROR);
                    }
                }
            }
        }
    }

    /**
     * 修复发货单及货款结算明细相关表数据
     */
    public  static function repairDeliveryGoodsSettlement()
    {
        $deliverySettlements = DeliverySettlement::model()->findAll();
        if(is_array($deliverySettlements) && !empty($deliverySettlements)){
            foreach ($deliverySettlements as $deliverySettlement){
                $settlementDetail = DeliverySettlementDetail::model()->findAll('order_id='.$deliverySettlement->order_id);
                if(!empty($settlementDetail)){
                    foreach ($settlementDetail as $order){
                        $order->settle_id = $deliverySettlement->settle_id;
                        $orderDetail = DeliveryOrderDetail::model()->find("order_id=".$order->order_id." and goods_id=".$order->goods_id);
                        $order->quantity = $orderDetail->quantity_actual;
                        $res = $order->save();
                        if(!$res)
                            Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' item_id:' . $settlementDetail->item_id . ',DeliveryOrderDetail update error:' . $res . '', CLogger::LEVEL_ERROR);

                        $settlementGood = ContractSettlementGoods::model()->findByPk($order->item_id);
                        if(empty($settlementGood)){
                            $settlementGood = new ContractSettlementGoods();
                            $settlementGood->item_id = $order->item_id;
                            $settlementGood->project_id = $order->project_id;
                            $settlementGood->contract_id = $order->contract_id;
                            $settlementGood->relation_id = $order->order_id;
                            $settlementGood->goods_id = $order->goods_id;
                            $settlementGood->unit_rate = $order->unit_rate;
                            $settlementGood->currency = $order->currency;
                        }

                        $settlementGood->settle_id = $deliverySettlement->settle_id;
                        $settlementGood->price = $order->price;
                        $settlementGood->amount = $order->amount;
                        $settlementGood->exchange_rate = $order->exchange_rate;
                        $settlementGood->price_cny = $order->price_cny;
                        $settlementGood->amount_cny = $order->amount_cny;
                        $settlementGood->unit = $order->unit;
                        $settlementGood->quantity_bill = $order->quantity;
                        $settlementGood->quantity = $order->quantity_settle;
                        $settlementGood->quantity_loss = $order->quantity_loss;


                        $res = $settlementGood->save();
                        if(!$res)
                            Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' item_id:' . $settlementGood->item_id . ',ContractSettlementGoods update error:' . $res . '', CLogger::LEVEL_ERROR);
                    }
                }
            }
        }
    }

    /**
     * 修复入库通知单结算附件表
     */
    public static function repairLadingSettlementAttachment(){
        $attachments = StockBatchSettlementAttachment::model()->findAll("status=1");
        if(!empty($attachments)){
            foreach ($attachments as $attachment) {
                $ladingSettlements = LadingSettlement::model()->with('contractSettlementGoods')->find("t.lading_id=".$attachment->base_id);
                if(!empty($ladingSettlements)){
                    foreach ($ladingSettlements->contractSettlementGoods as $contractSettlementGood){
                        try{
                            $attachment->updateAll(array('base_id'=>$contractSettlementGood->item_id,'update_time'=>new CDbExpression('now()')), 'base_id='.$contractSettlementGood->relation_id);
                        }catch (Exception $ee){
                            Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' base_id:' . $attachment->base_id . ',StockBatchSettlementAttachment update error:' . $ee->getMessage() . '', CLogger::LEVEL_ERROR);
                        }
                        break;
                    }
                }
            }
        }
    }

    /**
     * 修复发货单结算附件表
     */
    public static function repairDeliverySettlementAttachment(){
        $attachments = DeliverySettlementAttachment::model()->findAll("status=1 and type=3");
        if(!empty($attachments)){
            foreach ($attachments as $attachment) {
                $deliverySettlements = DeliverySettlement::model()->with('contractSettlementGoods')->find("t.order_id=".$attachment->base_id);
                if(!empty($deliverySettlements)){
                    foreach ($deliverySettlements->contractSettlementGoods as $contractSettlementGood){
                        try{
                            $attachment->updateAll(array('base_id'=>$contractSettlementGood->item_id,'update_time'=>new CDbExpression('now()')), 'base_id='.$contractSettlementGood->relation_id);
                        }catch (Exception $ee){
                            Mod::log(__CLASS__ . '::' . __FUNCTION__ . ' in line ' . __LINE__ . ' base_id:' . $attachment->base_id . ',StockBatchSettlementAttachment update error:' . $ee->getMessage() . '', CLogger::LEVEL_ERROR);
                        }
                        break;
                    }
                }
            }
        }
    }

    /**
     * 修复发货单实际出库数量
     */
    public static function repairDeliveryActualOutQuantity(){
        $stockOuts = StockOutOrder::model()->with('details')->findAll("t.status>=".StockOutOrder::STATUS_SUBMITED);
        if(is_array($stockOuts) && !empty($stockOuts)){
            foreach ($stockOuts as $stockOut) {
                $details = $stockOut->details;
                if(is_array($details) && !empty($details)){
                    foreach ($details as $detail) {
                        DeliveryOrderDetail::model()->updateByPk($detail->detail_id, array("quantity_actual"=>new CDbExpression("quantity_actual+".$detail->quantity)));
                    }
                }
            }
        }
    } 
}