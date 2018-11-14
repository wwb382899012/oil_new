<?php

/**
 * Created by vector.
 * DateTime: 2018/4/4 17:41
 * Describe：采购合同结算单仓储
 */

namespace ddd\repository\contractSettlement;

use ddd\domain\entity\contractSettlement\LadingBillSettlement;
use ddd\Common\IAggregateRoot;
use ddd\domain\entity\contractSettlement\AdjustmentItem;
use ddd\domain\entity\contractSettlement\BuyContractSettlement;
use ddd\domain\entity\contractSettlement\GoodsExpenseItem;
use ddd\domain\entity\contractSettlement\GoodsExpenseSettlementItem;
use ddd\domain\entity\contractSettlement\LadingBillSettlementItem;
use ddd\domain\entity\contractSettlement\OtherExpenseItem;
use ddd\domain\entity\contractSettlement\OtherExpenseSettlementItem;
use ddd\domain\entity\contractSettlement\SettlementMode;
use ddd\domain\entity\contractSettlement\SettlementStatus;
use ddd\domain\entity\contractSettlement\TaxItem;
use ddd\domain\entity\value\AdjustMode;
use ddd\domain\entity\value\Currency;
use ddd\domain\entity\value\Expense;
use ddd\domain\entity\value\OtherFee;
use ddd\domain\entity\value\Quantity;
use ddd\domain\entity\value\Tax;
use ddd\infrastructure\Utility;

use ddd\infrastructure\error\ZException;
use ddd\infrastructure\error\ZModelDeleteFalseException;
use ddd\infrastructure\error\ZModelNotExistsException;
use ddd\infrastructure\error\ZModelSaveFalseException;
use ddd\Common\Repository\EntityRepository;
use ddd\repository\contractSettlement\LadingBillSettlementRepository;
use ddd\repository\contract\TradeGoodsRepository;
use ddd\repository\stock\LadingBillRepository;


class BuyContractSettlementRepository extends EntityRepository
{

	public function init()
    {
        $this->with=array("contractSettlementSubjectDetail","contractSettlementSubjectDetail.otherAttachments","contractSettlementGoods","contractSettlementGoods.ladings","contractSettlementGoods.settleGoods","contractSettlementGoods.fees","contractSettlementGoods.goodsAttachments");
    }

    /**
     * 获取对应的数据模型的类名
     * @return string
     */
    public function getActiveRecordClassName()
    {
        return "ContractSettlement";
    }

    public function getNewEntity()
    {
        return new BuyContractSettlement();
    }

    

    /**
     * 数据模型转换成业务对象
     * @param $model
     * @return LadingBillStatement|Entity
     * @throws \Exception
     */
    public function dataToEntity($model)
    {
        $entity = $this->getNewEntity();
        $entity->setAttributes($model->getAttributes(),false);
        $entity->settle_currency = Currency::getCurrency($model->currency);
        $entity->goods_amount = $model->amount_goods;
        $entity->other_amount = $model->amount_other;
        $entity->total_amount = $model->amount;
        $entity->settle_type = $model->type;

        $contract = \Contract::model()->findByPk($model->contract_id);
        if($contract->settle_type==SettlementMode::LADING_BILL_MODE_SETTLEMENT){
            $settlements = LadingBillSettlementRepository::repository()->findAllByContractId($model->contract_id);
            if(empty($settlements))
                throw new ZException("LadingBillSettlement对象不存在");
                
            $entity->lading_bills = $settlements;
        }else{
            if(is_array($model->contractSettlementGoods) && !empty($model->contractSettlementGoods))
            {
                $ladingBills = LadingBillRepository::repository()->findAllByContractId($entity->contract_id);
                $ladingArr = array();
                if(!empty($ladingBills) && count($ladingBills)>0){
                    foreach ($ladingBills as $ladingBill) {
                        if(!empty($ladingBill->items)){
                            foreach ($ladingBill->items as $item){
                                $ladingArr[$item->goods_id][$ladingBill->id]['in_quantity'] = $item->in_quantity;
                                $ladingArr[$item->goods_id][$ladingBill->id]['in_quantity_sub'] = $item->in_quantity_sub;
                            }
                        }
                    }
                }

                $contractQuantity = LadingBillRepository::repository()->getContractGoodsInQuantity($entity->contract_id);

                foreach ($model->contractSettlementGoods as $p)
                {
                    $in_quantity = $contractQuantity[$p->goods_id]['in_quantity'];
                    $in_quantity_sub = $contractQuantity[$p->goods_id]['in_quantity_sub'];

                    $item=GoodsExpenseSettlementItem::create($p->goods_id);
                    $item->item_id          = $p->item_id;
                    $item->in_quantity      = new Quantity($in_quantity,$p->unit);
                    $item->settle_quantity  = new Quantity($p->quantity, $p->unit);
                    $item->loss_quantity    = new Quantity(($in_quantity - $p->quantity), $p->unit);
                    if(!empty($p->settleSub)){
                        $item->in_quantity_sub  = new Quantity($in_quantity_sub,$p->settleSub->unit);
                        $item->settle_quantity_sub  = new Quantity($p->settleSub->quantity, $p->settleSub->unit);
                        $item->loss_quantity_sub    = new Quantity(($in_quantity_sub - $p->settleSub->quantity), $p->settleSub->unit);
                    }
                    
                    $item->exchange_rate    = $p->exchange_rate;
                    $item->settle_price     = $p->price;
                    $item->settle_amount    = $p->amount;
                    $item->settle_price_cny     = $p->price_cny;
                    $item->settle_amount_cny    = $p->amount_cny;
                    $item->remark           = $p->remark;

                    $idArr = array();
                    if(is_array($p->ladings) && !empty($p->ladings)){
                        foreach ($p->ladings as $lading) {
                            $idArr[] =  $lading->batch_id;

                            $in_quantity_lading = $contractQuantity[$p->goods_id][$lading->batch_id]['in_quantity'];
                            $in_quantity_lading_sub = $contractQuantity[$p->goods_id][$lading->batch_id]['in_quantity_sub'];

                            $ladingItem = LadingBillSettlementItem::create($lading->goods_id);
                            $ladingItem->item_id = $lading->item_id;
                            $ladingItem->batch_id = $lading->batch_id;
                            $ladingItem->in_quantity      = new Quantity($in_quantity_lading,$lading->unit);
                            $ladingItem->settle_quantity  = new Quantity($lading->quantity, $lading->unit);
                            $ladingItem->loss_quantity        = new Quantity(($in_quantity_lading - $lading->quantity), $lading->unit);
                            if(!empty($lading->sub)){
                                $ladingItem->in_quantity_sub  = new Quantity($in_quantity_lading_sub,$lading->sub->unit);
                                $ladingItem->settle_quantity_sub  = new Quantity($lading->sub->quantity, $lading->sub->unit);
                                $ladingItem->loss_quantity_sub    = new Quantity(($in_quantity_lading_sub - $lading->sub->quantity), $lading->sub->unit);
                            }
                            $ladingItem->exchange_rate    = $lading->exchange_rate;
                            $ladingItem->settle_price     = $lading->price;
                            $ladingItem->settle_amount    = $lading->amount;
                            $ladingItem->settle_price_cny     = $lading->price_cny;
                            $ladingItem->settle_amount_cny    = $lading->amount_cny;
                            $ladingItem->remark           = $lading->remark;

                            $item->addLadingItem($ladingItem);
                        }
                    }

                    if(!empty($ladingArr) && !empty($ladingArr[$p->goods_id])){
                        foreach ($ladingArr[$p->goods_id] as $id=>$lading){
                            if(!in_array($id, $idArr)){
                                $ladingItem = LadingBillSettlementItem::create($p->goods_id);
                                $ladingItem->batch_id = $id;
                                $ladingItem->in_quantity = $lading['in_quantity'];
                                $ladingItem->settle_quantity = $lading['in_quantity'];
                                $ladingItem->loss_quantity = new Quantity(0, $lading['in_quantity']->unit);

                                if(!empty($lading['in_quantity_sub']->unit) && $lading['in_quantity']->unit != $lading['in_quantity_sub']->unit){
                                    $ladingItem->in_quantity_sub = $lading['in_quantity_sub'];
                                    $ladingItem->settle_quantity_sub = $lading['in_quantity_sub'];
                                    $ladingItem->loss_quantity_sub = new Quantity(0, $lading['in_quantity_sub']->unit);
                                }

                                $ladingItem->exchange_rate    = $p->exchange_rate;
                                $ladingItem->settle_price     = $p->price;
                                $ladingItem->settle_amount    = $lading['in_quantity']->quantity * $p->price ;
                                $ladingItem->settle_price_cny     = $p->price_cny;
                                $ladingItem->settle_amount_cny    = $lading['in_quantity']->quantity * $p->price_cny ;

                                $item->addLadingItem($ladingItem);
                            }
                        }
                    }
    
    
                    if(!empty($p->settleGoods)){
                        $item->isHaveDetail = true;

                        $settleGoods = $p->settleGoods;;
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
    
                    if (is_array($p->goodsAttachments) && !empty($p->goodsAttachments))
                    {
                        foreach ($p->goodsAttachments as $attachment)
                        {
                            if($attachment->type==1)
                                $item->addReceiptAttachment($this->getAttachmentEntity($attachment));
                            else if($attachment->type==2)
                                $item->addOtherAttachment($this->getAttachmentEntity($attachment));
                        }
                    }
    
                    $entity->addGoodsExpenseSettlementItem($item);
                }
            }
        }
        

        if(is_array($model->contractSettlementSubjectDetail) && !empty($model->contractSettlementSubjectDetail)){
            foreach ($model->contractSettlementSubjectDetail as $d) {
                $detail = OtherExpenseSettlementItem::create();
                $detail->detail_id = $d->detail_id;
                $detail->fee = OtherFee::getOtherFee($d->subject_id);
                $detail->currency = Currency::getCurrency($d->currency);
                $detail->amount = $d->amount;
                $detail->exchange_rate = $d->exchange_rate;
                $detail->amount_cny = $d->amount_cny;
                $detail->remark = $d->remark;

                if (is_array($d->otherAttachments) && !empty($d->otherAttachments))
                {
                    foreach ($d->otherAttachments as $file)
                    {
                        if($file->type==101)
                            $detail->addReceiptAttachment($this->getAttachmentEntity($file));
                    }
                }

                $entity->addOtherExpenseSettlementItem($detail);
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
       // print_r($entity);die;
        if(empty($entity))
            throw new ZException("BuyContractSettlement对象不存在");

        $contract = \Contract::model()->findByPk($entity->contract_id);
        if(empty($contract))
            throw new ZModelNotExistsException($entity->contract_id,'Contract');

        if(empty($contract->settle_type)){
            $contract->settle_type = SettlementMode::BUY_CONTRACT_MODE_SETTLEMENT;
            $res = $contract->save();
            if (!$res)
                throw new ZModelSaveFalseException($contract);
        }

        $id=$entity->getId();
        if(!empty($id))
            $model = \ContractSettlement::model()->with($this->with)->findByPk($id);

        if(empty($model))
        {
            $model = new \ContractSettlement();
            $model->settle_id = \IDService::getContractSettlementId();
            $model->code = \IDService::getContractSettlementCode();
            $model->type = SettlementMode::BUY_CONTRACT_MODE_SETTLEMENT;
        }


        $model->contract_id     = $entity->contract_id;
        $model->project_id      = $contract->project_id;
        $model->currency        = $entity->settle_currency->id;
        $model->amount_goods    = empty($entity->goods_amount) ? 0 : $entity->goods_amount;
        $model->amount_other    = empty($entity->other_amount) ? 0 : $entity->other_amount;
        $model->amount          = $model->amount_goods + $model->amount_other;
        $model->settle_date     = $entity->settle_date;
        $model->status          = $entity->status;
        $model->remark          = $entity->remark;

        $isNew = $model->isNewRecord;

        $items = $entity->goods_expense;
        if (!is_array($items))
            $items = array();

        $res = $model->save();
        if (!$res)
            throw new ZModelSaveFalseException($model);

        /*$old_ladings = array();
        $new_ladings = array();*/

        if($contract->settle_type==SettlementMode::BUY_CONTRACT_MODE_SETTLEMENT){
            if (!$isNew)
            {
                if (is_array($model->contractSettlementGoods) && !empty($model->contractSettlementGoods))
                {
                    foreach ($model->contractSettlementGoods as $p)
                    {
                        $item = $items[$p->goods_id];
                        $p->quantity_bill = $item->in_quantity->quantity;
                        $p->quantity_actual_sub  = $item->in_quantity_sub->quantity;
                        $p->quantity = $item->settle_quantity->quantity;
                        $p->quantity_sub  = $item->settle_quantity_sub->quantity;
                        $p->exchange_rate = $item->exchange_rate;
                        $p->quantity_loss = $item->loss_quantity->quantity;
                        $p->quantity_loss_sub = $item->loss_quantity_sub->quantity;
                        $p->price = $item->settle_price;
                        $p->amount = $item->settle_amount;
                        $p->price_cny = $item->settle_price_cny;
                        $p->amount_cny = $item->settle_amount_cny;
                        $p->remark = $item->remark;
                        $res = $p->save();
                        if (!$res)
                            throw new ZModelSaveFalseException($p);
    
                        
                        if (is_array($item->lading_items) && !empty($item->lading_items))
                        {
                            foreach ($item->lading_items as $lading)
                            {                            
                                if(!empty($lading->item_id)){
                                    $settleItem = \StockBatchSettlement::model()->findByPk($lading->item_id);
                                    if(empty($settleItem))
                                        throw new ZModelNotExistsException($lading->item_id, 'StockBatchSettlement');
                                }else{
                                    $settleItem = new \StockBatchSettlement();
                                    $settleItem->item_id = \IDService::getGoodsExpenseSettlementId();
                                    $settleItem->settle_id = $model->settle_id;
                                    $settleItem->project_id = $contract->project_id;
                                    $settleItem->contract_id = $entity->contract_id;
                                    $settleItem->batch_id = $lading->batch_id;
                                    $settleItem->goods_id = $lading->goods_id;
                                    $settleItem->unit = $p->unit;
                                    $settleItem->unit_sub = $p->unit_sub;
                                }

                               // $old_ladings[$lading->batch_id][$lading->goods_id] = $lading;

                                $settleItem->quantity_bill = $lading->in_quantity->quantity;
                                $settleItem->quantity_actual_sub = $lading->in_quantity_sub->quantity;
                                $settleItem->quantity = $lading->settle_quantity->quantity;
                                $settleItem->quantity_sub = $lading->settle_quantity_sub->quantity;
                                $settleItem->exchange_rate = $lading->exchange_rate;
                                $settleItem->quantity_loss = $lading->loss_quantity->quantity;
                                $settleItem->quantity_loss_sub = $lading->loss_quantity_sub->quantity;
                                $settleItem->price = $lading->settle_price;
                                $settleItem->amount = $lading->settle_amount;
                                $settleItem->price_cny = $lading->settle_price_cny;
                                $settleItem->amount_cny = $lading->settle_amount_cny;
                                $settleItem->remark = $lading->remark;
    
                                $res = $settleItem->save();
                                if (!$res)
                                    throw new ZModelSaveFalseException($settleItem);
                            }
                        }else{
                            throw new ZException("LadingBillSettlementItem对象不存在");
                        }

                        /*if(!empty($old_ladings)){
                            foreach ($old_ladings as $k => $old_lading) {
                                $ladingSettlement = \LadingSettlement::model()->find("lading_id=".$k);
                                if(empty($ladingSettlement))
                                    throw new ZException("LadingSettlement对象不存在");
                                $goods_amount = 0;

                                foreach ($old_lading as $v) {
                                    $settle_amount_cny = empty($v->settle_amount_cny) ? 0 : $v->settle_amount_cny;
                                    $goods_amount += $settle_amount_cny;
                                }
                                $ladingSettlement->amount_goods = $goods_amount;
                                $ladingSettlement->amount = $goods_amount;
                                $ladingSettlement->status = $entity->status;
                                $ladingSettlement->settle_date = $entity->settle_date;
                                $res = $ladingSettlement->save();
                                if (!$res)
                                    throw new ZModelSaveFalseException($ladingSettlement);
                            }
                        }else{
                            if(empty($old_ladings))
                                throw new ZException("LadingSettlement对象不存在");
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
    
                                $goodsItem = $goodsDetail->goodsItems;
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
                                                        throw new ZModelDeleteFalseException($tax);
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
                    // $settleGoods->relation_id = $entity->batch_id;
                    $settleGoods->goods_id = $item->goods_id;
                    $settleGoods->unit = $contractGoods->unit;
                    $settleGoods->unit_sub = $contractGoods->unit_store;
                    $settleGoods->quantity_bill = $item->in_quantity->quantity;
                    $settleGoods->quantity_actual_sub = $item->in_quantity_sub->quantity;
                    $settleGoods->quantity = $item->settle_quantity->quantity;
                    $settleGoods->quantity_sub  = $item->settle_quantity_sub->quantity;
                    $settleGoods->exchange_rate = $item->exchange_rate;
                    $settleGoods->quantity_loss = $item->loss_quantity->quantity;
                    $settleGoods->quantity_loss_sub = $item->loss_quantity_sub->quantity;
                    $settleGoods->price = $item->settle_price;
                    $settleGoods->amount = $item->settle_amount;
                    $settleGoods->price_cny = $item->settle_price_cny;
                    $settleGoods->amount_cny = $item->settle_amount_cny;
                    $settleGoods->remark = $item->remark;
                    $res = $settleGoods->save();
                    if (!$res)
                        throw new ZModelSaveFalseException($settleGoods);
    
                    if (is_array($item->lading_items) && !empty($item->lading_items))
                    {
                        foreach ($item->lading_items as $lading)
                        {                            
                           // $new_ladings[$lading->batch_id][$lading->goods_id] = $lading;

                            $settleItem = new \StockBatchSettlement();
                            $settleItem->item_id = \IDService::getGoodsExpenseSettlementId();
                            $settleItem->settle_id = $model->settle_id;
                            $settleItem->project_id = $contract->project_id;
                            $settleItem->contract_id = $entity->contract_id;
                            $settleItem->batch_id = $lading->batch_id;
                            $settleItem->goods_id = $lading->goods_id;
                            $settleItem->unit = $contractGoods->unit;
                            $settleItem->unit_sub = $contractGoods->unit_store;
                            $settleItem->quantity_bill = $lading->in_quantity->quantity;
                            $settleItem->quantity_actual_sub = $lading->in_quantity_sub->quantity;
                            $settleItem->quantity = $lading->settle_quantity->quantity;
                            $settleItem->quantity_sub = $lading->settle_quantity_sub->quantity;
                            $settleItem->exchange_rate = $lading->exchange_rate;
                            $settleItem->quantity_loss = $lading->loss_quantity->quantity;
                            $settleItem->quantity_loss_sub = $lading->loss_quantity_sub->quantity;
                            $settleItem->price = $lading->settle_price;
                            $settleItem->amount = $lading->settle_amount;
                            $settleItem->price_cny = $lading->settle_price_cny;
                            $settleItem->amount_cny = $lading->settle_amount_cny;
                            $settleItem->remark = $lading->remark;
    
                            $res = $settleItem->save();
                            if (!$res)
                                throw new ZModelSaveFalseException($settleItem);
                        }
                    }else{
                        throw new ZException("LadingBillSettlementItem对象不存在");
                    }

                    /*if(!empty($new_ladings)){
                        foreach ($new_ladings as $k => $new_lading) {
                            $ladingSettlement = new \LadingSettlement();
                            $ladingSettlement->settle_id = \IDService::getLadingSettlementId();
                            $ladingSettlement->code = \IDService::getLadingSettlementCode();
                            $ladingSettlement->project_id   = $contract->project_id;
                            $ladingSettlement->contract_id  = $entity->contract_id;
                            $ladingSettlement->lading_id = $k;
                            $ladingSettlement->currency = $entity->settle_currency->id;

                            $goods_amount = 0;
                            foreach ($new_lading as $v) {
                                $settle_amount_cny = empty($v->settle_amount_cny) ? 0 : $v->settle_amount_cny;
                                $goods_amount += $settle_amount_cny;
                            }
                            $ladingSettlement->amount_goods = $goods_amount;
                            $ladingSettlement->amount = $goods_amount;
                            $ladingSettlement->status = $entity->status;
                            $ladingSettlement->settle_date = $entity->settle_date;
                            $res = $ladingSettlement->save();
                            if (!$res)
                                throw new ZModelSaveFalseException($ladingSettlement);
                        }
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
        }

        if(empty($entity->other_expense) && !empty($model->contractSettlementSubjectDetail)){
            foreach ($model->contractSettlementSubjectDetail as $s) {
                $res = $s->delete();
                if (!$res)
                    throw new ZModelDeleteFalseException($s);
            }
        }else if(!empty($entity->other_expense)){
            if(empty($model->contractSettlementSubjectDetail)){
                foreach ($entity->other_expense as $ot)
                {
                    $otherExpenseItem = new \ContractSettlementSubjectDetail();
                    if(!empty($ot->detail_id))
                        $otherExpenseItem->detail_id = $ot->detail_id;
                    else
                        $otherExpenseItem->detail_id = \IDService::getOtherExpenseSettlementId();
                    $otherExpenseItem->settle_id = $model->settle_id;
                    $otherExpenseItem->contract_id = $entity->contract_id;
                    $otherExpenseItem->project_id = $contract->project_id;
                    $otherExpenseItem->subject_id = $ot->fee->id;
                    $otherExpenseItem->currency = $ot->currency->id;
                    $otherExpenseItem->amount = $ot->amount;
                    $otherExpenseItem->exchange_rate = $ot->exchange_rate;
                    $otherExpenseItem->amount_cny = $ot->amount_cny;
                    $otherExpenseItem->remark = $ot->remark;
                    $otherExpenseItem->status_time = Utility::getDateTime();
                    
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
                $otherEntity = $entity->other_expense;
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
                            $subItem->detail_id = \IDService::getOtherExpenseSettlementId();
                        $subItem->settle_id = $model->settle_id;
                        $subItem->contract_id = $entity->contract_id;
                        $subItem->project_id = $contract->project_id;
                        $subItem->subject_id = $oe->fee->id;
                        $subItem->currency = $oe->currency->id;
                        $subItem->amount = $oe->amount;
                        $subItem->exchange_rate = $oe->exchange_rate;
                        $subItem->amount_cny = $oe->amount_cny;
                        $subItem->remark = $oe->remark;
                        $subItem->status_time = Utility::getDateTime();
                        
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
        


        return true;

    }


    /**
     * 查询采购合同结算单
     * @param contractId
     * @return BuyContractSettlement
     */
    public function findContractSettlement($contractId)
    {
        $condition = "t.contract_id=" . $contractId;

        $model=$this->model()->find($condition);
        if(empty($model))
            return null;
        
        $entity = $this->getNewEntity();
        $entity->setAttributes($model->getAttributes(),false);
        $entity->settle_currency = Currency::getCurrency($model->currency);
        $entity->goods_amount = $model->amount_goods;
        $entity->total_amount = $model->amount;
//        $entity->settle_date  = Utility::getDate();

        return $entity;
    }


    /**
     * 更新采购合同结算单状态
     * @param $entity
     * @return bool
     * @throws \Exception
     */
    public function updateStatus(BuyContractSettlement $entity)
    {
        if(empty($entity))
            throw new ZException("BuyContractSettlement对象不存在");

        $model=\ContractSettlement::model()->findByPk($entity->settle_id);
        if(empty($model))
            throw new ZModelNotExistsException($entity->settle_id, "ContractSettlement");

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
     * @param BuyContractSettlement $settlement
     * @param $amount
     * @throws ZException
     */
    public function addAndSaveGoodsAmount(BuyContractSettlement $settlement,$amount)
    {
        try
        {
            $this->addAndSaveAmount($settlement->getId(),$amount);
        }
        catch (\Exception $e)
        {
            throw new ZException("增加采购合同结算单货款金额失败");
        }
    }

    /**
     * 更新指定金额
     * @param $id
     * @param $amount
     * @throws ZException
     */
    protected function addAndSaveAmount($id,$amount)
    {
        $rows=\ContractSettlement::model()->updateByPk($id
            ,array(
                "amount_goods"=>new \CDbExpression("amount_goods+".$amount),
                "amount"=>new \CDbExpression("amount+".$amount),
                "update_time"=>new \CDbExpression("now()")
            )
        );
        if($rows!==1)
            throw new ZException("更新金额失败");
    }


    /**
     * 保存提交
     * @param BuyContractSettlement $settlement
     * @throws \Exception
     */
    public function submit(BuyContractSettlement $settlement)
    {
        $this->updateStatus($settlement);
    }

    /**
     * 作废
     * @param BuyContractSettlement $settlement
     * @throws \Exception
     */
    public function trash(BuyContractSettlement $settlement)
    {
        $this->updateStatus($settlement);
    }

    /**
     * 驳回
     * @param BuyContractSettlement $settlement
     * @throws \Exception
     */
    public function back(BuyContractSettlement $settlement)
    {
        $this->updateStatus($settlement);
    }

    /**
     * 设置为结算完成
     * @param BuyContractSettlement $settlement
     * @throws \Exception
     */
    public function setSettled(BuyContractSettlement $settlement)
    {
        $this->updateStatus($settlement);
    }



}

