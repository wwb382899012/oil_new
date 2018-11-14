<?php

/**
 * @Name            预估合同利润
 * @DateTime        2018年8月27日 16:16:30
 * @Author          vector
 */



namespace ddd\Profit\Domain\EstimateProfit;

use ddd\Common\Domain\BaseService;
use ddd\Common\Domain\Value\Money;

use ddd\Common\Domain\Value\Quantity;
use ddd\domain\entity\value\Price;

use ddd\Profit\Domain\Contract\Contract;
use ddd\Profit\Domain\Model\Project;
use ddd\Profit\Domain\Price\PriceService;
use ddd\Profit\Domain\Service\ProfitService;
use ddd\Profit\Domain\EstimateProfit\EstimateContractProfitRepository;


class EstimateContractProfitService extends BaseService

{
    use EstimateContractProfitRepository;

    /**
     * createEstimateContractProfit  创建预估合同利润
     * @param * @param Contract $contract
     * @param bool $persistent
       @throw * @throws \ddd\infrastructure\error\ZException
     * @return static
     */
    public function createEstimateContractProfit(Contract $contract,$persistent=false)
    {
        $EstimateContractProfitEntity=EstimateContractProfit::create($contract);
        $goods_items = $contract->getGoodsItems();
        $sum_quantity= 0 ;
        $sum_amount = 0;
        $sum_buy_amount = 0;
        if(!empty($goods_items)){
            foreach($goods_items as $key=>$value){
                $quantity_ton = $value->quantity->quantity/$value->t_exchange_rate;//单位转换为吨
                $sum_quantity += $quantity_ton;
                $price = PriceService::service()->getSellPrice($contract->project_id,$value->goods_id);
                $sum_amount += $quantity_ton*$price;

                $buy_price = PriceService::service()->getBuyPrice($contract->project_id,$value->goods_id);
                $sum_buy_amount += $quantity_ton*$buy_price;

            }
        }
        $EstimateContractProfitEntity->sell_income = new EstimateCostInfo(new Quantity($sum_quantity), new Money($sum_amount));

        $EstimateContractProfitEntity->buy_cost = new EstimateCostInfo(new Quantity($sum_quantity), new Money($sum_buy_amount));

        if($persistent)
            $this->getEstimateContractProfitRepository()->store($EstimateContractProfitEntity);

        return $EstimateContractProfitEntity;
    }
}

