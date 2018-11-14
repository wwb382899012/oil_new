<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/10 15:43
 * Describe：
 */

namespace ddd\Profit\Domain\Model\Profit;


use ddd\Common\Domain\BaseService;
use ddd\domain\entity\contract\Contract;
use ddd\Profit\Domain\Model\Stock\DeliveryOrder;
use ddd\Profit\Domain\Model\Profit\BuyCost;
use ddd\Profit\Domain\Model\Profit\SellProfit;
use ddd\infrastructure\DIService;
use ddd\Profit\Domain\Model\Profit\IDeliveryOrderProfitRepository;
use ddd\Profit\Domain\Model\Stock\IDeliveryOrderDetailRepository;
use ddd\Profit\Domain\Model\Stock\IBuyGoodsCostRepository;
use ddd\Common\Domain\Value\Quantity;
use ddd\domain\entity\value\Price;
use ddd\Profit\Domain\Model\Stock\BuyGoodsCost;
use ddd\Profit\Domain\Model\Profit\SellContractProfitRepository;

class SellContractProfitService extends BaseService
{

    use SellContractProfitRepository;

    /**
     * @name:createSellContractProfit
     * @desc: 创建合同利润 对象
     * @param:* @param Contract $contract
     * @param bool $persistent
       @throw: * @throws \Exception
     * @return:static
     */
    public function createSellContractProfit(Contract $contract,$persistent=false)
    {

        $sellContractProfitEntity=SellContractProfit::create($contract);

        $deliveryOrderProfitList = DIService::getRepository(IDeliveryOrderProfitRepository::class)->findByContractId($contract->contract_id);

        $sellContractProfitEntity = $this->computeTotalPrice($deliveryOrderProfitList,$sellContractProfitEntity);
        //print_r($sellContractProfitEntity);
        if($persistent)
            $this->getSellContratcProfitRepository()->store($sellContractProfitEntity);

        return $sellContractProfitEntity;
    }

    /**
     * @name:computeTotalPrice
     * @desc: 计算销售利润、采购成本合计
     * @param:* @param $profitList
     * @param $entity
       @throw:
     * @return:mixed
     */
    protected function computeTotalPrice($profitList,$entity){
        $settle_quantity=$settle_amount=$buy_price=$buy_amount=0;
        if(!empty($profitList)){
            foreach($profitList as $key=>$item){
                if($item->sell_profit->settle_quantity->quantity>0) {
                    $settle_quantity += $item->sell_profit->settle_quantity->quantity;
                    $settle_amount += $item->sell_profit->settle_amount->price;
                    //$buy_price += $item->buy_cost->buy_price->price;
                    $buy_amount += $item->buy_cost->buy_amount->price;
                }
            }

        }

        $sellProfit = new SellProfit();
        $sellProfit->settle_quantity=new Quantity($settle_quantity,\ConstantMap::UNIT_TON);
        $sellProfit->settle_amount=new Price($settle_amount,\ConstantMap::CURRENCY_RMB);
        $entity->sell_profit =  $sellProfit;

        $buyCost = new  BuyCost();
        $buyCost->buy_price = new Price($buy_price,\ConstantMap::CURRENCY_RMB);
        $buyCost->buy_amount = new Price($buy_amount,\ConstantMap::CURRENCY_RMB);
        $entity->buy_cost = $buyCost;
        return $entity;

    }


}