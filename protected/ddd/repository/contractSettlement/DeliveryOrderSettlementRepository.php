<?php

/**
 * Created by vector.
 * DateTime: 2018/3/26 11:33
 * Describe：提单结算单仓储
 */

namespace ddd\repository\contractSettlement;

use ddd\domain\entity\contractSettlement\DeliveryOrderSettlement;
use ddd\domain\entity\contractSettlement\GoodsExpenseSettlementItem;
use ddd\domain\entity\contractSettlement\GoodsExpenseItem;
use ddd\domain\entity\contractSettlement\OtherExpenseItem;
use ddd\domain\entity\contractSettlement\TaxItem;
use ddd\domain\entity\contractSettlement\AdjustmentItem;
use ddd\domain\entity\contractSettlement\DeliveryOrderSettlementItem;

use ddd\Common\IAggregateRoot;
use ddd\infrastructure\error\BusinessError;
// use ddd\infrastructure\error\ExceptionService;
use ddd\infrastructure\error\ZException;
use ddd\infrastructure\error\ZModelDeleteFalseException;
use ddd\infrastructure\error\ZModelNotExistsException;
use ddd\infrastructure\error\ZModelSaveFalseException;
use ddd\Common\Repository\EntityRepository;
use ddd\repository\contract\TradeGoodsRepository;
use ddd\domain\entity\value\Currency;
use ddd\domain\entity\value\Tax;
use ddd\domain\entity\value\Expense;
use ddd\domain\entity\value\Quantity;
use ddd\domain\entity\value\AdjustMode;
use ddd\domain\entity\contractSettlement\SettlementStatus;
use ddd\domain\entity\contractSettlement\SettlementMode;
use ddd\repository\stock\DeliveryOrderRepository;


class DeliveryOrderSettlementRepository extends EntityRepository
{
    
	public function init()
    {
        $this->with=array("contractSettlementGoods","contractSettlementGoods.orderSettlement","contractSettlementGoods.settleGoods","contractSettlementGoods.fees","contractSettlementGoods.deliveryAttachments");
    }

    /**
     * 获取对应的数据模型的类名
     * @return string
     */
    public function getActiveRecordClassName()
    {
        return "DeliverySettlement";
    }

    public function getNewEntity()
    {
        return new DeliveryOrderSettlement();
    }

    

    /**
     * 数据模型转换成业务对象
     * @param $model
     * @return DeliveryOrderSettlement|Entity
     * @throws \Exception
     */
    public function dataToEntity($model)
    {
        $entity = $this->getNewEntity();
        $entity->setAttributes($model->getAttributes(),false);
        $entity->settle_currency = Currency::getCurrency($model->currency);
        $entity->order_id = $model->order_id;
        $entity->goods_amount = $model->amount_goods;

        $deliveryQuantity = DeliveryOrderRepository::repository()->getDeliveryGoodsInQuantity($entity->order_id);

        if(is_array($model->contractSettlementGoods) && !empty($model->contractSettlementGoods))
        {
            foreach ($model->contractSettlementGoods as $p)
            {
                $out_quantity = $deliveryQuantity[$p->goods_id]['out_quantity'];
                // $out_quantity_sub = $deliveryQuantity[$p->goods_id]['out_quantity_sub'];

                $item=GoodsExpenseSettlementItem::create($p->goods_id);
                $item->item_id          = $p->item_id;
                $item->relation_id      = $p->relation_id;
                // $item->out_quantity     = new Quantity($p->quantity_bill,$p->unit);
                $item->out_quantity     = new Quantity($out_quantity,$p->unit);
                $item->settle_quantity  = new Quantity($p->quantity, $p->unit);
                //$item->loss_quantity    = new Quantity($p->quantity_loss, $p->unit);
                $item->loss_quantity    = new Quantity(($out_quantity - $p->quantity), $p->unit);
                /*if(!empty($p->settleSub)){
                    //$item->out_quantity_sub     = new Quantity($p->settleSub->quantity_actual,$p->settleSub->unit);
                    $item->out_quantity_sub     = new Quantity($out_quantity_sub,$p->settleSub->unit);
                    $item->settle_quantity_sub  = new Quantity($p->settleSub->quantity, $p->settleSub->unit);
                    //$item->loss_quantity_sub    = new Quantity($p->settleSub->quantity_loss, $p->settleSub->unit);
                    $item->loss_quantity_sub    = new Quantity(($out_quantity_sub - $p->settleSub->quantity), $p->settleSub->unit);
                }*/
                
                $item->exchange_rate    = $p->exchange_rate;
                $item->settle_price     = $p->price;
                $item->settle_amount    = $p->amount;
                $item->settle_price_cny     = $p->price_cny;
                $item->settle_amount_cny    = $p->amount_cny;
                $item->remark           = $p->remark;

                if(!empty($p->orderSettlement)){
                    $order = $p->orderSettlement;
                    $orderItem = DeliveryOrderSettlementItem::create($order->goods_id);
                    $orderItem->item_id = $order->item_id;
                    $orderItem->out_quantity      = $item->out_quantity;
                    $orderItem->settle_quantity  = $item->settle_quantity;
                    $orderItem->loss_quantity    = $item->loss_quantity;
                    /*if(!empty($order->sub)){
                        $orderItem->out_quantity_sub      = $item->out_quantity_sub;
                        $orderItem->settle_quantity_sub  = $item->settle_quantity_sub;
                        $orderItem->loss_quantity_sub    = $item->loss_quantity_sub;
                    }*/
                    $orderItem->exchange_rate    = $order->exchange_rate;
                    $orderItem->settle_price     = $order->price;
                    $orderItem->settle_amount    = $order->amount;
                    $orderItem->settle_price_cny     = $order->price_cny;
                    $orderItem->settle_amount_cny    = $order->amount_cny;
                    $orderItem->remark           = $order->remark;

                    $item->addOrderItem($orderItem);
                }


                if(!empty($p->settleGoods)){
                    $item->isHaveDetail = true;

                    $settleGoods = $p->settleGoods;

                    $payItem = GoodsExpenseItem::create();
                    $payItem->currency = Currency::getCurrency($settleGoods->currency);
                    $payItem->amount = $settleGoods->amount_currency;
                    $payItem->tax_exchange_rate = $settleGoods->exchange_rate_tax;
                    $payItem->tax_amount_cny = $settleGoods->amount_goods_tax;
                    $payItem->exchange_rate = $settleGoods->exchange_rate;
                    $payItem->price = $settleGoods->price_goods;
                    $payItem->amount_cny = $settleGoods->amount_goods;
                    $item->addGoodsExpenseItem($payItem);

                    $adjustItem = AdjustmentItem::create();
                    $adjustItem->adjust_type = AdjustMode::getAdjustMode($settleGoods->adjust_type);
                    $adjustItem->adjust_amount = $settleGoods->amount_adjust;
                    $adjustItem->adjust_reason = $settleGoods->adjust_reason;
                    $adjustItem->settle_quantity = $item->settle_quantity;
                    $adjustItem->settle_amount_cny = $settleGoods->amount;
                    $adjustItem->settle_price_cny = $settleGoods->price;
                    $adjustItem->confirm_quantity = $item->settle_quantity;
                    $adjustItem->confirm_amount_cny = $settleGoods->amount_actual;
                    $adjustItem->confirm_price_cny = $settleGoods->price_actual;
                    $item->addAdjustmentItem($adjustItem);
                }


                if(is_array($p->fees) && !empty($p->fees)){
                    foreach ($p->fees as $fee) {
                        if($fee->type==\ContractSettlementGoodsDetailItem::TAX_RATE_TYPE){
                            $taxItem = TaxItem::create();
                            $taxItem->tax = Tax::getTax($fee->subject_id);
                            // $taxItem->type= \ContractSettlementGoodsDetailItem::TAX_RATE_TYPE;
                            $taxItem->tax_rate = $fee->rate;
                            $taxItem->tax_amount = $fee->amount;
                            $taxItem->tax_price = $fee->price;
                            $taxItem->remark = $fee->remark;
                            $item->addTaxItem($taxItem);
                        }else{
                            $otherItem = OtherExpenseItem::create();
                            $otherItem->expense = Expense::getExpense($fee->subject_id);
                            // $otherItem->type= \ContractSettlementGoodsDetailItem::OTHER_EXPENSE_TYPE;
                            $otherItem->expense_amount = $fee->amount;
                            $otherItem->expense_price = $fee->price;
                            $otherItem->remark = $fee->remark;
                            $item->addOtherExpenseItem($otherItem);
                        }
                        
                    }
                }

                if (is_array($p->deliveryAttachments) && !empty($p->deliveryAttachments))
                {
                    foreach ($p->deliveryAttachments as $attachment)
                    {
                        if($attachment->type==3)//结算单据
                            $item->addReceiptAttachment($this->getAttachmentEntity($attachment));
                        else if($attachment->type==4)//其他附件
                            $item->addOtherAttachment($this->getAttachmentEntity($attachment));
                    }
                }

                $entity->addGoodsExpenseSettlementItem($item);
            }
        }

        return $entity;
    }



    /**
     * 把对象持久化到数据库
     * @param IAggregateRoot $entity
     * @return bool
     * @throws \Exception
     */
    public function store(IAggregateRoot $entity)
    {
        if(empty($entity))
            throw new ZException("DeliveryOrderSettlement对象不存在");
        
        $contract = \Contract::model()->findByPk($entity->contract_id);
        if(empty($contract))
            throw new ZModelNotExistsException($entity->contract_id, "Contract");
        if(empty($contract->settle_type)){
            $contract->settle_type = SettlementMode::DELIVERY_ORDER_MODE_SETTLEMENT;
            $res = $contract->save();
            if (!$res)
                throw new ZModelSaveFalseException($contract);
        }else{
            if($contract->settle_type != SettlementMode::DELIVERY_ORDER_MODE_SETTLEMENT)
                throw new ZException(BusinessError::Delivery_Order_Cannot_Settle,array("contract_code"=>$contract->contract_code));
        }

        $contractSettlement = \ContractSettlement::model()->find("contract_id=".$entity->contract_id);
        if(empty($contractSettlement)){
            $contractSettlement = new \ContractSettlement();
            $contractSettlement->settle_id = \IDService::getContractSettlementId();
            $contractSettlement->code = \IDService::getContractSettlementCode();
            $contractSettlement->type = SettlementMode::DELIVERY_ORDER_MODE_SETTLEMENT;
            $contractSettlement->contract_id    = $entity->contract_id;
            $contractSettlement->project_id     = $contract->project_id;
            $contractSettlement->currency       = $entity->settle_currency->id;
            $contractSettlement->status         = SettlementStatus::STATUS_NEW;
        }

        /*$contractSettlement->amount_goods += empty($entity->goods_amount) ? 0 : $entity->goods_amount;
        $contractSettlement->amount = $contractSettlement->amount_goods;*/
        $res = $contractSettlement->save();
        if (!$res)
            throw new ZModelSaveFalseException($contractSettlement);
        
        $id=$entity->getId();

        if(!empty($id))
            $model = \DeliverySettlement::model()->with($this->with)->findByPk($id);


        if(empty($model))
        {
            $model = new \DeliverySettlement();
            $model->settle_id = \IDService::getDeliverySettlementId();
            $model->code = \IDService::getDeliverySettlementCode();
        }

        
        $model->contract_id = $entity->contract_id;
        $model->order_id = $entity->order_id;
        $model->project_id = $contract->project_id;
        $model->currency = $entity->settle_currency->id;
        $model->amount_goods = empty($entity->goods_amount) ? 0 : $entity->goods_amount;
        // $model->amount_other = $entity->other_amount;
        $model->amount = $model->amount_goods;
        $model->status = $entity->status;
        $model->settle_date = $entity->settle_date;

        $isNew = $model->isNewRecord;

        $items = $entity->goods_expense;
        if (!is_array($items))
            $items = array();



        $res = $model->save();
        if (!$res)
            throw new ZModelSaveFalseException($model);

        if (!$isNew)
        {
            if (is_array($model->contractSettlementGoods) && !empty($model->contractSettlementGoods))
            {
                foreach ($model->contractSettlementGoods as $p)
                {
                    $item = $items[$p->goods_id];
                    $p->quantity_bill = $item->out_quantity->quantity;
                    // $p->quantity_actual_sub = $item->out_quantity_sub->quantity;
                    $p->quantity = $item->settle_quantity->quantity;
                    // $p->quantity_sub  = $item->settle_quantity_sub->quantity;
                    $p->exchange_rate = $item->exchange_rate;
                    $p->quantity_loss = $item->loss_quantity->quantity;
                    // $p->quantity_loss_sub = $item->loss_quantity_sub->quantity;
                    $p->price = $item->settle_price;
                    $p->amount = $item->settle_amount;
                    $p->price_cny = $item->settle_price_cny;
                    $p->amount_cny = $item->settle_amount_cny;
                    $p->remark = $item->remark;
                    $res = $p->save();
                    if (!$res)
                        throw new ZModelSaveFalseException($p);

                    $settleItem = \DeliverySettlementDetail::model()->findByPk($p->item_id);
                    if(empty($settleItem))
                        throw new ZModelNotExistsException($p->item_id, "DeliverySettlementDetail");

                    $settleItem->quantity = $item->out_quantity->quantity;
                    $settleItem->quantity_settle = $item->settle_quantity->quantity;
                    $settleItem->exchange_rate = $item->exchange_rate;
                    $settleItem->quantity_loss = $item->loss_quantity->quantity;
                    $settleItem->price = $item->settle_price;
                    $settleItem->amount = $item->settle_amount;
                    $settleItem->price_cny = $item->settle_price_cny;
                    $settleItem->amount_cny = $item->settle_amount_cny;
                    $settleItem->remark = $item->remark;

                    $res = $settleItem->save();
                    if (!$res)
                        throw new ZModelSaveFalseException($settleItem);
                    /*if (is_array($item->order_items) && !empty($item->order_items))
                    {
                        foreach ($item->order_items as $order)
                        {
                            $settleItem->quantity = $order->out_quantity->quantity;
                            // $settleItem->quantity_sub = $order->out_quantity_sub->quantity;
                            $settleItem->quantity_settle = $order->settle_quantity->quantity;
                            // $settleItem->quantity_settle_sub = $order->settle_quantity_sub->quantity;
                            $settleItem->exchange_rate = $order->exchange_rate;
                            $settleItem->quantity_loss = $order->loss_quantity->quantity;
                            // $settleItem->quantity_loss_sub = $order->loss_quantity_sub->quantity;
                            $settleItem->price = $order->settle_price;
                            $settleItem->amount = $order->settle_amount;
                            $settleItem->price_cny = $order->settle_price_cny;
                            $settleItem->amount_cny = $order->settle_amount_cny;
                            $settleItem->remark = $order->remark;

                            $res = $settleItem->save();
                            if (!$res)
                                throw new ZModelSaveFalseException($settleItem);
                        }
                    }else{
                        throw new ZException("DeliveryOrderSettlementItem对象不存在");
                    }*/

                    // 判断货款明细提交时有没有，
                    // 有：更新，没有：删除
                    // 获取调整明细和货款明细
                    $goodsDetail= \ContractSettlementGoodsDetail::model()->with('goodsItems')->find("t.item_id=".$p->item_id);
                    if(!$item->isHaveDetail){
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
                        if(empty($goodsDetail)){
                            $contractGoods = TradeGoodsRepository::repository()->findByContractIdAndGoodsId($entity->contract_id, $p->goods_id);
                            if(empty($contractGoods))
                                throw new ZException("ContractGoods对象不存在");
                            foreach ($item->goods_expense_items as $goods)
                            {                                
                                $settleGoodsItem = new \ContractSettlementGoodsDetail();
                                $settleGoodsItem->contract_id = $entity->contract_id;
                                $settleGoodsItem->item_id = $p->item_id;
                                $settleGoodsItem->goods_id = $p->goods_id;
                                $settleGoodsItem->unit = $contractGoods->unit;
                                
                                $settleGoodsItem->currency = $goods->currency->id;
                                $settleGoodsItem->amount_currency = $goods->amount;
                                $settleGoodsItem->exchange_rate = $goods->exchange_rate;
                                $settleGoodsItem->amount_goods = $goods->amount_cny;
                                $settleGoodsItem->price_goods = $goods->price;
                                $settleGoodsItem->exchange_rate_tax = $goods->tax_exchange_rate;
                                $settleGoodsItem->amount_goods_tax = $goods->tax_amount_cny;
                            }

                            if(!empty($item->adjustment_items)) {
                                foreach ($item->adjustment_items as $adjust) {
                                    $settleGoodsItem->adjust_type = $adjust->adjust_type->id;
                                    $settleGoodsItem->amount_adjust = $adjust->adjust_amount;
                                    $settleGoodsItem->adjust_reason = $adjust->adjust_reason;
                                    // $settleGoodsItem->quantity = $adjust->settle_quantity->quantity;
                                    // $settleGoodsItem->quantity_sub = $adjust->settle_quantity_sub->quantity;
                                    $settleGoodsItem->price = $adjust->settle_price_cny;
                                    $settleGoodsItem->amount = $adjust->settle_amount_cny;
                                    $settleGoodsItem->amount_actual = $adjust->confirm_amount_cny;
                                    $settleGoodsItem->price_actual = $adjust->confirm_price_cny;
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
                                    $taxItem->contract_id = $entity->contract_id;
                                    $taxItem->item_id = $p->item_id;
                                    $taxItem->detail_id = $settleGoodsItem->detail_id;
                                    $taxItem->subject_id = $tax->tax->id;
                                    $taxItem->type = \ContractSettlementGoodsDetailItem::TAX_RATE_TYPE;
                                    $taxItem->rate = $tax->tax_rate;
                                    $taxItem->amount = $tax->tax_amount;
                                    $taxItem->price = $tax->tax_price;
                                    $taxItem->remark = $tax->remark;

                                    $res = $taxItem->save();
                                    if (!$res)
                                        throw new ZModelSaveFalseException($taxItem);
                                }
                            }

                            if(!empty($item->other_expense_items)){
                                foreach ($item->other_expense_items as $expense) {
                                    $otherItem = new \ContractSettlementGoodsDetailItem();
                                    $otherItem->contract_id = $entity->contract_id;
                                    $otherItem->item_id = $p->item_id;
                                    $otherItem->detail_id = $settleGoodsItem->detail_id;
                                    $otherItem->subject_id = $expense->expense->id;
                                    $otherItem->type = \ContractSettlementGoodsDetailItem::OTHER_EXPENSE_TYPE;
                                    $otherItem->amount = $expense->expense_amount;
                                    $otherItem->price = $expense->expense_price;
                                    $otherItem->remark = $expense->remark;

                                    $res = $otherItem->save();
                                    if (!$res)
                                        throw new ZModelSaveFalseException($otherItem);
                                }
                            }

                        }else{
                            foreach ($item->goods_expense_items as $goods)
                            {
                                $goodsDetail->currency = $goods->currency->id;
                                $goodsDetail->amount_currency = $goods->amount;
                                $goodsDetail->exchange_rate = $goods->exchange_rate;
                                $goodsDetail->amount_goods = $goods->amount_cny;
                                $goodsDetail->price_goods = $goods->price;
                                $goodsDetail->exchange_rate_tax = $goods->tax_exchange_rate;
                                $goodsDetail->amount_goods_tax = $goods->tax_amount_cny;
                            }
                            if(!empty($item->adjustment_items)) {
                                foreach ($item->adjustment_items as $adjust) {
                                    $goodsDetail->adjust_type = $adjust->adjust_type->id;
                                    $goodsDetail->amount_adjust = $adjust->adjust_amount;
                                    $goodsDetail->adjust_reason = $adjust->adjust_reason;
                                    /*$goodsDetail->quantity = $adjust->settle_quantity->quantity;
                                    $goodsDetail->quantity_sub = $adjust->settle_quantity_sub->quantity;*/
                                    $goodsDetail->price = $adjust->settle_price_cny;
                                    $goodsDetail->amount = $adjust->settle_amount_cny;
                                    $goodsDetail->amount_actual = $adjust->confirm_amount_cny;
                                    $goodsDetail->price_actual = $adjust->confirm_price_cny;
                                }
                            }else{
                                throw new ZException("AdjustmentItem对象不存在");
                            }
    
                            $res = $goodsDetail->save();
                            if (!$res)
                                throw new ZModelSaveFalseException($goodsDetail);

                            $goodsItem  = $goodsDetail->goodsItems;
                            $taxes      = array();
                            $expenses   = array();
                            if(empty($goodsItem)){
                                if(!empty($item->tax_items)){
                                    foreach ($item->tax_items as $tax) {
                                        $taxItem = new \ContractSettlementGoodsDetailItem();
                                        $taxItem->contract_id = $entity->contract_id;
                                        $taxItem->item_id = $p->item_id;
                                        $taxItem->detail_id = $goodsDetail->detail_id;
                                        $taxItem->subject_id = $tax->tax->id;
                                        $taxItem->type = \ContractSettlementGoodsDetailItem::TAX_RATE_TYPE;
                                        $taxItem->rate = $tax->tax_rate;
                                        $taxItem->amount = $tax->tax_amount;
                                        $taxItem->price = $tax->tax_price;
                                        $taxItem->remark = $tax->remark;
    
                                        $res = $taxItem->save();
                                        if (!$res)
                                            throw new ZModelSaveFalseException($taxItem);
                                    }
                                }
    
                                if(!empty($item->other_expense_items)){
                                    foreach ($item->other_expense_items as $expense) {
                                        $otherItem = new \ContractSettlementGoodsDetailItem();
                                        $otherItem->contract_id = $entity->contract_id;
                                        $otherItem->item_id = $p->item_id;
                                        $otherItem->detail_id = $goodsDetail->detail_id;
                                        $otherItem->subject_id = $expense->expense->id;
                                        $otherItem->type = \ContractSettlementGoodsDetailItem::OTHER_EXPENSE_TYPE;
                                        $otherItem->amount = $expense->expense_amount;
                                        $otherItem->price = $expense->expense_price;
                                        $otherItem->remark = $expense->remark;
    
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
                                            $taxItem->contract_id = $entity->contract_id;
                                            $taxItem->item_id = $p->item_id;
                                            $taxItem->detail_id = $goodsDetail->detail_id;
                                            $taxItem->subject_id = $tax->tax->id;
                                            $taxItem->type = \ContractSettlementGoodsDetailItem::TAX_RATE_TYPE;
                                            $taxItem->rate = $tax->tax_rate;
                                            $taxItem->amount = $tax->tax_amount;
                                            $taxItem->price = $tax->tax_price;
                                            $taxItem->remark = $tax->remark;
        
                                            $res = $taxItem->save();
                                            if (!$res)
                                                throw new ZModelSaveFalseException($taxItem);
                                        }
                                    }else{
                                        $taxEntity = $item->tax_items;
                                        foreach ($taxes as $tax) {
                                            if(isset($taxEntity[$tax->subject_id])){
                                                $tItem = $taxEntity[$tax->subject_id];
                                                $tax->rate = $tItem->tax_rate;
                                                $tax->amount = $tItem->tax_amount;
                                                $tax->price = $tItem->tax_price;
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
                                                $taxItem->contract_id = $entity->contract_id;
                                                $taxItem->item_id = $p->item_id;
                                                $taxItem->detail_id = $goodsDetail->detail_id;
                                                $taxItem->subject_id = $te->tax->id;
                                                $taxItem->type = \ContractSettlementGoodsDetailItem::TAX_RATE_TYPE;
                                                $taxItem->rate = $te->tax_rate;
                                                $taxItem->amount = $te->tax_amount;
                                                $taxItem->price = $te->tax_price;
                                                $taxItem->remark = $te->remark;
            
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
                                            $otherItem->contract_id = $entity->contract_id;
                                            $otherItem->item_id = $p->item_id;
                                            $otherItem->detail_id = $goodsDetail->detail_id;
                                            $otherItem->subject_id = $expense->expense->id;
                                            $otherItem->type = \ContractSettlementGoodsDetailItem::OTHER_EXPENSE_TYPE;
                                            $otherItem->amount = $expense->expense_amount;
                                            $otherItem->price = $expense->expense_price;
                                            $otherItem->remark = $expense->remark;
        
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
                                                $expense->price = $fItem->expense_price;
                                                $expense->remark = $fItem->remark;
                                                
                                                $res = $expense->save();
                                                if (!$res)
                                                    throw new ZModelSaveFalseException($expense);

                                                unset($feeEntity[$expense->subject_id]);
                                            }else{
                                                $res = $expense->delete();
                                                if (!$res)
                                                    throw new ZModelDeleteFalseException($expense);
                                            }
                                        }

                                        if (is_array($feeEntity) && count($feeEntity) > 0){
                                            foreach ($feeEntity as $fe) {
                                                $feeItem = new \ContractSettlementGoodsDetailItem();
                                                $feeItem->contract_id = $entity->contract_id;
                                                $feeItem->item_id = $p->item_id;
                                                $feeItem->detail_id = $goodsDetail->detail_id;
                                                $feeItem->subject_id = $fe->expense->id;
                                                $feeItem->type = \ContractSettlementGoodsDetailItem::OTHER_EXPENSE_TYPE;
                                                $feeItem->amount = $fe->expense_amount;
                                                $feeItem->price = $fe->expense_price;
                                                $feeItem->remark = $fe->remark;
            
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

                    unset($items[$p->goods_id]);
                }
            }
        }


        if (is_array($items) && count($items) > 0)
        {
            foreach ($items as $item)
            {
                $contractGoods = TradeGoodsRepository::repository()->findByContractIdAndGoodsId($entity->contract_id, $item->goods_id);
                if(empty($contractGoods))
                    throw new ZException("ContractGoods对象不存在");

                $settleGoods = new \ContractSettlementGoods();
                $settleGoods->item_id = $item->item_id;
                $settleGoods->settle_id = $model->settle_id;
                $settleGoods->project_id = $contract->project_id;
                $settleGoods->contract_id = $entity->contract_id;
                $settleGoods->relation_id = $entity->order_id;
                $settleGoods->goods_id = $item->goods_id;
                $settleGoods->unit = $contractGoods->unit;
//                $settleGoods->unit_sub = $contractGoods->unit_store;
                $settleGoods->quantity_bill = $item->out_quantity->quantity;
//                $settleGoods->quantity_actual_sub = $item->out_quantity_sub->quantity;
                $settleGoods->quantity = $item->settle_quantity->quantity;
//                $settleGoods->quantity_sub  = $item->settle_quantity_sub->quantity;
                $settleGoods->exchange_rate = $item->exchange_rate;
                $settleGoods->quantity_loss = $item->loss_quantity->quantity;
//                $settleGoods->quantity_loss_sub = $item->loss_quantity_sub->quantity;
                $settleGoods->price = $item->settle_price;
                $settleGoods->amount = $item->settle_amount;
                $settleGoods->price_cny = $item->settle_price_cny;
                $settleGoods->amount_cny = $item->settle_amount_cny;
                $settleGoods->remark = $item->remark;
                $res = $settleGoods->save();
                if (!$res)
                    throw new ZModelSaveFalseException($settleGoods);


                $settleItem = new \DeliverySettlementDetail();
                $settleItem->item_id = $item->item_id;
                $settleItem->settle_id = $model->settle_id;
                $settleItem->project_id = $contract->project_id;
                $settleItem->contract_id = $entity->contract_id;
                $settleItem->order_id = $entity->order_id;
                $settleItem->goods_id = $item->goods_id;
                $settleItem->unit = $contractGoods->unit;
                $settleItem->quantity = $item->out_quantity->quantity;
                $settleItem->quantity_settle = $item->settle_quantity->quantity;
                $settleItem->exchange_rate = $item->exchange_rate;
                $settleItem->quantity_loss = $item->loss_quantity->quantity;
                $settleItem->price = $item->settle_price;
                $settleItem->amount = $item->settle_amount;
                $settleItem->price_cny = $item->settle_price_cny;
                $settleItem->amount_cny = $item->settle_amount_cny;
                $settleItem->remark = $item->remark;

                $res = $settleItem->save();
                if (!$res)
                    throw new ZModelSaveFalseException($settleItem);

                /*if (is_array($item->order_items) && !empty($item->order_items))
                {
                    foreach ($item->order_items as $order)
                    {                            
                        $settleItem = new \DeliverySettlementDetail();
                        $settleItem->item_id = $item->item_id;
                        $settleItem->settle_id = $model->settle_id;
                        $settleItem->project_id = $contract->project_id;
                        $settleItem->contract_id = $entity->contract_id;
                        $settleItem->order_id = $entity->order_id;
                        $settleItem->goods_id = $order->goods_id;
                        $settleItem->unit = $contractGoods->unit;
//                        $settleItem->unit_sub = $contractGoods->unit_store;
                        $settleItem->quantity = $order->out_quantity->quantity;
//                        $settleItem->quantity_sub = $order->out_quantity_sub->quantity;
                        $settleItem->quantity_settle = $order->settle_quantity->quantity;
//                        $settleItem->quantity_settle_sub = $order->settle_quantity_sub->quantity;
                        $settleItem->exchange_rate = $order->exchange_rate;
                        $settleItem->quantity_loss = $order->loss_quantity->quantity;
//                        $settleItem->quantity_loss_sub = $order->loss_quantity_sub->quantity;
                        $settleItem->price = $order->settle_price;
                        $settleItem->amount = $order->settle_amount;
                        $settleItem->price_cny = $order->settle_price_cny;
                        $settleItem->amount_cny = $order->settle_amount_cny;
                        $settleItem->remark = $order->remark;

                        $res = $settleItem->save();
                        if (!$res)
                            throw new ZModelSaveFalseException($settleItem);
                    }
                }else{
                    throw new ZException("DeliveryOrderSettlementItem对象不存在");
                }*/

                if($item->isHaveDetail && !empty($item->goods_expense_items)){
                    foreach ($item->goods_expense_items as $goods)
                    {                                
                        $settleGoodsItem = new \ContractSettlementGoodsDetail();
                        $settleGoodsItem->contract_id = $entity->contract_id;
                        $settleGoodsItem->item_id = $item->item_id;
                        $settleGoodsItem->goods_id = $item->goods_id;
                        $settleGoodsItem->unit = $contractGoods->unit;
                        
                        $settleGoodsItem->currency = $goods->currency->id;
                        $settleGoodsItem->amount_currency = $goods->amount;
                        $settleGoodsItem->exchange_rate = $goods->exchange_rate;
                        $settleGoodsItem->amount_goods = $goods->amount_cny;
                        $settleGoodsItem->price_goods = $goods->price;
                        $settleGoodsItem->exchange_rate_tax = $goods->tax_exchange_rate;
                        $settleGoodsItem->amount_goods_tax = $goods->tax_amount_cny;
                    }

                    if(!empty($item->adjustment_items)) {
                        foreach ($item->adjustment_items as $adjust) {
                            $settleGoodsItem->adjust_type = $adjust->adjust_type->id;
                            $settleGoodsItem->amount_adjust = $adjust->adjust_amount;
                            $settleGoodsItem->adjust_reason = $adjust->adjust_reason;
                            // $settleGoodsItem->quantity = $adjust->settle_quantity->quantity;
                            // $settleGoodsItem->quantity_sub = $adjust->settle_quantity_sub->quantity;
                            $settleGoodsItem->price = $adjust->settle_price_cny;
                            $settleGoodsItem->amount = $adjust->settle_amount_cny;
                            $settleGoodsItem->amount_actual = $adjust->confirm_amount_cny;
                            $settleGoodsItem->price_actual = $adjust->confirm_price_cny;
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
                            $taxItem->contract_id = $entity->contract_id;
                            $taxItem->item_id = $item->item_id;
                            $taxItem->detail_id = $settleGoodsItem->detail_id;
                            $taxItem->subject_id = $tax->tax->id;
                            $taxItem->type = \ContractSettlementGoodsDetailItem::TAX_RATE_TYPE;
                            $taxItem->rate = $tax->tax_rate;
                            $taxItem->amount = $tax->tax_amount;
                            $taxItem->price = $tax->tax_price;
                            $taxItem->remark = $tax->remark;

                            $res = $taxItem->save();
                            if (!$res)
                                throw new ZModelSaveFalseException($taxItem);
                        }
                    }

                    if(!empty($item->other_expense_items)){
                        foreach ($item->other_expense_items as $expense) {
                            $otherItem = new \ContractSettlementGoodsDetailItem();
                            $otherItem->contract_id = $entity->contract_id;
                            $otherItem->item_id = $item->item_id;
                            $otherItem->detail_id = $settleGoodsItem->detail_id;
                            $otherItem->subject_id = $expense->expense->id;
                            $otherItem->type = \ContractSettlementGoodsDetailItem::OTHER_EXPENSE_TYPE;
                            $otherItem->amount = $expense->expense_amount;
                            $otherItem->price = $expense->expense_price;
                            $otherItem->remark = $expense->remark;

                            $res = $otherItem->save();
                            if (!$res)
                                throw new ZModelSaveFalseException($otherItem);
                        }
                    }

                }

            }
        }


        return true;

    }


    /**
     * 查询合同下所有的发货结算单
     * @param contractId
     * @return DeliveryOrderSettlement
     */
    public function findAllByContractId($contractId)
    {
        $condition = "t.contract_id=" . $contractId;

        return $this->findAll($condition);
    }


    /**
     * 更新发货单结算单状态
     * @param $entity
     * @return bool
     * @throws \Exception
     */
    protected function updateStatus(DeliveryOrderSettlement $entity)
    {
        if(empty($entity))
            throw new ZException("DeliveryOrderSettlement对象不存在");

        $model=\DeliverySettlement::model()->findByPk($entity->settle_id);
        if(empty($model))
            throw new ZModelNotExistsException($entity->settle_id, "DeliverySettlement");

        if($model->status != $entity->status)
        {
            $model->status = $entity->status;
            $res = $model->save();
            if(!$res)
                throw new ZModelSaveFalseException($model);
        }

        return true;
    }


    /**
     * 保存提交
     * @param DeliveryOrderSettlement $settlement
     * @throws \Exception
     */
    public function submit(DeliveryOrderSettlement $settlement)
    {
        $this->updateStatus($settlement);
    }

    /**
     * 作废
     * @param DeliveryOrderSettlement $settlement
     * @throws \Exception
     */
    public function trash(DeliveryOrderSettlement $settlement)
    {
        $this->updateStatus($settlement);
    }

    /**
     * 驳回
     * @param DeliveryOrderSettlement $settlement
     * @throws \Exception
     */
    public function back(DeliveryOrderSettlement $settlement)
    {
        $this->updateStatus($settlement);
    }

    /**
     * 设置为结算完成
     * @param DeliveryOrderSettlement $settlement
     * @throws \Exception
     */
    public function setSettled(DeliveryOrderSettlement $settlement)
    {
        $this->updateStatus($settlement);
    }

}

