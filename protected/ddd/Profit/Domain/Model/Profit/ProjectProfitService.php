<?php
/**
 * Created by youyi000.
 * DateTime: 2018/8/10 15:43
 * Describe：
 */

namespace ddd\Profit\Domain\Model\Profit;


use ddd\Common\Domain\BaseService;
use ddd\domain\entity\contract\Contract;
use ddd\Profit\Domain\Model\Payment\ProjectPayment;
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
use ddd\Profit\Domain\Model\Payment\IProjectPaymentRepository;
use ddd\Profit\Repository\Payment\ProjectPaymentRepository;

class ProjectProfitService extends BaseService
{

    use ProjectProfitRepository;
    use SellContractProfitRepository;

    /**
     * @name:createProjectProfit
     * @desc: 创建合同利润 对象
     * @param:* @param Project $project
     * @param bool $persistent
       @throw: * @throws \Exception
     * @return:static
     */
    public function createProjectProfit(Project $project,$persistent=false)
    {

        $projectProfitEntity=ProjectProfit::create($project);

        $sellContractProfitList = $this->getSellContratcProfitRepository()->findByProjectId($project->project_id);
        $projectProfitEntity = $this->computeTotalPrice($sellContractProfitList,$projectProfitEntity);

        if($persistent)
            $this->getProjectProfitRepository()->store($projectProfitEntity);

        return $projectProfitEntity;
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
        $project_id=0;
        if(!empty($profitList)){
            foreach($profitList as $key=>$item){

                $project_id = $item->project_id;
                $settle_quantity += $item->sell_profit->settle_quantity->quantity;
                $settle_amount += $item->sell_profit->settle_amount->price;
                /*$buy_price += $item->buy_cost->buy_price->price;*/
                $buy_amount += $item->buy_cost->buy_amount->price;

                $buy_invoice_amount+=$item->buy_invoice_amount->price;
                $sell_invoice_amount+=$item->sell_invoice_amount->price;
                $pay_amount+=$item->pay_amount->price;
                $receive_amount+=$item->receive_amount->price;

                //杂费
                if($item->contract_type==\ConstantMap::CONTRACT_CATEGORY_SUB_BUY)
                    $miscellaneous_fee+=$item->miscellaneous_fee->price;
                else
                    $miscellaneous_fee-=$item->miscellaneous_fee->price;

            }

        }

        $projectPaymentEntity = ProjectPaymentRepository::repository()->findByProjectId($entity->project_id);

        if(!empty($projectPaymentEntity)) {
            $pay_amount += $projectPaymentEntity->pay_amount->price;//付款金额，要加上“项目下付款”
            $miscellaneous_fee += $projectPaymentEntity->miscellaneous_fee->price; //杂费，要加上“项目下付款”的杂费
        }

        $sellProfit = new SellProfit();
        $sellProfit->settle_quantity=new Quantity($settle_quantity,\ConstantMap::UNIT_TON);
        $sellProfit->settle_amount=new Price($settle_amount,\ConstantMap::CURRENCY_RMB);
        $entity->sell_profit =  $sellProfit;

        $buyCost = new  BuyCost();
        $buy_price = $settle_quantity==0?$buy_amount:$buy_amount/$settle_quantity;//采购金额总和 除以 结算数量总和
        $buyCost->buy_price = new Price($buy_price,\ConstantMap::CURRENCY_RMB);
        $buyCost->buy_amount = new Price($buy_amount,\ConstantMap::CURRENCY_RMB);
        $entity->buy_cost = $buyCost;

        $entity->buy_invoice_amount = new Price($buy_invoice_amount,\ConstantMap::CURRENCY_RMB);
        $entity->sell_invoice_amount = new Price($sell_invoice_amount,\ConstantMap::CURRENCY_RMB);
        $entity->pay_amount = new Price($pay_amount,\ConstantMap::CURRENCY_RMB);
        $entity->receive_amount = new Price($receive_amount,\ConstantMap::CURRENCY_RMB);
        $entity->miscellaneous_fee = new Price($miscellaneous_fee,\ConstantMap::CURRENCY_RMB);

        //实际毛利
        $actual_gross_profit = $entity->sell_profit->settle_amount->price -( $entity->sell_profit->settle_quantity->quantity *  $entity->buy_cost->buy_price->price);
        $entity->actual_gross_profit = new Price($actual_gross_profit,\ConstantMap::CURRENCY_RMB);
       /* $entity->freight = new Price($freight,\ConstantMap::CURRENCY_RMB);
        $entity->warehouse_fee = new Price($warehouse_fee,\ConstantMap::CURRENCY_RMB);*/


        //增值税、附加税
        $vat = ($entity->actual_gross_profit->price / 1.16 * 0.16)-($entity->freight->price /1.1 * 0.1)- (($entity->warehouse_fee->price + $entity->miscellaneous_fee->price)/1.06*0.06);
        $entity->vat = new Price($vat,\ConstantMap::CURRENCY_RMB);

        $sur_tax=$vat*0.12;
        $entity->sur_tax = new Price($sur_tax,\ConstantMap::CURRENCY_RMB);

        $stamp_tax = $entity->sell_profit->settle_amount->price * 0.0003;
        $entity->stamp_tax = new Price($stamp_tax,\ConstantMap::CURRENCY_RMB);
        $after_tax_profit = $actual_gross_profit-($entity->freight->price)-($entity->warehouse_fee->price)-($entity->miscellaneous_fee->price)-($entity->vat->price)-($entity->sur_tax->price)-($entity->stamp_tax->price);
        $entity->after_tax_profit = new Price($after_tax_profit,\ConstantMap::CURRENCY_RMB);

        //资金成本
        \InterestReportService::addInterestInfo();
        \InterestReportService::addDayInterest($entity->project_id);

        $fund_cost = ProfitService::getFundCost($entity->project_id);
        $entity->fund_cost = new Price($fund_cost,\ConstantMap::CURRENCY_RMB);
        $entity->profit = new Price($entity->after_tax_profit->price - $entity->fund_cost->price,\ConstantMap::CURRENCY_RMB);

        return $entity;
    }


}