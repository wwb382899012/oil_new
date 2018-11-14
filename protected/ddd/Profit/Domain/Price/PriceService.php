<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/13 16:04
 * Describe：
 */

namespace ddd\Profit\Domain\Price;


use ddd\Common\Domain\BaseService;
use ddd\Common\Domain\Value\Money;
use ddd\Profit\Domain\Contract\Contract;
use ddd\Profit\Domain\EstimateProfit\EstimateContractProfitRepository;
use ddd\Profit\Domain\EstimateProfit\EstimateCostInfo;
use ddd\Profit\Domain\Model\Settlement\DeliveryOrderSettlement;
use ddd\Profit\Domain\Model\Settlement\LadingBillSettlement;
use ddd\Profit\Domain\Service\EventService;
use ddd\infrastructure\error\ZEntityNotExistsException;
use ddd\infrastructure\error\ZException;

class PriceService extends BaseService
{   
    use EstimateContractProfitRepository;

    /**
     * [getBuyPrice 获取商品采购单价]
     * @param
     * @param  [bigint] $projectId [项目id]
     * @param  [int] $goodsId   [商品id]
     * @return [int]
     */
    public function getBuyPrice($projectId,$goodsId)
    {
        $model = \GoodsPriceDetail::model()->find(array("condition"=>"project_id=".$projectId." and goods_id=".$goodsId." and type=1 and is_settled=1","order"=>"price_id desc"));
        if(empty($model))
            $model = \GoodsPriceDetail::model()->find(array("condition"=>"project_id=".$projectId." and goods_id=".$goodsId." and type=1 and is_settled=0","order"=>"price_id desc"));
        return !empty($model->price_cny) ? $model->price_cny : 0;

    }

    /**
     * [getBuyPrice 获取项目下所有采购合同商品]
     * @param
     * @param  [bigint] $projectId [项目id]
     * @return [int]
     */
    public function getBuyAllGoods($projectId)
    {
        $result = \Utility::query('
                    select goods_id from t_goods_price_detail a where a.project_id='.$projectId.' and a.type=1 group by a.goods_id
            ');
        return $result;
    }

    /**
     * [getBuyPrice 获取项目下所有销售合同商品]
     * @param
     * @param  [bigint] $projectId [项目id]
     * @return [int]
     */
    public function getSellAllGoods($projectId)
    {
        $result = \Utility::query('
                    select goods_id from t_goods_price_detail a where a.project_id='.$projectId.' and a.type=2 group by a.goods_id
            ');
        return $result;
    }

    /**
     * [isHaveSellSettled 判断对应项目商品是否有结算单价]
     * @param
     * @param  [bigint]  $projectId [项目id]
     * @param  [int]  $goodsId   [商品id]
     * @param  [int]  $type   [类型 销售或采购]
     * @return boolean
     */
    public function isHaveSettledPrice($projectId,$goodsId,$type)
    {
        $model = \GoodsPriceDetail::model()->find(array("condition"=>"project_id=".$projectId." and goods_id=".$goodsId." and type=".$type." and is_settled=1"));
        if(!empty($model))
            return true;
        return false;
    }

    /**
     * [getSellPrice 获取商品销售单价]
     * @param
     * @param  [bigint] $projectId [项目id]
     * @param  [int] $goodsId   [商品id]
     * @return [int]
     */
    public function getSellPrice($projectId,$goodsId)
    {
        $model = \GoodsPriceDetail::model()->find(array("condition"=>"project_id=".$projectId." and goods_id=".$goodsId." and type=2 and is_settled=1","order"=>"price_id desc"));
        if(empty($model))
            $model = \GoodsPriceDetail::model()->find(array("condition"=>"project_id=".$projectId." and goods_id=".$goodsId." and type=2 and is_settled=0","order"=>"price_id desc"));
        return !empty($model->price_cny) ? $model->price_cny : 0;
    }

    // ------以下事件可以按照合同id发送，只要将变更前后的值分两个数组传送给监听对象------

    /**
     * [updateSellPriceByContract 根据销售合同更新销售单价]
     * @param
     * @param  Contract $contract [description]
     * @return [type]
     */
    public function updateSellPriceByContract(Contract $contract)
    {
        if (empty($contract))
            throw new ZException("Contract对象不存在");

        $isCanUpdate = false;
        $priceArr    = array();
        $items       = $contract->getGoodsItems();
       /* if(!empty($items) && is_array($items)){
            foreach ($items as $item) {
                $isSettled = $this->isHaveSettledPrice($contract->project_id, $item->goods_id, $contract->type);
                if(!$isSettled)
                    $isCanUpdate = true;
                $priceArr[$item->goods_id] = $this->getSellPrice($contract->project_id, $item->goods_id);
            }
        }*/

        $goodsArr = $this->getSellAllGoods($contract->project_id);
        if(!empty($goodsArr)){
            foreach ($goodsArr as $k=>$v) {
                $priceArr[$v['goods_id']] = $this->getSellPrice($contract->project_id,$v['goods_id']);
            }
        }

        $contractProfitArr = array();
        if(!empty($priceArr)){
            $contractProfitArr = $this->getEstimateContractProfitRepository()->findAllByProjectId($contract->project_id);
            if(!empty($contractProfitArr) && is_array($contractProfitArr)){
                foreach ($contractProfitArr as $profitEntity) {
                    $goodsItems = $profitEntity->goodsItems;
                    $sellAmount = 0;
                    if(!empty($goodsItems) && is_array($goodsItems)){
                        foreach ($goodsItems as $goods) {
                            if(array_key_exists($goods->goods_id, $priceArr)){
                                $goods->sell_price = new Money($priceArr[$goods->goods_id], $goods->sell_price->currency->id);
                                $sellAmount += round($goods->sell_price->amount * ($goods->buy_quantity->quantity - $goods->out_quantity->quantity));
                            }
                        }
                    }

                    if($sellAmount > 0) {
                        $sell_amount = new Money($sellAmount, $profitEntity->sell_income->amount->currency->id);
                        $profitEntity->sell_income = new EstimateCostInfo($profitEntity->sell_income->quantity, $sell_amount);
                    }
                    $this->getEstimateContractProfitRepository()->store($profitEntity);

                }
            }

            \AMQPService::publishEstimateContractProfit($contract->project_id);
            EventService::service()->store($contract->contract_id, \Event::ESTIMATE_CONTRACT_PROFIT_EVENT_BY_PRICE, \Event::EstimateContractProfitEventByPrice);
        }
    }

    /**
     * [updateBuyPriceByContract 根据采购合同更新采购单价]
     * @param
     * @param  Contract $contract [description]
     * @return [type]
     */
    public function updateBuyPriceByContract(Contract $contract)
    {
        if (empty($contract))
            throw new ZException("Contract对象不存在");

        $isCanUpdate = false;
        $priceArr    = array();
        $items       = $contract->getGoodsItems();

        /*if(!empty($items) && is_array($items)){
            foreach ($items as $item) {
                $isSettled = $this->isHaveSettledPrice($contract->project_id, $item->goods_id, $contract->type);
                if(!$isSettled)
                    $isCanUpdate = true;
                $priceArr[$item->goods_id] = $this->getBuyPrice($contract->project_id, $item->goods_id);

            }
        }*/
        $goodsArr = $this->getBuyAllGoods($contract->project_id);
        if(!empty($goodsArr)){
            foreach ($goodsArr as $k=>$v) {
                $priceArr[$v['goods_id']] = $this->getBuyPrice($contract->project_id,$v['goods_id']);
            }
        }

        $contractProfitArr = array();
        if( !empty($priceArr)){
            $contractProfitArr = $this->getEstimateContractProfitRepository()->findAllByProjectId($contract->project_id);

            if(!empty($contractProfitArr) && is_array($contractProfitArr)){
                foreach ($contractProfitArr as $profitEntity) {
                    $goodsItems = $profitEntity->goodsItems;
                    $buyAmount = 0;
                    if(!empty($goodsItems) && is_array($goodsItems)){
                        foreach ($goodsItems as $goods) {
                            if(array_key_exists($goods->goods_id, $priceArr)){
                                $goods->buy_price  = new Money($priceArr[$goods->goods_id], $goods->buy_price->currency->id);
                                $buyAmount        += round($goods->buy_price->amount * ($goods->buy_quantity->quantity - $goods->out_quantity->quantity));
                            }
                        }
                    }

                    if($buyAmount > 0) {
                        $buy_amount = new Money($buyAmount, $profitEntity->buy_cost->amount->currency->id);
                        $profitEntity->buy_cost = new EstimateCostInfo($profitEntity->buy_cost->quantity, $buy_amount);
                    }
                    $this->getEstimateContractProfitRepository()->store($profitEntity);

                }
            }

            \AMQPService::publishEstimateContractProfit($contract->project_id);
            EventService::service()->store($contract->contract_id, \Event::ESTIMATE_CONTRACT_PROFIT_EVENT_BY_PRICE, \Event::EstimateContractProfitEventByPrice);
            
        }

    }


    /**
     * [updateBuyPriceByLadingSettlement 根据入库通知结算单更新采购单价]
     * @param
     * @param  LadingBillSettlement $ladingSettlement [description]
     * @return [type]
     */
    public function updateBuyPriceByLadingSettlement(LadingBillSettlement $ladingSettlement)
    {
        if (empty($ladingSettlement))
        {
            throw new ZException("LadingBillSettlement对象不存在");
        }

        $isCanUpdate = false;
        $priceArr    = array();
        /*$items       = $ladingSettlement->settle_items;
        if(!empty($items) && is_array($items)){
            foreach ($items as $item) {
                $priceArr[$item->goods_id] = $item->price_cny->amount;
            }
        }*/
        $goodsArr = $this->getBuyAllGoods($ladingSettlement->project_id);
        if(!empty($goodsArr)){
            foreach ($goodsArr as $k=>$v) {
                $priceArr[$v['goods_id']] = $this->getBuyPrice($ladingSettlement->project_id,$v['goods_id']);
            }
        }

        $contractProfitArr = array();
        if(!empty($priceArr)){
            $contractProfitArr = $this->getEstimateContractProfitRepository()->findAllByProjectId($ladingSettlement->project_id);
            if(!empty($contractProfitArr) && is_array($contractProfitArr)){
                foreach ($contractProfitArr as $profitEntity) {
                    $goodsItems = $profitEntity->goodsItems;
                    $buyAmount  = 0;
                    if(!empty($goodsItems) && is_array($goodsItems)){
                        foreach ($goodsItems as $goods) {
                            if(array_key_exists($goods->goods_id, $priceArr)){
                                $goods->buy_price  = new Money($priceArr[$goods->goods_id], $goods->buy_price->currency->id);
                                $buyAmount        += round($goods->buy_price->amount * ($goods->buy_quantity->quantity - $goods->out_quantity->quantity));

                            }
                        }
                    }

                    if($buyAmount > 0) {
                        $buy_amount = new Money($buyAmount, $profitEntity->buy_cost->amount->currency->id);
                        $profitEntity->buy_cost = new EstimateCostInfo($profitEntity->buy_cost->quantity, $buy_amount);
                    }
                    $this->getEstimateContractProfitRepository()->store($profitEntity);

                }

                \AMQPService::publishEstimateContractProfit($ladingSettlement->project_id);
                EventService::service()->store($ladingSettlement->bill_id, \Event::ESTIMATE_CONTRACT_PROFIT_EVENT_BY_PRICE, \Event::EstimateContractProfitEventByPrice);
            }
        }
    }


    public function updateSellPriceByDeliverySettlement(DeliveryOrderSettlement $deliverySettlement)
    {
        if (empty($deliverySettlement))
        {
            throw new ZException("DeliveryOrderSettlement对象不存在");
        }

        $priceArr    = array();
        /*$items       = $deliverySettlement->settle_items;
        if(!empty($items) && is_array($items)){
            foreach ($items as $item) {
                $priceArr[$item->goods_id] = $item->price_cny->amount;
            }
        }*/
        $goodsArr = $this->getSellAllGoods($deliverySettlement->project_id);
        if(!empty($goodsArr)){
            foreach ($goodsArr as $k=>$v) {
                $priceArr[$v['goods_id']] = $this->getSellPrice($deliverySettlement->project_id,$v['goods_id']);
            }
        }

        $contractProfitArr = array();
        if(!empty($priceArr)){
            $contractProfitArr = $this->getEstimateContractProfitRepository()->findAllByProjectId($deliverySettlement->project_id);
            if(!empty($contractProfitArr) && is_array($contractProfitArr)){
                foreach ($contractProfitArr as $profitEntity) {
                    $goodsItems = $profitEntity->goodsItems;
                    $sellAmount = 0;
                    if(!empty($goodsItems) && is_array($goodsItems)){
                        foreach ($goodsItems as $goods) {
                            if(array_key_exists($goods->goods_id, $priceArr)){
                                $goods->sell_price = new Money($priceArr[$goods->goods_id], $goods->sell_price->currency->id);
                                $sellAmount       += round($goods->sell_price->amount * ($goods->buy_quantity->quantity - $goods->out_quantity->quantity));
                            }
                        }
                    }

                    if($sellAmount > 0) {
                        $sell_amount = new Money($sellAmount, $profitEntity->sell_income->amount->currency->id);
                        $profitEntity->sell_income = new EstimateCostInfo($profitEntity->sell_income->quantity, $sell_amount);
                    }
                    $this->getEstimateContractProfitRepository()->store($profitEntity);

                }

                \AMQPService::publishEstimateContractProfit($deliverySettlement->project_id);
                EventService::service()->store($deliverySettlement->bill_id, \Event::ESTIMATE_CONTRACT_PROFIT_EVENT_BY_SELL_PRICE, \Event::EstimateContractProfitEventBySellPrice);
            }
        }
    }


}