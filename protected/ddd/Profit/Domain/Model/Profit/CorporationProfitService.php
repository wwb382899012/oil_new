<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/10 15:43
 * Describe：
 */

namespace ddd\Profit\Domain\Model\Profit;


use ddd\Common\Domain\BaseService;
use ddd\domain\entity\contract\Contract;
use ddd\Profit\Domain\Model\Project;
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
use ddd\Profit\Domain\Model\Profit\ProjectProfitRepository;
use ddd\Profit\Domain\Model\Profit\SellContractProfitRepository;
use ddd\Profit\Domain\Service\ProfitService;
use ddd\Profit\Domain\Model\Profit\CorporationProfitRepository;

class CorporationProfitService extends BaseService
{

    use ProjectProfitRepository;
    use CorporationProfitRepository;

    /**
     * @name:createProjectProfit
     * @desc: 创建交易主体利润 对象
     * @param:* @param Project $project
     * @param bool $persistent
       @throw: * @throws \Exception
     * @return:static
     */
    public function createCorporationProfit($corporation_id,$persistent=false)
    {

        $corporationProfitEntity=CorporationProfit::create($corporation_id);
        $projectProfitList = $this->getProjectProfitRepository()->findByCorporationId($corporation_id);

        $corporationProfitEntity = $this->computeTotalPrice($projectProfitList,$corporationProfitEntity);

        if($persistent)
            $this->getCorporationProfitRepository()->store($corporationProfitEntity);

        return $corporationProfitEntity;
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
        $settle_quantity=$settle_amount=$buy_price=$buy_amount=0;
        $buy_invoice_amount=$sell_invoice_amount=$pay_amount=$receive_amount=0;
        $actual_gross_profit=$freight=$warehouse_fee=$miscellaneous_fee=$vat=$sur_tax=$stamp_tax=$after_tax_profit=$fund_cost=$profit=0;

        if(!empty($profitList)){
            foreach($profitList as $key=>$item){

                $settle_quantity += $item->sell_profit->settle_quantity->quantity;
                $settle_amount += $item->sell_profit->settle_amount->price;
                //$buy_price += $item->buy_cost->buy_price->price;
                $buy_amount += $item->buy_cost->buy_amount->price;

                $buy_invoice_amount+=$item->buy_invoice_amount->price;
                $sell_invoice_amount+=$item->sell_invoice_amount->price;
                $pay_amount+=$item->pay_amount->price;
                $receive_amount+=$item->receive_amount->price;

                $actual_gross_profit+=$item->actual_gross_profit->price;
                $freight+=$item->freight->price;
                $warehouse_fee+=$item->warehouse_fee->price;
                $miscellaneous_fee+=$item->miscellaneous_fee->price;
                $vat+=$item->vat->price;
                $sur_tax+=$item->sur_tax->price;
                $stamp_tax+=$item->stamp_tax->price;
                $after_tax_profit+=$item->after_tax_profit->price;
                $fund_cost+=$item->fund_cost->price;
                $profit += $item->profit->price;
            }

        }
        $sellProfit = new SellProfit();
        $sellProfit->settle_quantity=new Quantity($settle_quantity,\ConstantMap::UNIT_TON);
        $sellProfit->settle_amount=new Price($settle_amount,\ConstantMap::CURRENCY_RMB);
        $entity->sell_profit =  $sellProfit;

        $buyCost = new  BuyCost();
        $buy_price = $settle_quantity==0?$buy_amount:$buy_amount/$settle_quantity;
        $buyCost->buy_price = new Price($buy_price,\ConstantMap::CURRENCY_RMB);
        $buyCost->buy_amount = new Price($buy_amount,\ConstantMap::CURRENCY_RMB);
        $entity->buy_cost = $buyCost;

        $entity->buy_invoice_amount = new Price($buy_invoice_amount,\ConstantMap::CURRENCY_RMB);
        $entity->sell_invoice_amount = new Price($sell_invoice_amount,\ConstantMap::CURRENCY_RMB);
        $entity->pay_amount = new Price($pay_amount,\ConstantMap::CURRENCY_RMB);
        $entity->receive_amount = new Price($receive_amount,\ConstantMap::CURRENCY_RMB);

        $entity->actual_gross_profit = new Price($actual_gross_profit,\ConstantMap::CURRENCY_RMB);
        $entity->freight = new Price($freight,\ConstantMap::CURRENCY_RMB);
        $entity->warehouse_fee = new Price($warehouse_fee,\ConstantMap::CURRENCY_RMB);
        $entity->miscellaneous_fee = new Price($miscellaneous_fee,\ConstantMap::CURRENCY_RMB);
        $entity->vat = new Price($vat,\ConstantMap::CURRENCY_RMB);
        $entity->sur_tax = new Price($sur_tax,\ConstantMap::CURRENCY_RMB);
        $entity->stamp_tax = new Price($stamp_tax,\ConstantMap::CURRENCY_RMB);
        $entity->after_tax_profit = new Price($after_tax_profit,\ConstantMap::CURRENCY_RMB);
        $entity->fund_cost = new Price($fund_cost,\ConstantMap::CURRENCY_RMB);
        $entity->profit = new Price($profit,\ConstantMap::CURRENCY_RMB);

        return $entity;
    }


}