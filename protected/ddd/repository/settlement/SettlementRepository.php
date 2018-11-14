<?php
/**
 * Created by vector.
 * DateTime: 2018/5/11 15:19
 * Describe：合同结算单仓储
 */

namespace ddd\repository\settlement;


use ddd\domain\entity\settlement\AdjustmentItem;
use ddd\domain\entity\settlement\BillSettlementItem;
use ddd\domain\entity\settlement\GoodsExpenseItem;
use ddd\domain\entity\settlement\GoodsSettlement;
use ddd\domain\entity\settlement\GoodsSettlementItem;
use ddd\domain\entity\settlement\OtherExpenseItem;
use ddd\domain\entity\settlement\OtherSettlement;
use ddd\domain\entity\settlement\SettlementMode;
use ddd\domain\entity\settlement\TaxItem;
use ddd\domain\entity\value\AdjustMode;
use ddd\domain\entity\value\Currency;
use ddd\domain\entity\value\Expense;
use ddd\domain\entity\value\OtherFee;
use ddd\domain\entity\value\Quantity;
use ddd\domain\entity\value\Tax;
use ddd\infrastructure\error\ZException;
use ddd\infrastructure\Utility;
use ddd\infrastructure\error\ZModelDeleteFalseException;
use ddd\infrastructure\error\ZModelSaveFalseException;
use ddd\repository\EntityRepository;
use ddd\repository\contract\TradeGoodsRepository;
use ddd\repository\stock\DeliveryOrderRepository;
use ddd\repository\stock\LadingBillRepository;

abstract class SettlementRepository extends EntityRepository
{
    /**
     * 添加货款实体对象
     * @param $model 数据库对象
     * *@param $entity 业务实体对象
     * @param $type 结算类型
     * @return Entity
     * @throws \Exception
     */
    protected function addGoodsEntity($model, $entity, $type)
    {
        $billArr = array();
        $billQuantity = array();
        if(is_array($model->contractSettlementGoods) && !empty($model->contractSettlementGoods))
        {
            if($type == SettlementMode::BUY_CONTRACT_MODE_SETTLEMENT){
                $billArr = $this->getContractOfLadingBillInQuantity($entity->contract_id);
                $billQuantity = $this->getContractOfBillQuantity($billArr);
            }else if($type == SettlementMode::SALE_CONTRACT_MODE_SETTLEMENT){
                $billArr = $this->getContractOfDeliveryOrderOutQuantity($entity->contract_id);
                $billQuantity = $this->getContractOfBillQuantity($billArr);
            }else if($type == SettlementMode::LADING_BILL_MODE_SETTLEMENT){
                $billQuantity = $this->getLadingOfBillQuantity($model->lading_id);
            }else if($type == SettlementMode::DELIVERY_ORDER_MODE_SETTLEMENT){
                $billQuantity = $this->getDeliveryOfBillQuantity($model->order_id);
            }

            foreach ($model->contractSettlementGoods as $p)
            {
                $bill_quantity     = $billQuantity[$p->goods_id]['bill_quantity'];
                $bill_quantity_sub = $billQuantity[$p->goods_id]['bill_quantity_sub'];

                $item = GoodsSettlement::create($p->goods_id);
                $item->item_id         = $p->item_id;
                if($type == SettlementMode::LADING_BILL_MODE_SETTLEMENT){
                    $item->relation_id = $model->lading_id;
                }else if($type == SettlementMode::DELIVERY_ORDER_MODE_SETTLEMENT){
                    $item->relation_id = $model->order_id;
                }
                $item->bill_quantity   = new Quantity($bill_quantity, $p->unit);
                $item->settle_quantity = new Quantity($p->quantity, $p->unit);
                $item->loss_quantity   = new Quantity(($bill_quantity - $p->quantity), $p->unit);
                if(!empty($p->settleSub)){
                    $item->bill_quantity_sub   = new Quantity($bill_quantity_sub,$p->settleSub->unit);
                    $item->settle_quantity_sub = new Quantity($p->settleSub->quantity, $p->settleSub->unit);
                    $item->loss_quantity_sub   = new Quantity(($bill_quantity_sub - $p->settleSub->quantity), $p->settleSub->unit);
                }

                $item->exchange_rate     = $p->exchange_rate;
                $item->settle_price      = $p->price;
                $item->settle_amount     = $p->amount;
                $item->settle_price_cny  = $p->price_cny;
                $item->settle_amount_cny = $p->amount_cny;
                $item->remark            = $p->remark;

                //添加入库通知单和发货单明细
                if($type == SettlementMode::BUY_CONTRACT_MODE_SETTLEMENT || $type == SettlementMode::SALE_CONTRACT_MODE_SETTLEMENT){
                    $this->addContractOfBillObject($p, $item, $type, $billArr);
                }else{
                    $this->addBillObject($p, $item, $type, $billQuantity);
                }

                //添加结算明细
                $this->addSettlementDetail($p, $item);
                
                //添加附件
                if($type == SettlementMode::BUY_CONTRACT_MODE_SETTLEMENT || $type == SettlementMode::SALE_CONTRACT_MODE_SETTLEMENT){
                    $this->addContractOfAttachment($p, $item);
                }else{
                    $this->addBillOfAttachment($p, $item, $type);
                }

                $entity->addGoodsSettlement($item);
            }
        }

        return $entity;
    }

    /**
     * 添加非货款实体对象
     * @param $model 数据库对象
     * @param $entity 业务实体对象
     * @return Entity
     * @throws \Exception
     */
    protected function addOtherEntity($model, $entity)
    {

        if(is_array($model->contractSettlementSubjectDetail) && !empty($model->contractSettlementSubjectDetail)){
            foreach ($model->contractSettlementSubjectDetail as $d) {
                $detail = OtherSettlement::create();
                $detail->detail_id     = $d->detail_id;
                $detail->fee           = OtherFee::getOtherFee($d->subject_id);
                $detail->currency      = Currency::getCurrency($d->currency);
                $detail->amount        = $d->amount;
                $detail->exchange_rate = $d->exchange_rate;
                $detail->amount_cny    = $d->amount_cny;
                $detail->remark        = $d->remark;

                if (is_array($d->otherAttachments) && !empty($d->otherAttachments))
                {
                    foreach ($d->otherAttachments as $file)
                    {
                        if($file->type==101)
                            $detail->addReceiptAttachment($this->getAttachmentEntity($file));
                    }
                }

                $entity->addOtherSettlement($detail);
            }
        }

        return $entity;
    }


    /**
     * 获取合并结算时，合同下每个入库通知单对应入库数量
     * @param $contractId
     * @return array
     */
    private function getContractOfLadingBillInQuantity($contractId)
    {
        $ladingArr = array();
        if(empty($contractId))
            return $ladingArr;

        $ladingBills = LadingBillRepository::repository()->findAllByContractId($contractId);

        if(!empty($ladingBills) && count($ladingBills)>0){
            foreach ($ladingBills as $ladingBill) {
                if(!empty($ladingBill->items)){
                    foreach ($ladingBill->items as $item){
                        $ladingArr[$item->goods_id][$ladingBill->id]['bill_quantity'] = $item->in_quantity;
                        $ladingArr[$item->goods_id][$ladingBill->id]['bill_quantity_sub'] = $item->in_quantity_sub;
                    }
                }
            }
        }

        return $ladingArr;
    }


    /**
     * 获取合并结算时，合同下每个发货单对应出库数量
     * @param $contractId
     * @return array
     */
    private function getContractOfDeliveryOrderOutQuantity($contractId)
    {
        $deliveryArr = array();
        if(empty($contractId))
            return $deliveryArr;

        $deliveryOrders = DeliveryOrderRepository::repository()->findAllByContractId($contractId);

        if(!empty($deliveryOrders) && count($deliveryOrders)>0){
            foreach ($deliveryOrders as $deliveryOrder) {
                if(!empty($deliveryOrder->items)){
                    foreach ($deliveryOrder->items as $item){
                        $deliveryArr[$item->goods_id][$deliveryOrder->order_id]['bill_quantity'] = $item->out_quantity;
                    }
                }
            }
        }

        return $deliveryArr;
    }

    /**
     * 获取合同入库或出库合计数量
     * @param
     * @param  $billArr
     * @return array
     */
    private function getContractOfBillQuantity($billArr)
    {
        $contractArr = array();
        if(empty($billArr))
            return $contractArr;

        foreach ($billArr as $key=>$bill) {
            foreach ($bill as $k=>$v) {
                $quantity       = !empty($v['bill_quantity']['quantity']) ? $v['bill_quantity']['quantity']: 0;
                $quantity_sub   = !empty($v['bill_quantity']['quantity_sub']) ? $v['bill_quantity']['quantity_sub'] : 0;

                $contractArr[$key]['bill_quantity']     += $quantity;
                $contractArr[$key]['bill_quantity_sub'] += $quantity_sub;
            }
        }

        return $contractArr;
    }


    /**
     * 获取提单下每个商品实际入库单数量
     * @param  $batchId
     * @return array
     */
    private function getLadingOfBillQuantity($batchId)
    {
        $qArr = array();
        if(empty($batchId))
            return $qArr;

        $ladingBill =  LadingBillRepository::repository()->findByPk($batchId);
        if(is_array($ladingBill->items) && !empty($ladingBill->items)){
            foreach ($ladingBill->items as $goods_id=>$item){
                $in_quantity = !empty($item->in_quantity->quantity) ? $item->in_quantity->quantity : 0;
                $in_quantity_sub = !empty($item->in_quantity_sub->quantity) ? $item->in_quantity_sub->quantity : 0;

                $qArr[$goods_id]['bill_quantity'] = $in_quantity;
                $qArr[$goods_id]['bill_quantity_sub'] = $in_quantity_sub;
            }
        }

        return $qArr;
    }

    /**
     * 获取发货单下每个商品实际出库单数量
     * @param  $orderId
     * @return array
     */
    private function getDeliveryOfBillQuantity($orderId)
    {
        $qArr = array();
        if(empty($orderId))
            return $qArr;

        $deliveryOrder = DeliveryOrderRepository::repository()->findByPk($orderId);
        if(is_array($deliveryOrder->items) && !empty($deliveryOrder->items)){
            foreach ($deliveryOrder->items as $goods_id=>$item){
                $out_quantity = !empty($item->out_quantity->quantity) ? $item->out_quantity->quantity : 0;
                $qArr[$goods_id]['bill_quantity'] = $out_quantity ;
            }
        }

        return $qArr;
    }

    /**
     * 合同合并结算时，添加入库通知单或发货单明细项对象
     * @param 
     * @return Entity
     */
    private function addContractOfBillObject($model,$entity,$type,$billArr)
    {
        $bills = array();
        if($type == SettlementMode::BUY_CONTRACT_MODE_SETTLEMENT){
            $bills = $model->ladings;
        }else if($type == SettlementMode::SALE_CONTRACT_MODE_SETTLEMENT){
            $bills = $model->orders;
        }

        $idArr = array();
        if(is_array($bills) && !empty($bills)){
            foreach ($bills as $bill) {
                $billId = 0;
                if($type == SettlementMode::BUY_CONTRACT_MODE_SETTLEMENT){
                    $billId = $bill->batch_id;
                }else if($type == SettlementMode::SALE_CONTRACT_MODE_SETTLEMENT){
                    $billId = $bill->order_id;
                }

                $idArr[] = $billId;

                $bill_quantity     = $billArr[$model->goods_id][$billId]['bill_quantity'];
                $bill_quantity_sub = $billArr[$model->goods_id][$billId]['bill_quantity_sub'];

                $billItem = BillSettlementItem::create($model->goods_id);
                $billItem->item_id = $bill->item_id;
                $billItem->bill_id = $billId;
                $billItem->bill_quantity   = new Quantity($bill_quantity->quantity, $bill->unit);
                $billItem->settle_quantity = new Quantity($bill->quantity, $bill->unit);
                $billItem->loss_quantity   = new Quantity(($bill_quantity->quantity - $bill->quantity), $bill->unit);
                if(!empty($bill->sub->unit) && $bill->unit != $bill->sub->unit){
                    $billItem->bill_quantity_sub   = new Quantity($bill_quantity_sub->quantity,$bill->sub->unit);
                    $billItem->settle_quantity_sub = new Quantity($bill->sub->quantity, $bill->sub->unit);
                    $billItem->loss_quantity_sub   = new Quantity(($bill_quantity_sub->quantity - $bill->sub->quantity), $bill->sub->unit);
                }
                $billItem->exchange_rate     = $bill->exchange_rate;
                $billItem->settle_price      = $bill->price;
                $billItem->settle_amount     = $bill->amount;
                $billItem->settle_price_cny  = $bill->price_cny;
                $billItem->settle_amount_cny = $bill->amount_cny;
                $billItem->remark            = $bill->remark;

                $entity->addBillSettlementItem($billItem);
            }
        }

        if(!empty($billArr) && !empty($billArr[$model->goods_id])){
            foreach ($billArr[$model->goods_id] as $id=>$bill){
                if(!in_array($id, $idArr)){
                    $billItem                  = BillSettlementItem::create($model->goods_id);
                    $billItem->bill_id         = $id;
                    $billItem->bill_quantity   = $bill['bill_quantity'];
                    $billItem->settle_quantity = $bill['bill_quantity'];
                    $billItem->loss_quantity   = new Quantity(0, $bill['bill_quantity']->unit);

                    if(!empty($bill['bill_quantity_sub']->unit) && $bill['bill_quantity']->unit != $bill['bill_quantity_sub']->unit){
                        $billItem->bill_quantity_sub   = $bill['bill_quantity_sub'];
                        $billItem->settle_quantity_sub = $bill['bill_quantity_sub'];
                        $billItem->loss_quantity_sub   = new Quantity(0, $bill['bill_quantity_sub']->unit);
                    }

                    $billItem->exchange_rate     = $model->exchange_rate;
                    $billItem->settle_price      = $model->price;
                    $billItem->settle_amount     = $bill['bill_quantity']->quantity * $model->price ;
                    $billItem->settle_price_cny  = $model->price_cny;
                    $billItem->settle_amount_cny = $bill['bill_quantity']->quantity * $model->price_cny ;

                    $entity->addBillSettlementItem($billItem);
                }
            }
        }
    }


    /**
     * 添加入库通知单或发货单明细项对象
     */
    private function addBillObject($model, $entity, $type, $billQuantity)
    {
        $billSettlement = "";
        if($type == SettlementMode::LADING_BILL_MODE_SETTLEMENT){
            $billSettlement = $model->ladingSettlement;
        }else if($type == SettlementMode::DELIVERY_ORDER_MODE_SETTLEMENT){
            $billSettlement = $model->orderSettlement;
        }
        if(!empty($billSettlement)){
            $bill_quantity     = $billQuantity[$billSettlement->goods_id]['bill_quantity'];
            $bill_quantity_sub = $billQuantity[$billSettlement->goods_id]['bill_quantity_sub'];
            
            $billItem                  = BillSettlementItem::create($billSettlement->goods_id);
            $billItem->item_id         = $billSettlement->item_id;
            if($type == SettlementMode::LADING_BILL_MODE_SETTLEMENT)
                $billItem->bill_id = $billSettlement->batch_id;
            else
                $billItem->bill_id      = $billSettlement->order_id;
            $billItem->bill_quantity   = new Quantity($bill_quantity,$billSettlement->unit);
            $billItem->settle_quantity = new Quantity($billSettlement->quantity, $billSettlement->unit);
            $billItem->loss_quantity   = new Quantity(($bill_quantity - $billSettlement->quantity), $billSettlement->unit);
            if(!empty($billSettlement->sub) && $billSettlement->sub->unit != $billSettlement->unit){
                $billItem->bill_quantity_sub   = new Quantity($bill_quantity_sub,$billSettlement->sub->unit);
                $billItem->settle_quantity_sub = new Quantity($billSettlement->sub->quantity, $billSettlement->sub->unit);
                $billItem->loss_quantity_sub   = new Quantity(($bill_quantity_sub - $billSettlement->sub->quantity), $billSettlement->sub->unit);
            }
            $billItem->exchange_rate     = $billSettlement->exchange_rate;
            $billItem->settle_price      = $billSettlement->price;
            $billItem->settle_amount     = $billSettlement->amount;
            $billItem->settle_price_cny  = $billSettlement->price_cny;
            $billItem->settle_amount_cny = $billSettlement->amount_cny;
            $billItem->remark            = $billSettlement->remark;

            $entity->addBillSettlementItem($billItem);
        }
    }

    /**
     * 添加结算明细项对象
     */
    private function addSettlementDetail($model, $entity)
    {
        $settleGoods = $model->settleGoods;
        if(!empty($settleGoods)){
            $entity->has_detail  = true;
            $goodsSettlementItem = GoodsSettlementItem::create();

            $payItem = GoodsExpenseItem::create();
            $payItem->currency          = Currency::getCurrency($settleGoods->currency);
            $payItem->amount            = $settleGoods->amount_currency;
            $payItem->tax_exchange_rate = $settleGoods->exchange_rate_tax;
            $payItem->tax_amount_cny    = $settleGoods->amount_goods_tax;
            $payItem->exchange_rate     = $settleGoods->exchange_rate;
            $payItem->price             = $settleGoods->price_goods;
            $payItem->amount_cny        = $settleGoods->amount_goods;
            $goodsSettlementItem->addGoodsExpenseItem($payItem);

            $adjustItem = AdjustmentItem::create();
            $adjustItem->adjust_type        = AdjustMode::getAdjustMode($settleGoods->adjust_type);
            $adjustItem->adjust_amount      = $settleGoods->amount_adjust;
            $adjustItem->adjust_reason      = $settleGoods->adjust_reason;
            $adjustItem->settle_quantity    = $entity->settle_quantity;
            $adjustItem->settle_amount_cny  = $settleGoods->amount;
            $adjustItem->settle_price_cny   = $settleGoods->price;
            $adjustItem->confirm_quantity   = $entity->settle_quantity;
            $adjustItem->confirm_amount_cny = $settleGoods->amount_actual;
            $adjustItem->confirm_price_cny  = $settleGoods->price_actual;
            $goodsSettlementItem->addAdjustmentItem($adjustItem);

            if(is_array($settleGoods->goodsItems) && !empty($settleGoods->goodsItems)){
                foreach ($settleGoods->goodsItems as $goodsItem) {
                    if($goodsItem->type == \ContractSettlementGoodsDetailItem::TAX_RATE_TYPE){
                        $taxItem = TaxItem::create();
                        $taxItem->tax        = Tax::getTax($goodsItem->subject_id);
                        $taxItem->tax_rate   = $goodsItem->rate;
                        $taxItem->tax_amount = $goodsItem->amount;
                        $taxItem->tax_price  = $goodsItem->price;
                        $taxItem->remark     = $goodsItem->remark;
                        $goodsSettlementItem->addTaxItem($taxItem);
                    }else{
                        $otherItem = OtherExpenseItem::create();
                        $otherItem->expense        = Expense::getExpense($goodsItem->subject_id);
                        $otherItem->expense_amount = $goodsItem->amount;
                        $otherItem->expense_price  = $goodsItem->price;
                        $otherItem->remark         = $goodsItem->remark;
                        $goodsSettlementItem->addOtherExpenseItem($otherItem);
                    }

                }
            }

            $entity->addGoodsSettlementItem($goodsSettlementItem);
        }
    }

    /**
     * 添加合同货款结算附件对象
     */
    private function addContractOfAttachment($model, $entity)
    {
        if (is_array($model->goodsAttachments) && !empty($model->goodsAttachments))
        {
            foreach ($model->goodsAttachments as $attachment)
            {
                if($attachment->type==1)
                    $entity->addReceiptAttachment($this->getAttachmentEntity($attachment));
                else if($attachment->type==2)
                    $entity->addOtherAttachment($this->getAttachmentEntity($attachment));
            }
        }
    }

    /**
     * 添加单据结算附件对象
     */
    private function addBillOfAttachment($model, $entity, $type)
    {
        $billAttachments = array();
        if($type == SettlementMode::LADING_BILL_MODE_SETTLEMENT)
            $billAttachments = $model->ladingAttachments;
        else
            $billAttachments = $model->deliveryAttachments;

        if (is_array($billAttachments) && !empty($billAttachments))
        {
            foreach ($billAttachments as $attachment)
            {
                if(($type==SettlementMode::LADING_BILL_MODE_SETTLEMENT && $attachment->type==1) || 
                    ($type==SettlementMode::DELIVERY_ORDER_MODE_SETTLEMENT && $attachment->type==3)){
                    $entity->addReceiptAttachment($this->getAttachmentEntity($attachment));
                }else if(($type==SettlementMode::LADING_BILL_MODE_SETTLEMENT && $attachment->type==11) ||
                    ($type==SettlementMode::DELIVERY_ORDER_MODE_SETTLEMENT && $attachment->type==4)){
                    $entity->addOtherAttachment($this->getAttachmentEntity($attachment));
                }
            }
        }
    }

    //==================================================================
    //以下是数据持久化相关操作
    /**
     * [saveContractSettlement 保存合同结算数据]
     * @param
     * @param  $model   
     * @param  $entity  
     * @param  $contract
     * @param  $type    
     */
    protected function saveContractSettlement($model, $entity, $contract, $type)
    {
        if(empty($model)){
            $model = new \ContractSettlement();
            $model->settle_id   = \IDService::getContractCodeId();
            $model->code        = \IDService::getContractSettlementCode();
            $model->type        = $type;
            $model->contract_id = $entity->contract_id;
            $model->project_id  = $contract->project_id;
            $model->currency    = $entity->settle_currency->id;
            $model->status      = SettlementStatus::STATUS_NEW;
        }

        if(in_array($type, array(SettlementMode::BUY_CONTRACT_MODE_SETTLEMENT, SettlementMode::SALE_CONTRACT_MODE_SETTLEMENT))){
            $model->amount_goods = empty($entity->goods_amount) ? 0 : $entity->goods_amount;
            $model->amount_other = empty($entity->other_amount) ? 0 : $entity->other_amount;
            $model->amount       = $model->amount_goods + $model->amount_other;
            $model->settle_date  = $entity->settle_date;
            $model->status       = $entity->status;
            $model->remark       = $entity->remark;
        }

        $res = $model->save();
        if (!$res)
            throw new ZModelSaveFalseException($model);
    }


    /**
     * [saveOtherSettlement 保存非货款结算数据]
     * @param  $entity 
     */
    protected function saveOtherSettlement($model, $entity)
    {
        if(empty($entity->other_settle_items) && !empty($model->contractSettlementSubjectDetail)){
            foreach ($model->contractSettlementSubjectDetail as $s) {
                $res = $s->delete();
                if (!$res)
                    throw new ZModelDeleteFalseException($s);
            }
        }else if(!empty($entity->other_settle_items)){
            if(empty($model->contractSettlementSubjectDetail)){
                foreach ($entity->other_settle_items as $ot)
                {
                    $otherExpenseItem = new \ContractSettlementSubjectDetail();
                    if(!empty($ot->detail_id))
                        $otherExpenseItem->detail_id = $ot->detail_id;
                    else
                        $otherExpenseItem->detail_id = \IDService::getOtherSettlementId();
                    
                    $otherExpenseItem->settle_id     = $model->settle_id;
                    $otherExpenseItem->contract_id   = $entity->contract_id;
                    $otherExpenseItem->project_id    = $entity->project_id;
                    $otherExpenseItem->subject_id    = $ot->fee->id;
                    $otherExpenseItem->currency      = $ot->currency->id;
                    $otherExpenseItem->amount        = $ot->amount;
                    $otherExpenseItem->exchange_rate = $ot->exchange_rate;
                    $otherExpenseItem->amount_cny    = $ot->amount_cny;
                    $otherExpenseItem->remark        = $ot->remark;
                    $otherExpenseItem->status_time   = Utility::getDateTime();
                    
                    $res = $otherExpenseItem->save();
                    if(!$res)
                        throw new ZModelSaveFalseException($otherExpenseItem);
                    
                    if(!empty($ot->receipt_attachments) && empty($ot->detail_id)){
                        foreach ($ot->receipt_attachments as $id=>$attachment) {
                            \ContractSettlementAttachment::model()->updateByPk($id,array('base_id'=>$otherExpenseItem->detail_id));
                        }    
                    }
                }
            }else{
                $otherEntity = $entity->other_settle_items;
                foreach ($model->contractSettlementSubjectDetail as $subject) {
                    if(isset($otherEntity[$subject->subject_id])){
                        $otherItem = $otherEntity[$subject->subject_id];
                        $subject->amount        = $otherItem->amount;
                        $subject->exchange_rate = $otherItem->exchange_rate;
                        $subject->amount_cny    = $otherItem->amount_cny;
                        $subject->currency      = $otherItem->currency->id;
                        $subject->remark        = $otherItem->remark;
                        $res = $subject->save();
                        if (!$res)
                            throw new ZModelSaveFalseException($subject);
    
                        unset($otherEntity[$subject->subject_id]);


                        if(!empty($otherItem->receipt_attachments) && empty($otherItem->detail_id)){
                            \ContractSettlementAttachment::model()->updateAll(array('status'=>0),'status=:status and base_id=:base_id',array(':status'=>1,':base_id'=>$subject->detail_id));

                            foreach ($otherItem->receipt_attachments as $id=>$attachment) {
                                \ContractSettlementAttachment::model()->updateByPk($id,array('base_id'=>$subject->detail_id));
                            }
                        }
                    }else{
                        $res = $subject->delete();
                        if (!$res)
                            throw new ZModelDeleteFalseException($subject);
                    }
                }
    
                if (is_array($otherEntity) && count($otherEntity) > 0){
                    foreach ($otherEntity as $oe) {
                        $subItem = new \ContractSettlementSubjectDetail();
                        if(!empty($oe->detail_id))
                            $subItem->detail_id = $oe->detail_id;
                        else
                            $subItem->detail_id = \IDService::getOtherSettlementId();
                        
                        $subItem->settle_id     = $model->settle_id;
                        $subItem->contract_id   = $entity->contract_id;
                        $subItem->project_id    = $entity->project_id;
                        $subItem->subject_id    = $oe->fee->id;
                        $subItem->currency      = $oe->currency->id;
                        $subItem->amount        = $oe->amount;
                        $subItem->exchange_rate = $oe->exchange_rate;
                        $subItem->amount_cny    = $oe->amount_cny;
                        $subItem->remark        = $oe->remark;
                        $subItem->status_time   = Utility::getDateTime();
                        
                        $res = $subItem->save();
                        if (!$res)
                            throw new ZModelSaveFalseException($subItem);

                        if(!empty($oe->receipt_attachments)  && empty($oe->detail_id)){
                            foreach ($oe->receipt_attachments as $id=>$attachment) {
                                \ContractSettlementAttachment::model()->updateByPk($id,array('base_id'=>$subItem->detail_id));
                            }
                        }
                    }
                }
            }
    
        }
    }

    /**
     * [saveBillSettlement 保存入库通知单或发货单结算数据]
     * @param  $model    
     * @param  $entity   
     * @param  $contract 
     * @param  $type     
     */
    protected function saveBillSettlement($model, $entity, $contract, $type)
    {
        if(empty($model))
        {
            if($type == SettlementMode::LADING_BILL_MODE_SETTLEMENT){
                $model = new \LadingSettlement();
                $model->settle_id = \IDService::getLadingSettlementId();
                $model->code      = \IDService::getLadingSettlementCode();
                $model->lading_id = $entity->bill_id;
            }
            else{
                $model = new \DeliverySettlement();
                $model->settle_id = \IDService::getDeliverySettlementId();
                $model->code      = \IDService::getDeliverySettlementCode();
                $model->order_id  = $entity->bill_id;
            }

            $model->contract_id  = $entity->contract_id;
            $model->project_id   = $contract->project_id;
            $model->currency     = $entity->settle_currency->id;
        }
        
        $model->amount_goods = empty($entity->goods_amount) ? 0 : $entity->goods_amount;
        $model->amount       = $model->amount_goods;
        $model->status       = $entity->status;
        $model->settle_date  = $entity->settle_date;

        $res = $model->save();
        if (!$res)
            throw new ZModelSaveFalseException($model);
    }

    /**
     * [saveSettlementDetail 保存结算明细数据]
     * @param
     * @param  $model  
     * @param  $entity 
     * @param  $type   
     */
    protected function saveSettlementDetail($model, $entity, $contract, $type)
    {
        $items = $entity->goods_settle_items;
        if (!is_array($items))
            $items = array();

        $params = array();
        $params['model']     = "\StockBatchSettlement";
        $params['flag']      = true;
        $params['type']      = $type;
        $params['bill_id']   = !empty($entity->bill_id) ? $entity->bill_id : 0;
        $params['settle_id'] = $model->settle_id;
        if(in_array($type, array(SettlementMode::SALE_CONTRACT_MODE_SETTLEMENT, SettlementMode::DELIVERY_ORDER_MODE_SETTLEMENT))){
            $params['model'] = "\DeliverySettlementDetail";
            // $params['flag']  = "DeliverySettlementDetail";
            $params['flag']  = false;
        }

        $params['mark']  = false;
        if(in_array($type,  array(SettlementMode::BUY_CONTRACT_MODE_SETTLEMENT, SettlementMode::SALE_CONTRACT_MODE_SETTLEMENT)))
            $params['mark'] = true;



        $isNew = $model->isNewRecord;

        if (!$isNew)
        {
            if (is_array($model->contractSettlementGoods) && !empty($model->contractSettlementGoods))
            {
                foreach ($model->contractSettlementGoods as $p)
                {
                    $item = $items[$p->goods_id];
                    $p->quantity_bill       = $item->bill_quantity->quantity;
                    $p->quantity_actual_sub = $item->bill_quantity_sub->quantity;
                    $p->quantity            = $item->settle_quantity->quantity;
                    $p->quantity_sub        = $item->settle_quantity_sub->quantity;
                    $p->exchange_rate       = $item->exchange_rate;
                    $p->quantity_loss       = $item->loss_quantity->quantity;
                    $p->quantity_loss_sub   = $item->loss_quantity_sub->quantity;
                    $p->price               = $item->settle_price;
                    $p->amount              = $item->settle_amount;
                    $p->price_cny           = $item->settle_price_cny;
                    $p->amount_cny          = $item->settle_amount_cny;
                    $p->remark              = $item->remark;
                    $res = $p->save();
                    if (!$res)
                        throw new ZModelSaveFalseException($p);

                    $this->saveBillSettlementDetail($item, $contract, $model->settle_id, $p->unit, $p->unit_sub, $type, $params);
                    
                    $this->saveSettlementDetailItem($item, $contract, $p, $params);

                    unset($items[$p->goods_id]);
                }
            }
        }

        $this->saveSettlementItem($items, $contract, $params);

    }


    /**
     * [saveBillSettlementDetail 保存提单或发货单结算明细数据]
     * @param
     * @param  $entity   
     * @param  $contract 
     * @param  $settleId 
     * @param  $unit     
     * @param  $unit_sub 
     * @param  $type     
     */
    private function saveBillSettlementDetail($entity, $contract, $settleId, $unit, $unit_sub, $type, $params)
    {
        if (is_array($entity->bill_items) && !empty($entity->bill_items))
        {
            foreach ($entity->bill_items as $bill)
            {
                if(!empty($bill->item_id)){
                    $settleItem = $params['model']::model()->findByPk($bill->item_id);
                    if(empty($settleItem))
                        throw new ZModelNotExistsException($bill->item_id, $params['msg']);
                }else{
                    if($params['mark']){
                        $settleItem = new $params['model']();
                        $settleItem->item_id     = \IDService::getGoodsSettlementId();
                        $settleItem->settle_id   = $settleId;
                        $settleItem->project_id  = $contract->project_id;
                        $settleItem->contract_id = $contract->contract_id;
                        $settleItem->goods_id    = $bill->goods_id;
                        $settleItem->unit        = $unit;
                        if($type == SettlementMode::BUY_CONTRACT_MODE_SETTLEMENT){
                            $settleItem->batch_id = $bill->bill_id;
                            $settleItem->unit_sub = $unit_sub;
                        }
                        else{
                            $settleItem->order_id = $bill->bill_id;
                        }  
                    }else{
                        throw new ZException($params['msg']."对象不存在！");
                    }
                }

                $settleItem->exchange_rate = $bill->exchange_rate;
                $settleItem->quantity_loss = $bill->loss_quantity->quantity;
                $settleItem->price         = $bill->settle_price;
                $settleItem->amount        = $bill->settle_amount;
                $settleItem->price_cny     = $bill->settle_price_cny;
                $settleItem->amount_cny    = $bill->settle_amount_cny;
                $settleItem->remark        = $bill->remark;

                if($params['flag']){
                    $settleItem->quantity_bill       = $bill->bill_quantity->quantity;
                    $settleItem->quantity_actual_sub = $bill->bill_quantity_sub->quantity;
                    $settleItem->quantity            = $bill->settle_quantity->quantity;
                    $settleItem->quantity_sub        = $bill->settle_quantity_sub->quantity;
                    $settleItem->quantity_loss_sub   = $bill->loss_quantity_sub->quantity;
                }else{
                    $settleItem->quantity        = $bill->bill_quantity->quantity;
                    $settleItem->quantity_settle = $bill->settle_quantity->quantity;
                }

                $res = $settleItem->save();
                if (!$res)
                    throw new ZModelSaveFalseException($settleItem);
            }
        }else{
            throw new ZException("BillSettlementItem对象不存在");
        }
    }

    /**
     * [saveSettlementDetailItem 保存结算录入明细项数据]
     * @param
     * @param  $item     
     * @param  $contract 
     * @param  $model    
     */
    private function saveSettlementDetailItem($entity, $contract, $model)
    {   
        // 判断货款明细提交时有没有，
        // 有：更新，没有：删除
        // 获取调整明细和货款明细
        $goodsDetail= \ContractSettlementGoodsDetail::model()->with('goodsItems')->find("t.item_id=".$model->item_id);
        if(!$entity->has_detail){
            if(!empty($goodsDetail)){
                if(!empty($goodsDetail->goodsItems)){
                    foreach ($goodsDetail->goodsItems as $g) {
                        $res = $g->delete();
                        if (!$res)
                            throw new ZModelDeleteFalseException($g);
                    }
                }

                $res = $goodsDetail->delete();
                if (!$res)
                    throw new ZModelDeleteFalseException($goodsDetail);
            }
        }else {
            $item = $entity->goods_settlement_item; //明细项对象

            if(empty($goodsDetail)){
                $contractGoods = TradeGoodsRepository::repository()->findByContractIdAndGoodsId($contract->contract_id, $model->goods_id);
                if(empty($contractGoods))
                    throw new ZException("ContractGoods对象不存在");
                foreach ($item->goods_expense_item as $goods)
                {                                
                    $settleGoodsItem = new \ContractSettlementGoodsDetail();
                    $settleGoodsItem->contract_id       = $contract->contract_id;
                    $settleGoodsItem->item_id           = $model->item_id;
                    $settleGoodsItem->goods_id          = $model->goods_id;
                    $settleGoodsItem->unit              = $contractGoods->unit;
                    $settleGoodsItem->currency          = $goods->currency->id;
                    $settleGoodsItem->amount_currency   = $goods->amount;
                    $settleGoodsItem->exchange_rate     = $goods->exchange_rate;
                    $settleGoodsItem->amount_goods      = $goods->amount_cny;
                    $settleGoodsItem->price_goods       = $goods->price;
                    $settleGoodsItem->exchange_rate_tax = $goods->tax_exchange_rate;
                    $settleGoodsItem->amount_goods_tax  = $goods->tax_amount_cny;
                }

                if(!empty($entity->adjustment_item)) {
                    foreach ($entity->adjustment_item as $adjust) {
                        $settleGoodsItem->adjust_type   = $adjust->adjust_type->id;
                        $settleGoodsItem->amount_adjust = $adjust->adjust_amount;
                        $settleGoodsItem->adjust_reason = $adjust->adjust_reason;
                        $settleGoodsItem->price         = $adjust->settle_price_cny;
                        $settleGoodsItem->amount        = $adjust->settle_amount_cny;
                        $settleGoodsItem->amount_actual = $adjust->confirm_amount_cny;
                        $settleGoodsItem->price_actual  = $adjust->confirm_price_cny;
                    }
                }else{
                    throw new ZException("AdjustmentItem对象不存在");
                }

                $res = $settleGoodsItem->save();
                if (!$res)
                    throw new ZModelSaveFalseException($settleGoodsItem);

                if(!empty($item->tax_items)){
                    foreach ($item->tax_items as $tax) {
                        $taxItem = new \ContractSettlementGoodsDetailItem();
                        $taxItem->contract_id = $contract->contract_id;
                        $taxItem->item_id     = $model->item_id;
                        $taxItem->detail_id   = $settleGoodsItem->detail_id;
                        $taxItem->subject_id  = $tax->tax->id;
                        $taxItem->type        = \ContractSettlementGoodsDetailItem::TAX_RATE_TYPE;
                        $taxItem->rate        = $tax->tax_rate;
                        $taxItem->amount      = $tax->tax_amount;
                        $taxItem->price       = $tax->tax_price;
                        $taxItem->remark      = $tax->remark;

                        $res = $taxItem->save();
                        if (!$res)
                            throw new ZModelSaveFalseException($taxItem);
                    }
                }

                if(!empty($item->other_expense_items)){
                    foreach ($item->other_expense_items as $expense) {
                        $otherItem = new \ContractSettlementGoodsDetailItem();
                        $otherItem->contract_id = $contract->contract_id;
                        $otherItem->item_id     = $model->item_id;
                        $otherItem->detail_id   = $settleGoodsItem->detail_id;
                        $otherItem->subject_id  = $expense->expense->id;
                        $otherItem->type        = \ContractSettlementGoodsDetailItem::OTHER_EXPENSE_TYPE;
                        $otherItem->amount      = $expense->expense_amount;
                        $otherItem->price       = $expense->expense_price;
                        $otherItem->remark      = $expense->remark;

                        $res = $otherItem->save();
                        if (!$res)
                            throw new ZModelSaveFalseException($otherItem);
                    }
                }
            }else{
                foreach ($item->goods_expense_item as $goods)
                {
                    $goodsDetail->currency          = $goods->currency->id;
                    $goodsDetail->amount_currency   = $goods->amount;
                    $goodsDetail->exchange_rate     = $goods->exchange_rate;
                    $goodsDetail->amount_goods      = $goods->amount_cny;
                    $goodsDetail->price_goods       = $goods->price;
                    $goodsDetail->exchange_rate_tax = $goods->tax_exchange_rate;
                    $goodsDetail->amount_goods_tax  = $goods->tax_amount_cny;
                }

                if(!empty($item->adjustment_item)) {
                    foreach ($item->adjustment_item as $adjust) {
                        $goodsDetail->adjust_type   = $adjust->adjust_type->id;
                        $goodsDetail->amount_adjust = $adjust->adjust_amount;
                        $goodsDetail->adjust_reason = $adjust->adjust_reason;
                        $goodsDetail->price         = $adjust->settle_price_cny;
                        $goodsDetail->amount        = $adjust->settle_amount_cny;
                        $goodsDetail->amount_actual = $adjust->confirm_amount_cny;
                        $goodsDetail->price_actual  = $adjust->confirm_price_cny;
                    }
                }else{
                    throw new ZException("AdjustmentItem对象不存在");
                }

                $res = $goodsDetail->save();
                if (!$res)
                    throw new ZModelSaveFalseException($goodsDetail);

                $goodsItem = $goodsDetail->goodsItems;
                $taxes      = array();
                $expenses   = array();
                if(empty($goodsItem)){
                    if(!empty($item->tax_items)){
                        foreach ($item->tax_items as $tax) {
                            $taxItem = new \ContractSettlementGoodsDetailItem();
                            $taxItem->contract_id = $contract->contract_id;
                            $taxItem->item_id     = $model->item_id;
                            $taxItem->detail_id   = $goodsDetail->detail_id;
                            $taxItem->subject_id  = $tax->tax->id;
                            $taxItem->type        = \ContractSettlementGoodsDetailItem::TAX_RATE_TYPE;
                            $taxItem->rate        = $tax->tax_rate;
                            $taxItem->amount      = $tax->tax_amount;
                            $taxItem->price       = $tax->tax_price;
                            $taxItem->remark      = $tax->remark;

                            $res = $taxItem->save();
                            if (!$res)
                                throw new ZModelSaveFalseException($taxItem);
                        }
                    }

                    if(!empty($item->other_expense_items)){
                        foreach ($item->other_expense_items as $expense) {
                            $otherItem = new \ContractSettlementGoodsDetailItem();
                            $otherItem->contract_id = $contract->contract_id;
                            $otherItem->item_id     = $model->item_id;
                            $otherItem->detail_id   = $goodsDetail->detail_id;
                            $otherItem->subject_id  = $expense->expense->id;
                            $otherItem->type        = \ContractSettlementGoodsDetailItem::OTHER_EXPENSE_TYPE;
                            $otherItem->amount      = $expense->expense_amount;
                            $otherItem->price       = $expense->expense_price;
                            $otherItem->remark      = $expense->remark;

                            $res = $otherItem->save();
                            if (!$res)
                                throw new ZModelSaveFalseException($otherItem);
                        }
                    }
                }else{
                    foreach ($goodsItem as $gi) {
                        if($gi->type==\ContractSettlementGoodsDetailItem::TAX_RATE_TYPE)
                            $taxes[$gi->subject_id]     = $gi;
                        else
                            $expenses[$gi->subject_id]  = $gi;
                    }

                    //保存计税明细
                    if(empty($item->tax_items) && !empty($taxes)){
                        foreach ($taxes as $t) {
                            $res = $t->delete();
                            if (!$res)
                                throw new ZModelDeleteFalseException($t);
                        }
                    }else if(!empty($item->tax_items)){
                        if(empty($taxes)){
                            foreach ($item->tax_items as $tax) {
                                $taxItem = new \ContractSettlementGoodsDetailItem();
                                $taxItem->contract_id = $contract->contract_id;
                                $taxItem->item_id     = $model->item_id;
                                $taxItem->detail_id   = $goodsDetail->detail_id;
                                $taxItem->subject_id  = $tax->tax->id;
                                $taxItem->type        = \ContractSettlementGoodsDetailItem::TAX_RATE_TYPE;
                                $taxItem->rate        = $tax->tax_rate;
                                $taxItem->amount      = $tax->tax_amount;
                                $taxItem->price       = $tax->tax_price;
                                $taxItem->remark      = $tax->remark;

                                $res = $taxItem->save();
                                if (!$res)
                                    throw new ZModelSaveFalseException($taxItem);
                            }
                        }else{
                            $taxEntity = $item->tax_items;
                            foreach ($taxes as $tax) {
                                if(isset($taxEntity[$tax->subject_id])){
                                    $tItem = $taxEntity[$tax->subject_id];
                                    $tax->rate   = $tItem->tax_rate;
                                    $tax->amount = $tItem->tax_amount;
                                    $tax->price  = $tItem->tax_price;
                                    $tax->remark = $tItem->remark;

                                    $res = $tax->save();
                                    if (!$res)
                                        throw new ZModelSaveFalseException($tax);

                                    unset($taxEntity[$tax->subject_id]);
                                }else{
                                    $res = $tax->delete();
                                    if (!$res)
                                        throw new ZModelDeleteFalseException($tax);
                                }
                            }

                            if (is_array($taxEntity) && count($taxEntity) > 0){
                                foreach ($taxEntity as $te) {
                                    $taxItem = new \ContractSettlementGoodsDetailItem();
                                    $taxItem->contract_id = $contract->contract_id;
                                    $taxItem->item_id     = $model->item_id;
                                    $taxItem->detail_id   = $goodsDetail->detail_id;
                                    $taxItem->subject_id  = $te->tax->id;
                                    $taxItem->type        = \ContractSettlementGoodsDetailItem::TAX_RATE_TYPE;
                                    $taxItem->rate        = $te->tax_rate;
                                    $taxItem->amount      = $te->tax_amount;
                                    $taxItem->price       = $te->tax_price;
                                    $taxItem->remark      = $te->remark;

                                    $res = $taxItem->save();
                                    if (!$res)
                                        throw new ZModelSaveFalseException($taxItem);
                                }
                            }
                        }
                    }

                    //保存其他费用明细
                    if(empty($item->other_expense_items) && !empty($expenses)){
                        foreach ($expenses as $e) {
                            $res = $e->delete();
                            if (!$res)
                                throw new ZModelDeleteFalseException($e);
                        }
                    }else if(!empty($item->other_expense_items)){
                        if(empty($expenses)){
                            foreach ($item->other_expense_items as $expense) {
                                $otherItem = new \ContractSettlementGoodsDetailItem();
                                $otherItem->contract_id = $contract->contract_id;
                                $otherItem->item_id     = $model->item_id;
                                $otherItem->detail_id   = $goodsDetail->detail_id;
                                $otherItem->subject_id  = $expense->expense->id;
                                $otherItem->type        = \ContractSettlementGoodsDetailItem::OTHER_EXPENSE_TYPE;
                                $otherItem->amount      = $expense->expense_amount;
                                $otherItem->price       = $expense->expense_price;
                                $otherItem->remark      = $expense->remark;

                                $res = $otherItem->save();
                                if (!$res)
                                    throw new ZModelSaveFalseException($otherItem);
                            }
                        }else{
                            $feeEntity = $item->other_expense_items;
                            foreach ($expenses as $expense) {
                                if(isset($feeEntity[$expense->subject_id])){
                                    $fItem = $feeEntity[$expense->subject_id];
                                    $expense->amount = $fItem->expense_amount;
                                    $expense->price  = $fItem->expense_price;
                                    $expense->remark = $fItem->remark;

                                    $res = $expense->save();
                                    if (!$res)
                                        throw new ZModelSaveFalseException($expense);

                                    unset($feeEntity[$expense->subject_id]);
                                }else{
                                    $res = $expense->delete();
                                    if (!$res)
                                        throw new ZModelDeleteFalseException($tax);
                                }
                            }

                            if (is_array($feeEntity) && count($feeEntity) > 0){
                                foreach ($feeEntity as $fe) {
                                    $feeItem = new \ContractSettlementGoodsDetailItem();
                                    $feeItem->contract_id = $contract->contract_id;
                                    $feeItem->item_id     = $model->item_id;
                                    $feeItem->detail_id   = $goodsDetail->detail_id;
                                    $feeItem->subject_id  = $fe->expense->id;
                                    $feeItem->type        = \ContractSettlementGoodsDetailItem::OTHER_EXPENSE_TYPE;
                                    $feeItem->amount      = $fe->expense_amount;
                                    $feeItem->price       = $fe->expense_price;
                                    $feeItem->remark      = $fe->remark;

                                    $res = $feeItem->save();
                                    if (!$res)
                                        throw new ZModelSaveFalseException($feeItem);
                                }
                            }
                        }
                    }
                }

            }

        }
    }


    /**
     * [saveSettlementItem 新增结算时保存相关数据]
     * @param
     * @param  [array] $items    
     * @param  [object] $contract
     * @param  [array] $params   
     */
    private function saveSettlementItem($items, $contract, $params)
    {
        if (is_array($items) && count($items) > 0)
        {
            foreach ($items as $item)
            {
                $contractGoods = TradeGoodsRepository::repository()->findByContractIdAndGoodsId($contract->contract_id, $item->goods_id);
                if(empty($contractGoods))
                    throw new ZException("ContractGoods对象不存在");

                $settleGoods = new \ContractSettlementGoods();
                $settleGoods->item_id       = $item->item_id;
                $settleGoods->settle_id     = $params['settle_id'];
                $settleGoods->quantity_bill = $item->bill_quantity->quantity;
                $settleGoods->quantity      = $item->settle_quantity->quantity;
                $settleGoods->quantity_loss = $item->loss_quantity->quantity;
                $settleGoods->project_id    = $contract->project_id;
                $settleGoods->contract_id   = $contract->contract_id;
                $settleGoods->relation_id   = $params['bill_id'];
                $settleGoods->goods_id      = $item->goods_id;
                $settleGoods->unit          = $contractGoods->unit;
                $settleGoods->exchange_rate = $item->exchange_rate;
                $settleGoods->price         = $item->settle_price;
                $settleGoods->amount        = $item->settle_amount;
                $settleGoods->price_cny     = $item->settle_price_cny;
                $settleGoods->amount_cny    = $item->settle_amount_cny;
                $settleGoods->remark        = $item->remark;

                if($params['flag']){
                    $settleGoods->unit_sub            = $contractGoods->unit_store;
                    $settleGoods->quantity_actual_sub = $item->bill_quantity_sub->quantity;
                    $settleGoods->quantity_sub        = $item->settle_quantity_sub->quantity;
                    $settleGoods->quantity_loss_sub   = $item->loss_quantity_sub->quantity;
                }

                $res = $settleGoods->save();
                if (!$res)
                    throw new ZModelSaveFalseException($settleGoods);

                if (is_array($item->bill_items) && !empty($item->bill_items))
                {
                    foreach ($item->bill_items as $bill)
                    {                            
                        $settleItem = new $params['model']();
                        if($params['mark']){
                            $settleItem->item_id = \IDService::getGoodsSettlementId();
                        }else{
                            $settleItem->item_id = $item->item_id;
                        }

                        if($params['flag']){
                            $settleItem->batch_id            = $bill->bill_id;
                            $settleItem->quantity_bill       = $bill->bill_quantity->quantity;
                            $settleItem->quantity_actual_sub = $bill->bill_quantity_sub->quantity;
                            $settleItem->quantity            = $bill->settle_quantity->quantity;
                            $settleItem->quantity_sub        = $bill->settle_quantity_sub->quantity;
                            $settleItem->quantity_loss_sub   = $bill->loss_quantity_sub->quantity;
                            $settleItem->unit_sub            = $contractGoods->unit_store;
                        }else{
                            $settleItem->order_id        = $bill->bill_id;
                            $settleItem->quantity        = $bill->bill_quantity->quantity;
                            $settleItem->quantity_settle = $bill->settle_quantity->quantity;
                        }

                        $settleItem->quantity_loss = $bill->loss_quantity->quantity;
                        $settleItem->settle_id     = $params['settle_id'];
                        $settleItem->project_id    = $contract->project_id;
                        $settleItem->contract_id   = $contract->contract_id;
                        $settleItem->goods_id      = $bill->goods_id;
                        $settleItem->unit          = $contractGoods->unit;
                        $settleItem->exchange_rate = $bill->exchange_rate;
                        $settleItem->price         = $bill->settle_price;
                        $settleItem->amount        = $bill->settle_amount;
                        $settleItem->price_cny     = $bill->settle_price_cny;
                        $settleItem->amount_cny    = $bill->settle_amount_cny;
                        $settleItem->remark        = $bill->remark;

                        $res = $settleItem->save();
                        if (!$res)
                            throw new ZModelSaveFalseException($settleItem);
                    }
                }else{
                    throw new ZException("BillSettlementItem对象不存在");
                }

                $detail = $item->goods_settlement_item;
                if($item->has_detail && !empty($detail))
                {
                    foreach ($detail->goods_expense_items as $goods)
                    {                                
                        $settleGoodsItem = new \ContractSettlementGoodsDetail();
                        $settleGoodsItem->contract_id       = $contract->contract_id;
                        $settleGoodsItem->item_id           = $item->item_id;
                        $settleGoodsItem->goods_id          = $item->goods_id;
                        $settleGoodsItem->unit              = $contractGoods->unit;
                        $settleGoodsItem->currency          = $goods->currency->id;
                        $settleGoodsItem->amount_currency   = $goods->amount;
                        $settleGoodsItem->exchange_rate     = $goods->exchange_rate;
                        $settleGoodsItem->amount_goods      = $goods->amount_cny;
                        $settleGoodsItem->price_goods       = $goods->price;
                        $settleGoodsItem->exchange_rate_tax = $goods->tax_exchange_rate;
                        $settleGoodsItem->amount_goods_tax  = $goods->tax_amount_cny;
                    }

                    if(!empty($detail->adjustment_items)) {
                        foreach ($detail->adjustment_items as $adjust) {
                            $settleGoodsItem->adjust_type   = $adjust->adjust_type->id;
                            $settleGoodsItem->amount_adjust = $adjust->adjust_amount;
                            $settleGoodsItem->adjust_reason = $adjust->adjust_reason;
                            $settleGoodsItem->price         = $adjust->settle_price_cny;
                            $settleGoodsItem->amount        = $adjust->settle_amount_cny;
                            $settleGoodsItem->amount_actual = $adjust->confirm_amount_cny;
                            $settleGoodsItem->price_actual  = $adjust->confirm_price_cny;
                        }
                    }else{
                        throw new ZException("AdjustmentItem对象不存在");
                    }

                    $res = $settleGoodsItem->save();
                    if (!$res)
                        throw new ZModelSaveFalseException($settleGoodsItem);

                    if(!empty($detail->tax_items)){
                        foreach ($detail->tax_items as $tax) {
                            $taxItem = new \ContractSettlementGoodsDetailItem();
                            $taxItem->contract_id = $contract->contract_id;
                            $taxItem->item_id     = $item->item_id;
                            $taxItem->detail_id   = $settleGoodsItem->detail_id;
                            $taxItem->subject_id  = $tax->tax->id;
                            $taxItem->type        = \ContractSettlementGoodsDetailItem::TAX_RATE_TYPE;
                            $taxItem->rate        = $tax->tax_rate;
                            $taxItem->amount      = $tax->tax_amount;
                            $taxItem->price       = $tax->tax_price;
                            $taxItem->remark      = $tax->remark;

                            $res = $taxItem->save();
                            if (!$res)
                                throw new ZModelSaveFalseException($taxItem);
                        }
                    }

                    if(!empty($detail->other_expense_items)){
                        foreach ($detail->other_expense_items as $expense) {
                            $otherItem = new \ContractSettlementGoodsDetailItem();
                            $otherItem->contract_id = $contract->contract_id;
                            $otherItem->item_id     = $item->item_id;
                            $otherItem->detail_id   = $settleGoodsItem->detail_id;
                            $otherItem->subject_id  = $expense->expense->id;
                            $otherItem->type        = \ContractSettlementGoodsDetailItem::OTHER_EXPENSE_TYPE;
                            $otherItem->amount      = $expense->expense_amount;
                            $otherItem->price       = $expense->expense_price;
                            $otherItem->remark      = $expense->remark;

                            $res = $otherItem->save();
                            if (!$res)
                                throw new ZModelSaveFalseException($otherItem);
                        }
                    }

                }

            }
        }
    }
}