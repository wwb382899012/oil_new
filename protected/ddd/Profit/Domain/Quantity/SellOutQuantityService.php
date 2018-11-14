<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/10 15:43
 * Describe：
 */

namespace ddd\Profit\Domain\Quantity;


use ddd\Common\Domain\BaseService;
use ddd\Common\Domain\Value\Money;
use ddd\Common\Domain\Value\Quantity;
use ddd\Profit\Domain\Contract\Contract;
use ddd\Profit\Domain\EstimateProfit\EstimateCostInfo;
use ddd\Profit\Domain\Model\Settlement\DeliveryOrderSettlement;
use ddd\Profit\Domain\Quantity\SellOutQuantity;
use ddd\Profit\Domain\Quantity\SellOutQuantityRepository;
use ddd\Profit\Domain\EstimateProfit\EstimateContractProfitRepository;
use ddd\Profit\Domain\Contract\ContractRepository;

class SellOutQuantityService extends BaseService
{

    use SellOutQuantityRepository;
    use EstimateContractProfitRepository;
    use ContractRepository;

    /**
     * createSellOutQuantity  创建结算出库数量
     * @param * @param DeliveryOrderSettlement $deliveryOrderSettlement
     * @param bool $persistent
       @throw * @throws \ddd\Profit\Domain\Quantity\ZException
     * @return mixed
     */
    public function createSellOutQuantity(DeliveryOrderSettlement $deliveryOrderSettlement,$persistent=false)
    {

        $SellOutQuantityEntity=SellOutQuantity::create($deliveryOrderSettlement);
        if($persistent)
            $return = $this->getSellOutQuantityRepository()->store($SellOutQuantityEntity);

        return $return;
    }

    /**
     * [getBuyPrice 获取项目下所有销售合同商品]
     * @param
     * @param  [bigint] $projectId [项目id]
     * @return [int]
     */
    public function getGoodsOutQuantity($contractId,$goods_id)
    {
        $result = \Utility::query('
                    select sum(ifnull(a.out_quantity,0)) out_quantity from t_goods_out_quantity_detail a where a.contract_id='.$contractId.' and a.goods_id='.$goods_id.' group by a.goods_id
            ');
        return empty($result)?0:$result[0]['out_quantity'];
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
                    select a.goods_id from t_goods_price_detail a where a.project_id='.$projectId.' group by a.goods_id
            ');
        return $result;
    }

    /**
     * updateEstimateSellQuantity
     * @param * @param \ddd\Profit\Domain\Quantity\SellOutQuantity $sellOutQuantity
     * @throw * @throws ZException
     * @return void
     */
    public function updateEstimateSellQuantity(SellOutQuantity $sellOutQuantity)
    {
        if (empty($sellOutQuantity))
            throw new ZException("SellOutQuantity对象不存在");

        $outArr = array();
        $items  = $sellOutQuantity->out_items;
        $contract  = $this->getContractRepository()->findByContractId($sellOutQuantity->contract_id);
        $contractArr=array();
        if(!empty($items)){
            foreach($items as $i){
                if(!in_array($i->contract_id,$contractArr))
                array_push($contractArr,$i->contract_id);
            }
        }
        $contract_id = empty($items)?0:$items[0]->contract_id;
        if(empty($contract_id)){
            $result = \Utility::query('select contract_id from t_contract where type=1 and project_id='.$contract->project_id);
            //$contract_id = empty($result)?0:$result[0]['contract_id'];
        }
        $goodsArr = $this->getSellAllGoods($contract->project_id);

        if(!empty($contractArr)){
            foreach($contractArr as $con){
                if(!empty($goodsArr)) {
                    foreach ($goodsArr as $k=>$v) {
                        $outArr[$con][$v['goods_id']] = $this->getGoodsOutQuantity($contract_id,$v['goods_id']);

                    }
                }
            }
        }

        $contractProfitArr = array();
        if(!empty($outArr)){
            foreach ($outArr as $contractId=>$out) {
                $contractProfitArrTemp = $this->getEstimateContractProfitRepository()->findByContractId($contractId);
                $contractProfitArr[0]=$contractProfitArrTemp;

                if(!empty($contractProfitArr) && is_array($contractProfitArr)){
                    foreach ($contractProfitArr as $profitEntity) {
                        $goodsItems    = $profitEntity->goodsItems;
                        $buyAmount     = 0;
                        $sellAmount    = 0;
                        $totalQuantity = 0;

                        if(!empty($goodsItems) && is_array($goodsItems)){
                            foreach ($goodsItems as $goods) {
                                if(array_key_exists($goods->goods_id, $out)){

                                    //$goods->out_quantity = new Quantity($goods->out_quantity->quantity + $out[$goods->goods_id]);
                                    $goods->out_quantity = new Quantity($out[$goods->goods_id]);
                                    $sellQuantity        = $goods->buy_quantity->quantity - $goods->out_quantity->quantity;

                                    $buyAmount          += round($goods->buy_price->amount * $sellQuantity);
                                    $sellAmount         += round($goods->sell_price->amount * $sellQuantity);

                                    $totalQuantity      += $sellQuantity;
                                }
                            }
                        }

                        //if($sellAmount > 0){
                            $buy_amount     = new Money($buyAmount, $profitEntity->buy_cost->amount->currency->id);
                            $sell_amount    = new Money($sellAmount, $profitEntity->sell_income->amount->currency->id);
                            $total_quantity = new Quantity($totalQuantity);
                            $profitEntity->buy_cost    = new EstimateCostInfo($total_quantity, $buy_amount);
                            $profitEntity->sell_income = new EstimateCostInfo($total_quantity, $sell_amount);


                            $this->getEstimateContractProfitRepository()->store($profitEntity);
                        //}
                    }

                    $projectId = $contractProfitArr->projectId;
                }
            }

            if(!empty($projectId)){
                \AMQPService::publishEstimateContractProfit($projectId);
                EventService::service()->store($sellOutQuantity->bill_id, \Event::ESTIMATE_CONTRACT_PROFIT_EVENT_BY_QUANTITY, \Event::EstimateContractProfitEventByQuantity);
            }
        }
        
    }



}