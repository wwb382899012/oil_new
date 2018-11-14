<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/10 15:43
 * Describe：
 */

namespace ddd\Profit\Domain\EstimateProfit;


use ddd\Common\Domain\BaseService;
use ddd\Common\Domain\Value\Money;
use ddd\domain\entity\contract\Contract;

use ddd\Common\Domain\Value\Quantity;
use ddd\domain\entity\value\Price;
use ddd\Profit\Domain\EstimateProfit\EstimateContractProfitRepository;
use ddd\Profit\Domain\EstimateProfit\EstimateProjectProfitRepository;
use ddd\Profit\Domain\EstimateProfit\EstimateCorporationProfitRepository;
use ddd\Profit\Domain\Model\Corporation;
use ddd\Profit\Domain\Model\Project;
use ddd\Profit\Domain\Service\ProfitService;

class EstimateCorporationProfitService extends BaseService
{

    use EstimateCorporationProfitRepository;
    use EstimateProjectProfitRepository;

    /**
     * @name:createCorporationProfit
     * @desc: 创建交易主体利润 对象
     * @param:* @param Project $project
     * @param bool $persistent
       @throw: * @throws \Exception
     * @return:static
     */
    public function createCorporationProfit(Corporation $corporation,$persistent=false)
    {

        $estimateCorporationProfitEntity=EstimateCorporationProfit::create($corporation);
        $estimateProjectProfitList = $this->getEstimateProjectProfitRepository()->findByCorporationId($corporation->corporation_id);

        $estimateCorporationProfitEntity = $this->computeTotalPrice($estimateProjectProfitList,$estimateCorporationProfitEntity);
       
        if($persistent)
            $this->getEstimateCorporationProfitRepository()->store($estimateCorporationProfitEntity);

        return $estimateCorporationProfitEntity;
    }

    /**
     * @name:computeTotalPrice
     * @desc: 计算合计
     * @param:* @param $profitList
     * @param $entity
       @throw:
     * @return:mixed
     */
    protected function computeTotalPrice($profitList,$entity){
        $sell_quantity=$sell_amount=$buy_price=$buy_amount=0;
        $invoice_amount=0;
        $gross_profit=$added_tax=$surtax=$stamp_tax=$post_profit=$fund_cost=$actual_profit=0;

        if(!empty($profitList)){
            foreach($profitList as $key=>$item){

                $sell_quantity += $item->sell_quantity->quantity;
                $sell_amount += $item->sell_amount->amount;
                //$buy_price += $item->buy_cost->buy_price->price;
                $buy_amount += $item->buy_amount->amount;

                $invoice_amount+=$item->invoice_amount->amount;

                $gross_profit+=$item->gross_profit->amount;

                $added_tax+=$item->added_tax->amount;
                $surtax+=$item->surtax->amount;
                $stamp_tax+=$item->stamp_tax->amount;
                $post_profit+=$item->post_profit->amount;
                $fund_cost+=$item->fund_cost->amount;
                $actual_profit += $item->actual_profit ->amount;
            }

        }

        $entity->sell_quantity=new Quantity($sell_quantity,\ConstantMap::UNIT_TON);
        $entity->sell_amount=new Money($sell_amount,\ConstantMap::CURRENCY_RMB);

        $buy_price = $sell_quantity==0?$buy_amount:$buy_amount/$sell_quantity;
        $entity->buy_price = new Money($buy_price,\ConstantMap::CURRENCY_RMB);
        $entity->buy_amount = new Money($buy_amount,\ConstantMap::CURRENCY_RMB);


        $entity->invoice_amount = new Money($invoice_amount,\ConstantMap::CURRENCY_RMB);

        $entity->gross_profit = new Money($gross_profit,\ConstantMap::CURRENCY_RMB);

        $entity->added_tax = new Money($added_tax,\ConstantMap::CURRENCY_RMB);
        $entity->surtax = new Money($surtax,\ConstantMap::CURRENCY_RMB);
        $entity->stamp_tax = new Money($stamp_tax,\ConstantMap::CURRENCY_RMB);
        $entity->post_profit = new Money($post_profit,\ConstantMap::CURRENCY_RMB);
        $entity->fund_cost = new Money($fund_cost,\ConstantMap::CURRENCY_RMB);
        $entity->actual_profit = new Money($actual_profit,\ConstantMap::CURRENCY_RMB);

        return $entity;
    }


}