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

class DeliveryOrderProfitService extends BaseService
{

    use DeliveryOrderProfitRepository;

    /**
     * @param DeliveryOrder $deliveryOrder
     * @param bool $persistent
     * @return DeliveryOrderProfit
     * @throws \Exception
     */
    public function createDeliveryOrderProfit(DeliveryOrder $deliveryOrder,$persistent=false)
    {

        $profitEntity=DeliveryOrderProfit::create($deliveryOrder);

        //销售利润
        $sellProfit = new SellProfit();
        $sellProfit->settle_quantity = $deliveryOrder->settle_quantity;
        $sellProfit->settle_amount = $deliveryOrder->settle_amount;
        $sell_price = ($sellProfit->settle_quantity->quantity)==0?$sellProfit->settle_amount->price:($sellProfit->settle_amount->price/$sellProfit->settle_quantity->quantity);
        $sellProfit->sell_price = new Price($sell_price,$sellProfit->settle_amount->currency);
        $profitEntity->sell_profit =  $sellProfit;

        //采购成本
        $profitEntity->buy_cost=$this->computeBuyCost($deliveryOrder);

        //其他
       /* $actual_gross_profit = $sellProfit->settle_amount->price -( $sellProfit->settle_quantity->quantity *  $profitEntity->buy_cost->buy_price->price);
        $profitEntity->actual_gross_profit = new Price($actual_gross_profit,\ConstantMap::CURRENCY_RMB);

        $vat = ($actual_gross_profit / 1.16 * 0.16)-($profitEntity->freight->price /1.1 * 0.1)- (($profitEntity->warehouse_fee->price + $profitEntity->miscellaneous_fee->price)/1.06*0.06);
        $profitEntity->vat = new Price($vat,\ConstantMap::CURRENCY_RMB);

        $sur_tax=$vat*0.12;
        $profitEntity->sur_tax = new Price($sur_tax,\ConstantMap::CURRENCY_RMB);

        $stamp_tax = $sellProfit->settle_amount->price * 0.0003;
        $profitEntity->stamp_tax = new Price($stamp_tax,\ConstantMap::CURRENCY_RMB);

        $after_tax_profit = $actual_gross_profit-($profitEntity->freight->price)-($profitEntity->warehouse_fee->price)-($profitEntity->miscellaneous_fee->price)-($profitEntity->vat->price)-($profitEntity->sur_tax->price)-($profitEntity->stamp_tax->price);
        $profitEntity->after_tax_profit  = new Price($after_tax_profit,\ConstantMap::CURRENCY_RMB);*/


        if($persistent)
            $this->getDeliveryOrderProfitRepository()->store($profitEntity);

        return $profitEntity;
    }

    /**
     * 计算发货单利润的采购成本
     * @param DeliveryOrderProfit $profit
     * @return BuyCost
     * @throws \Exception
     */
    public function computeBuyCost(DeliveryOrder $deliveryOrder)
    {
        $unit=0;
        $currency=0;
        $sum_amount=0;
        $sum_quantity=0;
        if(empty($deliveryOrder->delivery_items)){//没有发货明细
            $sum_quantity=0;
            $sum_amount=0;
        }else{
            $buyGoodsCost = new BuyGoodsCost();
            $buyGoodsCost = $buyGoodsCost->create($deliveryOrder);
            if(!empty($buyGoodsCost)){
                foreach($buyGoodsCost as & $item){
                    $unit=$item->out_quantity->unit;
                    $currency=$item->goods_price->currency;

                    $sum_amount += (($item->out_quantity->quantity) * ($item->goods_price->price) );
                    $sum_quantity += $item->out_quantity->quantity;
                }
            }
        }

        $buyCost = new  BuyCost();
        $buyCost->out_quantity = new Quantity($sum_quantity,$unit);
        $buyCost->buy_amount = new Price($sum_amount,$currency);
        $buy_price = ($buyCost->out_quantity->quantity)==0?$buyCost->buy_amount->price:($buyCost->buy_amount->price/$buyCost->out_quantity->quantity);
        $buyCost->buy_price = new Price($buy_price,$buyCost->buy_amount->currency);

        return $buyCost;
    }




}